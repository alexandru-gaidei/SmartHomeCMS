<?php

use App\Sensor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('set null');

            $table->string('name');
            $table->enum('source_type', array_keys(Sensor::$SRC_TYPES))->default(Sensor::SRC_TYPE_FETCH);
            $table->text('source_url_fetch')->nullable();
            $table->string('parameter')->nullable();
            $table->string('identifier')->nullable();
            $table->enum('value_type', array_keys(Sensor::$VAL_TYPES))->default(Sensor::VAL_TYPE_NUM);
            $table->string('execute_at_rrule')->nullable();
            $table->decimal('min_value', 16, 4)->nullable();
            $table->decimal('value', 16, 4)->default(0);
            $table->decimal('max_value', 16, 4)->nullable();

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
        Schema::dropIfExists('sensors');
    }
}
