@extends('main')
@section('content')

    <h4>Successfully Delete Data Sets!</h4>

    <?php
        foreach ($urls as $url)
        {
            echo "<script>window.open('".$url."', '_blank')</script>";
        }  
    ?>
@endsection
