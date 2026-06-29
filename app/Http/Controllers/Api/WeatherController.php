<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Exception;

class WeatherController extends Controller
{
    // Modern PHP 8 constructor property promotion
    public function __construct(
        protected WeatherService $weatherService
    ) {}

    public function getRealtime(string $city): JsonResponse
    {
        try {
            $data = $this->weatherService->fetchFromApi($city);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getCached(string $city): JsonResponse
    {
        try {
            $data = $this->weatherService->fetchCached($city);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Formats structured and predictable error states.
     */
    protected function errorResponse(Exception $e): JsonResponse
    {
        $statusCode = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
        
        return response()->json([
            'error' => $e->getMessage(),
        ], $statusCode);
    }
}