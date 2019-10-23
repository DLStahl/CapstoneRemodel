<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilterRotation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'filter_rotation';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
