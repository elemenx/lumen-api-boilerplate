<?php

return [
    'timeout' => 5.0,

    'default' => [
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
        'gateways' => ['qcloud', 'yunpian'],
    ],

    'gateways' => [
        'errorlog' => [
            'file' => './storage/logs/easy-sms.log',
        ],
        'qcloud' => [
            'sdk_app_id' => env('SMS_QCLOUD_SDK_APP_ID'),
            'app_key'    => env('SMS_QCLOUD_APP_KEY'),
        ],
        'yunpian' => [
            'signature' => env('SMS_YUNPIAN_SIGNATURE'),
            'api_key'   => env('SMS_YUNPIAN_API_KEY')
        ]
    ]
];
