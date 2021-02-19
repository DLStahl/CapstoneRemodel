<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnesthesiologistIdToOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('option', function (Blueprint $table) {
            $table->unsignedInteger('anesthesiologist_id')->nullable();

            $table->foreign('anesthesiologist_id')
                ->references('id')
                ->on('anesthesiologists')
                ->onDelete('cascade');
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
            $table->dropForeign('option_anesthesiologist_id_foreign');
            $table->dropColumn('anesthesiologist_id');
        });
    }
}
