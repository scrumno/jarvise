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

    private function log($msg)
    {
        // Пишем в корень проекта в файл step.log
        file_put_contents(__DIR__ . '/../../../../../../step.log', date('H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->log('1. Скрипт запущен!');

        $update = $request->getParsedBody();

        // Если $update пустой, попробуем прочитать raw body (иногда Slim шалит)
        if (empty($update)) {
            $body = (string) $request->getBody();
            $update = json_decode($body, true);
            $this->log('1.1 Raw Body decoded: ' . substr($body, 0, 50) . '...');
        }

        if (!$update) {
            $this->log('ОШИБКА: Пришел пустой запрос');

            return $response->withStatus(200);
        }

        $this->log('2. Данные получены. Update ID: ' . ($update['update_id'] ?? 'none'));

        if (!isset($update['message']['text'])) {
            $this->log('СТОП: Это не текстовое сообщение');

            return $response->withStatus(200);
        }

        $chatId = $update['message']['chat']['id'];
        $userMessage = $update['message']['text'];
        $isBot = $update['message']['from']['is_bot'] ?? false;

        $this->log("3. Чат: $chatId, Юзер пишет: $userMessage");

        if ($isBot) {
            $this->log('СТОП: Это сообщение от бота');

            return $response->withStatus(200);
        }

        try {
            $this->log('4. Отправляем запрос в Gemini...');

            // Засекаем время
            $start = microtime(true);
            $reply = $this->geminiService->chat((string) $chatId, $userMessage);
            $time = round(microtime(true) - $start, 2);

            $this->log("5. Gemini ответил за {$time} сек: " . mb_substr($reply, 0, 20) . '...');

            $this->log('6. Отправляем ответ в Telegram...');
            $this->telegramService->sendMessage((string) $chatId, $reply);

            $this->log('7. УСПЕХ! Сообщение отправлено.');
        } catch (\Exception $e) {
            $this->log('CRITICAL ERROR: ' . $e->getMessage());
            $this->log('Trace: ' . $e->getTraceAsString());
        }

        return $response->withStatus(200);
    }
}
