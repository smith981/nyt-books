<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BestSellersSearch extends Component
{
    #[Validate('sometimes|string')]
    public $title = '';

    #[Validate('sometimes|string')]
    public $author = '';

    #[Validate('sometimes|string')]
    public $isbn = '';

    #[Validate('sometimes|integer')]
    public $offset = 0;

    public $numResults = [];
    public $results = [];
    public $errorMessage = '';

    public $lastRequest = '';

    public function search()
    {
        try {
            // Make a GET request to your API endpoint
            $this->lastRequest = route('api.v1.best-sellers.index', array_filter([
                'api-key' => config('services.nyt.api_key'),
                'title' => $this->title,
                'author' => $this->author,
                'isbn' => $this->isbn,
                'offset' => $this->offset,
            ]));

            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->get($this->lastRequest);

            if ($response->successful()) {
                $this->numResults = $response->json()['num_results'];
                $this->results = $response->json()['results'];
                $this->errorMessage = '';
            } else {
                $this->errorMessage = $response->getStatusCode().': '.$response->json()['message'];
                $this->results = [];
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred: ' . $e->getMessage();
            $this->results = [];
        }
    }

    /**
     * Show the basic form, errors (if applicable) and results if they are returned
     */
    public function render()
    {
        return <<<'EOL'
<div class="p-6 bg-white shadow rounded-lg max-w-6xl mx-auto">
    <div class="text-center">
        <h1 class="text-xl font-semibold mb-6">Search Best Sellers</h1>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="search" class="space-y-4">
        <!-- Title -->
        <div>
            <label for="title" class="block font-medium">Title</label>
            <input
                type="text"
                id="title"
                wire:model="title"
                class="w-full border-gray-300 rounded-lg shadow-sm"
                placeholder="Enter a book title"
            />
        </div>

        <!-- Author -->
        <div>
            <label for="author" class="block font-medium">Author</label>
            <input
                type="text"
                id="author"
                wire:model="author"
                class="w-full border-gray-300 rounded-lg shadow-sm"
                placeholder="Enter an author's name"
            />
        </div>

        <!-- ISBN -->
        <div>
            <label for="isbn" class="block font-medium">ISBN</label>
            <input
                type="text"
                id="isbn"
                wire:model="isbn"
                class="w-full border-gray-300 rounded-lg shadow-sm"
                placeholder="Enter ISBN (e.g., 0871404427)"
            />
        </div>

        <!-- Offset -->
        <div>
            <label for="offset" class="block font-medium">Offset</label>
            <input
                type="number"
                id="offset"
                wire:model="offset"
                class="w-full border-gray-300 rounded-lg shadow-sm"
                placeholder="Enter offset (e.g., 0)"
            />
        </div>

        <!-- Submit Button -->
        <div>
            <button
                wire:click="search()"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-500"
            >
                Search
            </button>
        </div>
    </form>

    <!-- Error Message -->
    @if ($errorMessage)
        <div class="mt-6 text-red-600 font-semibold">
            <strong>{{ $errorMessage }}</strong>
        </div>
    @endif

    <!-- Results -->
    @if ($numResults == 0)
    <div class="space-y-6">
        <p class="text-gray-500">No results found.</p>
    </div>
    @elseif (empty($numResults))
    <div class="space-y-6">
        <p class="text-gray-500">Enter your search terms above.</p>
    </div>
    @else
        <div class="space-y-6">
            <div class="mt-2">
                Request: {{ $lastRequest }}
            </div>
            <div class="mt-2">
                Results found: {{ $numResults }}
            </div>
            @foreach ($results as $index => $item)
                <div class="p-4 bg-gray-100 rounded-lg shadow">
                    <h2 class="text-lg font-bold">Result #{{ $index + 1 }}</h2>
                    <div class="mt-2">
                        {!! $this->renderNested($item) !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
EOL;
    }

    /**
     * Recursively render results
     */
    public function renderNested($data)
    {
        $html = '';

        foreach ($data as $key => $value) {
            $beautifulKey = Str::of($key)->snake()->replace('_', ' ')->title();

            if (is_array($value)) {
                $html .= <<<HTML
                <div class="mt-2">
                    <strong class="text-sm font-semibold capitalize">$beautifulKey:</strong>
                    <div class="ml-4 border-l-2 border-gray-200 pl-4">
                        {$this->renderNested($value)}
                    </div>
                </div>
            HTML;
            } elseif (is_object($value)) {
                $html .= <<<HTML
                <div class="mt-2">
                    <strong class="text-sm font-semibold capitalize">$beautifulKey:</strong>
                    <div class="ml-4 border-l-2 border-gray-200 pl-4">
                        {$this->renderNested((array) $value)}
                    </div>
                </div>
            HTML;
            } else {
                $escapedValue = e($value); // Escape the value
                $html .= <<<HTML
                <div class="mt-2">
                    <strong class="text-sm font-semibold capitalize">$beautifulKey:</strong>
                    <span class="ml-2 text-gray-700">$escapedValue</span>
                </div>
            HTML;
            }
        }

        return $html;
    }
}
