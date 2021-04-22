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
    protected $table = 'evaluation_data';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
