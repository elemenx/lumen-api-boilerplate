<?php

namespace App\Essential\Observers;

use App\Essential\Models\LoginLog;
use App\Essential\Models\WechatTemplateLog;
use App\Jobs\WechatTemplateJob;

class LoginLogObserver
{
    public function created(LoginLog $loginLog)
    {
        if ($loginLog->object_type != 'user' || empty($loginLog->object) || empty($loginLog->object->wechat)) {
            return;
        }
        $lastLog = LoginLog::where('object_type', 'user')->where('object_id', $loginLog->object_id)->skip(1)->take(1)->orderBy('id', 'DESC')->first();
        if (!empty($lastLog) && $lastLog->location == $loginLog->location) {
            return;
        }
        $wechatTemplateLog = WechatTemplateLog::create([
            'status' => 'pending',
            'openid' => $loginLog->object->wechat->openid,
            'slug'   => 'login_log_created',
            'data'   => [
                'time' => strval($loginLog->created_at),
                'ip'   => strval($loginLog->ip) . '(' . rtrim(strval($loginLog->location)) . ')',
            ],
            'url' => '',
        ]);

        dispatch((new WechatTemplateJob($wechatTemplateLog))->onQueue('high'));
    }
}
