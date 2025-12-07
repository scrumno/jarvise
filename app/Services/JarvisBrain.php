<?php

namespace App\Services;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class JarvisBrain
{
    private $http;
    private $settings;

    public function __construct(Client $http, ContainerInterface $container)
    {
        $this->http = $http;
        $this->settings = $container->get('settings')['api'];
    }

    // Получить погоду
    public function getWeather(float $lat, float $lon)
    {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=precipitation_sum,precipitation_probability_max&timezone=auto";
        $response = $this->http->get($url);

        return json_decode($response->getBody(), true);
    }

    // Спросить Gemini (через Cloudflare Proxy)
    public function askGemini(string $prompt)
    {
        $proxy = rtrim($this->settings['proxy_url'], '/');
        $key = $this->settings['gemini_key'];

        // Формируем URL к твоему Worker'у
        $url = "{$proxy}/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";

        $body = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
        ];

        try {
            $response = $this->http->post($url, [
                'json' => $body,
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Джарвис не понял ответа от сервера.';
        } catch (\Exception $e) {
            return 'Ошибка связи с мозгом: ' . $e->getMessage();
        }
    }

    // Отправить в Telegram
    public function notifyTelegram(string $message)
    {
        $token = $this->settings['tg_token'];
        $chatId = $this->settings['tg_chat_id'];

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        try {
            $this->http->post($url, [
                'json' => [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'Markdown', // Можно выделять жирным
                ],
            ]);
        } catch (\Exception $e) {
            // Игнорируем ошибки отправки, чтобы не валить весь скрипт
        }
    }
}
