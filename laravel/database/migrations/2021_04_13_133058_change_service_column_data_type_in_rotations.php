<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeServiceColumnDataTypeInRotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->renameColumn('Service', 'evaluation_forms_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->renameColumn('evaluation_forms_id', 'Service');
        });
    }
}
