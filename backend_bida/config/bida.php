<?php

return [
    'hourly_rates' => [
        'thuong' => (int) env('BIDA_HOURLY_RATE_THUONG', 50000),
        'vip' => (int) env('BIDA_HOURLY_RATE_VIP', 70000),
    ],
];
