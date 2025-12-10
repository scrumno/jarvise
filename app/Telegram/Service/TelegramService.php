<?php

namespace App\Telegram\Service;

use GuzzleHttp\Client;

class TelegramService
{
    public function __construct(
        private readonly Client $http,
        private readonly string $url,
        private readonly string $adminChatId,
    ) {
    }

    public function getAdminChatId()
    {
        return $this->adminChatId;
    }

    public function sendMessage(string $text): void
    {
        try {
            $this->http->post($this->url . '/sendMessage', [
                'json' => [
                    'chat_id'    => $this->adminChatId,
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
        $this->http->post($this->url . '/sendMessage', [
            'json' => [
                'chat_id'    => $this->adminChatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ],
        ]);
    }
}
