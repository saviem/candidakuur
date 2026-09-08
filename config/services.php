<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openrouter' => [
        'key' => env('OPENAI_API_KEY'),
        'url' => env('OPENAI_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENAI_MODEL', 'openai/gpt-4o-mini'),
    ],

    'mollie' => [
        'key' => env('MOLLIE_KEY'),
        'api_url' => env('MOLLIE_API_URL', 'https://api.mollie.com'),
        'plus_amount' => env('MOLLIE_PLUS_AMOUNT', '9.90'),
        'plus_days' => (int) env('MOLLIE_PLUS_DAYS', 30),
        'donate_amounts' => array_map('intval', explode(',', env('MOLLIE_DONATE_AMOUNTS', '5,10,25'))),
    ],

];
