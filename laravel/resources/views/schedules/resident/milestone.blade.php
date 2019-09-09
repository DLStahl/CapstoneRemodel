@extends('main')

@section('content')
    
	<div id = "Resident Form">
        <h4>Resident Preferences</h4><br>
        <form method="POST" action="../../submit">
		<div class="form-group">
                <h5>Your Preference: Room {{ $room1 }} with {{ $attending1 }}</h5>
                <label>Select your Milestone:</label><br>

                <select name="milestones1" id="milestones1" required>
                        <option disabled selected value> -- Select a Milestone -- </option>
                        <option value="PC1" title="Patient Care 1">PC1 - Pre-anesthetic patient evaluation, assessment, and prep</option>
                        <option value="PC2" title="Patient Care 2">PC2 - Anesthetic plan and conduct</option>
                        <option value="PC3" title="Patient Care 3">PC3 - Peri-procedural pain management</option>
                        <option value="PC4" title="Patient Care 4">PC4 - Management of peri-anesthetic complications</option>
                        <option value="PC5" title="Patient Care 5">PC5 - Crisis management</option>
                        <option value="PC6" title="Patient Care 6">PC6 - Triage and management of the critically-ill patient in non-op setting</option>
                        <option value="PC7" title="Patient Care 7">PC7 - Acute, chronic, and cancer-related pain cs and mgmt</option>
                        <option value="PC8" title="Patient Care 8">PC8 - Technical skills: Airway management</option>
                        <option value="PC9" title="Patient Care 9">PC9 - Technical skills: Use/Interpretation of monitoring+equipment</option>
                        <option value="PC10" title="Patient Care 10">PC10 - Technical skills: Regional anesthesia</option>
                        <option value="MK1" title="Medical Knowledge">MK1 - Knowledge as outlined in the ABA Content Outline</option>
                        <option value="SBP1" title="Systems-based Practice 1">SBP1 - Coordination of patient care within the health care system</option>
                        <option value="SBP2" title="Systems-based Practice 2">SBP2 - Patient safety and quality improvement</option>
                        <option value="PBLI1" title="Practice-based Learning and Improvement 1">PBLI1 - Incorporation of QI and pt safety initiatives into personal practice</option>
                        <option value="PBLI2" title="Practice-based Learning and Improvement 2">PBLI2 - Analysis of practice to identify areas in need of improvement</option>
                        <option value="PBLI3" title="Practice-based Learning and Improvement 3">PBLI3 - Self-directed learning</option>
                        <option value="PBLI4" title="Practice-based Learning and Improvement 4">PBLI4 - Education of patient, families, students, residents, etc</option>
                        <option value="PRO1" title="Professionalism 1">PRO1 - Responsibility to patients, families, and society</option>
                        <option value="PRO2" title="Professionalism 2">PRO2 - Honesty, integrity, and ethical behavior</option>
                        <option value="PRO3" title="Professionalism 3">PRO3 - Commitment to institution, department, and colleagues</option>
                        <option value="PRO4" title="Professionalism 4">PRO4 - Receiving and giving feedback</option>
                        <option value="PRO5" title="Professionalism 5">PRO5 - Responsibility to maintain personal emotional/physical/mental health</option>
                        <option value="ICS1" title="Interpersonal and Communication Skills 1">ICS1 - Communication with patients and families</option>
                        <option value="ICS2" title="Interpersonal and Communication Skills 2">ICS2 - Communication with other professionals</option>
                        <option value="ICS3" title="Interpersonal and Communication Skills 3">ICS3 - Team and leadership skills</option>
                </select>
                    
                <br>
						    
                <label>What is your educational objective for this OR today?</label><br>
                
                <textarea rows="3" name="objectives1" id="objectives1" class="form-control" required></textarea><br>
				
				<h5>Your Preference: Room {{ $room2 }} with {{ $attending2 }}</h5>
                <label>Select your Milestone:</label><br>

                <select name="milestones2" id="milestones2" required>
                        <option disabled selected value> -- Select a Milestone -- </option>
                        <option value="PC1" title="Patient Care 1">PC1 - Pre-anesthetic patient evaluation, assessment, and prep</option>
                        <option value="PC2" title="Patient Care 2">PC2 - Anesthetic plan and conduct</option>
                        <option value="PC3" title="Patient Care 3">PC3 - Peri-procedural pain management</option>
                        <option value="PC4" title="Patient Care 4">PC4 - Management of peri-anesthetic complications</option>
                        <option value="PC5" title="Patient Care 5">PC5 - Crisis management</option>
                        <option value="PC6" title="Patient Care 6">PC6 - Triage and management of the critically-ill patient in non-op setting</option>
                        <option value="PC7" title="Patient Care 7">PC7 - Acute, chronic, and cancer-related pain cs and mgmt</option>
                        <option value="PC8" title="Patient Care 8">PC8 - Technical skills: Airway management</option>
                        <option value="PC9" title="Patient Care 9">PC9 - Technical skills: Use/Interpretation of monitoring+equipment</option>
                        <option value="PC10" title="Patient Care 10">PC10 - Technical skills: Regional anesthesia</option>
                        <option value="MK1" title="Medical Knowledge">MK1 - Knowledge as outlined in the ABA Content Outline</option>
                        <option value="SBP1" title="Systems-based Practice 1">SBP1 - Coordination of patient care within the health care system</option>
                        <option value="SBP2" title="Systems-based Practice 2">SBP2 - Patient safety and quality improvement</option>
                        <option value="PBLI1" title="Practice-based Learning and Improvement 1">PBLI1 - Incorporation of QI and pt safety initiatives into personal practice</option>
                        <option value="PBLI2" title="Practice-based Learning and Improvement 2">PBLI2 - Analysis of practice to identify areas in need of improvement</option>
                        <option value="PBLI3" title="Practice-based Learning and Improvement 3">PBLI3 - Self-directed learning</option>
                        <option value="PBLI4" title="Practice-based Learning and Improvement 4">PBLI4 - Education of patient, families, students, residents, etc</option>
                        <option value="PRO1" title="Professionalism 1">PRO1 - Responsibility to patients, families, and society</option>
                        <option value="PRO2" title="Professionalism 2">PRO2 - Honesty, integrity, and ethical behavior</option>
                        <option value="PRO3" title="Professionalism 3">PRO3 - Commitment to institution, department, and colleagues</option>
                        <option value="PRO4" title="Professionalism 4">PRO4 - Receiving and giving feedback</option>
                        <option value="PRO5" title="Professionalism 5">PRO5 - Responsibility to maintain personal emotional/physical/mental health</option>
                        <option value="ICS1" title="Interpersonal and Communication Skills 1">ICS1 - Communication with patients and families</option>
                        <option value="ICS2" title="Interpersonal and Communication Skills 2">ICS2 - Communication with other professionals</option>
                        <option value="ICS3" title="Interpersonal and Communication Skills 3">ICS3 - Team and leadership skills</option>
                </select>
                    
                <br>
						    
                <label>What is your educational objective for this OR today?</label><br>
                
                <textarea rows="3" name="objectives2" id="objectives2" class="form-control" required></textarea><br>
				
				<h5>Your Preference: Room {{ $room3 }} with {{ $attending3 }}</h5>
                <label>Select your Milestone:</label><br>

                <select name="milestones3" id="milestones3" required>
                        <option disabled selected value> -- Select a Milestone -- </option>
                        <option value="PC1" title="Patient Care 1">PC1 - Pre-anesthetic patient evaluation, assessment, and prep</option>
                        <option value="PC2" title="Patient Care 2">PC2 - Anesthetic plan and conduct</option>
                        <option value="PC3" title="Patient Care 3">PC3 - Peri-procedural pain management</option>
                        <option value="PC4" title="Patient Care 4">PC4 - Management of peri-anesthetic complications</option>
                        <option value="PC5" title="Patient Care 5">PC5 - Crisis management</option>
                        <option value="PC6" title="Patient Care 6">PC6 - Triage and management of the critically-ill patient in non-op setting</option>
                        <option value="PC7" title="Patient Care 7">PC7 - Acute, chronic, and cancer-related pain cs and mgmt</option>
                        <option value="PC8" title="Patient Care 8">PC8 - Technical skills: Airway management</option>
                        <option value="PC9" title="Patient Care 9">PC9 - Technical skills: Use/Interpretation of monitoring+equipment</option>
                        <option value="PC10" title="Patient Care 10">PC10 - Technical skills: Regional anesthesia</option>
                        <option value="MK1" title="Medical Knowledge">MK1 - Knowledge as outlined in the ABA Content Outline</option>
                        <option value="SBP1" title="Systems-based Practice 1">SBP1 - Coordination of patient care within the health care system</option>
                        <option value="SBP2" title="Systems-based Practice 2">SBP2 - Patient safety and quality improvement</option>
                        <option value="PBLI1" title="Practice-based Learning and Improvement 1">PBLI1 - Incorporation of QI and pt safety initiatives into personal practice</option>
                        <option value="PBLI2" title="Practice-based Learning and Improvement 2">PBLI2 - Analysis of practice to identify areas in need of improvement</option>
                        <option value="PBLI3" title="Practice-based Learning and Improvement 3">PBLI3 - Self-directed learning</option>
                        <option value="PBLI4" title="Practice-based Learning and Improvement 4">PBLI4 - Education of patient, families, students, residents, etc</option>
                        <option value="PRO1" title="Professionalism 1">PRO1 - Responsibility to patients, families, and society</option>
                        <option value="PRO2" title="Professionalism 2">PRO2 - Honesty, integrity, and ethical behavior</option>
                        <option value="PRO3" title="Professionalism 3">PRO3 - Commitment to institution, department, and colleagues</option>
                        <option value="PRO4" title="Professionalism 4">PRO4 - Receiving and giving feedback</option>
                        <option value="PRO5" title="Professionalism 5">PRO5 - Responsibility to maintain personal emotional/physical/mental health</option>
                        <option value="ICS1" title="Interpersonal and Communication Skills 1">ICS1 - Communication with patients and families</option>
                        <option value="ICS2" title="Interpersonal and Communication Skills 2">ICS2 - Communication with other professionals</option>
                        <option value="ICS3" title="Interpersonal and Communication Skills 3">ICS3 - Team and leadership skills</option>
                </select>
                    
                <br>
						    
                <label>What is your educational objective for this OR today?</label><br>

                <input type="hidden" name="schedule_id" value="{{ $id }}">
                
                <textarea rows="3" name="objectives3" id="objectives3" class="form-control" required></textarea><br>
                  
                <br>
                
                <input align = "right" type='submit' class='btn btn-md btn-success'>
		</div>
        </form>
	</div>
	
@endsection