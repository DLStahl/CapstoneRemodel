<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleData extends Model
{
    /*
            $table->increments('id');

            $table->date('date');
            $table->text('location');
            $table->text('room');
            $table->longText('case_procedure');
            $table->text('lead_surgeon');
            $table->longText('patient_class');
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedule_data';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
