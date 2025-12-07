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
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=precipitation_sum,precipitation_probability_max,temperature_2m_max&timezone=auto";

        $response = $this->http->get($url);
        $data = json_decode($response->getBody(), true);

        return [
            'rain_prob' => $data['daily']['precipitation_probability_max'][0] ?? 0,
            'temp_max'  => $data['daily']['temperature_2m_max'][0] ?? 0,
        ];
    }
}
