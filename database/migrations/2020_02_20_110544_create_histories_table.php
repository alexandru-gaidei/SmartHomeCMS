<?php

use App\History;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('historyable_id');
            $table->string("historyable_type");
            $table->enum("status", [History::OK, History::FAIL])->default(History::FAIL);
            $table->text("data");
            $table->decimal('value', 16, 4)->nullable();
            $table->dateTime('ocurrence_at');

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
        Schema::dropIfExists('histories');
    }
}
