@extends('main')

@section('content')
    <h1><?php
        date_default_timezone_set('America/New_York');
        echo "Today's Date: ".date("l", strtotime('today')),' ', date('F',strtotime('today')),' ',date('j',strtotime('today'));
        echo "<br>Visit Time: ".date('h:i:s a', time());
    ?></h1>
    <br><br><br>
    <h5>Select Your Date</h5>
    <br><br>
    <button type="button" class="btn btn-primary" onclick="location.href='schedule/firstday';"><?php
        if (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day'));
        }
        else{
            echo date("l", strtotime('+1 day')),' ', date('F',strtotime('+1 day')),' ',date('j',strtotime('+1 day')); 
        }    
    ?></button>	
	<button type="button" class="btn btn-primary" onclick="location.href='schedule/secondday';"><?php

        if(date("l", strtotime('today'))=='Thursday'){
            echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
        }
        elseif (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
        }
        else{
            echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day')); 
        }
    
    ?></button>
	<button type="button" class="btn btn-primary" onclick="location.href='schedule/thirdday';"><?php

        if(date("l", strtotime('today'))=='Wednesday'){
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif(date("l", strtotime('today'))=='Thursday'){
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
        }
        else{
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day')); 
        }
    
    ?></button>

    <br><br><br>
	
@endsection('content')