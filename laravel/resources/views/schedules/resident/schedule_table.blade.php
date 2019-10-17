@extends('schedules.resident.schedule_basic')

@section('table_generator')
    @if(sizeof($schedule_data)>0)
        <div id="schedule_table"></div>
    <br>
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
                var first = $('#schedule_table select [value=1]:selected');
                var second = $('#schedule_table select [value=2]:selected');
                var third = $('#schedule_table select [value=3]:selected');
                if(first.length > 0){
                    $('#first').html(first.parent().attr('title').replace(/\n/g, "<br>"));
                    highlightPreference(first);
                }
                if(second.length > 0){
                    $('#second').html(second.parent().attr('title').replace(/\n/g, "<br>"));
                    highlightPreference(second);
                }
                if(third.length > 0){
                    $('#third').html(third.parent().attr('title').replace(/\n/g, "<br>"));
                    highlightPreference(third);
                }

                if (window.location.href.indexOf("firstday") > -1){
                    $("#schedule_footer").hide();
                } else {
                    $('#page_footer hr').hide();
                    adjustWidth();
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
                if($('[data-id="'+prevDataId+'"]').is(':even')){
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
                $('[data-id="'+selectedRoomId+'"]').css('background-color', 'rgba(255, 215, 215, 0.65)');
                $('.'+selectedRoomId).css('background-color', 'rgba(255, 215, 215, 0.65)');
            }

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
