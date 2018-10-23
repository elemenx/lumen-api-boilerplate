<?php

namespace App\Essential\Services\Msg\Gateway;

/**
 * 微信模板发送类
 */
class WechatGateway extends Gateway
{
    public function send($openid, $slug, $params = [], $url = '')
    {
        if (empty($openid)) {
            return false;
        }
        if ($template = config('wechat_template.'.$slug)) {
            foreach ($template['params'] as $key => $value) {
                if (!isset($params[$key])) {
                    $params[$key] = $value;
                }
            }
            foreach ($params as $key => $value) {
                $params[$key] = [
                    'value' => $value,
                    'color' => in_array($key, ['first', 'remark', 'reason']) ? '#103275' : '#777777'
                ];
            }

            return app('wechat.official_account')->template_message->send([
                'touser' => $openid,
                'template_id' => $template['template_id'],
                'url' => substr($url, 0, 4) != 'http' ? env('PAGE_URL').$url : $url,
                'data' => $params
            ]);
        }
        return false;
        
    }
}
