<?php

return [
    'services' => [
        'judge' => [
            'status' => env('JUDGE_SERVICE_OK', true),
        ],
    ],
    'data_path' => storage_path('data'),
    'google_analytic_code' => env('GOOGLE_ANALYTIC_CODE', 'UA-6733942-1'),
];
