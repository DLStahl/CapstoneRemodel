<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePreferenceMilestonesObjectivesFromAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("assignment", function (Blueprint $table) {
            $table->dropColumn("preference");
            $table->dropColumn("milestones");
            $table->dropColumn("objectives");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("assignment", function (Blueprint $table) {
            $table->unsignedInteger("preference")->nullable();
            $table->longText("milestones")->nullable();
            $table->longText("objectives")->nullable();
        });
    }
}
