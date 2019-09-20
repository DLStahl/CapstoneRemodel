<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilestoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestone', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('category')->nullable();  // Category of milestones: PC, PBLI, SBP, etc
            $table->longText('title')->nullable(); // detailed title of milestones
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('milestone');
    }
}