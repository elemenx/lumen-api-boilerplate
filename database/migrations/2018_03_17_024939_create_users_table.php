<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('mobile', 11)->unique()->comment('手机号');
            $table->string('password')->nullable()->comment('密码[bcrypt加密后值]');
            $table->string('name')->unique()->comment('名称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('unionid', 64)->nullable()->index()->comment('微信开放平台unionid');
            $table->string('session_key')->nullable()->comment('微信小程序session_key');
            $table->integer('district_id')->unsigned()->default(0)->index()->comment('所属城市ID');
            $table->integer('school_id')->unsigned()->default(0)->index()->comment('所属学校ID');
            $table->integer('department_id')->unsigned()->default(0)->index()->comment('所属院系ID');
            $table->integer('credit')->unsigned()->default(0)->comment('积分');
            $table->boolean('locked')->default(0)->comment('账号是否锁定');
            $table->boolean('enabled')->default(1)->comment('账号是否开启');
            $table->boolean('has_wechat')->default(0)->comment('是否绑定微信');
            $table->timestamps();
            $table->timestamp('issued_at')->nullable()->comment('鉴权颁发时间');
            $table->timestamp('reset_at')->nullable()->comment('密码重置时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
