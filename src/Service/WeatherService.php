<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private const UNKNOWN = 'unknown';
    private const LOG_PATH = __DIR__ . '/../../var/log/weather_errors.log';

    public function __construct(
        private HttpClientInterface $weatherClient,
        private string $weatherApiKey,
    ) {
    }

    public function getWeatherData(string $city): array
    {
        $uri = "/v1/current.json?key={$this->weatherApiKey}&q={$city}";

        try {
            $response = $this->weatherClient->request('GET', $uri, [
                'timeout' => 30,
            ]);

            $data = json_decode($response->getContent(), true);

            return [
                'city' => $data['location']['name'] ?? self::UNKNOWN,
                'country' => $data['location']['country'] ?? self::UNKNOWN,
                'temperature' => $data['current']['temp_c'] ?? self::UNKNOWN,
                'condition' => $data['current']['condition']['text'] ?? self::UNKNOWN,
                'humidity' => $data['current']['humidity'] ?? self::UNKNOWN,
                'wind_speed' => $data['current']['wind_kph'] ?? self::UNKNOWN,
                'last_updated' => $data['current']['last_updated'] ?? self::UNKNOWN,
            ];
        } catch (\Throwable $e) {
            $this->logError($e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function logError(string $message): void
    {
        $formatted = sprintf("[%s] ERROR: %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(self::LOG_PATH, $formatted, FILE_APPEND);
    }
}