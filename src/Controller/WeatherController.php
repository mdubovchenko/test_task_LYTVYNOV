<?php

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/weather/{city}', name: 'weather')]
class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    public function __invoke(Request $request, string $city = 'Kyiv'): Response
    {
        $weather = $this->weatherService->getWeatherData($city);

        return $this->render('weather.html.twig', [
            'weather' => $weather,
            'city' => $city
        ]);
    }
}
