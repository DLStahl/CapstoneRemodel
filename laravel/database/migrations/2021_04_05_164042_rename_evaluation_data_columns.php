<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEvaluationDataColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluation_data', function (Blueprint $table) {
            $table->renameColumn('rId', 'resident_id');
            $table->renameColumn('aId', 'attending_id');
            $table->renameColumn('diff', 'time_with_attending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluation_data', function (Blueprint $table) {
            $table->renameColumn('resident_id', 'rId');
            $table->renameColumn('attending_id', 'aId');
            $table->renameColumn('time_with_attending', 'diff');
        });
    }
}
