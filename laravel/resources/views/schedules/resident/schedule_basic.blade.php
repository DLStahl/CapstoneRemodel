@extends('main')

@section('content')
    <h3>Date: {{ $mon }}/{{ $day }}/{{ $year }}</h3>

	<?php 
	
		$url = $_SERVER['REQUEST_URI'];
		
		//echo $url;
		
		$urlSplit = explode("/", $url);
		
		if( sizeof($urlSplit) == 6){
			$room = "null";
			$leadSurgeon = "null";
			$patientClass ="null";
			$start_after = "null";
			$end_before = "null";
		}
		else
		{
			$room = str_replace("%20", " ", $urlSplit[7]);
			$leadSurgeon = str_replace("%20", " ", $urlSplit[8]);
			$patientClass =str_replace("%20", " ", $urlSplit[9]);
			$timeSplit = explode("_", $urlSplit[10]);
			$start_after = $timeSplit[0];
			$end_before = $timeSplit[1]; 
		}
		
		if($room == "null"){
			$room = "-Room-";
		}
		if($leadSurgeon == 'null'){
			$leadSurgeon = "-Lead Surgeon-";
		}
		if($patientClass == 'null'){
			$patientClass = "-Patient Class-";
		}
		if($start_after == 'null'){
			$start_after = "-Start After-";
		}
		if($end_before == 'null')
		{
			$end_before = "-End Before-";
		}
	
	?>
    
	<div id="filter">        
        
		<!--//patient class filter-->
    	<select id="room">
            <option value="null" label ="{{ $room }}"></option>
    	</select>
		
		<!--//patient class filter-->
    	<select id="leadSurgeon">
            <option value="null" label ="{{ $leadSurgeon }}"></option>
    	</select>
		
    	<!--//patient class filter-->
    	<select id="patientClasses">
            <option value="null" label ="{{ $patientClass }}"></option>
    	</select>
		
    	<!--//start after filter-->
    	<select id="start_after">
    	    <option value="null" label ="{{ $start_after }}"></option>
            @for($i=0; $i<10; $i++)
                <option value="0{{$i}}:00:00">0{{$i}}:00:00</option>
            @endfor
            @for($i=10; $i<24; $i++)
                <option value="{{$i}}:00:00">{{$i}}:00:00</option>
            @endfor
    	</select>

    	<!--//end before filter-->
    	<select id="end_before">
            <option value="null" label ="{{ $end_before }}"></option>
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
	<br> 
	
	<div class="float-right">
		<button type="button" class="btn btn-primary" id = "{{$year}}-{{$mon}}-{{$day}}" onclick="clearPreferences(this.id)">Clear Preferences</button>
		<button type="button" class="btn btn-primary" name = "submitButton" value="Submit" Onclick="checkPreference();">Submit</button>
	</div>

	<br><br>

	<div class = "container">
	    @yield('table_generator')
    </div>

	
	    <script type="text/javascript">
        
        var tab, docList;
		var rooms = [];
		var leadSurgeons = [];
        var patientClasses = [];
        tab = document.getElementById("sched_table");
		roomList = document.getElementById("room");
		leadSurgeonList = document.getElementById("leadSurgeon");
		patientClassList = document.getElementById("patientClasses");
        
        
        // Get all unique patient class names and sort by alphabetical order
		var rowHeadersIndicies = []; 
		
		for(var i = 0; i < tab.rows.length; i++){
            if(i != 0){
                
				var room = (tab.rows[i].cells[0]);
                if(room.rowSpan > 0){
                    rooms.push(room.innerHTML);
                    //console.log(element);
					rowHeadersIndicies.push(i)
					i = i+room.rowSpan-1; 
					 
                }
			}
		}
		
        for(var i = 0; i < tab.rows.length; i++){
            if(i != 0){
                
				if(rowHeadersIndicies.includes(i)){
					var leadSurgeon = (tab.rows[i].cells[2]).innerText.trim();
				}
				else
				{
					var leadSurgeon = (tab.rows[i].cells[1]).innerText.trim();
				}
                if(!leadSurgeons.includes(leadSurgeon)){
                    leadSurgeons.push(leadSurgeon);
                    //console.log(leadSurgeons);
                }
				
				if(rowHeadersIndicies.includes(i)){
					var patientClass = (tab.rows[i].cells[3].innerHTML).trim();
				}
				else
				{
					var patientClass = (tab.rows[i].cells[2].innerHTML).trim();
				}
                if(!patientClasses.includes(patientClass)){
                    patientClasses.push(patientClass);
                    //console.log(element);
                }
            }
        }
		rooms.sort();
		leadSurgeons.sort(); 
        patientClasses.sort();
       
        // Create options for select
		
		for(var i = 0; i < rooms.length; i++){
            var option = document.createElement("option");
            option.value = rooms[i];
            option.text = rooms[i];
            roomList.appendChild(option);
        }
		
		for(var i = 0; i < leadSurgeons.length; i++){
            var option = document.createElement("option");
            option.value = leadSurgeons[i];
            option.text = leadSurgeons[i];
            leadSurgeonList.appendChild(option);
        }
		
        for(var i = 0; i < patientClasses.length; i++){
            var option = document.createElement("option");
            option.value = patientClasses[i];
            option.text = patientClasses[i];
            patientClassList.appendChild(option);
        }

        // Update filter
        function filterUpdate()
        {
            var room = document.getElementById("room");
			var leadSurgeon = document.getElementById("leadSurgeon");
			var patient_class = document.getElementById("patientClasses");
            var start_after = document.getElementById("start_after");
            var end_before = document.getElementById("end_before");

			var room_selected = room.options[room.selectedIndex].value;
			var leadSurgeon_selected = leadSurgeon.options[leadSurgeon.selectedIndex].value;
            var patient_class_selected = patient_class.options[patient_class.selectedIndex].value;
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
            url = url + "/filter/" + room_selected + "/" + leadSurgeon_selected + "/" + patient_class_selected + "/" + start_after_selected + "_" + end_before_selected;
            window.location.href = url;
        }
		
		function clearFilter()
		{
			var current_url = window.location.href;
			if(window.location.href.indexOf("filter") > -1){
				var url = current_url.search('/filter/') > -1 ? current_url.substr(0, current_url.search('/filter/')) : current_url;
				window.location.href = url;
			}
			else
			{
				
			}
           
		}
        
    </script>

    <!--Preference JS -->
    <script type="text/javascript">
        function storePreference(id1, id2 = false, id3 = false)
        {
            var current_url = window.location.href;
            var url = current_url.substr(0, current_url.search('/schedule/'));
            if (current_url.includes('secondday')) {
                    url = url + "/schedule/secondday/" + id1 + id2 + id3 +"/";
            } else {
                url = url + "/schedule/thirdday/" + id1 + id2 + id3 +"/";
			}
            window.location.href = url;
        }
		
		function clearPreferences(date){
			var current_url = window.location.href;
            var url = current_url.substr(0, current_url.search('/schedule/'));
            if (current_url.includes('secondday')) {
                    url = url + "/schedule/secondday/preferences/clear/"+ date;
            } else {
                url = url + "/schedule/thirdday/preferences/clear/"+ date;
			}
            window.location.href = url;
		}
    </script>
    
@endsection('content')
