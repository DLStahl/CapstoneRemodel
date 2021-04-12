<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rotations'))
        {
            Schema::create('rotations', function (Blueprint $table) {
                $table->String('Name');
                $table->bigIncrements('ID');
                $table->Integer('Level');
                $table->Integer('Service');
                $table->String('Site');
                $table->date('Start');
                $table->date('End');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotations');
    }
}
