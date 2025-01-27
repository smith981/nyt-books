<?php

namespace Tests\Feature\API\V1\NYT;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BestSellerTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
    }

    public function test_get_best_seller_data_returns_data(): void
    {
        $mockResponse = self::get_mock_successful_response();
        Http::fake([
            'https://api.nytimes.com/*' => Http::response($mockResponse, 200),
        ]);

        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'isbn' => '0871404427',
            ])
        )->assertStatus(200);
    }

    public function test_get_best_seller_data_validates_title(): void
    {
        Http::fake();

        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'title' => 'King Lear'
            ])
        )->assertStatus(200);

        // We don't want to allow empty strings-- if empty they shouldn't send it
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'title' => ''
            ])
        )->assertStatus(422);
    }

    public function test_returns_422_if_no_api_key(): void
    {
        Http::fake();

        $this->getJson(
            route('api.v1.best-sellers.index')
        )->assertStatus(422);
    }

    public function test_returns_401_if_invalid_api_key(): void
    {
        // Define the mocked 401 response
        $mockResponse = json_encode([
            "fault" => [
                "faultstring" => "Invalid ApiKey",
                "detail" => [
                    "errorcode" => "oauth.v2.InvalidApiKey",
                ],
            ],
        ]);

        // Http client will return the response it would have if the API key was invalid
        Http::fake([
            'https://api.nytimes.com/*' => Http::response($mockResponse, 401),
        ]);

        $this->getJson(
            route('api.v1.best-sellers.index', ['api-key' => 'invalid-api-key'])
        )->assertStatus(401);
    }

    public function test_it_handles_500_errors_from_api(): void
    {
        // Http client will return the code if there was some 500 error
        Http::fake([
            'https://api.nytimes.com/*' => Http::response(['NYT Books API is currently down, please try again later'], 500),
        ]);

        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
            ])
        )->assertStatus(500);
    }

    public function test_it_only_allows_offset_multiple_of_20(): void
    {
        Http::fake();

        // Fails -- offset not multiple of 20
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'offset' => 1
            ])
        )->assertStatus(422);

        // Fails -- empty string
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'offset' => ''
            ])
        )->assertStatus(422)
            ->assertJsonValidationErrors('offset');

        // Passes -- offset is 20
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'offset' => 20
            ])
        )->assertStatus(200);

        // Passes -- offset is 40
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'offset' => 40
            ])
        )->assertStatus(200);
    }

    public function test_get_best_seller_data_validates_isbn(): void
    {
        Http::fake();

        // Passes b/c ISBN is valid
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'isbn' => '0871404427',
            ])
        )->assertStatus(200);

        // Fails b/c a negative ISBN is passed, which is invalid
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'isbn' => '-0871404427'
            ])
        )->assertStatus(422);

        // Fails b/c ISBN is invalid
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'isbn' => 'foo'
            ])
        )->assertStatus(422);
    }

    public function test_get_best_seller_data_author_must_be_valid_name_if_passed(): void
    {
        Http::fake();

        // Not a legit name in any human culture
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'author' => 1245])
        )->assertStatus(422);

        // Can't be empty string
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'author' => ''])
        )->assertStatus(422);

        // "Normal" name
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'author' => 'Jack Davidson'])
        )->assertStatus(200);

        // Technically a legit name, but an extreme edge case demonstrating elaborate titles and diacritics
        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'author' => 'His Róyal Holiness General Santa Anø King Lupé VIII'])
        )->assertStatus(200);
    }

    public function test_returns_data_all_filters_used(): void
    {
        Http::fake();

        $this->getJson(
            route('api.v1.best-sellers.index', [
                'api-key' => config('services.nyt.api_key'),
                'isbn' => '0871404427',
                'author' => 'Jack Davidson',
                'title' => 'King Lear',
            ])
        )->assertStatus(200);
    }

    public function test_multiple_requests_use_cache()
    {
        $mockResponse = self::get_mock_successful_response();
        Http::fake([
            'https://api.nytimes.com/*' => Http::response($mockResponse, 200),
        ]);

        // Clear the cache for a clean start
        Cache::flush();

        $url = route('api.v1.best-sellers.index', [
            'api-key' => config('services.nyt.api_key'),
            'isbn' => '0871404427',
        ]);

        // Assert cache doesn't have the key yet
        $this->assertTrue(Cache::missing('api_results_' . md5($url . '_' . config('services.nyt.api_key'))));

        // First request (cache miss)
        $this->getJson($url)
            ->assertStatus(200);

        // Assert cache contains the key
        $this->assertTrue(Cache::has('api_results_' . md5($url . '_' . config('services.nyt.api_key'))));

        // Second request (cache hit)
        $response = $this->getJson($url)
            ->assertStatus(200);

        // Ensure cached response is returned
        $cachedData = Cache::get('api_results_' . md5($url . '_' . config('services.nyt.api_key')));
        $this->assertEquals($response->json(), $cachedData['body']);
    }

    private function get_mock_successful_response(): string
    {
        $json = <<<EOL
{
  "status": "OK",
  "copyright": "Copyright (c) 2025 The New York Times Company.  All Rights Reserved.",
  "num_results": 1,
  "results": [
    {
      "title": "\"MOST BLESSED OF THE PATRIARCHS\"",
      "description": "A character study that attempts to make sense of Jefferson’s contradictions.",
      "contributor": "by Annette Gordon-Reed and Peter S. Onuf",
      "author": "Annette Gordon-Reed and Peter S Onuf",
      "contributor_note": "",
      "price": "0.00",
      "age_group": "",
      "publisher": "Liveright",
      "isbns": [
        {
          "isbn10": "0871404427",
          "isbn13": "9780871404428"
        }
      ],
      "ranks_history": [
        {
          "primary_isbn10": "0871404427",
          "primary_isbn13": "9780871404428",
          "rank": 16,
          "list_name": "Hardcover Nonfiction",
          "display_name": "Hardcover Nonfiction",
          "published_date": "2016-05-01",
          "bestsellers_date": "2016-04-16",
          "weeks_on_list": 1,
          "rank_last_week": 0,
          "asterisk": 1,
          "dagger": 0
        }
      ],
      "reviews": [
        {
          "book_review_link": "",
          "first_chapter_link": "",
          "sunday_review_link": "",
          "article_chapter_link": ""
        }
      ]
    }
  ]
}
EOL;
        return $json;
    }
}
