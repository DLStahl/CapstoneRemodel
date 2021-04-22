<?php

namespace App\Models;

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
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
