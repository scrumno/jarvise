<?php

namespace App\Telegram\Service;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class TelegramService
{
    private $token;
    private $chatId;

    public function __construct(
        private readonly Client $http,
        private readonly ContainerInterface $c
    ) {
        $settings = $c->get('settings')['api'];
        $this->token = $settings['tg_token'];
        $this->chatId = $settings['tg_chat_id'];
    }

    public function sendMessage(string $text): void
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        try {
            $this->http->post($url, [
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
}
