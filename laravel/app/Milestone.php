<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'milestone';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
