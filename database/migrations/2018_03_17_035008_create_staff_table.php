<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('员工账户名');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('realname')->nullable()->comment('真实姓名');
            $table->string('password')->comment('密码[bcrypt值]');
            $table->string('tfa_secret')->nullable()->comment('双因素验证秘钥');
            $table->enum('role', ['root', 'service'])->default('service')->comment('角色类型:root管理员,service客服');
            $table->boolean('enabled')->default(1)->comment('是否启用');
            $table->boolean('locked')->default(0)->comment('是否锁定');
            $table->boolean('has_wechat')->default(0)->comment('是否绑定微信');
            $table->boolean('has_tfa')->default(0)->comment('是否绑定双因素');
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
        Schema::dropIfExists('staff');
    }
}
