<?php

return [
    'services' => [
        'judge' => [
            'status' => env('JUDGE_SERVICE_OK', true),
        ],
    ],
    'special_judge_enabled' => false,
    'submit'                => [
        'interval' => 10,
    ],
    'data_path'            => env('HOJ_DATA_PATH', storage_path('data')),
    'google_analytic_code' => env('GOOGLE_ANALYTIC_CODE', 'UA-6733942-1'),
];
