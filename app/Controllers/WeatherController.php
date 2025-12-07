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
        // 1. Получаем данные о погоде (Москва)
        $weather = $this->jarvis->getWeather(55.75, 37.61);

        $rainProb = $weather['daily']['precipitation_probability_max'][0] ?? 0;
        $tempMax = $weather['daily']['temperature_2m_max'][0] ?? 0;

        // 2. Формируем запрос к ИИ
        $prompt = "Ты - Джарвис. Прогноз: макс. температура {$tempMax}°C, вероятность дождя {$rainProb}%. Напиши короткое, полезное и слегка ироничное сообщение хозяину. Напомни про зонт, если надо.";

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
