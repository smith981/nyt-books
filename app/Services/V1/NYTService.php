<?php

namespace App\Services\V1;

use Illuminate\Support\Facades\Http;

class NYTService
{
   public function getBestSellers(string $apiKey, ?string $author = null, ?string $title = null, ?int $offset = null, ?string $isbn = null)
   {
       $response = Http::get(config('services.nyt.api_url.v1.best-sellers'), [
           'author' => $author,
           'title' => $title,
           'offset' => $offset,
           'isbn' => $isbn,
           'api-key' => $apiKey,
       ]);

       return $response;
   }
}
