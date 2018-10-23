<?php

namespace App\Essential\Observers;

use App\Essential\Services\IPService;
use App\Essential\Models\User;
use Carbon\Carbon;

class UserObserver
{
    public function creating(User $user)
    {
        $user->issued_at = Carbon::now();
    }

    public function saving(User $user)
    {
        $dirtyData = $user->getDirty();

        if (!empty($dirtyData['password'])) {
            $user->reset_at = Carbon::now();
        }

        if (!empty($user->id) && !empty($dirtyData['issued_at'])) {
            $ip = app('request')->ip();
            $data['location'] = implode("\t", IPService::find($ip));
            $data['ip'] = $ip;
            $data['ua'] = app('request')->header('User-Agent');
            $user->loginLogs()->create($data);
        }
    }
}
