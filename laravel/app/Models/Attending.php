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
    protected $table = "attending";

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
