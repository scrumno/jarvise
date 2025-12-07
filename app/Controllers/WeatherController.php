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
        $w = $this->weather->getForecast(48.48, 135.07);

        // 2. Генерируем промпт
        $prompt = "Ты — мой кент в Хабаровске. 
Погода на сегодня: 
- На улице: {$w['condition']}
- Температура: днем {$w['temp_max']}°C (ночью до {$w['temp_min']}°C).
- Ощущается как: {$w['feels_like']}°C (Ветер: {$w['wind_speed']} км/ч).
- Шанс осадков: {$w['precip_prob']}%.

Напиши короткое сообщение в ТГ (до 2 предложений). Стиль: современный сленг, дерзкий.
1. Если 'feels_like' ниже -20, ори, чтобы я оделся как капуста.
2. Если идет снег (is_snow = true), скажи про дрифт или сугробы.
3. Если ветер сильный (>20 км/ч), скажи, что сдует лицо.";

        // 3. AI генерирует текст
        $message = $this->ai->generateText($prompt);

        // 4. Отправляем в ТГ
        $this->tg->sendMessage($message);

        // 5. Ответ
        $payload = json_encode([
            'status'  => 'success',
            'data'    => $w,
            'message' => $message,
        ]);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
