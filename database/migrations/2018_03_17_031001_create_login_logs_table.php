<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('object_type', ['staff', 'user', 'client'])->default('user')->comment('对象类型');
            $table->integer('object_id')->unsigned()->comment('对象ID');
            $table->integer('ip')->unsigned()->comment('IP地址');
            $table->string('location')->comment('地理位置');
            $table->integer('device_id')->unsigned()->default(0)->index()->comment('设备ID');
            $table->string('mac')->nullable()->comment('设备mac地址');
            $table->text('ua')->nullable()->comment('设备userAgent信息');
            $table->index(['object_type', 'object_id']);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_logs');
    }
}
