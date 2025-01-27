<?php

namespace App\Http\Controllers\V1\NYT;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\NYT\IndexBestSellersRequest;
use App\Services\V1\NYTService;
use Illuminate\Support\Facades\Cache;

class BestSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBestSellersRequest $request)
    {
        // Generate a unique cache key based on the URL and query parameters
        $cacheKey = 'api_results_' . md5($request->fullUrl());

        // Cache for a configurable number of minutes (default 10)
        $ttl = now()->addMinutes((int)config('services.nyt.cache_minutes'));

        // Check if cached data exists
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            return response()->json($cachedData['body'], $cachedData['status']);
        }

        // Fetch fresh data
        $response = app(NYTService::class)->getBestSellers(
            apiKey: $request->validated('apikey'),
            author: $request->validated('author'),
            title: $request->validated('title'),
            offset: $request->validated('offset'),
            isbn: $request->validated('isbn'),
        );

        // Cache and return successful responses
        if ($response->successful()) {
            $responseData = [
                'body' => json_decode($response->getBody()->getContents(), true),
                'status' => $response->getStatusCode(),
            ];
            Cache::put($cacheKey, $responseData, $ttl);

            return response()->json($responseData['body'], $responseData['status']);
        }

        // Return the error response
        return response()->json(
            ['error' => 'Failed to fetch data', 'details' => $response->getBody()->getContents()],
            $response->getStatusCode()
        );
    }
}
