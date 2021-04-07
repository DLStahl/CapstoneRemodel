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

    public function residents()
    {
        return $this->belongsTo(Resident::class);
    }

    public function options()
    {
        return $this->belongsTo(Option::class);
    }

    public function scheduledatas()
    {
        return $this->belongsTo(ScheduleData::class);
    }

    public function attendings()
    {
        return $this->belongsTo(Attending::class);
    }

    public function anesthesiologist()
    {
        return $this->belongsTo(Anesthesiologist::class);
    }
}
