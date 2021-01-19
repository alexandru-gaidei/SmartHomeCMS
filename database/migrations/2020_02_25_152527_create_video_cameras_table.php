<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_cameras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('set null');
                
            $table->string('name');
            $table->string('stream_url');

            $table->boolean('store')->default(false);
            $table->string('length')->nullable();
            $table->string('size_height')->nullable();
            $table->integer('keep_days')->nullable();
            $table->string('pid')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_cameras');
    }
}
