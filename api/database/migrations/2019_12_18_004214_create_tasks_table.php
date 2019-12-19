<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->string("uuid", 36)->primary();
            $table->string("content", 200)->nullable(false);
            $table->integer("sort_order")->nullable(false);
            $table->boolean("done")->default(false)->nullable(false);
            $table->enum("type", ["shopping", "work"])->nullable(false);
            $table->dateTime("date_created")->nullable(false);
            $table->dateTime("last_update");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
