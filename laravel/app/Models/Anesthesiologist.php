<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anesthesiologist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["first_name", "last_name", "staff_key"];

    /**
     * https://laravel.com/docs/8.x/eloquent-relationships#one-to-many-inverse
     * get the options where this resident was chosen
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
