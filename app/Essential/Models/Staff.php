<?php

namespace App\Essential\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Staff extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $casts = [
        'issued_at'  => 'datetime',
        'reset_at'   => 'datetime',
        'enabled'    => 'boolean',
        'locked'     => 'boolean',
        'has_wechat' => 'boolean',
        'has_tfa'    => 'boolean'
    ];

    protected $columns = [
        'id',
        'name',
        'nickname',
        'realname',
        'password',
        'tfa_secret',
        'role',
        'enabled',
        'locked',
        'has_wechat',
        'has_tfa',
        'created_at',
        'updated_at',
        'issued_at',
        'reset_at',
    ];

    public function loginLogs()
    {
        return $this->morphMany(LoginLog::class, 'object');
    }

    public function posts()
    {
        return $this->morphMany(Post::class, 'object');
    }

    public function TFAScratchCodes()
    {
        return $this->morphMany(TFAScratchCode::class, 'object');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'object');
    }

    public function wechat()
    {
        return $this->hasOne(WechatOpenid::class, 'creator_id')->where('creator_type', 'staff');
    }

    public function own(Model $model, $check_type = 'staff_id')
    {
        if (ends_with($check_type, '_id')) {
            if (isset($model[$check_type]) && $model[$check_type] == $this->id) {
                return true;
            }
        } else {
            if (isset($model[$check_type . '_type']) && $model[$check_type . '_type'] == 'staff' && $model[$check_type . '_id'] == $this->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Eloquent Model method
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
