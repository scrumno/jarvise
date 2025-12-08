<?php

namespace App\AI\Gemini\Service;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class GeminiService
{
    public function __construct(
        private readonly Client $http,
        private readonly string $url,
        private readonly ContainerInterface $c,
    ) {
    }

    public function generateText(string $prompt): string
    {
        $body = ['contents' => [['parts' => [['text' => $prompt]]]]];

        try {
            $response = $this->http->post($this->url, ['json' => $body]);
            $data = json_decode($response->getBody(), true);

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Бро, я не понял, что ответил Гугл.';
        } catch (\Exception $e) {
            return 'Ошибка AI: ' . $e->getMessage();
        }
    }

    public function generateTextForPost(): string
    {
        $body = ['contents' => [['parts' => [['text' => $this->c->get('prompts')['generatePost']]]]]];

        $response = $this->http->post($this->url, ['json' => $body]);
        $data = json_decode($response->getBody(), true);

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
}
