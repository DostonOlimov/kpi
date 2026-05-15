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
    | a conventional file to locate the various Services credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
    ],

    'turniket' => [
        'ip'              => env('TURNIKET_IP', '194.93.24.34'),
        'user'            => env('TURNIKET_USER', 'admin'),
        'pass'            => env('TURNIKET_PASS', 'password'),
        'timezone'        => env('TURNIKET_TZ', '+05:00'),
        'connect_timeout' => env('TURNIKET_CONNECT_TIMEOUT', 5),
        'timeout'         => env('TURNIKET_TIMEOUT', 15),
        'page_size'       => env('TURNIKET_PAGE_SIZE', 100),

        // Two physical devices: 8002 = entry (check-in), 8003 = exit (check-out)
        'devices' => [
            'in'  => [
                'port'      => env('TURNIKET_IN_PORT', '8002'),
                'direction' => 'in',
            ],
            'out' => [
                'port'      => env('TURNIKET_OUT_PORT', '8003'),
                'direction' => 'out',
            ],
        ],
    ],

];
