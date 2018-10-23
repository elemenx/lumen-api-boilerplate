<?php

namespace App\Essential\Providers;

use App\Essential\Models\LoginLog;
use App\Essential\Models\SmsLog;
use App\Essential\Models\Staff;
use App\Essential\Models\User;
use App\Essential\Observers\LoginLogObserver;
use App\Essential\Observers\SmsLogObserver;
use App\Essential\Observers\StaffObserver;
use App\Essential\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        LoginLog::observe(LoginLogObserver::class);
        SmsLog::observe(SmsLogObserver::class);
        Staff::observe(StaffObserver::class);
        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Relation::morphMap([
            'user'  => 'App\Essential\Models\User',
            'staff' => 'App\Essential\Models\Staff',
        ]);
    }
}
