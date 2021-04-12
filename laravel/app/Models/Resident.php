<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resident';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function evaluate_datas()
    {
        return $this->hasMany(EvaluateData::class);
    }

    public function probability()
    {
        return $this->hasOne(Probability::class);
    }
    
}
