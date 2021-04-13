<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationForms extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'evaluation_forms';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	public function getTableColumns() {
		return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
	}

	public function rotations()
	{
		return $this->hasMany(Rotations::class);
	}

}
