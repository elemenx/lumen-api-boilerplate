<?php

namespace App\Essential\Observers;

use App\Essential\Models\SmsLog;
use Carbon\Carbon;

class SmsLogObserver
{
    public function saving(SmsLog $smsLog)
    {
        $dirtyData = $smsLog->getDirty();

        if (!empty($dirtyData['status'])) {
            if ($dirtyData['status'] == 'success') {
                $smsLog->sent_at = Carbon::now();
            }
        }
    }
}
