<?php

namespace App\Essential\Observers;

use App\Essential\Services\IPService;
use App\Essential\Models\Staff;
use Carbon\Carbon;

class StaffObserver
{
    public function creating(Staff $staff)
    {
        $staff->issued_at = Carbon::now();
    }

    public function saving(Staff $staff)
    {
        $dirtyData = $staff->getDirty();
        if (!empty($dirtyData['password'])) {
            $staff->reset_at = Carbon::now();
        }
        if (!empty($staff->id) && !empty($dirtyData['issued_at'])) {
            $ip = app('request')->ip();
            $data['location'] = implode("\t", IPService::find($ip));
            $data['ip'] = $ip;
            $data['ua'] = app('request')->header('User-Agent');
            $staff->loginLogs()->create($data);
        }
    }
}
