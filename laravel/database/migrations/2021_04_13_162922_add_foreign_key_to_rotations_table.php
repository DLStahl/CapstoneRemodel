<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToRotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->foreign('evaluation_forms_id')
                ->references('id')
                ->on('evaluation_forms');
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
            $table->dropForeign('rotations_evaluation_forms_id_foreign');
        });
    }
}
