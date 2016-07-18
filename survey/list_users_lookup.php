<?php 	

require 'includes.php';


// Get patients
if ($_GET['q']) {
	$patients = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE id=" . $_GET['q'] . " ORDER BY lastname ASC");
	$myArrayString = '$arr = array(';
	
	if($patients){
		
		foreach($patients as $patient){
				
			$id = $patient->id;
			$name = $patient->name;
			$firstname = $patient->firstname;
			$lastname = $patient->lastname;
						
			$myArrayString .=  'array(
		                "firstname" => "' .  $firstname . '",
		                "lastname" => "' .  $lastname . '",
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

echo json_encode($arr);
}
?>