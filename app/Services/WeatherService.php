<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    /**
     * Get weather forecast for a specific location.
     * Uses Open-Meteo API (free, no key required).
     */
    public function getForecast(float $lat, float $lon, string $date)
    {
        $cacheKey = "weather_{$lat}_{$lon}_{$date}";

        return Cache::remember($cacheKey, 3600, function () use ($lat, $lon, $date) {
            try {
                $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'daily' => 'weathercode,precipitation_probability_max',
                    'timezone' => 'auto',
                    'start_date' => $date,
                    'end_date' => $date,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!isset($data['daily']['weathercode'][0])) {
                        return null;
                    }

                    $code = $data['daily']['weathercode'][0];
                    $prob = $data['daily']['precipitation_probability_max'][0] ?? 0;
                    
                    // WMO Weather interpretation codes
                    $isRaining = in_array($code, [51, 53, 55, 61, 63, 65, 80, 81, 82, 95, 96, 99]);
                    
                    $condition = 'Cerah / Berawan';
                    if ($isRaining) $condition = 'Hujan';
                    if (in_array($code, [0, 1])) $condition = 'Cerah';
                    if (in_array($code, [2, 3])) $condition = 'Berawan';

                    return [
                        'condition' => $condition,
                        'is_raining' => $isRaining,
                        'rain_probability' => $prob,
                    ];
                }
            } catch (\Exception $e) {
                // Ignore error, return null
            }

            return null;
        });
    }
}
