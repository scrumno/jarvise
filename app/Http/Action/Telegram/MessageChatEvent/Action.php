<?php

namespace App\Http\Action\Telegram\MessageChatEvent;

use App\AI\Gemini\Service\GeminiService;
use App\Telegram\Service\TelegramService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Action
{
    public function __construct(
        private readonly GeminiService $geminiService,
        private readonly TelegramService $telegramService,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response)
    {
        $input = $request->getParsedBody();

        if (!isset($input['message']['text'])) {
            return $response->withStatus(200);
        }

        $userMessage = $input['message']['text'];
        $chatId = $input['message']['chat']['id'];

        if (isset($input['message']['from']['is_bot']) && $input['message']['from']['is_bot']) {
            return $response->withStatus(200);
        }

        $message = $this->geminiService->chat($chatId, $userMessage);

        $this->telegramService->sendMessage($chatId, $message);

        return $response->withStatus(200);
    }
}
