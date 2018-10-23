<?php

namespace App\Essential\Jobs;

use App\Essential\Models\SmsLog;
use App\Essential\Services\MsgService;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class SmsJob extends Job
{
    private $smsLog;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SmsLog $smsLog)
    {
        $this->smsLog = $smsLog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($result = (new MsgService('sms'))->send($this->smsLog->mobile, $this->smsLog->template, $this->smsLog->data)) {
                $this->smsLog->update([
                    'status' => 'success'
                ]);
            } else {
                return $this->error('gateway');
            }
        } catch (NoGatewayAvailableException $e) {
            return $this->error('gateway');
        }

        return;
    }

    private function error($type)
    {
        $notes = [
            'gateway' => '短信通道调用失败',
            'balance' => '账户余额不足',
            'user'    => '用户不存在'
        ];
        $this->smsLog->update([
            'status' => 'fail',
            'note'   => $notes[$type],
        ]);
        return;
    }
}
