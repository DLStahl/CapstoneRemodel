@extends('schedules.resident.schedule_basic')
@section('table_generator')
	@if(!is_null($schedule_data))
		<table class="table table-striped table-bordered" id="sched_table">
			<tr>
				<th onclick="sortTable(1)">No.</th>
				<th onclick="sortTable(1)">Room</th>
				<th >Case Procedures</th>
				<th >Lead Surgeon</th>
				<th >Patient Class</th>
				<!-- <th onclick="sortTable(2)">Case Procedures</th>
				<th onclick="sortTable(3)">Lead Surgeon</th>
				<th onclick="sortTable(4)">Patient Class</th> -->
				<th onclick="sortTable(5)">Start Time</th>
				<th onclick="sortTable(6)">End Time</th>
				@if ($flag == 1)
					<th onclick="sortTable(7)">Assignment</th>
				@elseif($flag==2)
					<th>Preference</th>
					<th>Submit</th>
				@endif
			</tr>

			
			<?php 
				$count = 1;				
			?>
			@foreach ($schedule_data as $row)
				<tr>
				<?php			

					echo '<td>'.$count.'</td>';
					echo '<td align="left">'.$row['room'].'</td>';


					//changing;
					echo '<td class="inTable" colspan="3">';
					echo '<table frame = "void" >';
					$case_procedure = $row['case_procedure'];
					$lead_surgeon=$row['lead_surgeon'];
					$patient_class=$row['patient_class'];


					while ($case_procedure != false) {
						echo '<tr>';
							$pos= stripos($case_procedure, "\n");
							$tmp_procedure= substr($case_procedure, 0,$pos);
							$pos= stripos($lead_surgeon, "\n");
							$tmp_surgeon= substr($lead_surgeon, 0,$pos);
							$pos= stripos($patient_class, "\n");
							$tmp_patient= substr($patient_class, 0,$pos);

							echo '<td align="left" width = "36.9%">';
								echo '<ul class = "three" style="list-style-type:none">';
								while ($tmp_procedure!=false) {
									$ep = stripos($tmp_procedure, '[');
									echo '<li>'.substr($tmp_procedure, 0, $ep).'</li>';
									$ep = stripos(substr($tmp_procedure, 0), ']');
									$tmp_procedure = substr($tmp_procedure, $ep+1);
								}
							echo "</td>";

							echo '<td align="left" width = "32.71%">';
								echo '<ul class = "three" style="list-style-type:none">';
								while ($tmp_surgeon!=false) {
									$ep = stripos($tmp_surgeon, '[');
									echo '<li>'.substr($tmp_surgeon, 0, $ep).'</li>';
									$ep = stripos(substr($tmp_surgeon, 0), ']');
									$tmp_surgeon = substr($tmp_surgeon, $ep+1);
								}
							echo "</td>";

							echo '<td align="left" width = "31.59%">';
								echo $tmp_patient;
							echo "</td>";
						
						echo '</tr>';

						$case_procedure=substr($case_procedure, stripos($case_procedure, "\n")+1);
						$lead_surgeon=substr($lead_surgeon, stripos($lead_surgeon, "\n")+1);
						$patient_class=substr($patient_class, stripos($patient_class, "\n")+1);

						
					}
					

					echo '</tr>';
					echo '</table>';
					echo '</td>';


					echo '<td align="left">'.$row['start_time'].'</td>';
					echo '<td align="left">'.$row['end_time'].'</td>';
					$count = $count + 1;
				?>
				@if ($flag == 1)
					@if ($row['resident'] != null)
						<td align="left">{{ $row['resident'] }}</td>
					@else
						<td align="left">TBD</td>
					@endif
				@elseif($flag==2)
					<td>
						<select class = "PreferenceSelector" id = "{{ $row['id'] }}_">
							<option disabled selected="selected" value= "default">Choose here</option>
							<option value= "1">First</option>
							<option value= "2">Second</option>
							<option value= "3">Third</option>
						</select>
					</td>
					<td>
							<input align = "center" type="button" value="Select" id="{{ $row['id'] }}" class='btn btn-md btn-success' onclick="storePreference(this.id);">						
					</td>
				@endif
				</tr>
			@endforeach



		</table>
		<script>
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = document.getElementById("sched_table");
			  switching = true;
			  dir = "asc"; 
			  
			  while (switching) {
				switching = false;
				rows = table.getElementsByTagName("TR");
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
	@else
		<h2>Error loading table!</h2>
	@endif

@endsection