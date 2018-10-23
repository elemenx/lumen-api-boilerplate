<?php

namespace App\Essential\Services;

use Cache;
use App\Essential\Jobs\SmsJob;
use App\Essential\Models\SmsLog;

class SmsCodeService
{
    private $mobile = null;
    private $length = 6;
    private $ttl = 15;
    private $max_attempts = 10;

    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    public function verify($code)
    {
        $attempts = Cache::get('mobile_attempts_' . $this->mobile, 0);
        if ($attempts > $this->max_attempts) {
            return false;
        }
        if ($this->get() == $code) {
            Cache::forget('mobile_check_' . $this->mobile);
            Cache::forget('mobile_attempts_' . $this->mobile);
            return true;
        }
        $attempts++;
        Cache::put('mobile_attempts_' . $this->mobile, $attempts, $this->ttl);
        return false;
    }

    public function get($forceRefresh = false)
    {
        if ($forceRefresh || !Cache::has('mobile_check_' . $this->mobile)) {
            $code = str_pad(
                mt_rand(
                    1,
                    str_repeat(9, $this->length)
                ),
                $this->length,
                '0',
                STR_PAD_LEFT
            );
            Cache::forget('mobile_attempts_' . $this->mobile);
            Cache::put('mobile_check_' . $this->mobile, $code, $this->ttl);
            return $code;
        }
        return Cache::get('mobile_check_' . $this->mobile);
    }

    public function send()
    {
        if (Cache::has('mobile_' . $this->mobile)) {
            abort_sys(42223);
        }

        $code = $this->get(true);

        $smsLog = SmsLog::create([
            'mobile'   => $this->mobile,
            'template' => 'code',
            'data'     => ['code' => $code]
        ]);

        Cache::put('mobile_' . $this->mobile, $code, 1);

        return dispatch((new SmsJob($smsLog))->onQueue('high')) ? $code : false;
    }
}
