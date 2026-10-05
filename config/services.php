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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    // Place suggestions for the booking form's "Lokasi Tujuan" (OpenStreetMap data via a Photon server).
    // The public Photon server has no SLA and expects fair use: results are cached and the endpoint is throttled.
    // Point GEOCODER_URL at a self-hosted or paid Photon-compatible server for heavier use.
    'geocoder' => [
        'url' => env('GEOCODER_URL', 'https://photon.komoot.io/api/'),
        'user_agent' => env('GEOCODER_USER_AGENT', 'SiPerkasa/1.0 (Dinkes PPKB Purbalingga)'),
        // Results are biased towards Purbalingga and limited to Indonesia (minLon,minLat,maxLon,maxLat).
        'bias_lat' => -7.388,
        'bias_lon' => 109.364,
        'bbox' => '95,-11,141,6',
        'timeout' => 4,
        'cache_seconds' => 60 * 60 * 24,
    ],

];
