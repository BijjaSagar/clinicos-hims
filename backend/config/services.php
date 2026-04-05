<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API
    |--------------------------------------------------------------------------
    */

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v19.0'),
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'clinicos_verify'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v19.0'),
        /**
         * Optional: Meta-approved utility template with exactly ONE body variable {{1}} for the full message.
         * Required for first contact / outside the 24h session window (otherwise plain text is rejected).
         * Super Admin may override via system_settings key `whatsapp_utility_text_template`.
         */
        'utility_text_template' => env('WHATSAPP_UTILITY_TEXT_TEMPLATE', ''),
        'utility_text_template_language' => env('WHATSAPP_UTILITY_TEXT_TEMPLATE_LANG', 'en'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Razorpay Payment Gateway
    |--------------------------------------------------------------------------
    */

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID', ''),
        'secret' => env('RAZORPAY_KEY_SECRET', ''),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
        /** Optional: clinic SaaS billing via Razorpay Subscriptions (store plan id in clinics.settings too if needed) */
        'subscription_plan_id' => env('RAZORPAY_SUBSCRIPTION_PLAN_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | ABDM / Ayushman Bharat Digital Mission
    |--------------------------------------------------------------------------
    */

    'abdm' => [
        'client_id' => env('ABDM_CLIENT_ID', ''),
        'client_secret' => env('ABDM_CLIENT_SECRET', ''),
        'base_url' => env('ABDM_BASE_URL', 'https://healthidsbx.abdm.gov.in'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Services
    |--------------------------------------------------------------------------
    | Per-clinic keys (encrypted) are stored in clinics.settings by the owner under
    | Settings → AI & APIs. These env vars are the server-wide fallback when no
    | clinic key is set (or for CLI/queues without an authenticated user).
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        /**
         * Default false: OPENAI_API_KEY in .env is used first when set; clinic Settings key is fallback only.
         * Set true for multi-tenant SaaS where per-clinic keys in Settings must override the server .env key.
         */
        'clinic_key_overrides_env' => env('OPENAI_CLINIC_KEY_OVERRIDES_ENV', false),
        /** @deprecated Use OPENAI_CLINIC_KEY_OVERRIDES_ENV=false instead. When true, same as clinic_key_overrides_env=false. */
        'prefer_env_key' => env('OPENAI_PREFER_ENV_KEY', false),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lab Integrations
    |--------------------------------------------------------------------------
    */
    'labs' => [
        /*
         * When false (default), external lab test pickers use the built-in demo catalog only.
         * Set LAB_CATALOG_REMOTE=true when real provider API keys are configured and should be called.
         */
        'remote_catalog_enabled' => env('LAB_CATALOG_REMOTE', false),

        'lal_pathlabs' => [
            'api_base' => env('LAB_LAL_API_BASE', 'https://api.lalpathlabs.com/v1'),
            'api_key' => env('LAB_LAL_API_KEY', ''),
        ],
        'srl' => [
            'api_base' => env('LAB_SRL_API_BASE', 'https://api.srl.in/v1'),
            'api_key' => env('LAB_SRL_API_KEY', ''),
        ],
        'thyrocare' => [
            'api_base' => env('LAB_THYROCARE_API_BASE', 'https://api.thyrocare.com/v3'),
            'api_key' => env('LAB_THYROCARE_API_KEY', ''),
        ],
        'metropolis' => [
            'api_base' => env('LAB_METROPOLIS_API_BASE', 'https://api.metropolisindia.com/v1'),
            'api_key' => env('LAB_METROPOLIS_API_KEY', ''),
        ],
        'pathkind' => [
            'api_base' => env('LAB_PATHKIND_API_BASE', 'https://api.pathkindlabs.com/v1'),
            'api_key' => env('LAB_PATHKIND_API_KEY', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Photo vault — optional at-rest encryption (local disk + APP_KEY)
    |--------------------------------------------------------------------------
    */
    'photo_vault' => [
        'encrypt_uploads' => env('PHOTO_VAULT_ENCRYPT_UPLOADS', false),
    ],

];
