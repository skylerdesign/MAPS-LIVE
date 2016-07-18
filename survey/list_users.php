<?php 	

require 'includes.php';


// Get patients
	$patients = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role=3 ORDER BY lastname ASC");
	$myArrayString = '$arr = array(';
	
	if($patients){
		
		foreach($patients as $patient){
				
			$id = $patient->id;
			$name = $patient->name;
			$firstname = $patient->firstname;
			$lastname = $patient->lastname;
			$dob = stripslashes($patient->dob);
						
			$myArrayString .=  'array(
		                "firstname" => "' .  $firstname . '",
		                "lastname" => "' .  $lastname . '",
		                "dob" => "' . $dob . '",
		                "id" => "' . $id . '"
		        ),';
				
		}
		
	} 
	
	// Trim the last comma
	$myArrayString = rtrim($myArrayString, ',');
	$myArrayString .= ');';


//echo $myArrayString;

// Now make the array
eval($myArrayString);	

//echo json_encode($arr);
echo str_replace('\/','/',json_encode($arr));
?>