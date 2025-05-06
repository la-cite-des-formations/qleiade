<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWealthsIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wealths_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wealth_id');
            $table->foreign('wealth_id')
                ->references('id')
                ->on('wealth')->onDelete('cascade');
            $table->unsignedBigInteger('indicator_id');
            $table->foreign('indicator_id')
                ->references('id')
                ->on('indicator')->onDelete('cascade');
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
        Schema::dropIfExists('wealths_indicators');
    }
}
