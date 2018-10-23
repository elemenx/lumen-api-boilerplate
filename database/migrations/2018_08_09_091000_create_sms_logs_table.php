<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['pending', 'success', 'fail'])->default('pending')->comment('状态:open待处理,success成功,fail失败');
            $table->char('mobile', 11)->comment('手机号');
            $table->string('template')->comment('模板ID');
            $table->text('data')->comment('数据[json-array]');
            $table->string('note')->comment('失败原因')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('sent_at')->nullable()->comment('实际发送时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
}
