<?php

namespace App\Providers;

use Validator;
use Carbon\Carbon;
use App\Services\SMSService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Carbon::setLocale('zh');
        Validator::extend('mobile', function ($attribute, $value) {
            if (!preg_match("/^(?:1[3-9]\d{9})$/", $value)) {
                return false;
            }
            return true;
        });
        Validator::extend('mobile_code', function ($attribute, $value) {
            if ($mobile = request('mobile')) {
                return (new SMSService($mobile))->verifyCode($value);
            }
            return false;
        });
    }
}
