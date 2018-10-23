<?php

namespace App\Essential\Services\Msg\Gateway;

abstract class Gateway
{
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * 发送消息
     *
     * @param array|string $to
     * @param string $title
     * @param string $content
     * @return void
     */
    abstract public function send($to, $title, $content = '');
}
