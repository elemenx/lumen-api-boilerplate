<?php

namespace App\Essential\Services\Msg\Gateway;

use Overtrue\EasySms\EasySms;

/**
 * 短信发送类
 */
class SmsGateway extends Gateway
{
    protected $instance;

    public function __construct()
    {
        $this->instance = new EasySms(config('sms'));
    }

    /**
     * 发送消息
     *
     * @param array|string $to
     * @param string $slug
     * @param array $params
     * @return void
     */
    public function send($to, $slug, $params = [])
    {
        $className = 'App\\Essential\\Services\\Msg\\Message\\Sms\\' . studly_case($slug) . 'Message';
        if (!class_exists($className)) {
            return false;
        }

        return $this->instance->send($to, new $className($params));
    }
}
