<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffKeyToAnesthesiologistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("anesthesiologists", function (Blueprint $table) {
            $table->string("staff_key")->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("anesthesiologists", function (Blueprint $table) {
            $table->dropColumn("staff_key");
        });
    }
}
