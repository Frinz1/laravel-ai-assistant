<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | URL:      Base URL of your FastAPI service.
    | TOKEN:    Optional Bearer token if your AI requires auth.
    | TIMEOUT:  HTTP client timeout in seconds.
    | RETRIES:  Number of retry attempts on transient failures.
    | MAX_TOKENS: Suggested max tokens to ask from AI per reply.
    |
    */

    'url'        => env('AI_URL', 'http://127.0.0.1:8001'),
    'token'      => env('AI_TOKEN', null),
    'timeout'    => (int) env('AI_TIMEOUT', 30),
    'retries'    => (int) env('AI_RETRIES', 2),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 300),

];