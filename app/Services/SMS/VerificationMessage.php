<?php

namespace App\Services\SMS;

use Overtrue\EasySms\Message;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Strategies\OrderStrategy;
use Overtrue\EasySms\Gateways\AlidayuGateway;

class VerificationMessage extends Message
{
    protected $code;
    protected $gateways = ['alidayu'];

    public function __construct($code)
    {
        $this->code = $code;
    }

    // 定义使用模板发送方式平台所需要的模板 ID
    public function getTemplate(GatewayInterface $gateway = null)
    {
        return $gateway->getConfig()['code']['template'];
    }

    public function getData(GatewayInterface $gateway = null)
    {
        $params = $gateway->getConfig()['code']['params'];
        $params['code'] =  $this->code;
        return $params;
    }
}
