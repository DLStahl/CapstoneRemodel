<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluationDataForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluation_data', function (Blueprint $table) {
            $table->foreign('resident_id')
                ->references('id')
                ->on('resident');

            $table->foreign('attending_id')
                ->references('id')
                ->on('attending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign('evaluation_data_resident_id_foreign');
        $table->dropForeign('evaluation_attending_id_foreign');
    }
}
