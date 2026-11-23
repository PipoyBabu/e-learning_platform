<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class WeatherController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $key = 'weather:'.sha1($request->user()->id.'|'.$data['lat'].'|'.$data['lon']);
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['message' => 'Please wait before requesting weather again.'], 429);
        }

        RateLimiter::hit($key, 60);

        if (! config('services.openweather.key')) {
            return response()->json(['message' => 'Weather is not configured yet.'], 503);
        }

        try {
            $weather = Http::timeout(10)
                ->get('https://api.openweathermap.org/data/2.5/weather', [
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                    'units' => 'metric',
                    'appid' => config('services.openweather.key'),
                ])
                ->throw()
                ->json();
        } catch (\Throwable) {
            return response()->json(['message' => 'Weather is currently unavailable.'], 502);
        }

        return response()->json([
            'place' => $weather['name'] ?? 'Selected location',
            'temperature' => $weather['main']['temp'] ?? null,
            'description' => $weather['weather'][0]['description'] ?? null,
            'humidity' => $weather['main']['humidity'] ?? null,
        ]);
    }
}
