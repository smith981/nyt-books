<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'nyt' => [
        'app_id' => env('NYT_APP_ID'),
        'api_key' => env('NYT_API_KEY'),
        'api_url' => [
            'v1' => [
                'best-sellers' => env('NYT_API_URL_V1', 'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json'),
            ],
        ],
        'rate_limit' => env('NYT_RATE_LIMIT', 60),
        'cache_minutes' => env('NYT_CACHE_MINUTES', 10),
    ],
];
