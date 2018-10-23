<?php

namespace App\Essential\Services;

class MsgService
{
    public $gateway;

    public function __construct($gateway)
    {
        $className = 'App\\Essential\\Services\\Msg\\Gateway\\' . studly_case($gateway) . 'Gateway';
        if (!class_exists($className)) {
            return false;
        }

        $this->gateway = new $className;
    }

    public function send(...$params)
    {
        return call_user_func_array([$this->gateway, 'send'], $params);
    }
}
