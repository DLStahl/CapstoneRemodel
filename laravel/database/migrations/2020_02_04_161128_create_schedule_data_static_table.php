<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleDataStaticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Example of what's needed (provided by stahl):
		id, location, room, case_procedure, case_procedure_code, lead_surgeon, lead_surgeon_code, patient_class, start_time, end_time, rotation
          1,           'IR',    'IR2',     'IR Procedure',                                       '-1',                'OORA',                                   '-1',                  'TBD',    '07:00:00',  '17:00:00',    'OORA'
		*/

        Schema::create("schedule_data_static", function (Blueprint $table) {
            $table->increments("id"); // Primary key
            $table->text("location"); // Location of the surgery
            $table->text("room"); // Room of the surgery
            $table->longText("case_procedure"); // Case procedure description
            $table->text("case_procedure_code"); // QGenda code of the case procedure
            $table->text("lead_surgeon"); // Lead surgeon of the surgery
            $table->text("lead_surgeon_code"); // QGenda code of the surgeon/attending
            $table->longText("patient_class"); // Patient class of the surgery
            $table->time("start_time"); // Start time of the schedule item
            $table->time("end_time"); // End time of the schedule item
            $table->text("rotation"); // Which rotation it is under / foreign key to 'filter_rotation' table

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("schedule_data_static");
    }
}
