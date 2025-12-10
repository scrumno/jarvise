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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        // 1. Получаем JSON от Telegram
        $update = $request->getParsedBody();

        // 2. Проверяем структуру (Телеграм присылает вложенный массив)
        if (!isset($update['message']['text'])) {
            // Если это не текст, просто отвечаем ОК
            return $response->withStatus(200);
        }

        // ВАЖНО: Берем данные из правильных полей
        $chatId = $update['message']['chat']['id'];
        $userMessage = $update['message']['text'];

        // Защита от зацикливания (не отвечаем сами себе)
        if (isset($update['message']['from']['is_bot']) && $update['message']['from']['is_bot']) {
            return $response->withStatus(200);
        }

        try {
            // 3. Спрашиваем Gemini
            $reply = $this->geminiService->chat((string) $chatId, $userMessage);

            // 4. Отправляем ответ
            $this->telegramService->sendMessage((string) $chatId, $reply);
        } catch (\Exception $e) {
            // Логируем ошибку, если она случилась
            file_put_contents(__DIR__ . '/../../../../../../telegram_error.log', $e->getMessage(), FILE_APPEND);
        }

        return $response->withStatus(200);
    }
}
