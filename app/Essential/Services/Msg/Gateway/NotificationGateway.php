<?php

namespace App\Essential\Services\Msg\Gateway;

use App\Essential\Models\Notification;

/**
 * 消息提醒发送类
 */
class NotificationGateway extends Gateway
{
    public function send($user_id, $slug, $params = [])
    {
        if (!in_array($slug, ['withdraw_completed', 'issue_post_created'])) {
            return false;
        }
        return Notification::create([
            'user_id' => $user_id,
            'title'   => trans('notification.' . $slug . '.title'),
            'content' => trans('notification.' . $slug . '.content', $params)
        ]);
    }
}
