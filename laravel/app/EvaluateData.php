<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvaluateData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluation_data';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
