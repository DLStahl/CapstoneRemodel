<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attending extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attending';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
