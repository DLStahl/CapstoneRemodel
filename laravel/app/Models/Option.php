<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'option';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * https://laravel.com/docs/8.x/eloquent-relationships#one-to-many-inverse
     * get the anesthesiologist that the resident chose
     */
    public function anesthesiologist()
    {
        return $this->belongsTo(Anesthesiologist::class);
    }
}
