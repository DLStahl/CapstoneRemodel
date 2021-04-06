<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "schedule_data";

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
