<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWealthsActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wealths_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wealth_id');
            $table->foreign('wealth_id')
                ->references('id')
                ->on('wealth')->onDelete('cascade');
            $table->unsignedBigInteger('action_id');
            $table->foreign('action_id')
                ->references('id')
                ->on('action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wealths_actions');
    }
}
