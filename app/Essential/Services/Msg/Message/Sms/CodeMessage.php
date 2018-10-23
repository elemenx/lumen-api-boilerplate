<?php

namespace App\Essential\Services\Msg\Message\Sms;

use Overtrue\EasySms\Contracts\GatewayInterface;

class CodeMessage extends Message
{
    public function getContent(GatewayInterface $gateway = null)
    {
        return $this->getPrefix($gateway) . sprintf('您的验证码为%s，请在15分钟内使用该验证码完成验证（如非本人操作请忽略本短信）', $this->params['code']);
    }
}
