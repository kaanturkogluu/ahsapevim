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

    'iyzico' => [
        'api_key'    => env('IYZICO_API_KEY'),
        'secret_key' => env('IYZICO_SECRET_KEY'),
        'base_url'   => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
    ],

    'netgsm' => [
        'usercode'    => env('NETGSM_USERCODE'),
        'password'    => env('NETGSM_PASSWORD'),
        'header'      => env('NETGSM_HEADER'),
        'admin_phone' => env('ADMIN_PHONE_NUMBER'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', 'http://localhost/ahsapevim/public/auth/google/callback'),
    ],

    'facebook' => [
        'pixel_id'     => env('FACEBOOK_PIXEL_ID', '1151884751162206'),
        'access_token' => env('FACEBOOK_ACCESS_TOKEN', 'EAAaur7B13B4BSb1P8ZAdbIdT0uNY26NzpwVVtMsoKv3qvUD9kaVJo6nIT9O1XdGPMnbh1B4xT6lg2KItz4F65nfOmGIKPwkNG3vFHluziYhS7UlobwEQedeQZCW1CM5bEt1xXofLJAoKLqqQ5ucXpvjcMmZA7ZA7yuyXs8SA2BNqWi5ERCQdVJ713XJ7lAZDZD'),
    ],

];
