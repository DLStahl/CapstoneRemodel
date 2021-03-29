<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleDataStatic extends Model
{
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedule_data_static';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
	
	public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
