<?php

namespace App\Telegram\Query\CreatePostTelegram;

use App\AI\Gemini\Service\GeminiService;
use App\Telegram\Service\TelegramService;

class Handler
{
    public function __construct(
        private readonly TelegramService $telegramService,
        private readonly GeminiService $geminiService,
    ) {
        throw new \Exception('Not implemented');
    }

    public function handle(
        array $message,
        string $chatId
    ) {
        if ($chatId !== $this->telegramService->getAdminChatId()) {
            return;
        }

        $text = $this->geminiService->generateTextForPost();

        $this->telegramService->createPost($text);

        throw new \Exception('Not implemented');
    }
}
