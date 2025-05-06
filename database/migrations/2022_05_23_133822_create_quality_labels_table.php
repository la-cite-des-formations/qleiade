<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateQualityLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_label', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->integer('criterias_count_expected')
                ->nullable();
            $table->integer('indicator_count_expected')
                ->nullable();
            $table->string('description', 1500)
                ->nullable();
            $table->string('image', 1500)
                ->nullable();
            $table->string('audit_frequency', 10)
                ->nullable();
            $table->dateTime('last_audit_date')
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
        Schema::dropIfExists('quality_label');
    }
}
