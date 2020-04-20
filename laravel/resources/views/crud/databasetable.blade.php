@extends('main')

@section('content')

<div class="modal fade" id="confirmActionModal" role="dialog" aria-labelledby="confirmActionModal" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button id="confirmActionButton" class="btn btn-danger btn-ok" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

@component('crud.databasetableinputmodal', [
	'columns' => $columns,
	'uneditable' => $uneditable,
	'id' => 'editModal',
	'title' => 'Edit',
	'z_index' => 1400,
	'has_options' => true,
	'confirm_button_id' => 'updateButton',
	'confirm_button_name' => 'Update',
	'confirm_button_type' => 'btn-primary',
	'input_id_suffix' => 'edit'
])
@endcomponent

@component('crud.databasetableinputmodal', [
	'columns' => $columns,
	'uneditable' => [],
	'id' => 'addModal',
	'title' => 'Add New',
	'z_index' => 1200,
	'has_options' => false,
	'confirm_button_id' => 'addButton',
	'confirm_button_name' => 'Add',
	'confirm_button_type' => 'btn-success',
	'input_id_suffix' => 'add'
])
@endcomponent

<script>
	var id = -1;
	var confirmModal = $('#confirmActionModal');
	var editModal = $('#editModal');
	var addModal = $('#addModal');

	// add modal confirm button functionality
	addModal.find('#addButton').off("click").on("click", addRow);

	// delete button must set the text of the confirmation modal and set the confirm button functionality
	editModal.find("#deleteButton").off("click").on("click", function() {
		confirmModal.find('.modal-header').text("Confirm deletion?");
		confirmModal.find('.modal-body').text("This action cannot be undone.");
		$("#confirmActionButton").off("click").on("click", function() {
			deleteRow(id);
			editModal.modal('hide');
		});
	});
	
	// duplicate button
	editModal.find("#duplicateButton").off("click").on("click", function() {
		confirmModal.find('.modal-header').text("Confirm duplication?");
		confirmModal.find('.modal-body').text("You can always delete it later.");
		$("#confirmActionButton").off("click").on("click", function() {
			duplicateRow(id);
			editModal.modal('hide');
		});
	});
	
	// update button
	editModal.find("#updateButton").off("click").on("click", function() {
		updateRow(id);
	});

	$('#editModal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		id = button.data('id')
		
		// for each column we're showing, set the textfields text to that data
		@foreach($columns as $column)
		editModal.find("#{{$column}}-edit")
			.val(
					$('tr#' + id)
						.find(
							'td[name="{{$column}}"]'
						).text()
				);
		@endforeach		
	});
	
	function enableEdit(flag) { $('.actionEdit').prop("disabled", !flag); }
	
	var baseURL = "/laravel/public/api/admin/db/"
	
	function createParams(methodType, bodyArray) {
		return {
			headers: {
				"content-type":"application/json",
			},
			method:methodType,
			body: JSON.stringify(bodyArray)
		};
	}
	
    function deleteRow(id)
    {
		enableEdit(false);
		console.log("Delete " + id);
		
		var URL = baseURL + "delete";
		var params = createParams("DELETE", {
				table:"{{$fullyQualifiedName}}",
				rowID:id
			}
		);
		fetch(URL,params)
			// parse data
			.then(data=>{return data.json()})
			// respond to data
			.then(res=>{
				console.log("Delete response: ");
				console.log(res);
				
				if(res['successful'] == 'true') {
					$('tr#' + id).remove();
				} else {
					alert("There was an error deleting that row.");
				}
			})
			// exception thrown
			.catch(function() {
				console.log("Delete failed");
			})
			// re-enable the inputs, there's no finally options so we have to do both cases
			.then(function() { enableEdit(true); })
			.catch(function() { enableEdit(true); });
    }
	
	function duplicateRow(id)
	{
		console.log("Duplicate " + id);
		addRowHelper(@foreach($columns as $column)$("#" + id).find("[name='{{$column}}']").text(),@endforeach);
	}

    function updateRow(id)
    {
		enableEdit(false);
		console.log("Update " + id);
		
		var URL = baseURL + "update";
		var params = createParams("PUT", {
				table:"{{$fullyQualifiedName}}",
				rowID:id,
				data:JSON.stringify({
					@foreach($columns as $column)
					{{$column}}:$("#{{$column}}-edit").val(),
					@endforeach
				})
			}
		);
		fetch(URL,params)
			// parse data
			.then(data=>{return data.json()})
			// respond to data
			.then(res=>{
				console.log("Update response: ");
				console.log(res);
				
				if(res['successful'] == 'true') {
					var newData = res['newData'];
					
					@foreach($columns as $column)
					$('tr#' + newData.{{$primaryKeyField}})
						.find(
							'td[name="{{$column}}"]'
						)
						.text(newData.{{$column}})
					@endforeach
					
					editModal.modal('hide');
				} else {
					alert("There was an issue updating that row.");
				}
			})
			// exception thrown
			.catch(function() {
				console.log("Update failed");
			})
			// re-enable the inputs, there's no finally options so we have to do both cases
			.then(function() { enableEdit(true); })
			.catch(function() { enableEdit(true); });
    }
	
	function addRow() {
		addRowHelper(@foreach($columns as $column)$("#{{$column}}-add").val(),@endforeach);
	}
	
	function addRowHelper(@foreach($columns as $column){{$column}},@endforeach)
	{
		enableEdit(false);
		console.log("Add");
		
		var URL = baseURL + "add";
		var params = createParams("POST", {
				table:"{{$fullyQualifiedName}}",
				data:JSON.stringify({
					@foreach($columns as $column)
					{{$column}}:{{$column}},
					@endforeach
				})
			}
		);
		fetch(URL,params)
			// parse data
			.then(data=>{return data.json()})
			// respond to data
			.then(res=>{
				console.log("Add response: ");
				console.log(res);
				
				if(res['successful'] == 'true') {
					var newData = res['newData'];
					
					addTableRowLocally(newData.id, @foreach($columns as $column)newData.{{$column}},@endforeach);
					
					addModal.modal('hide');
				} else {
					alert("There was an issue adding that row.");
				}
			})
			// exception thrown
			.catch(function() {
				console.log("Add new row failed");
			})
			// re-enable the inputs, there's no finally options so we have to do both cases
			.then(function() { enableEdit(true); })
			.catch(function() { enableEdit(true); });
	}
	
	function addTableRowLocally(id, @foreach($columns as $column){{$column}},@endforeach) {
		var rowHTML = `@component('crud.databasetablerow', ['columns' => $columns])@endcomponent`
		rowHTML = rowHTML.replace(/%id%/g, id);
		@foreach($columns as $column)
		rowHTML = rowHTML.replace(/%{{$column}}%/g, {{$column}});
		@endforeach
		$('#dataTableBody').prepend(rowHTML);
	}
