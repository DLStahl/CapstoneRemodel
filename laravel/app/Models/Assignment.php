<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assignment';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function schedule_data()
    {
        return $this->belongsTo(ScheduleData::class);
    }

    public function anesthesiologist()
    {
        return $this->belongsTo(Anesthesiologist::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
