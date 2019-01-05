@extends('main')

@section('content')
    <h3>Date: {{ $mon }}/{{ $day }}/{{ $year }}</h3>
    <br><br><br>
    <br><br><br>
    
	<div id="filter">
        <h5>Filter Schedule</h5> 
        
        <br>
        
    	<!-- <!--category filter
    	<select id="categories">
            <option value="none">-Category-</option>
            <option value="none">-Admin needs to set-</option>
        </select> -->
        
    	<!--//doctor filter-->
    	<select id="doctors">
            <option value="null">-Doctors-</option>
    	</select>
    	<!--//start after filter-->
    	<select id="start_after">
    	    <option value="null">-Start After-</option>
            @for($i=0; $i<10; $i++)
                <option value="0{{$i}}:00:00">0{{$i}}:00:00</option>
            @endfor
            @for($i=10; $i<24; $i++)
                <option value="{{$i}}:00:00">{{$i}}:00:00</option>
            @endfor
    	</select>

    	<!--//end before filter-->
    	<select id="end_before">
            <option value="null">-End Before-</option>
            @for($i=0; $i<10; $i++)
                <option value="0{{$i}}:00:00">0{{$i}}:00:00</option>
            @endfor
            @for($i=10; $i<24; $i++)
                <option value="{{$i}}:00:00">{{$i}}:00:00</option>
            @endfor
        </select>
        
    	<button type="button" class="btn btn-primary" onclick="filterUpdate()">Filter</button>
	</div>

	<br><br>

	<div class = "container">
	    @yield('table_generator')
    </div>
    
    <script type="text/javascript">
        
        var tab, docList;
        var docs = [];
        tab = document.getElementById("sched_table");
        docList = document.getElementById("doctors");
        
        // Get all unique doctor names and sort by alphabetical order
        for(var i = 0; i < tab.rows.length; i++){
            if(i != 0){
                var element = tab.rows[i].cells[3].id;
                if(!docs.includes(element)){
                    docs.push(element);
                    //console.log(element);
                }
            }
        }
        docs.sort();
       
        // Create options for select
        for(var i = 0; i < docs.length; i++){
            var option = document.createElement("option");
            option.value = docs[i];
            option.text = docs[i].substr(0, docs[i].indexOf('['));
            docList.appendChild(option);
        }

        // Update filter
        function filterUpdate()
        {
            var doctor = document.getElementById("doctors");
            var start_after = document.getElementById("start_after");
            var end_before = document.getElementById("end_before");

            var doctor_selected = doctor.options[doctor.selectedIndex].value;
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
            url = url + "/filter/" + doctor_selected + "_" + start_after_selected + "_" + end_before_selected;
            window.location.href = url;
        }
        
    </script>

    <!--Preference JS -->
    <script type="text/javascript">
        function storePreference(id)
        {
            var current_url = window.location.href;
            var url = current_url.substr(0, current_url.search('/schedule/'));
            
            if (current_url.includes('secondday')) {
                if (document.getElementById(id+"_").value.localeCompare("default")==0) {
                    alert("Please Select a preference!");
                    url=url+"/schedule/secondday/";
                }
                else{
                    url = url + "/schedule/secondday/" + id + "/" + document.getElementById(id+"_").value;

                }

            } else {
                if (document.getElementById(id+"_").value.localeCompare("default")==0){
                    alert("Please Select a preference!");
                    url=url+"/schedule/thirdday/";

                }
                else{

                    url = url + "/schedule/thirdday/" + id + "/" + document.getElementById(id+"_").value;
                }
            }
            window.location.href = url;
        }
    </script>
    
@endsection('content')