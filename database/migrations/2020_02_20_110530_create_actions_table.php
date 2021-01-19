<?php

use App\Action;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('sensor_id')->nullable();
            $table->foreign('sensor_id')
                ->references('id')
                ->on('sensors')
                ->onDelete('set null');

            $table->string('name');
            $table->enum('value_type', array_keys(Action::$VAL_TYPES))->default(Action::VAL_TYPE_MIN);
            $table->enum('type', array_keys(Action::$TYPES))->default(Action::TYPE_HTTP_GET);
            $table->text('subject')->nullable();

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
        Schema::dropIfExists('actions');
    }
}
