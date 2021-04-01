@extends('main')
@section('content')
    <form action="../admin/upload" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <h4> Upload Resident Rotations Schedule txt file </h4>
            <input type="file" class="form-control-file" name="fileUpload" id="InputFile">
            <small id="fileHelp" class="form-text text-muted">Please upload a valid .txt file</small>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
