<?php

namespace App\Telegram\Service;

use GuzzleHttp\Client;

class TelegramService
{
    public function __construct(
        private readonly Client $http,
        private readonly string $url,
        private readonly string $chatId,
    ) {
    }

    public function sendMessage(string $text): void
    {
        try {
            $this->http->post($this->url, [
                'json' => [
                    'chat_id'    => $this->chatId,
                    'text'       => $text,
                    'parse_mode' => 'Markdown',
                ],
            ]);
        } catch (\Exception $e) {
            // Log error
        }
    }

    public function createPost(string $text): void
    {
    }
}
