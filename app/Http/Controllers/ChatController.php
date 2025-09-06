<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Support\PktMap;
use App\Support\TopicNormalizer;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ----------------------- Small helpers ----------------------- */

    private function yesLike(string $t): bool
    {
        $t = mb_strtolower($t);
        $yes = [
            'yes','yeah','yup','y','oo','opo','sige','go','okay','ok','sure','confirm',
            'proceed','continue','send it','send','download','dl','push',
            'pkt','packet tracer','packet-tracer','give pkt','give me pkt','i want pkt'
        ];
        foreach ($yes as $w) {
            if (preg_match('/\b'.preg_quote($w, '/').'\b/i', $t)) return true;
        }
        return false;
    }

    private function looksLikeTopicYes(string $t, ?string $topic): bool
    {
        if (!$topic) return false;
        return preg_match('/\b'.preg_quote(mb_strtolower($topic), '/').'\b/', mb_strtolower($t)) === 1;
    }

    private function generateConversationTitle(string $message): string
    {
        $title = trim($message);
        $title = mb_substr($title, 0, 50);
        if (mb_strlen($message) > 50) $title .= '...';
        return $title !== '' ? $title : 'New conversation';
    }

    private function getFallbackResponse(): string
    {
        $fallbacks = [
            "I’m having trouble reaching the AI right now. Please try again in a moment.",
            "The AI service is temporarily unavailable. Your question is saved—please retry shortly.",
            "Network hiccup reaching the AI backend. Please resend your message or try again later.",
        ];
        return $fallbacks[array_rand($fallbacks)];
    }

    /**
     * Coerce/repair possibly non-JSON model output into an array.
     */
    private function coerceJson(string $raw): array
    {
        $raw = trim($raw);

        // Strip ```json fences if present
        if (preg_match('/^```(?:json)?\s*(.*)```$/is', $raw, $m)) {
            $raw = trim($m[1]);
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;

        // Extract the first {...} block
        $start = strpos($raw, '{');
        $end   = strrpos($raw, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $candidate = substr($raw, $start, $end - $start + 1);
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) return $decoded;
        }

        // Safe fallback
        return [
            'reply'      => "Sorry, that seems to be outside of my scope.",
            'action_key' => 'NONE',
            'awaiting'   => 'completed',
            'intent'     => 'out_of_scope',
        ];
    }

    /* ----------------------- Views / CRUD ----------------------- */

    public function index()
    {
        $user = Auth::user();

        if (!$user->hasActivePlan()) {
            return redirect()->route('plans.index')->with('error', 'Please select a plan to continue.');
        }

        $conversations = $user->conversations()
            ->with('latestMessage')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function getConversation(Request $request, $conversationId)
    {
        $userId = $request->user()->id;

        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get(['id', 'content', 'is_user', 'ai_response_time', 'created_at']);

        return response()->json([
            'success'         => true,
            'conversation_id' => (int) $conversationId,
            'title'           => $conversation->title,
            'messages'        => $messages,
        ]);
    }

    public function deleteConversation(Request $request, $conversationId)
    {
        $userId = $request->user()->id;

        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /* ----------------------- Main Chat API ----------------------- */

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:12000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        if (method_exists($user, 'hasActivePlan') && !$user->hasActivePlan()) {
            return response()->json(['success' => false, 'error' => 'No active plan. Please select a plan to continue.'], 402);
        }
        if (method_exists($user, 'canSendMessage') && !$user->canSendMessage()) {
            return response()->json(['success' => false, 'error' => 'Plan limit reached.'], 402);
        }

        $conversationId = $request->input('conversation_id', $request->input('conversationId'));
        $incoming = trim($request->input('message'));

        if (!$conversationId) {
            $conversation = Conversation::create([
                'user_id'   => $user->id,
                'title'     => $this->generateConversationTitle($incoming),
                'is_active' => true,
            ]);
            $conversationId = $conversation->id;
        } else {
            $conversation = Conversation::where('id', $conversationId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        $userMessage = Message::create([
            'conversation_id'  => $conversationId,
            'user_id'          => $user->id,
            'content'          => $incoming,
            'is_user'          => true,
            'ai_response_time' => null,
        ]);

        $history = Message::where('conversation_id', $conversationId)
            ->orderBy('id')
            ->get(['content', 'is_user'])
            ->map(fn($m) => ['role' => $m->is_user ? 'user' : 'assistant', 'content' => $m->content])
            ->values()
            ->all();

        // Awaiting yes/no for PKT?
        $awaiting = session("awaiting.$conversationId");
        $topic    = session("topic.$conversationId");

        if ($awaiting === 'pkt_choice') {
            if ($this->yesLike($incoming) || $this->looksLikeTopicYes($incoming, $topic)) {
                if ($path = PktMap::pathFor($topic)) {
                    $url = URL::temporarySignedRoute('pkt.download', now()->addMinutes(15), ['topic' => $topic]);
                    session()->forget(["awaiting.$conversationId", "topic.$conversationId"]);
                    $reply = "Here’s your Packet Tracer simulation for {$topic}: {$url}";
                    $assistantMessage = Message::create([
                        'conversation_id'  => $conversationId,
                        'user_id'          => $user->id,
                        'content'          => $reply,
                        'is_user'          => false,
                        'ai_response_time' => 0.0,
                    ]);
                    $conversation->touch();
                    return response()->json([
                        'success'           => true,
                        'conversation_id'   => $conversationId,
                        'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
                        'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
                    ]);
                }
            } elseif ($this->noLike($incoming)) {
                session()->forget(["awaiting.$conversationId", "topic.$conversationId"]);
                $reply = "No problem. If you need it later, just ask.";
                $assistantMessage = Message::create([
                    'conversation_id'  => $conversationId,
                    'user_id'          => $user->id,
                    'content'          => $reply,
                    'is_user'          => false,
                    'ai_response_time' => 0.0,
                ]);
                $conversation->touch();
                return response()->json([
                    'success'           => true,
                    'conversation_id'   => $conversationId,
                    'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
                    'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
                ]);
            }
            // else: fall through to model
        }

        /* ---- DIRECT PKT REQUEST FAST-PATH ---------------------------------- */
        $fast = mb_strtolower($incoming);
        if (preg_match('/\b(pkt|packet\s*-?\s*tracer)\b.*\b(virus|trojan|worm|worms|adware|keylogger|spyware|ransomware|email[-\s]?phishing|spear[-\s]?phishing|smishing|clone[-\s]?phishing)\b/i', $fast, $m)) {
            $rawTopic = $m[2];
            $topicCanon = TopicNormalizer::toCanonical($rawTopic);
            if ($topicCanon && ($path = PktMap::pathFor($topicCanon))) {
                $url = URL::temporarySignedRoute('pkt.download', now()->addMinutes(15), ['topic' => $topicCanon]);
                $assistantMessage = Message::create([
                    'conversation_id'  => $conversationId,
                    'user_id'          => $user->id,
                    'content'          => "Here’s your Packet Tracer simulation for {$topicCanon}: {$url}",
                    'is_user'          => false,
                    'ai_response_time' => 0.0,
                ]);
                $conversation->touch();
                return response()->json([
                    'success'           => true,
                    'conversation_id'   => $conversationId,
                    'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
                    'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
                ]);
            }
        }
        /* -------------------------------------------------------------------- */

        // ---- Call model ----
        $t0 = microtime(true);
        try {
            $model = $this->getAIResponse($history, (string)$conversationId); // ARRAY
            $elapsed = microtime(true) - $t0;

            $intent     = $model['intent'] ?? 'educational';
            $rawTopic   = $model['topic']  ?? null;
            $topicCanon = TopicNormalizer::toCanonical($rawTopic);

            // Explicitly out-of-scope filters
            if (preg_match('/\b(essay|write .* essay|paragraph|composition)\b/i', $incoming)) {
                $intent = 'out_of_scope';
            }
            if (preg_match('/\b(\d+\s*\+\s*\d+|what\s+is\s+1\s*\+\s*1)\b/i', $incoming)) {
                $intent = 'out_of_scope';
            }
            if ($rawTopic && !$topicCanon) {
                $intent = 'out_of_scope';
            }

            if ($intent === 'out_of_scope') {
                $reply = "Sorry, that seems to be outside of my scope.";
                $assistantMessage = Message::create([
                    'conversation_id'  => $conversationId,
                    'user_id'          => $user->id,
                    'content'          => $reply,
                    'is_user'          => false,
                    'ai_response_time' => $elapsed,
                ]);
                $conversation->touch();
                return response()->json([
                    'success'           => true,
                    'conversation_id'   => $conversationId,
                    'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
                    'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
                ]);
            }

            $reply  = (string)($model['reply'] ?? $this->getFallbackResponse());
            $action = $model['action_key'] ?? 'NONE';
            $await  = $model['awaiting']   ?? 'completed';

            if (in_array($intent, ['detection', 'incident_response'], true)) {
                if ($await === 'pkt_choice' && $action === 'SERVE_PKT' && $topicCanon) {
                    session(["awaiting.$conversationId" => 'pkt_choice']);
                    session(["topic.$conversationId"    => $topicCanon]);
                }
            } else {
                // educational or municipal_info: never hold PKT state
                session()->forget(["awaiting.$conversationId", "topic.$conversationId"]);
            }

            $assistantMessage = Message::create([
                'conversation_id'  => $conversationId,
                'user_id'          => $user->id,
                'content'          => $reply,
                'is_user'          => false,
                'ai_response_time' => $elapsed,
            ]);

        } catch (\Throwable $e) {
            $assistantMessage = Message::create([
                'conversation_id'  => $conversationId,
                'user_id'          => $user->id,
                'content'          => $this->getFallbackResponse(),
                'is_user'          => false,
                'ai_response_time' => null,
            ]);
            $conversation->touch();

            return response()->json([
                'success'           => false,
                'conversation_id'   => $conversationId,
                'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
                'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
                'error'             => app()->isLocal() ? $e->getMessage() : 'AI backend error',
            ], 502);
        }

        $conversation->touch();

        return response()->json([
            'success'           => true,
            'conversation_id'   => $conversationId,
            'user_message'      => ['id' => $userMessage->id, 'content' => $userMessage->content, 'is_user' => true],
            'assistant_message' => ['id' => $assistantMessage->id, 'content' => $assistantMessage->content, 'is_user' => false],
        ]);
    }

    /* ----------------------- Signed PKT download ----------------------- */

    public function downloadPkt(Request $request, string $topic)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Link expired or invalid.');
        }

        $canonical = TopicNormalizer::toCanonical($topic);
        $path = PktMap::pathFor($canonical);

        abort_unless($path && Storage::disk('simulations')->exists($path), 404, 'PKT not found.');

        $filename = basename($path);

        return Storage::disk('simulations')->download(
            $path,
            $filename,
            [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /* ----------------------- Model call (Gemini JSON) ----------------------- */

    /**
     * Returns ARRAY: { reply, action_key, topic?, awaiting, intent }
     */
    private function getAIResponse(array $messages, ?string $sessionId = null): array
    {
        // Flatten dialog
        $userText = '';
        foreach ($messages as $m) {
            $userText .= ($m['role'] === 'user' ? "User: " : "Assistant: ") . $m['content'] . "\n";
        }

        $model    = config('services.gemini.model');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        // Topics we can map to PKT
        $allowedTopics = [
            "virus","trojan","worms","adware","keylogger","spyware","ransomware",
            "email phishing","spear phishing","smishing","clone phishing",
            "email-phishing","spear-phishing","clone-phishing","worm"
        ];

        // Schema the model must follow
        $schema = [
            "type" => "object",
            "properties" => [
                "reply"      => ["type" => "string"],
                "action_key" => ["type" => "string", "enum" => ["SERVE_PKT","NONE"]],
                "topic"      => ["type" => "string", "enum" => $allowedTopics],
                "awaiting"   => ["type" => "string", "enum" => ["pkt_choice","completed"]],
                "intent"     => ["type" => "string", "enum" => [
                    "educational",
                    "detection",
                    "incident_response",
                    "municipal_info",
                    "out_of_scope"
                ]]
            ],
            "required" => ["reply","action_key","awaiting","intent"]
        ];

        // ----------------- SYSTEM INSTRUCTION -----------------
        $systemInstruction = [
            "role"  => "user",
            "parts" => [[ "text" =>
"YOU ARE: SAN FABIAN MUNICIPAL HALL AI.

ALLOWED INTENTS
- educational: explain municipal cybersecurity topics (virus, trojan, worms, adware, keylogger, spyware, ransomware, email-phishing, spear-phishing, smishing, clone-phishing). No essays.
- detection / incident_response: user reports a suspected/active problem; return step-by-step guidance.
- municipal_info: general info about San Fabian / Municipal Hall (mayor, offices/departments, address, office hours, contacts, brief history).
- Packet Tracer inquiries are in-scope:
  • “What is Packet Tracer?” → educational (describe tool and how LGU uses simulations). No PKT question.
  • “Give me a Packet Tracer for {topic}” → may be served by the app’s fast-path; otherwise follow detection/IR shape.
- out_of_scope: math (e.g., 'what is 1+1'), generic essays, unrelated topics.

STRICT RULES
- Do NOT write meta lines such as 'As an AI…' or 'I cannot create/transmit files'.
- NEVER promise to prepare or generate files; the host app handles files via action_key.
- For detection/incident_response, do NOT include long lists of 'symptoms' or speculative signs; focus on actions.

- detection / incident_response (MANDATORY FORMAT; MARKDOWN):
  1) Start with a one- or two-sentence **summary** that explains the risk and stresses quick action.
     Do NOT include long lists of possible “symptoms” or speculative descriptions of what the user might see.
  2) A section titled exactly **Preventative Measures** using bullet points. Each bullet begins with a **bolded action** label (e.g., **Install and Maintain Antivirus Software:** …).
  3) A section titled exactly **What to Do If You Suspect Infection** using a **numbered list** with precise, actionable steps (disconnect, safe mode, full scan, quarantine/remove, reset passwords, report to LGU ICT). Tailor steps to the specific threat (e.g., ransomware backups/no payment; phishing reporting; trojan persistence checks).
  4) End with this exact sentence (and nothing after it on the same message):
Do you want a simple Packet Tracer simulation about {topic}?
  → For detection/incident_response set action_key='SERVE_PKT' and awaiting='pkt_choice'.

- educational:
  • Clear, concise explanation (no essay). Use short Markdown where helpful.
  • Naturally weave ONE relevant San Fabian LGU detail only if it helps (e.g., ICT scanning policy, shared drives).
  • No PKT question. Set action_key='NONE', awaiting='completed'.

- municipal_info:
  • Answer directly (e.g., 'The current mayor is Marlyn Espino Agbayani.') and add brief LGU context if helpful.
  • No PKT question. Set action_key='NONE', awaiting='completed'.

- out_of_scope:
  • Reply ONLY with: 'Sorry, that seems to be outside of my scope.'
  • action_key='NONE', awaiting='completed'.

OUTPUT CONTRACT
- Return STRICT JSON that conforms to the schema. The 'reply' value may contain Markdown, but no text is allowed outside the JSON object."
            ]]
        ];
        // ------------------------------------------------------

        $payload = [
            "systemInstruction" => $systemInstruction,
            "contents" => [[
                "role"  => "user",
                "parts" => [["text" => $userText]]
            ]],
            "generationConfig" => [
                "responseMimeType" => "application/json",
                "responseSchema"   => $schema,
                "maxOutputTokens"  => 900
            ]
        ];

        $resp = Http::timeout((int) config('services.ai.timeout', 30))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint.'?key='.config('services.gemini.key'), $payload)
            ->throw();

        $data     = $resp->json();
        $jsonText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        // Harden: coerce/repair JSON so callers always get an array
        return $this->coerceJson($jsonText);
    }
}