<?php

namespace App\Services;

use Cache;
use App\Models\SMS;
use App\Services\SMS\CodeMessage;
use Overtrue\EasySms\EasySms;

class SMSService
{
    private $instance = null;
    private $mobile = null;
    private $config = null;

    public function __construct($mobile)
    {
        $this->config = config('sms');
        $this->instance = new EasySms($this->config);
        $this->mobile = $mobile;
    }

    public function isValidate()
    {
        if (!preg_match("/^(?:1[3|5|7|8]\d{9}|14[5|7]\d{8})$/", $this->mobile)) {
            return false;
        }

        return true;
    }

    public function verifyCode($code)
    {
        $attempts = Cache::get('mobile_attempts_'.$this->mobile, 0);
        if ($attempts > $this->config['code']['max_attempts']) {
            return false;
        }
        if ($this->getCode() == $code) {
            Cache::forget('mobile_check_'.$this->mobile);
            Cache::forget('mobile_attempts_'.$this->mobile);
            return true;
        }
        $attempts++;
        Cache::put('mobile_attempts_'.$this->mobile, $attempts, $this->config['code']['ttl']);
        return false;
    }

    public function getCode($refresh = false)
    {
        if ($refresh || !Cache::has('mobile_check_'.$this->mobile)) {
            $code = str_pad(
                mt_rand(
                    1,
                    str_repeat(9, $this->config['code']['length'])
                ),
                $this->config['code']['length'],
                "0",
                STR_PAD_LEFT
            );
            Cache::forget('mobile_attempts_'.$this->mobile);
            Cache::put('mobile_check_'.$this->mobile, $code, $this->config['code']['ttl']);
            return $code;
        }
        return Cache::get('mobile_check_'.$this->mobile);
    }

    public function code()
    {
        if (!$this->isValidate()) {
            return false;
        }
        $code = $this->getCode(true);
        $this->log('code', ['code' => $code]);

        return $this->instance->send($this->mobile, new CodeMessage($code));
    }

    public function send($template, $params = [])
    {
        if (!$this->isValidate()) {
            return false;
        }
        $this->log($template, $params);

        return $this->instance->send($this->mobile, [
            'template' => $template,
            'data' => $params
        ]);
    }

    private function log($template, $params = [])
    {
        return SMS::create([
            'user_id'  => auth_user() ? auth_user()->id : 0,
            'mobile'   => $this->mobile,
            'template' => $template,
            'data'     => $params
        ]);
    }
}
