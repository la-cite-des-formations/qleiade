<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quality_label_id');
            $table->foreign('quality_label_id')->references('id')->on('quality_label')->onDelete('cascade');
            $table->string('name');
            $table->string('label');
            $table->string('number', 2)
            ->nullable();
            $table->string('description', 1500)
            ->nullable();
            $table->unsignedBigInteger('criteria_id')
                ->nullable();
            $table->foreign('criteria_id')->references('id')->on('criteria')->onDelete('cascade');
            $table->unsignedTinyInteger('conformity_level_expected')
                ->nullable();
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
        Schema::dropIfExists('indicator');
    }
}
