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

    'vietqr' => [
        'bank_id' => env('VIETQR_BANK_ID', 'ICB'), // Mã ngân hàng Napas (ICB: Vietinbank, VCB: Vietcombank, MB: MBBank)
        'bank_name' => env('VIETQR_BANK_NAME', 'VietinBank'),
        'account_no' => env('VIETQR_ACCOUNT_NO', '108876543210'),
        'account_name' => env('VIETQR_ACCOUNT_NAME', 'CONG TY PHU TUNG MAY THUY JPM'),
        'template' => env('VIETQR_TEMPLATE', 'compact2'), // compact, compact2, qr_only, print
    ],

    'sepay' => [
        'api_key' => env('SEPAY_API_KEY', ''),
        'webhook_token' => env('SEPAY_WEBHOOK_TOKEN', 'jpm_secure_webhook_token_2026'),
    ],

    'zalo_zns' => [
        'app_id' => env('ZALO_ZNS_APP_ID', ''),
        'secret_key' => env('ZALO_ZNS_SECRET_KEY', ''),
        'oa_id' => env('ZALO_OA_ID', ''),
        'template_quote_ready' => env('ZALO_ZNS_TEMPLATE_QUOTE', '123456'),
        'template_deposit_success' => env('ZALO_ZNS_TEMPLATE_DEPOSIT', '123457'),
        'template_shipping' => env('ZALO_ZNS_TEMPLATE_SHIPPING', '123458'),
        'sandbox' => env('ZALO_ZNS_SANDBOX', true),
    ],

    'sms_brandname' => [
        'provider' => env('SMS_PROVIDER', 'mock'), // mock, speedsms, esms, viettel
        'api_key' => env('SMS_API_KEY', ''),
        'brandname' => env('SMS_BRANDNAME', 'JPM_MARINE'),
        'sandbox' => env('SMS_SANDBOX', true),
    ],

];
