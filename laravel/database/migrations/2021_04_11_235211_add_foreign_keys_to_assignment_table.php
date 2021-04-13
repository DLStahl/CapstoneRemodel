<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->foreign('resident_id')
                ->references('id')
                ->on('resident');
            
            $table->foreign('schedule_data_id')
                ->references('id')
                ->on('schedule_data');

            $table->foreign('option_id')
                ->references('id')
                ->on('option');
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
            $table->dropForeign('assignment_resident_id_foreign');
            $table->dropForeign('assignment_schedule_data_id_foreign');
            $table->dropForeign('assignment_option_id_foreign');
        });
    }
}
