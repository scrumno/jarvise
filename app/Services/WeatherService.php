<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherService
{
    private $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    public function getForecast(float $lat, float $lon): array
    {
        // Добавляем новые параметры:
        // weather_code - код погоды (ясно, снег, дождь)
        // apparent_temperature_max - ощущаемая температура (важно для зимы!)
        // wind_speed_10m_max - ветер
        // temperature_2m_min - ночная температура
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=weather_code,temperature_2m_max,temperature_2m_min,apparent_temperature_max,precipitation_probability_max,wind_speed_10m_max&timezone=auto";

        try {
            $response = $this->http->get($url);
            $data = json_decode($response->getBody(), true);
            $daily = $data['daily'];

            // Получаем код погоды (число)
            $code = $daily['weather_code'][0] ?? 0;

            return [
                'temp_max'    => $daily['temperature_2m_max'][0] ?? 0,
                'temp_min'    => $daily['temperature_2m_min'][0] ?? 0,
                'feels_like'  => $daily['apparent_temperature_max'][0] ?? 0, // Ощущается как
                'wind_speed'  => $daily['wind_speed_10m_max'][0] ?? 0,       // м/с или км/ч
                'precip_prob' => $daily['precipitation_probability_max'][0] ?? 0,
                'condition'   => $this->decodeWeatherCode($code), // Превращаем код в понятное слово
                'is_snow'     => $this->isSnow($code), // Флаг: это снег или нет?
            ];
        } catch (\Exception $e) {
            // Возвращаем безопасную заглушку
            return [
                'temp_max'    => 0,
                'temp_min'    => 0,
                'feels_like'  => 0,
                'wind_speed'  => 0,
                'precip_prob' => 0,
                'condition'   => 'неизвестно',
                'is_snow'     => false,
            ];
        }
    }

    // Хелпер: понимает, снег сейчас или нет
    private function isSnow(int $code): bool
    {
        // Коды 71-77 (снег), 85-86 (снегопад)
        return in_array($code, [71, 73, 75, 77, 85, 86]);
    }

    // Хелпер: расшифровка кодов WMO для Open-Meteo
    private function decodeWeatherCode(int $code): string
    {
        return match ($code) {
            0 => 'Чистое небо ☀️',
            1, 2, 3 => 'Облачно ☁️',
            45, 48 => 'Туман 🌫️',
            51, 53, 55 => 'Морось 💧',
            56, 57 => 'Ледяная морось ❄️💧',
            61, 63, 65 => 'Дождь ☔',
            66, 67 => 'Ледяной дождь 🧊',
            71, 73, 75 => 'Снег ❄️',
            77 => 'Снежные зерна 🌨️',
            80, 81, 82 => 'Ливень 🌧️',
            85, 86 => 'Снегопад 🌨️❄️',
            95, 96, 99 => 'Гроза ⚡',
            default => 'Хз, посмотри в окно',
        };
    }
}
