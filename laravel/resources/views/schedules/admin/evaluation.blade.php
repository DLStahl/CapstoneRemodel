@extends('main')

@section('content')

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- <meta name="viewport" content="initial-scale=0.35"> -->
    <h5>Date: <input id="datepicker" autocomplete="off" class="" style="width: 300px;" type="text" value= "{{ $date }}" /></h5>
    <script type="text/javascript">  
        $('#datepicker').datepicker({ 
            beforeShowDay: $.datepicker.noWeekends,
            dateFormat: 'yy-mm-dd',
            onSelect: function(date) {
                window.location.replace("/laravel/public/admin/evaluation/"+date)
            }            
         }); 
    </script>  

    <table id="sched_table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Location</th>
                <th>Diagnosis</th>
                <th>Procedure</th>
                <th>ASA status</th>
                <th>Resident</th>
                <th>Attending</th>
                <th>Milestone</th>
                <th>Learning Objective</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $count = 1;             
            ?>
            @foreach ($evaluate_data as $row)
            <tr>
                <td align="left">{{ $count }}</td>
                @if ($row['location'] != null)
                    <td align="left">{{ $row['location'] }}</td>
                @else
                    <td align="left"><br></td>
                @endif
                @if ($row['diagnosis'] != null)
                    <td align="left">{{ $row['diagnosis'] }}</td>
                @else
                    <td align="left"><br></td>
                @endif
                @if ($row['procedure'] != null)
                    <td align="left"> -
                    <?php 
                    $procedure = preg_replace( "/\n/", "\n- ", $row['procedure']);
                    echo nl2br($procedure) ?></td>
                @else
                    <td align="left"><br></td>
                @endif
                @if ($row['ASA'] != null)
                    <td align="left">{{ $row['ASA'] }}</td>
                @else
                    <td align="left"><br></td>
                @endif
                @if ($row['resident'] != null)
                    <td align="left">{{ $row['resident'] }}</td>
                @else
                    <td align="left"><br></td>
                @endif
                @if ($row['attending'] != null)
                    <td align="left">{{ $row['attending'] }}</td>
                @else
                    <td align="left"><br></td>
                @endif

                @if ($row['milestone'] != null)
                    <td align="left">{{ $row['milestone'] }}</td>
                @else
                    <td align="left">TBD</td>
                @endif
                @if ($row['objective'] != null)
                    <td align="left">{{ $row['objective'] }}</td>
                @else
                    <td align="left">TBD</td>
                @endif
            
            <?php
            $count = $count + 1;
            ?>
                        
         </tr>
         @endforeach
         </tbody>
    </table>


    <br><br><br>


<style>
    table { 
      width: 100%; 
      border-collapse: collapse; 
    }
    /* Zebra striping */
    tr:nth-of-type(odd) { 
      background: #eee;
      border: 1px solid #ccc;
    }
    th { 
      background: white; 
      font-weight: bold; 
      text-align: center; 
      padding: 6px; 
      border: 1px solid #ccc; 
    }
    td{ 
      padding: 6px; 
      border: 1px solid #ccc; 
      text-align: left; 
      /*hyphens: auto;*/
    }
</style>

<!--[if !IE]><!-->
<style>
/* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 1200px){
    .btn{
        padding: .3rem .7rem;
        font-size: 14px;
    }


    /* Force table to not be like tables anymore */
    #sched_table table, #sched_table thead, #sched_table tbody, #sched_table th, #sched_table td, #sched_table tr { 
        display: block; 
    }
    
    /* Hide table headers (but not display: none;, for accessibility) */
    #sched_table thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    #sched_table tr { border: 1px solid #ccc; }
    
    #sched_table td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 43%; 
    }

    #sched_table tr:nth-of-type(odd) td:not(:last-of-type){
        border-bottom: 1px solid white; 
    }
    
    #sched_table td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 5px; 
        white-space: normal;
    }
    
    /*
    Label the data
    */
    #sched_table td:nth-of-type(1):before { content: "No."; }
    #sched_table td:nth-of-type(2):before { content: "Location"; }
    #sched_table td:nth-of-type(3):before { content: "Diagnosis"; }
    #sched_table td:nth-of-type(4):before { content: "Procedure"; }
    #sched_table td:nth-of-type(5):before { content: "ASA status"; }
    #sched_table td:nth-of-type(6):before { content: "Resident"; }
    #sched_table td:nth-of-type(7):before { content: "Attending"; }
    #sched_table td:nth-of-type(8):before { content: "Milestone"; }
    #sched_table td:nth-of-type(9):before { content: "Learning Objective"; }
}

</style>
<!--<![endif]-->

@endsection('content')
