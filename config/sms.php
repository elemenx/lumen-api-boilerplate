<?php

return [
    'timeout' => 5.0,

    'code' => [
        'length' => 6,
        'ttl' => 15,
        'max_attempts' => 10,
    ],

    'default' => [
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        'gateways' => [
            'alidayu'
        ],
    ],

    'gateways' => [
        'errorlog' => [
            'file' => './storage/logs/easy-sms.log',
        ],
        'alidayu' => [
            'app_key' => env('ALIDAYU_APP_KEY'),
            'app_secret' => env('ALIDAYU_APP_SECRET'),
            'sign_name' => env('ALIDAYU_SIGN_NAME'),
            'code' => [
                'template' => 'SMS_62465413',
                'params' => [
                    'type' => '绑定手机'
                ]
            ]
        ],
    ],
];
