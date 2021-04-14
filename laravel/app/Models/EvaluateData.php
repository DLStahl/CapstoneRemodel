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
    public $timestamps = true;
    
    protected $fillable = [
        "date", 
        "location", 
        "diagnosis", 
        "procedure", 
        "ASA", 
        "resident_id", 
        "resident", 
        "attending_id", 
        "attending", 
        "time_with_attending"
    ];
}
