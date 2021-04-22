<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_data', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->text('location')->nullable();
            $table->text('diagnosis')->nullable();
            $table->longText('procedure')->nullable();
            $table->string('ASA');
            $table->unsignedInteger('rId')->nullable();
            $table->longText('resident')->nullable();
            $table->unsignedInteger('aId')->nullable();
            $table->longText('attending')->nullable();
            $table->unsignedBigInteger('diff')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_data');
    }
}
