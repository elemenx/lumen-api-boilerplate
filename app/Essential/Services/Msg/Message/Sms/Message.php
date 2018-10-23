<?php

namespace App\Essential\Services\Msg\Message\Sms;

use Overtrue\EasySms\Message as BaseMessage;
use Overtrue\EasySms\Gateways\YunpianGateway;

class Message extends BaseMessage
{
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    protected function getPrefix($gateway)
    {
        $prefix = '';
        if ($gateway instanceof YunpianGateway) {
            $prefix = '【' . urldecode($gateway->getConfig()['signature']) . '】';
        }
        return $prefix;
    }
}
