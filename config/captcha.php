<?php

return [
    // config of nocaptcha
    'enabled' => env('CAPTCHA_ENABLED', false),
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
];
