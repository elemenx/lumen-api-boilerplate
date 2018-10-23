<?php

namespace App\Essential\Models;

class SmsLog extends Model
{
    const UPDATED_AT = null;

    protected $casts = [
        'data' => 'array'
    ];

    protected $columns = [
        'id',
        'status',
        'mobile',
        'template',
        'data',
        'note',
        'created_at',
        'sent_at',
    ];
}
