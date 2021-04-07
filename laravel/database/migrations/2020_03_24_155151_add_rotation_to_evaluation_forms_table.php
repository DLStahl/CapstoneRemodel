<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRotationToEvaluationFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("evaluation_forms", function (Blueprint $table) {
            $table->text("rotation")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("evaluation_forms", function (Blueprint $table) {
            $table->dropColumn("rotation");
        });
    }
}
