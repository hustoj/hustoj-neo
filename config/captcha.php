<?php

return [
    // config of nocaptcha
    'enabled' => env('CAPTCHA_ENABLED', false),
    'recaptcha' => [
        'script_url' => 'https://www.recaptcha.net/recaptcha/api.js',
        'verify_url' => 'https://www.recaptcha.net/recaptcha/api/siteverify',
    ],
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
];
