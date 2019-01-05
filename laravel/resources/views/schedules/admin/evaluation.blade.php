@extends('main')

@section('content')

    <h3><?php
        date_default_timezone_set('America/New_York');
        echo "Today is ".date("Y/m/d"),", here is the assignment for ";
        echo date("Y/m/d", strtotime('-1 day')); 
        

    ?></h3> 
    <br><br>

    <table class="table table-striped table-bordered" id="sched_table">
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

     <?php 
     $count = 1;             
     ?>
     @foreach ($evaluate_data as $row)
     <tr>
        <?php           

        echo '<td>'.$count.'</td>';
        echo '<td align="left">'.$row['location'].'</td>';
        echo '<td align="left">'.$row['diagnosis'].'</td>';
        echo '<td align="left">'.$row['procedure'].'</td>';
        echo '<td align="left">'.$row['ASA'].'</td>';
        echo '<td align="left">'.$row['resident'].'</td>';
        echo '<td align="left">'.$row['attending'].'</td>';
        ?>

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
    </table>


    <br><br><br>
	
@endsection('content')