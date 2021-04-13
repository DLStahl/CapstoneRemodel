<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMigrationIdColumnToIntInOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('option', function (Blueprint $table) {
            $table->unsignedInteger('milestone_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('option', function (Blueprint $table) {
            $table->longText('milestone_id')->nullable()->change();
        });
    }
}
