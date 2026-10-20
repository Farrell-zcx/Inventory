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

    'sso' => [
        'base_url'     => env('SSO_BASE_URL', 'http://sso-engine.test'),
        'internal_url' => env('SSO_INTERNAL_URL', env('SSO_BASE_URL', 'http://sso-engine.test')),
        'client_id'    => env('SSO_CLIENT_ID'),
        'redirect_uri' => env('SSO_REDIRECT_URI', 'http://inventory.test/callback'),
        'public_key'   => file_exists(base_path('keys/public.pem')) ? base_path('keys/public.pem') : storage_path('app/keys/public.pem'),
    ],

];
