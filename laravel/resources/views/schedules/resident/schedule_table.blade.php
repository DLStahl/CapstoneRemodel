@extends('schedules.resident.schedule_basic')

@section('table_generator')
    @if(sizeof($schedule_data)>0)
        <div id="schedule_table"></div>
        <div id="sched_table_container" class="container">
        <table class="table table-bordered" id="sched_table" style="border: 1px solid black; ">
            <tbody style="overflow: scroll; height: 100px;">
            <tr style="border: 2px solid gray; background-color:#bb0000; color: white; font-size: 18px;">
                <th onclick=""><u>Room</u></th>
                <th onclick=""><u>Case Procedures</u></th>
                <th onclick=""><u>Lead Surgeon</u></th>
                <th onclick=""><u>Patient Class</u></th>
                <th onclick=""><u>Start Time</u></th>
                <th onclick=""><u>End Time</u></th>
                @if ($flag == 1)
                    <th onclick="sortTable(7)">Assignment</th>
                @elseif($flag==2)
                    <th>Preference</th>
                @endif
            </tr>


            <?php
                // rowID is used to group the different rows that all occur in the same room together
                $rowID = 0;
            ?>

            @foreach ($schedule_data as $row)

                <?php


                    $case_procedure = $row['case_procedure'];
                    $lead_surgeon=$row['lead_surgeon'];
                    $patient_class=$row['patient_class'];
                    // trim off the extra whitespace
                    $case_procedure = trim($case_procedure);
                    // add a newline character to the last line again so that we know when to end
                    $case_procedure = $case_procedure."\n";

                    $tmp_case_procedure = $case_procedure;

                    $rowSpan = 0;
                    while(strlen($tmp_case_procedure) > 0)
                    {
                        $casePos= stripos($tmp_case_procedure, "\n");
                        $tmp_case_procedure=substr($tmp_case_procedure, $casePos+1);

                        $rowSpan++;
                        $tmp_tmp_procedure= substr($tmp_case_procedure, 0,$casePos);
                        if($tmp_tmp_procedure == '\n' || strlen($tmp_tmp_procedure) == 0){
                            break;
                        }
                    }

                    $isFirstRow = true;
                    while (strlen($case_procedure) > 0) {
                        // used to stripe the table based on the row groupings
                        if($rowID%2){
                            echo '<tr class ="'.$rowID.'" style = "background-color: #F0F0F0">';
                        }
                        else{
                            echo '<tr class ="'.$rowID.'" style = "background-color: #FFFFFF">';
                        }

                        if($isFirstRow)
                        {
                            echo '<td rowspan = "'.$rowSpan.'" align="left">'.$row['room'].'</td>';
                        }

                        $casePos= stripos($case_procedure, "\n");
                        $tmp_procedure= substr($case_procedure, 0,$casePos);
                        if($tmp_procedure == '\n' || strlen($tmp_procedure) == 0){
                            //echo "</tr>";
                            break;
                        }

                        // $tmp_procedure in this format: (start_time-end_time)procedure, procedure, procedure, ...
                        // Get start/end time of the surgery.
                        $timeIndex = strpos($tmp_procedure, ")");
                        $time_duration = substr($tmp_procedure, 1, $timeIndex-1);
                        $connectIndex = strpos($time_duration, "-");
                        $start_time = substr($time_duration, 0, $connectIndex);
                        $end_time = substr($time_duration, $connectIndex+1);
                        $tmp_procedure = substr($tmp_procedure, $timeIndex+1);

                        $leadPos= stripos($lead_surgeon, "\n");
                        $tmp_surgeon= substr($lead_surgeon, 0,$leadPos);
                        $patientPos= stripos($patient_class, "\n");
                        $tmp_patient= substr($patient_class, 0,$patientPos);
                        echo '<td align="left" >';
                            echo '<ul class = "three" style="list-style-type:disc">';
                            // cases/procedures are connected by ','
                            $procedures = explode(",", $tmp_procedure);
                            foreach ($procedures as $tmp_procedure){
                                $tmp_procedure = trim($tmp_procedure);
                                $ep = stripos($tmp_procedure, '[');
                                if ($ep){
                                    echo '<li>'.substr($tmp_procedure, 0, $ep).'</li>';
                                } else {
                                    echo '<li>'.$tmp_procedure.'</li>';
                                }
                            }
                        echo "</td>";
                        echo '<td align="left" >';
                            echo '<ul class = "three" style="list-style-type:none">';
                            while ($tmp_surgeon!=false) {
                                $ep = stripos($tmp_surgeon, '[');
                                echo '<li>'.substr($tmp_surgeon, 0, $ep).'</li>';
                                $ep = stripos(substr($tmp_surgeon, 0), ']');
                                $tmp_surgeon = substr($tmp_surgeon, $ep+1);
                            }
                        echo "</td>";
                        echo '<td align="left" >';
                            echo $tmp_patient;
                        echo "</td>";

                        $case_procedure=substr($case_procedure, $casePos+1);
                        $lead_surgeon=substr($lead_surgeon, $leadPos+1);
                        $patient_class=substr($patient_class, $patientPos+1);

                        echo '<td align="left" >';
                            if (strlen($start_time) < 1){
                                echo "N/A";
                            } else {
                                echo $start_time;
                            }
                        echo "</td>";

                        echo '<td align="left" >';
                            if (strlen($end_time) < 1){
                                echo "N/A";
                            } else {
                                echo $end_time;
                            }
                        echo "</td>";

                        if($isFirstRow)
                        {
                            ?>

                            @if ($flag == 1)
                                @if ($row['resident'] != null)
                                    <td <?php echo 'rowspan = "'.$rowSpan.'"';?> align="left">{{ $row['resident'] }}</td>
                                @else
                                    <td <?php echo 'rowspan = "'.$rowSpan.'"';?> align="left">TBD</td>
                                @endif
                            @elseif($flag==2)
                                <td <?php echo 'rowspan = "'.$rowSpan.'"';?>>
                                    <select class = "PreferenceSelector" name = "RM {{$row['room']}}<br>{{$row['start_time']}}-{{$row['end_time']}}" id = "{{ $row['id'] }}_">
                                        <option selected="selected" value= "">Choose here</option>
                                        <option value= "1">First</option>
                                        <option value= "2">Second</option>
                                        <option value= "3">Third</option>
                                    </select>
                                </td>
                            @endif
                            </tr>

                            <?php
                            $isFirstRow = false;
                        }


                    }

                ?>


            <?php
                // increase the row number now that we are done with that particular grouping by room
                $rowID = $rowID + 1;
            ?>

            @endforeach



        </tbody>
    </table>
    </div>

    <br><br>
        <div id="schedule_footer">
            <div id="preferences" class="row">
                <div class="col-4 col-sm-3 preferences ">
                    <strong>1st</strong>
                    <div id="first" align="center">N/A</div>
                </div>
                <div class="col-4 col-sm-3 preferences ">
                    <strong>2nd</strong>
                    <div id="second" align="center">N/A</div>
                </div>
                <div class="col-4 col-sm-3 preferences ">
                    <strong>3rd</strong>
                    <div id="third" align="center">N/A</div>
                </div>
                <div class="col-12 col-sm-3 preferences btn-group-vertical" id='verticalButtons'>
                    <button type="button" class="btn btn-primary" onclick="clearPreferences()">Clear</button>
                    <button type="button" class="btn btn-primary" name = "submitButton" value="Submit" Onclick="submitPreference();">Submit</button>
                </div>
            </div>
            <div class="float-right" id='horizontalButtons'>
                <button type="button" class="btn btn-primary" onclick="clearPreferences()">Clear Preferences</button>
                <button type="button" class="btn btn-primary" name = "submitButton" value="Submit" Onclick="submitPreference();">Submit</button>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {

                first = $('table select [value=1]:selected');
                second = $('table select [value=2]:selected');
                third = $('table select [value=3]:selected');
                if(first.length > 0){
                    $('#first').html(first.parent().attr('name'));
                    highlightPreference(first.parent())
                }
                if(second.length > 0){
                    $('#second').html(second.parent().attr('name'));
                    highlightPreference(second.parent())
                }
                if(third.length > 0){
                    $('#third').html(third.parent().attr('name'));
                    highlightPreference(third.parent())
                }

                if (window.location.href.indexOf("firstday") > -1){
                    $("#schedule_footer").hide();
                } else {
                    $('#page_footer hr').hide();
                    adjustWidth();
                    adjustHeight();
                    $(window).resize(
                        function() {
                            adjustWidth();
                            adjustHeight();
                     });
                }

                // adjust the width of the footer
                function adjustWidth() {
                    if(window.innerWidth < 576){
                        $("#schedule_footer").width(window.innerWidth);
                        $('#schedule_footer').css('left','0px');
                        $('#verticalButtons').hide();
                        $('#horizontalButtons').show();
                    } else {
                        var parentwidth = $("#schedule_footer").parent().width();
                        $("#schedule_footer").width(parentwidth);
                        $('#schedule_footer').css('left','auto');
                        $('#horizontalButtons').hide();
                        $('#verticalButtons').show();
                    }
                }

                // adjust the height of the schedule table
                function adjustHeight(){
                    $('#sched_table_container').css('height',$('#schedule_footer').offset().top-100)
                }

                // Update information when a new preference is selected
                $("select").on('focus', function () {
                    var selection = $(this);
                    selection.data('previous', selection.val());
                }).on('change', function () {
                    var selection = $(this);
                    var previous = selection.data('previous');
                    selection.data('previous', selection.val());
                    if ($(this).val() == 1) {
                        var id = $(this).attr('id');
                        console.log(id);
                        console.log(id.substring(0, id.length - 1));
                        // Get previous selected first preference
                        // var prevFirst = $('table select[id!='+id+'] [value=1]:selected').parent();
                        var prevFirst = $('div.sked-tape__location select[id!='+id+'] [value=1]:selected').parent();
                        console.log(prevFirst);
                        // Deselect previous selected first preference
                        clearPrevious(prevFirst, previous);
                        // update current selection info
                        // $('#first').html($(this).attr('name'));
                        $('#first').html($(this).parent().attr('title').replace(/\n/g, "<br>"));
                        // Highlight the selected room
                        highlightPreference($(this));
                    }else if ($(this).val() == 2) {
                        var id = $(this).attr('id');
                        console.log(id);
                        console.log(id.substring(0, id.length - 1));
                        // Get previous selected second preference
                        // prevSecond = $('table select[id!='+id+'] [value=2]:selected').parent();
                        var prevSecond = $('div.sked-tape__location select[id!='+id+'] [value=2]:selected').parent();
                        // Deselect previous selected second preference
                        clearPrevious(prevSecond, previous);
                        // update current selection info
                        // $('#second').html($(this).attr('name'));
                        $('#second').html($(this).parent().attr('title').replace(/\n/g, "<br>"));
                        // Highlight the selected room
                        highlightPreference($(this));
                    }else if ($(this).val() == 3) {
                        var id = $(this).attr('id');
                        console.log(id);
                        console.log(id.substring(0, id.length - 1));
                        // Get previous selected third preference
                        // var prevThird = $('table select[id!='+id+'] [value=3]:selected').parent();
                        var prevThird = $('div.sked-tape__location select[id!='+id+'] [value=3]:selected').parent();
                        // Deselect previous selected thrid preference
                        clearPrevious(prevThird, previous);
                        // update current selection info
                        // $('#third').html($(this).attr('name'));
                        $('#third').html($(this).parent().attr('title').replace(/\n/g, "<br>"));
                        // Highlight the selected room
                        highlightPreference($(this));
                    }else if ($(this).val() ==""){
                        clearPrevious($(this), previous);
                    }
                });
            });

            // Deselect previous options, update current selection info, change background color to default color
            function clearPrevious(prevSelected, previous){
                prevSelected.val("");
                if(previous == 1){
                    $('#first').html("N/A");
                } else if(previous == 2){
                    $('#second').html("N/A");
                } else if(previous == 3){
                    $('#third').html("N/A");
                }
                // Change the background color of previous selected room to default
                // var prevClass = prevSelected.parent().parent().attr('class');
                var prevDataId = prevSelected.parent().attr('data-id');
                console.log(prevSelected)
                // if(prevClass%2){
                //     $('.'+prevClass).css('background-color', '#F0F0F0');
                // } else {
                //     $('.'+prevClass).css('background-color', '#FFFFFF');
                // }
                if($('[data-id="'+prevDataId+'"]').is(':odd')){
                    $('[data-id="'+prevDataId+'"]').css('background-color', '#F4F4F4');
                    $('.'+prevDataId).css('background-color', '#F4F4F4');
                } else {
                    $('[data-id="'+prevDataId+'"]').css('background-color', '#FFFFFF');
                    $('.'+prevDataId).css('background-color', '#FFFFFF');
                }
            }

            // highlight the selected room
            function highlightPreference(selected){
                // var selectedClass = selected.parent().parent().attr('class');
                var selectedRoomId = selected.parent().attr('data-id');
                // $('.'+selectedClass).css('background-color', 'rgba(255, 215, 215, 0.65)');
                $('[data-id="'+selectedRoomId+'"]').css('background-color', 'rgba(255, 215, 215, 0.65)');
                $('.'+selectedRoomId).css('background-color', 'rgba(255, 215, 215, 0.65)');
            }

            function clearPreferences(){
                // var first = $('table select [value=1]:selected');
                // var second = $('table select [value=2]:selected');
                // var third = $('table select [value=3]:selected');
                var first = $('#schedule_table select [value=1]:selected');
                var second = $('#schedule_table select [value=2]:selected');
                var third = $('#schedule_table select [value=3]:selected');
                if(first.length > 0){
                    clearPrevious(first.parent(), 1);
                }
                if(second.length > 0){
                    clearPrevious(second.parent(), 2);
                }
                if(third.length > 0){
                    clearPrevious(third.parent(), 3);
                }
            }  


            function sortTable(n) {
                var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                table = document.getElementById("sched_table");
                switching = true;
                dir = "asc";

                while (switching) {
                    switching = false;
                    rows = table.rows;
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("TD")[n];
                        y = rows[i + 1].getElementsByTagName("TD")[n];
                        if (dir == "asc") {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                shouldSwitch= true;
                                break;
                            }
                        } else if (dir == "desc") {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount ++;
                    } else {
                        if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                      }
                    }
                }
            }

            function submitPreference()
            {
                // Store preferences
                var preferencedIDs = ["", "", ""];
                // var first = $('table select [value=1]:selected');
                // var second = $('table select [value=2]:selected');
                // var third = $('table select [value=3]:selected');
                var first = $('#schedule_table select [value=1]:selected');
                var second = $('#schedule_table select [value=2]:selected');
                var third = $('#schedule_table select [value=3]:selected');
                if(first.length > 0){
                    preferencedIDs[0] = first.parent().attr('id');
                }
                if(second.length > 0){
                    preferencedIDs[1] = second.parent().attr('id');
                }
                if(third.length > 0){
                    preferencedIDs[2] = third.parent().attr('id');
                }

                // Check Preferences
                // make sure that all 3 preferences are selected
                if(preferencedIDs[0] != "" && preferencedIDs[1] != "" && preferencedIDs[2] != ""){
                    // send 3 preferences to be stored and processed
                    storePreference(preferencedIDs[0], preferencedIDs[1], preferencedIDs[2]);
                }
                else if (preferencedIDs[0] != "" && preferencedIDs[1] != ""){
                    storePreference(preferencedIDs[0], preferencedIDs[1]);
                }
                else if (preferencedIDs[0] != "" && preferencedIDs[2] != ""){
                    alert("In order to have a third preference, you must have a second");
                }
                else if (preferencedIDs[0] != ""){
                    storePreference(preferencedIDs[0]);
                }
                else{
                    alert("Please select a first preference!");
                }
            }

        </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="{{ asset('js/jquery.skedTape.js') }}"></script>
    <script type="text/javascript">
        // --------------------------- Data --------------------------------
        
        var locations = [];
        var events = [];

        var schedule_data = <?php echo json_encode($schedule_data); ?>;
        schedule_data.forEach(function(schedule){
            // add room and start/end time of each room to locations in this format:
            // var locations = [
            //     {id: 'schedule['id']_', name: 'room name', userData: {'time':'start-end'}},
            //     {id: 'schedule['id']_', name: 'room name', userData: {'time':'start-end' }},
            // ];
            if(schedule['start_time'] != null && schedule['end_time'] != null){
                locations.push({
                    id: schedule['id']+'_', 
                    name: schedule['room'], 
                    userData: {'time': schedule['start_time']+'-'+schedule['end_time']}
                });
            } else {
                locations.push({
                    id: schedule['id']+'_', 
                    name: schedule['room'], 
                    userData: {'time': 'Time: N/A'}
                });
            }

            // get information of each surgery
            var case_procedure = schedule['case_procedure'];
            var lead_surgeon = schedule['lead_surgeon'];
            var patient_class = schedule['patient_class'];
            // trim off the extra whitespace
            case_procedure = case_procedure.trim();
            // add a newline character to the last line again so that we know when to end
            case_procedure = case_procedure + "\n";

            var count = 0;
            while (case_procedure.length > 0) {
                count += 1;
                var casePos = case_procedure.indexOf("\n");
                var tmp_procedure = case_procedure.substring(0, casePos);
                if (tmp_procedure == '\n' || tmp_procedure.length == 0){
                    break;
                }

                // tmp_procedure in this format: (start_time-end_time)procedure, procedure, procedure, ...
                // Get start/end time of the surgery.
                var timeIndex = tmp_procedure.indexOf(")");
                var time_duration = tmp_procedure.substring(1, timeIndex);
                var connectIndex = time_duration.indexOf("-");
                var start_time = time_duration.substring(0, connectIndex);
                var end_time = time_duration.substring(connectIndex+1);
                var tmp_procedure = tmp_procedure.substring(timeIndex+1);

                var leadPos= lead_surgeon.indexOf("\n");
                var tmp_surgeon= lead_surgeon.substring(0, leadPos);
                var patientPos= patient_class.indexOf("\n");
                var tmp_patient= patient_class.substring(0, patientPos);

                var complete_cases = "";
                console.log(tmp_procedure);
                // cases/procedures are connected by ','
                var procedures = tmp_procedure.split(",");
                procedures.forEach(function(tmp_procedure){
                    tmp_procedure = tmp_procedure.trim();
                    var ep = tmp_procedure.indexOf('[');
                    if (ep != -1){
                        complete_cases += tmp_procedure.substring(0, ep);
                    } else {
                        complete_cases += tmp_procedure;
                    }
                    complete_cases += "\n";
                });
                complete_cases = complete_cases.trim();
                console.log(complete_cases);

                var ep = tmp_surgeon.indexOf('[');
                if (ep != -1){
                    tmp_surgeon = tmp_surgeon.substring(0, ep);
                }

                // Add surgery to events in this format:
                // var events = [
                //     {
                //         name: 'room name + number',
                //         location: 'scheduleId',
                //         start: today(startHour, startMinutes),
                //         end: today(endHour, endMinutes),
                //         userData: {
                //             'time': 'startTime-endTime',
                //             'case': 'case1\ncase2\ncase3',
                //             'lead_surgeon': 'surgeon name',
                //             'patient_class': 'patient class',
                //         }
                //     }, {...}
                //]
                if(start_time.length > 0 && end_time.length > 0){
                    events.push({
                        name: schedule['room'] + '-' + count,
                        location: schedule['id']+'_',
                        start: createTime(start_time),
                        end: createTime(end_time),
                        userData: {
                            'time': time_duration,
                            'case': complete_cases,
                            'lead_surgeon': tmp_surgeon,
                            'patient_class': tmp_patient,
                        }
                    })
                } else {
                    events.push({
                        name: schedule['room'] + '-' + count,
                        location: schedule['id']+'_',
                        start: today(4+(count-1)*3, 0),
                        end: today(4+(count-1)*3 + 2, 0),
                        userData: {
                            'time': 'Time N/A',
                            'case': complete_cases,
                            'lead_surgeon': tmp_surgeon,
                            'patient_class': tmp_patient,
                        }
                    })
                }
                
                // Get information of next surgery in the room
                case_procedure = case_procedure.substring(casePos+1);
                lead_surgeon = lead_surgeon.substring(leadPos+1);
                patient_class = patient_class.substring(patientPos+1);
            }
        });
        console.log(locations);
        console.log(events);

        // -------------------------- Helpers ------------------------------
        function today(hours, minutes) {
            var date = new Date();
            date.setHours(hours, minutes, 0, 0);
            return date;
        }
        function yesterday(hours, minutes) {
            var date = today(hours, minutes);
            date.setTime(date.getTime() - 24 * 60 * 60 * 1000);
            return date;
        }
        function tomorrow(hours, minutes) {
            var date = today(hours, minutes);
            date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
            return date;
        }
        function createTime(time){
            var startHour = parseInt(time.substr(0,2));
            var startMinutes  = parseInt(time.substr(3, 2));
            return today(startHour, startMinutes);
        }
        // Set configuration of Sked Tape Timeline
        var sked2Config = {
            caption: 'Rooms',
            start: today(4, 0),
            end: tomorrow(0, 0),
            showEventTime: false,
            showEventDuration: false,
            showDates: false,
            locations: locations,
            events: events.slice(),
            tzOffset: 0,
            sorting: true,
            orderBy: 'name',
        };
        var $sked2 = $.skedTape(sked2Config);
        $sked2.appendTo('#schedule_table').skedTape('render');
        $sked2.skedTape(sked2Config);
    </script>
    @else
        <h2>Error loading table! <br>No schedule of the chosen date can be found.</h2>
    @endif
    

@endsection
