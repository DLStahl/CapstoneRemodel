<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluateData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "evaluation_data";

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

    public function attendings()
    {
        return $this->belongsTo(Attending::class);
    }
}
