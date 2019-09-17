<?php

return [
    'services' => [
        'judge' => [
            'status' => env('JUDGE_SERVICE_OK', true),
        ],
    ],
    'data_path'            => env('HOJ_DATA_PATH', storage_path('data')),
    'google_analytic_code' => env('GOOGLE_ANALYTIC_CODE', 'UA-6733942-1'),
];
