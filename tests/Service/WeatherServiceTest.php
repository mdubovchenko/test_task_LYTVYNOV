<?php

namespace App\Tests\Service;

use App\Service\WeatherService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WeatherServiceTest extends TestCase
{
    public function testGetWeatherReturnsCorrectData()
    {
        $mockResponseData = [
            'location' => [
                'name' => 'London',
                'country' => 'UK',
            ],
            'current' => [
                'temp_c' => 18.5,
                'condition' => ['text' => 'Sunny'],
                'humidity' => 60,
                'wind_kph' => 15.2,
                'last_updated' => '2025-05-21 12:00',
            ],
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getContent')
            ->willReturn(json_encode($mockResponseData));

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn($mockResponse);

        $service = new WeatherService($mockHttpClient, 'dummy-api-key');
        $result = $service->getWeatherData('London');

        $this->assertEquals('London', $result['city']);
        $this->assertEquals('UK', $result['country']);
        $this->assertEquals(18.5, $result['temperature']);
        $this->assertEquals('Sunny', $result['condition']);
        $this->assertEquals(60, $result['humidity']);
        $this->assertEquals(15.2, $result['wind_speed']);
        $this->assertEquals('2025-05-21 12:00', $result['last_updated']);
    }

    public function testGetWeatherHandlesApiError()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getContent')
            ->willThrowException(new \Exception('City not found'));

        $mockHttpClient->method('request')
            ->willReturn($mockResponse);

        $service = new WeatherService($mockHttpClient, 'dummy-api-key');

        $result = $service->getWeatherData('InvalidCity');

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('City not found', $result['error']);
    }
}
