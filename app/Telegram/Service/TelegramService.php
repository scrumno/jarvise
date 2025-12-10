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

    public function sendMessage(string $chatId, string $text): void
    {
        try {
            $this->http->post($this->url . '/sendMessage', [
                'json' => [
                    'chat_id'    => $chatId,
                    'text'       => $text,
                    'parse_mode' => 'Markdown',
                ],
            ]);
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../../../../telegram_error.log', date('Y-m-d H:i:s') . ' Error: ' . $e->getMessage() . "\n", FILE_APPEND);
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
