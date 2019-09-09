@extends('main')

@section('content')

    <h5>Date: <input id="datepicker" autocomplete="off" class="" style="width: 300px;" type="text" value= "{{ $date }}" /></h5>
    <script type="text/javascript">  
        $('#datepicker').datepicker({ 
            beforeShowDay: $.datepicker.noWeekends,
            dateFormat: 'yy-mm-d',
            onSelect: function(date) {
                window.location.replace("/laravel/public/admin/evaluation/"+date)
            }            
         }); 
    </script>  

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
