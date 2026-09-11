<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'ghn' => [
        'api_url' => env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api'),
        'api_token' => env('GHN_API_TOKEN'),
        'shop_id' => (int) env('GHN_SHOP_ID', 216720),
        'origin_province_id' => (int) env('GHN_ORIGIN_PROVINCE_ID', 201),
        'origin_district_id' => (int) env('GHN_ORIGIN_DISTRICT_ID', 1492),
        'origin_ward_code' => (string) env('GHN_ORIGIN_WARD_CODE', '1A0501'),
        'tracking_url' => env('GHN_TRACKING_URL', 'https://5sao.ghn.dev/order'),
    ],

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'),
        'access_key'   => env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'),
        'secret_key'   => env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'),
        'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'api_endpoint' => env('MOMO_API_ENDPOINT', 'https://test-payment.momo.vn'),
        'verify_ssl'   => env('MOMO_VERIFY_SSL', false),
        'redirect_url' => env('MOMO_REDIRECT_URL', 'http://localhost:8000/payment/momo/callback'),
        'ipn_url'      => env('MOMO_IPN_URL', 'http://localhost:8000/payment/momo/ipn'),
        'request_type' => env('MOMO_REQUEST_TYPE', 'payWithCC'),
    ],

];
