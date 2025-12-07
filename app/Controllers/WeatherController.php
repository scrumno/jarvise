<?php

namespace App\Controllers;

use App\Services\WeatherService;
use App\AI\Gemini\Service\GeminiService;
use App\Telegram\Service\TelegramService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WeatherController
{
    public function __construct(
        private readonly WeatherService $weather,
        private readonly GeminiService $ai,
        private readonly TelegramService $tg
    ) {
    }

    public function handle(Request $request, Response $response)
    {
        // 1. Получаем погоду (Хабаровск)
        $weather = $this->weather->getForecast(48.48, 135.07);

        // 2. Генерируем промпт
        $prompt = "Ты — мой бро. Мы в Хабаровске. 
        Погода: макс +{$weather['temp_max']}C, дождь {$weather['rain_prob']}%.
        Напиши короткое сообщение в ТГ.
        Если дождь > 40% — скажи взять зонт. Используй сленг.";

        // 3. AI генерирует текст
        $message = $this->ai->generateText($prompt);

        // 4. Отправляем в ТГ
        $this->tg->sendMessage($message);

        // 5. Ответ
        $payload = json_encode([
            'status'  => 'success',
            'data'    => $weather,
            'message' => $message,
        ]);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
