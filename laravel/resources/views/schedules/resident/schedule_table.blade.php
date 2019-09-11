@extends('schedules.resident.schedule_basic')

@section('table_generator')
	@if(!is_null($schedule_data))
		<table class="table table-bordered" id="sched_table" style="border: 1px solid black;">
			<tr style="border: 2px solid gray; background-color:#bb0000; color: white; font-size: 18px">
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
							echo '<tr id ="'.$rowID.'" style = "background-color: #F0F0F0">';
						}
						else{
							echo '<tr id ="'.$rowID.'">';
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
						$leadPos= stripos($lead_surgeon, "\n");
						$tmp_surgeon= substr($lead_surgeon, 0,$leadPos);
						$patientPos= stripos($patient_class, "\n");
						$tmp_patient= substr($patient_class, 0,$patientPos);

						echo '<td align="left" >';
							echo '<ul class = "three" style="list-style-type:disc">';
							while ($tmp_procedure!=false) {
								$ep = stripos($tmp_procedure, '[');
                                $tmp_procedure = str_replace(',','', $tmp_procedure);
								echo '<li>'.substr($tmp_procedure, 0, $ep).'</li>';
								$ep = stripos(substr($tmp_procedure, 0), ']');
								$tmp_procedure = substr($tmp_procedure, $ep+1);
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
												
						if($isFirstRow)
						{
							echo '<td rowspan = "'.$rowSpan.'" align="left">'.$row['start_time'].'</td>';
							echo '<td rowspan = "'.$rowSpan.'" align="left">'.$row['end_time'].'</td>';
							
						}
						
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
									<form method="post" action="#" name = "preferences">
										<select class = "PreferenceSelector" name = "pref" id = "{{ $row['id'] }}_" onchange=savePreference(this)>
											<option disabled selected="selected" value= "default">Choose here</option>
											<option value= "1">First</option>
											<option value= "2">Second</option>
											<option value= "3">Third</option>
										</select>
									</form>
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
		
			


		</table>
		
		<div class="float-right">
				<input align = "center" name = "submitButton" type="button" value="Submit" class='btn btn-md btn-success' Onclick="checkPreference();">
		</div>
		
		
		<script>
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
		</script>
		
		<script>
			
			// global array to keep track of the 3 preferences
			var preferencedIDs = ["", "", ""]; 
		
			function savePreference(pref){
				// get the preference number from the select box
				var preferenceNumber = pref.options[pref.selectedIndex].getAttribute('value');
				// get the id of the schedule data being selected
				var schedule_id = pref.getAttribute('id'); 
				
				// loop through saved preferences to check for duplicates
				for(var i=0; i<3; i++)
				{
					if(preferencedIDs[i] === schedule_id)
					{
						preferencedIDs[i] = ""; 
					}
				}
				
				// set the preferenced data
				preferencedIDs[preferenceNumber-1] = schedule_id; 
												
			}
			
			function checkPreference()
			{
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
	@else
		<h2>Error loading table!</h2>
	@endif

@endsection