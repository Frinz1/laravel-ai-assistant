<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class AiClient
{
    protected string $baseUrl;
    protected ?string $token;
    protected int $timeout;
    protected int $retries;
    protected int $maxTokens;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('ai.url'), '/');
        $this->token     = config('ai.token');
        $this->timeout   = (int) config('ai.timeout', 30);
        $this->retries   = (int) config('ai.retries', 2);
        $this->maxTokens = (int) config('ai.max_tokens', 300);
    }

    /**
     * @param array<int,array{role:string,content:string}> $messages
     * @param string|null $sessionId
     * @return string model text
     * @throws RequestException
     */
    public function generate(array $messages, ?string $sessionId = null): string
    {
        $req = Http::timeout($this->timeout)
            ->retry($this->retries, 200);

        if ($this->token && $this->token !== '') {
            $req = $req->withToken($this->token);
        }

        $response = $req->post("{$this->baseUrl}/generate", [
            'messages'   => $messages,
            'session_id' => $sessionId,
            'max_tokens' => $this->maxTokens,
        ])->throw();

        $json = $response->json();

        // Expecting { "text": "..." }
        if (!is_array($json) || !array_key_exists('text', $json)) {
            throw new \RuntimeException('AI response malformed: missing "text"');
        }

        $text = (string) $json['text'];
        return $text;
    }

    public function health(): bool
    {
        try {
            $resp = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $resp->ok();
        } catch (\Throwable $e) {
            return false;
        }
    }
}