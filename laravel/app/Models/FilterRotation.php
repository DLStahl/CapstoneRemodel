<?php

namespace App\Models;

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
	
	public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
