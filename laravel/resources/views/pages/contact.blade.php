@extends('main')

@section('title', '| Contact')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <h1>
            <hr>
            
            <form action="contact" method ="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <label name="email">Email:</label>
                    <input id="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label name="subject">Subject:</label>
                    <input id="subject" name="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label name="body">Body:</label>
                    <textarea id="body" name="body" class="form-control" placeholder="Type your message here..." /></textarea>
                </div>

                <input type="submit" value="Send Message" class="btn btn-success">
            </form>
            </h1>
        </div>
    </div>
@endsection