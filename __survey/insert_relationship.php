<?php
require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

if(!AdminCanManageSurvey()){
	if(AdminCanManageAdmins()){
		Leave('admins.php');
	}else{
		Leave('signin.php');
	}
}

	
	$errors = false;
	$values = array();
	$format = array();
	$error_msg = '';
	
	if(isset($_POST['patient_id']) AND $_POST['patient_id'] != ''){
		$values['patient_id'] = Clean($_POST['patient_id']);
		$format[] = "%s";
	}
	if(isset($_POST['relationship_id']) AND $_POST['relationship_id'] != ''){
		$values['relationship_id'] = Clean($_POST['relationship_id']);
		$format[] = "%s";
	}
	
	$db->insert("junction_relationships", $values, $format);
?>