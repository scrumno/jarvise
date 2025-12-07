<?php

namespace App\Controllers;

use App\Services\JarvisBrain;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WeatherController
{
    private $jarvis;

    public function __construct(JarvisBrain $jarvis)
    {
        $this->jarvis = $jarvis;
    }

    public function check(Request $request, Response $response)
    {
        // 1. Получаем данные о погоде (Хабаровск)
        // Координаты: 48.48, 135.07
        $weather = $this->jarvis->getWeather(48.48, 135.07);

        // Берем данные (если API вернул null, ставим 0)
        $rainProb = $weather['daily']['precipitation_probability_max'][0] ?? 0;
        $tempMax = $weather['daily']['temperature_2m_max'][0] ?? 0;

        // 2. Формируем запрос к ИИ (Новая личность)
        $prompt = "Ты — мой кент и лучший друг. Мы в Хабаровске.
        Прогноз на сегодня: макс. температура {$tempMax}°C.
        
        Напиши мне короткое сообщение в Телеграм (максимум 2 предложения).
        Стиль: современный, смешной, используй сленг (типа «бро», «жесть», «имба»), можно легкий мат или наезды.
        
        Задача:
        Если больше -20, скажи, что сильно холодно
        Если меньше -20, скажи, что не сильно холодно
        Если есть ветер или снег, скажи, что есть снег
        ";

        // 3. Получаем текст от ИИ
        $aiMessage = $this->jarvis->askGemini($prompt);

        // 4. Отправляем в Телеграм
        $this->jarvis->notifyTelegram($aiMessage);

        // 5. Отдаем JSON для PWA (или для логов)
        $payload = json_encode([
            'status'  => 'success',
            'weather' => [
                'rain_prob' => $rainProb,
                'temp_max'  => $tempMax,
            ],
            'message' => $aiMessage,
        ], JSON_UNESCAPED_UNICODE);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
