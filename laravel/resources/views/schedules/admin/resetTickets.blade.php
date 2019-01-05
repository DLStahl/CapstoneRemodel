@extends('main')
@section('content')
	<form method="POST" action="updateTickets">
		{{ csrf_field() }}
	    <h1>Notice: </h1><br>
	    @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
        @endif
	    <h4>This is an irrevocable operation, please make sure you want to reset tickets for ALL residents!</h4>

	    <br>
	    
	    <input type="submit" value="Reset" class="btn btn-success">
	</form>
@endsection