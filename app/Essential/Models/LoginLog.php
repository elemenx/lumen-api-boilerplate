<?php

namespace App\Essential\Models;

class LoginLog extends Model
{
    const UPDATED_AT = null;

    protected $columns = [
        'id',
        'object_type',
        'object_id',
        'ip',
        'location',
        'device_id',
        'mac',
        'ua',
        'created_at'
    ];

    public function object()
    {
        return $this->morphTo();
    }

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long($value);
    }

    public function getIpAttribute($value)
    {
        return long2ip($value);
    }
}
