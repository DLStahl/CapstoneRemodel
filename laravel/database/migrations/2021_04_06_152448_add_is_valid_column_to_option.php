<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsValidColumnToOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('option', 'isValid'))
        {
            Schema::table('option', function (Blueprint $table) {
                $table->integer('isValid');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('option', 'isValid'))
        {
            Schema::table('option', function (Blueprint $table) {
                $table->dropColumn('isValid');
            });
        }
    }   
}
