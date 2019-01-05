<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Probability extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'probability';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
