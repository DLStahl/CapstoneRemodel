{{-- /resources/views/crud/databasetablerow.blade.php --}}

<?php
	// this is basically upholding two versions of the row template
	// one, when $entry is not null, where we insert the row data into the html here
	// and the second, when $entry is null, where we put placeholders in the html to be replaced later by javascript

	$hasData = isset($entry);
	$id = $hasData ? $entry[$primaryKeyField] : '%id%';
	
	$columnData = [];
	
	if($hasData) {
		$columnData = $entry;
	} else {
		foreach($columns as $column) {
			$columnData[$column] = '%'.$column.'%';
		}
	}
?>

<tr id="{{$id}}" name="entry">
	<td class="b1 operation">
		<button type="button" align="center" class="btn btn-md btn-primary" data-toggle="modal" data-target="#editModal" data-id="{{$id}}">Edit</button>
	</td>
	
	@foreach($columns as $column)
	<td align="left" name="{{$column}}">{{$columnData[$column]}}</td>
	@endforeach
</tr>