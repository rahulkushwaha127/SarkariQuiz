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

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-3-haiku-20240307'),
    ],

    // Alias for frontend Firebase config (Scrap-style: config('services.firebase'))
    'firebase' => [
        'api_key' => env('FCM_API_KEY', env('FIREBASE_API_KEY')),
        'auth_domain' => env('FCM_AUTH_DOMAIN', env('FIREBASE_AUTH_DOMAIN')),
        'project_id' => env('FCM_PROJECT_ID', env('FIREBASE_PROJECT_ID')),
        'storage_bucket' => env('FCM_STORAGE_BUCKET', env('FIREBASE_STORAGE_BUCKET')),
        'messaging_sender_id' => env('FCM_SENDER_ID', env('FIREBASE_MESSAGING_SENDER_ID', env('FIREBASE_SENDER_ID'))),
        'app_id' => env('FCM_APP_ID', env('FIREBASE_APP_ID')),
    ],

    'fcm' => [
        // FCM HTTP v1: path to Firebase service account JSON (Project Settings → Service accounts → Generate new private key).
        // Alternatively set GOOGLE_APPLICATION_CREDENTIALS in .env or environment.
        'service_account_json' => env('FCM_SERVICE_ACCOUNT_JSON', env('GOOGLE_APPLICATION_CREDENTIALS')),
        // Legacy server key (deprecated; no longer used; v1 uses OAuth2 from service account).
        'server_key' => env('FCM_SERVER_KEY'),
        // Web SDK config (public, same shape as Firebase Console snippet)
        'api_key' => env('FCM_API_KEY', env('FIREBASE_API_KEY')),
        'auth_domain' => env('FCM_AUTH_DOMAIN', env('FIREBASE_AUTH_DOMAIN')),
        'project_id' => env('FCM_PROJECT_ID', env('FIREBASE_PROJECT_ID')),
        'storage_bucket' => env('FCM_STORAGE_BUCKET', env('FIREBASE_STORAGE_BUCKET')),
        'messaging_sender_id' => env('FCM_SENDER_ID', env('FIREBASE_MESSAGING_SENDER_ID', env('FIREBASE_SENDER_ID'))),
        'app_id' => env('FCM_APP_ID', env('FIREBASE_APP_ID')),
        // Web Push VAPID key (Firebase Console → Project Settings → Cloud Messaging → Web Push certificates)
        'vapid_key' => env('FCM_VAPID_KEY', env('FIREBASE_VAPID_KEY', env('VITE_FIREBASE_VAPID_KEY'))),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

];
