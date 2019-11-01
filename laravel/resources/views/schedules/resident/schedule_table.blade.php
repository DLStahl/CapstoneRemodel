@extends('main')

@section('content')
    @include('schedules.resident.schedule_dates')
    <?php
        $url = $_SERVER['REQUEST_URI'];

        //echo $url;

        $urlSplit = explode("/", $url);

        if( sizeof($urlSplit) == 6){
            // No filter option selected
            $room = "null";
            $leadSurgeon = "null";
            $rotation = "null";
            $start_after = "null";
            $end_before = "null";
        }
        else
        {
            // get filter options from Request URI
            $room = str_replace("%20", " ", $urlSplit[7]);
            $leadSurgeon = str_replace("%20", " ", $urlSplit[8]);
            $rotation = $urlSplit[9];
            $timeSplit = explode("_", $urlSplit[10]);
            $start_after = $timeSplit[0];
            $end_before = $timeSplit[1];
        }
    ?><div id="filter">

        <!--room filter-->
        <select id="room">
            <option value="null" selected> - Room - </option>
            @foreach ($filter_options['rooms'] as $roomOption)
                <option value="{{$roomOption}}" > {{$roomOption}}</option>
            @endforeach
        </select>

        <!--lead surgeon filter-->
        <select id="leadSurgeon">
            <option value="null" selected> - Surgeon - </option>
            @foreach ($filter_options['leadSurgeons'] as $leadSurgeonOption)
                <option value="{{$leadSurgeonOption}}" > {{$leadSurgeonOption}}</option>
            @endforeach
        </select>

        <!-- rotation filter -->
        <select id="rotation">
            <option value="null" selected> - Rotation - </option>
            <option value="Basic"> Basic </option>
            <option value="Liver"> Liver </option>
            <option value="Neuro"> Neuro </option>
            <option value="Thoracic"> Thoracic </option>
            <option value="Cardiac"> Cardiac </option>
            <option value="Vascular"> Vascular </option>
        </select>

        <!--start after filter-->
        <select id="start_after">
            <option value="null" selected> - Start After - </option>
            @for($i=0; $i<10; $i++)
                <option value="0{{$i}}:00:00">0{{$i}}:00:00</option>
            @endfor
            @for($i=10; $i<24; $i++)
                <option value="{{$i}}:00:00">{{$i}}:00:00</option>
            @endfor
        </select>

        <!--end before filter-->
        <select id="end_before">
            <option value="null" selected> - End Before - </option>
            @for($i=0; $i<10; $i++)
                <option value="0{{$i}}:00:00">0{{$i}}:00:00</option>
            @endfor
            @for($i=10; $i<24; $i++)
                <option value="{{$i}}:00:00">{{$i}}:00:00</option>
            @endfor
        </select>

        <div class="float-right">
            <button type="button" class="btn btn-primary" onclick="filterUpdate()">Filter</button>
            <button type="button" class="btn btn-primary" onclick="clearFilter()">Clear Filter</button>
        </div>

    </div>

    <br>
    <script type="text/javascript">
        $(document).ready(function() {
            // print filter options in console for test
            var filter_options = <?php echo json_encode($filter_options); ?>;
            console.log(filter_options);

            // Set initial filter options
            var room = "<?php echo $room ?>";
            $('#room').val(room);

            var leadSurgeon = "<?php echo $leadSurgeon ?>";
            $('#leadSurgeon').val(leadSurgeon);

            var rotation = "<?php echo $rotation ?>";
            $('#rotation').val(rotation);

            var start_after = "<?php echo $start_after ?>";
            $('#start_after').val(start_after);

            var end_before = "<?php echo $end_before ?>";
            $('#end_before').val(end_before);

        });
        
        // Update filter
        function filterUpdate()
        {
            var room = document.getElementById("room");
            var leadSurgeon = document.getElementById("leadSurgeon");
            var rotation = document.getElementById("rotation");
            var start_after = document.getElementById("start_after");
            var end_before = document.getElementById("end_before");

            var room_selected = room.options[room.selectedIndex].value;
            var leadSurgeon_selected = leadSurgeon.options[leadSurgeon.selectedIndex].value;
            var rotation_selected = rotation.options[rotation.selectedIndex].value;
            var start_after_selected = start_after.options[start_after.selectedIndex].value;
            var end_before_selected = end_before.options[end_before.selectedIndex].value;

            if (start_after_selected.localeCompare(end_before_selected) >= 0 &&
                start_after_selected.localeCompare("null") != 0 &&
                end_before_selected.localeCompare("null") != 0)
            {
                alert("Invalid selection!");
                return;
            }

            /**
             * Update url.
             */
            var current_url = window.location.href;
            var url = current_url.search('/filter/') > -1 ? current_url.substr(0, current_url.search('/filter/')) : current_url;
            url = url + "/filter/" + room_selected + "/" + leadSurgeon_selected + "/" + rotation_selected + "/" + start_after_selected + "_" + end_before_selected;
            window.location.href = url;
        }

        function clearFilter()
        {
            $('#filter select').val('null');
            var current_url = window.location.href;
            if(window.location.href.indexOf("filter") > -1){
                var url = current_url.search('/filter/') > -1 ? current_url.substr(0, current_url.search('/filter/')) : current_url;
                window.location.href = url;
            }

        }

    </script>

    @if(sizeof($schedule_data)>0)
        <div id="schedule_table"></div>
    <br><br>
        <!-- A block fixed at the bottom of the page. Display information for selected preferences. -->
        <div id="schedule_footer" style="display:none">
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

            <!-- Display buttons horizontally for small screens. -->
            <div class="float-right" id='horizontalButtons'>
                <button type="button" class="btn btn-primary" onclick="clearPreferences()">Clear Preferences</button>
                <button type="button" class="btn btn-primary" name = "submitButton" value="Submit" Onclick="submitPreference();">Submit</button>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                // if current page is not first day, display information of selected information;
                if (window.location.href.indexOf("firstday") == -1){
                    $("#schedule_footer").show();
                    $('#page_footer hr').hide();
                    adjustWidth();
                    // adjust the width of the footer when the window is resized
                    $(window).resize(
                        function() {
                            adjustWidth();
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

                // Update information when a new preference is selected
                $('.sked-tape__location select').on('focus', function () {
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
                        var prevFirst = $('div.sked-tape__location select[id!='+id+'] [value=1]:selected').parent();
                        console.log(prevFirst);
                        // Deselect previous selected first preference
                        clearPrevious(prevFirst, previous);
                        // update current selection info
                        $('#first').html($(this).parent().attr('title').replace(/\n/g, "<br>"));
                        // Highlight the selected room
                        highlightPreference($(this));
                    }else if ($(this).val() == 2) {
                        var id = $(this).attr('id');
                        console.log(id);
                        console.log(id.substring(0, id.length - 1));
                        // Get previous selected second preference
                        var prevSecond = $('div.sked-tape__location select[id!='+id+'] [value=2]:selected').parent();
                        // Deselect previous selected second preference
                        clearPrevious(prevSecond, previous);
                        // update current selection info
                        $('#second').html($(this).parent().attr('title').replace(/\n/g, "<br>"));
                        // Highlight the selected room
                        highlightPreference($(this));
                    }else if ($(this).val() == 3) {
                        var id = $(this).attr('id');
                        console.log(id);
                        console.log(id.substring(0, id.length - 1));
                        // Get previous selected third preference
                        var prevThird = $('div.sked-tape__location select[id!='+id+'] [value=3]:selected').parent();
                        // Deselect previous selected thrid preference
                        clearPrevious(prevThird, previous);
                        // update current selection info
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
                var prevDataId = prevSelected.parent().attr('data-id');
                console.log(prevSelected)
                if($('[data-id="'+prevDataId+'"]').is(':nth-child(even)')){
                    $('[data-id="'+prevDataId+'"]').css('background-color', '#F4F4F4');
                    $('.'+prevDataId).css('background-color', '#F4F4F4');
                } else {
                    $('[data-id="'+prevDataId+'"]').css('background-color', '#FFFFFF');
                    $('.'+prevDataId).css('background-color', '#FFFFFF');
                }
            }

            // highlight the selected room
            function highlightPreference(selected){
                var selectedRoomId = selected.parent().attr('data-id');
                $('[data-id="'+selectedRoomId+'"]').css('background-color', 'rgba(255, 215, 215, 0.65)');
                $('.'+selectedRoomId).css('background-color', 'rgba(255, 215, 215, 0.65)');
            }

            // Clear currently selected preferences on the page
            function clearPreferences(){
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

            // store selected schedule ids in url, then redirects to the milestone and educational objective selection page
            function storePreference(id1, id2, id3)
            {
                // Update url to the selecting milestone/objective page
                var current_url = window.location.href;
                var url = current_url.substr(0, current_url.search('/schedule/'));
                var id1 = id1 || '0_';
                var id2 = id2 || '0_';
                var id3 = id3 || '0_';
                url = url + "/schedule/milestones/" + id1 + id2 + id3 +"/";
                window.location.href = url;
            }

            function submitPreference()
            {
                // Store preferences
                var preferencedIDs = ["", "", ""];
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
    <script src="{{ asset('js/jquery.skedTape.js?v=1.2') }}"></script>
    <script type="text/javascript">
        // --------------------------- Data --------------------------------
        var locations = [];
        var events = [];
        var schedule_data = <?php echo json_encode($schedule_data); ?>;
        console.log(schedule_data);
        schedule_data.forEach(function(schedule){
            // add room and start/end time of each room to locations in this format:
            // var locations = [
            //     {id: 'schedule['id']_', name: 'room name', userData: {'time':'start-end'}},
            //     {id: 'schedule['id']_', name: 'room name', userData: {'time':'start-end'}},
            // ];

            // add rooms that have not null start/end time
            if(schedule['start_time'] != null && schedule['end_time'] != null){
                locations.push({
                    id: schedule['id']+'_', 
                    name: schedule['room'], 
                    userData: {'time': schedule['start_time']+'-'+schedule['end_time']}
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

                // Add surgeries to events in this format:
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

                // add surgeries that have not null start/end time
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

        var minTime = "<?php echo $minTime ?>";
        console.log(minTime);
        var startTime = createTime(minTime);
        
        var maxTime = "<?php echo $maxTime ?>";
        console.log(maxTime);
        var endTime = createTime(maxTime);
        // Set configuration of Sked Tape Timeline
        var sked2Config = {
            caption: 'Rooms',
            start: startTime,
            end: endTime,
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
        <h2>No schedule found.</h2>
    @endif
@endsection('content')