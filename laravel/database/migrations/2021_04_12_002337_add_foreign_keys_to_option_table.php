<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('option', function (Blueprint $table) {
            $table->foreign('resident_id')
                ->references('id')
                ->on('resident');
            
            $table->foreign('schedule_data_id')
                ->references('id')
                ->on('schedule_data');

            $table->foreign('milestone_id')
                ->references('id')
                ->on('milestone');
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
            $table->dropForeign('option_resident_id_foreign');
            $table->dropForeign('option_schedule_data_id_foreign');
            $table->dropForeign('option_milestone_id_foreign');
        });
    }
}
