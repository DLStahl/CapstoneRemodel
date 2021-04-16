@extends('main')
@section('content')

    <h4>Successfully Deleted Data Sets!</h4>

    @foreach (urls as url)
        <script>window.open("{{ $url }}", "_blank");</script>
    @endforeach
@endsection
