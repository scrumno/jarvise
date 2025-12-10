<?php

namespace App\AI\Gemini\Service;

use App\Database\ChatMessage\Model\ChatMessage;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class GeminiService
{
    public function __construct(
        private readonly Client $http,
        private readonly string $url,
        private readonly ContainerInterface $c,
        private readonly string $systemInstruction,
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

    public function chat(string $chatId, string $userMessage): string
    {
        ChatMessage::create([
            'chat_id' => $chatId,
            'role'    => 'user',
            'content' => $userMessage,
        ]);

        $history = ChatMessage::where('chat_id', $chatId)->orderBy('created_at', 'desc')->take(20)->get()->reverse()->values();

        $contents = $history->map(function ($msg) {
            return [
                'role'  => $msg->role,
                'parts' => [['text' => $msg->content]],
            ];
        })->toArray();

        $res = $this->http->post($this->url, [
            'json' => [
                'system_instruction' => [
                    'parts' => [
                        'text' => $this->systemInstruction,
                    ],
                ],
                'contents' => $contents,
            ],
        ]);

        $data = json_decode($res->getBody(), true);

        $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Ошибка, пустой ответ';

        ChatMessage::create([
            'chat_id' => $chatId,
            'role'    => 'model',
            'content' => $botReply,
        ]);

        return $botReply;
    }
}
