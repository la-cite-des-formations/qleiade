<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWealthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wealth', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wealth_type_id');
            $table->foreign('wealth_type_id')->references('id')->on('wealth_type');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('unit');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('wealth');
            $table->string('name');
            $table->string('description', 1500)
                ->nullable();
            $table->string('tracking', 1500)
                ->nullable();
            $table->jsonb('granularity')
                ->nullable();
            $table->unsignedTinyInteger('conformity_level')
                ->nullable();
            $table->dateTime('validity_date')
                ->nullable();
            $table->dateTime('archived_at')
                ->nullable();
            $table->jsonb('attachment')->nullable();
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
        Schema::dropIfExists('wealth');
    }
}
