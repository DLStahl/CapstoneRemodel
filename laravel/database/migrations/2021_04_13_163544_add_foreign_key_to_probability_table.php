<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToProbabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probability', function (Blueprint $table) {
            $table->foreign('resident_id')
                ->references('id')
                ->on('resident');
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
            $table->dropForeign('probability_resident_id_foreign');
        });
    }
}
