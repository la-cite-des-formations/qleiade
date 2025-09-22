<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEssentialToWealthsIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wealths_indicators', function (Blueprint $table) {
            $table->boolean('is_essential')->default(TRUE)->after('indicator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wealths_indicators', function (Blueprint $table) {
            $table->dropColumn('is_essential');
        });
    }
}
