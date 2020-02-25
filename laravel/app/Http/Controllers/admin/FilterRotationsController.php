<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\FilterRotation;

class FilterRotationsController extends Controller
{
    public function viewPage() {
		$table = new FilterRotation;
		
		$columns = $table->getTableColumns();
		
		// remove 'id', 'created_at', and 'updated_at' from the columns, user should not worry about these
		$invisibleColumns = array('id', 'created_at', 'updated_at');
		foreach($invisibleColumns as $invisibleColumn) {
			$indexInColumns = array_search($invisibleColumn, $columns);
			if($indexInColumns !== false) {
				unset($columns[$indexInColumns]);
			}
		}
		
		$data = FilterRotation::select('*')->get();
		
		$primaryKeyField = $table->getKeyName();		
		
		// technically exposing file structure with this, but it's semi-necessary and not dangerous if we sanitize
		$fullyQualifiedName = 'filter_rotation';
		
		return view("crud.databasetable", compact('columns', 'data', 'primaryKeyField', 'fullyQualifiedName'));
	}
}
