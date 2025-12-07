<?php

namespace App\AI\Gemini\Service;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class GeminiService
{
    private $http;
    private $settings;

    public function __construct(Client $http, ContainerInterface $c)
    {
        $this->http = $http;
        $this->settings = $c->get('settings')['api'];
    }

    public function generateText(string $prompt): string
    {
        $proxy = rtrim($this->settings['proxy_url'], '/');
        $key = $this->settings['gemini_key'];

        // Используем gemini-pro для стабильности
        $url = "{$proxy}/v1beta/models/gemini-2.5-flash-live:generateContent?key={$key}";

        $body = ['contents' => [['parts' => [['text' => $prompt]]]]];

        try {
            $response = $this->http->post($url, ['json' => $body]);
            $data = json_decode($response->getBody(), true);

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Бро, я не понял, что ответил Гугл.';
        } catch (\Exception $e) {
            return 'Ошибка AI: ' . $e->getMessage();
        }
    }
}
