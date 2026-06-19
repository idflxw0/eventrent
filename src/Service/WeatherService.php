<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private const API_URL = 'https://api.openweathermap.org/data/2.5/forecast';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey = '',
    ) {
    }

    public function getForecast(string $city, \DateTimeImmutable $date): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        $now = new \DateTimeImmutable();
        $daysUntilEvent = (int) $now->diff($date)->days;

        if ($daysUntilEvent > 7) {
            return 'Prévision météo non disponible — la date est trop éloignée (> 7 jours).';
        }

        try {
            $response = $this->httpClient->request('GET', self::API_URL, [
                'query' => [
                    'q' => $city . ',FR',
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'fr',
                    'cnt' => (int) min($daysUntilEvent * 8 + 8, 40),
                ],
                'timeout' => 3,
            ]);

            $data = $response->toArray();

            $targetTimestamp = $date->setTime(12, 0)->getTimestamp();
            $closest = null;
            $minDiff = PHP_INT_MAX;

            foreach ($data['list'] ?? [] as $entry) {
                $diff = abs($entry['dt'] - $targetTimestamp);
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closest = $entry;
                }
            }

            if (!$closest) {
                return 'Données météo indisponibles pour cette date.';
            }

            $temp = round($closest['main']['temp']);
            $desc = $closest['weather'][0]['description'] ?? 'inconnu';
            $rain = isset($closest['rain']['3h']) ? ' — Risque de pluie' : '';

            return sprintf('%s, %d°C%s', ucfirst($desc), $temp, $rain);
        } catch (\Throwable) {
            return 'Météo : service temporairement indisponible.';
        }
    }
}
