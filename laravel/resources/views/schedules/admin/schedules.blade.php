@extends('main')
@section('content')
    <form method="POST" action="updateDB">
        <div class="form-group">             
            <label>Select your operation:</label>
            <select name="op" required>
                <option disabled selected value> -- Select an Operation -- </option>
                <option value="add">Add Data Sets</option>
                <option value="delete">Delete Data Sets</option>
                <option value="edit">Edit Data Sets</option>
            </select>
                
            <br>
                        
            <label>Select the date:</label>
            <input type="date" name="date" required>
                
            <br>
            
            <input align = "right" type='submit' class='btn btn-md btn-success'>

		</div>
    </form>
@endsection