</script>

<button type="button" align="center" class="btn btn-md btn-success" data-toggle="modal" data-target="#addModal">Add</button>

<h3>{{$fullyQualifiedName}}</h3>
<table id="table">
    <thead>
        <tr>
            <th style="width:1%; white-space:nowrap;"></th>
			@foreach($columns as $column)
			<th>{{$column}}</th>
			@endforeach
        </tr>
    </thead>
    <tbody id="dataTableBody">
		@foreach($data as $entry)
		@component('crud.databasetablerow', ['columns' => $columns, 'entry' => $entry, 'primaryKeyField' => $primaryKeyField])
		@endcomponent
		@endforeach
    </tbody>
</table>
<style>
    table { 
      width: 100%; 
      border-collapse: collapse; 
    }
    /* Zebra striping */
    tr:nth-of-type(odd) { 
      background: rgba(0,0,0,.05);
      border: 1px solid #dee2e6;
    }
    th { 
      background: white; 
      font-weight: bold; 
      text-align: center; 
      padding: 6px; 
      border: 1px solid #dee2e6; 
    }
    td{ 
      padding: 6px; 
      border: 1px solid #dee2e6; 
      text-align: left; 
      /*hyphens: auto;*/
    }

    .b1{
        white-space: nowrap
    }
</style>
@endsection
