<?php

namespace App\Essential\Providers;

use DB;
use App\Essential\Services\SmsCodeService;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app('validator')->extend('mobile', function ($attribute, $value) {
            if (!preg_match("/^(?:1[3-9]\d{9})$/", $value)) {
                return false;
            }
            return true;
        });
        app('validator')->extend('sms_code', function ($attribute, $value, $parameters) {
            if (isset($parameters[0]) && $parameters[0] == 'auth') {
                if ($user = auth_user()) {
                    return (new SmsCodeService($user->getOriginal('mobile')))->verify($value);
                }
            } elseif ($mobile = request('mobile')) {
                return (new SmsCodeService($mobile))->verify($value);
            }
            return false;
        });
        app('validator')->extend('captcha_code', function ($attribute, $value) {
            if ($challenge = request('captcha_challenge')) {
                return app('captcha')->checkCaptchaById($value, $challenge);
            }
            return false;
        });
        app('validator')->extend('exists_without_zero', function ($attribute, $value, $parameters) {
            if (!isset($parameters[0])) {
                return false;
            }
            if ($value != 0 && !DB::table($parameters[0])->where($parameters[1] ?? 'id', $value)->exists()) {
                return false;
            }
            return true;
        });
    }
}
