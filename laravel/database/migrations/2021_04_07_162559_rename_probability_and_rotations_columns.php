<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameProbabilityAndRotationsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probability', function (Blueprint $table) {
            $table->renameColumn('resident', 'resident_id');
        });

        Schema::table('rotations', function (Blueprint $table) {
            $table->renameColumn('ID', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probability', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'resident');
        });

        Schema::table('rotations', function (Blueprint $table) {
            $table->renameColumn('id', 'ID');
        });
    }
}
