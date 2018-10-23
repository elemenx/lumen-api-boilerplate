<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_type')->nullable()->comment('内容类型');
            $table->integer('object_id')->unsigned()->default(0)->comment('内容ID');
            $table->enum('uploader_type', ['staff', 'user'])->default('user')->nullable()->comment('上传者类型:staff员工,user用户');
            $table->integer('uploader_id')->unsigned()->default(0)->comment('上传者ID');
            $table->string('name')->nullable()->comment('原始名称');
            $table->string('mime')->nullable()->comment('mimeType');
            $table->string('path')->comment('路径');
            $table->integer('width')->unsigned()->default(0)->comment('图片宽');
            $table->integer('height')->unsigned()->default(0)->comment('图片高');
            $table->tinyInteger('sequence')->unsigned()->default(0)->comment('排序,从小到大');
            $table->index(['object_type', 'object_id']);
            $table->index(['uploader_type', 'uploader_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
