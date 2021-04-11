<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAssignmentColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->renameColumn('resident', 'resident_id');
            $table->renameColumn('attending', 'attending_id');
            $table->renameColumn('schedule', 'schedule_data_id');
            $table->renameColumn('option', 'option_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'resident');
            $table->renameColumn('attending_id', 'attending');
            $table->renameColumn('schedule_data_id', 'schedule');
            $table->renameColumn('option_id', 'option');
        });
    }
}
