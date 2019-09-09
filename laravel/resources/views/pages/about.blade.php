@extends('main')
@section('title', '| About')
@section('content')
    <div class="row">
        <div class="col-md-12">            
            <h2>Personal Information</h2>
            <table class="table table-striped table-bordered" id="personal_info_table">
                <tr>
                    <td align="left">Name</td>
                    <td aligh="left">{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td align="left">Email</td>
                    <td align="left">{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td align="left">Role</td>
                    @if (count($data['roles']) > 0)
                        <td align="left">
                        @foreach($data['roles'] as $role)
                            <p>{{ $role }}</p>
                        @endforeach
                        </td>
                    @else
                        <td align="left">None</td>
                    @endif
                </tr>
                <tr>
                    <td align="left"><?php
        if (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day'));
        }
        else{
            echo date("l", strtotime('+1 day')),' ', date('F',strtotime('+1 day')),' ',date('j',strtotime('+1 day')); 
        }    
    ?></td>
                    <td align="left">
                    @if ($data['firstday'] != null)
                        {{ $data['firstday'] }}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td align="left"><?php

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
    
    ?></td>
                    <td align="left">
                        @foreach ($data['secondday'] as $choice)
                            @if ($choice != null)
                                <p>{{ $choice }}</p>
                            @endif
                        @endforeach
                    </td>
                </tr>  
                <tr>
                    <td align="left"><?php

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
    
    ?></td>
                    <td align="left">
                        @foreach ($data['thirdday'] as $choice)
                            @if ($choice != null)
                                <p>{{ $choice }}</p>
                            @endif
                        @endforeach
                    </td>   
                </tr>                              
            </table>
        </div>
    </div>
@endsection