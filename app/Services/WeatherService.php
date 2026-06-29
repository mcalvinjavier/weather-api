<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class WeatherService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key') ?? '';
        $this->baseUrl = config('services.openweather.url') ?? '';
    }

    /**
     * Fetch fresh real-time data directly from OpenWeatherMap API.
     */
    public function fetchFromApi(string $city): array
    {
        $response = Http::get("{$this->baseUrl}/weather", [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric', // Formats temperature cleanly to Celsius
        ]);

        if ($response->failed()) {
            $message = $response->json()['message'] ?? 'Failed to fetch weather data';
            throw new Exception($message, $response->status());
        }

        return $this->transformResponse($response->json(), 'external');
    }

    /**
     * Retrieve data from cache or fetch/store dynamically for 10 minutes.
     */
    public function fetchCached(string $city): array
    {
        $cacheKey = 'weather:' . Str::slug($city);

        // Check if cached entry exists to change the source value to 'cache' 
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            $cachedData['source'] = 'cache';
            return $cachedData;
        }

        // If missing, hit the live endpoint and store the results
        $freshData = $this->fetchFromApi($city);
        
        Cache::put($cacheKey, $freshData, now()->addMinutes(10));

        return $freshData;
    }

    /**
     * Map response safely using modern PHP 8 optional chaining features.
     */
    protected function transformResponse(array $data, string $source): array
    {
        return [
            'city' => $data['name'] ?? '',
            'temperature' => $data['main']['temp'] ?? null,
            'weather_description' => $data['weather'][0]['description'] ?? '',
            'timestamp' => now()->toIso8601String(),
            'source' => $source,
        ];
    }
}