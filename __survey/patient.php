<?php

require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

if(!AdminCanManageAdmins()){
	if(AdminCanManageSurvey()){
		//Leave('index.php');
	}else{
		//Leave('signin.php');
	}
}



$layout = PrivatePage('patient', '{{ST:administrators}}');

$layout->AddContentById('currentusername', $_SESSION['username']);

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}else{
	Leave('patients.php');
}

if($id == intval($_SESSION['surveyengine_admin_user_id'])){
	echo $tmpl_strings->Get('cant_edit_yourself') . ' <a href="patients.php">'.$tmpl_strings->Get('back').'</a>';
	exit();
}

$admin = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "admin WHERE id = $id ORDER BY id DESC LIMIT 0,1");

if(isset($_POST['delete'])){
	$db->query("DELETE FROM " . TABLES_PREFIX . "admin WHERE id = " . $id);
	Leave('patients.php');
}

$layout->AddContentById('id', $admin->id);

if(isset($_POST['submit'])){
	
	$errors = false;
	$values = array();
	$format = array();
	$error_msg = '';
	
	if(isset($_POST['email']) AND $_POST['email'] != ''){
		$layout->AddContentById('email', $_POST['email']);
		$check_email = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "admin WHERE email = '" . Clean($_POST['email']) . "' ORDER BY id DESC LIMIT 0,1");
		if($check_email AND intval($check_email->id) != $id){
			$errors = true;
			$error_msg .=  '{{ST:email_already_in_use}} ';
		}elseif(!ValidateEmail($_POST['email'])){
			$errors = true;
			$error_msg .=  '{{ST:email_is_invalid}} ';
		}else{
			$values['email'] = Clean($_POST['email']);
			$format[] = "%s";
		}
	}else{
		$errors = true;
		$error_msg .= '{{ST:email_required}} ';
	}
	
	if(isset($_POST['name']) AND $_POST['name'] != ''){
		$layout->AddContentById('name', $_POST['name']);
		$values['name'] = Clean($_POST['name']);
		$format[] = "%s";
	}else{
		$values['name'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['firstname']) AND $_POST['firstname'] != ''){
		$layout->AddContentById('firstname', $_POST['firstname']);
		$values['firstname'] = Clean($_POST['firstname']);
		$format[] = "%s";
	}else{
		$values['firstname'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['lastname']) AND $_POST['lastname'] != ''){
		$layout->AddContentById('lastname', $_POST['lastname']);
		$values['lastname'] = Clean($_POST['lastname']);
		$format[] = "%s";
	}else{
		$values['lastname'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['clinician']) AND $_POST['clinician'] != ''){
		$layout->AddContentById('clinician', $_POST['clinician']);
		$values['clinician'] = Clean($_POST['clinician']);
		$format[] = "%s";
	}else{
		$values['clinician'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['role']) AND $_POST['role'] != ''){
		$layout->AddContentById('role', $_POST['role']);
		$values['role'] = Clean($_POST['role']);
		$format[] = "%s";
	}else{
		//$values['role'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['address1']) AND $_POST['address1'] != ''){
		$layout->AddContentById('address1', $_POST['address1']);
		$values['address1'] = Clean($_POST['address1']);
		$format[] = "%s";
	}else{
		$values['address1'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['address2']) AND $_POST['address2'] != ''){
		$layout->AddContentById('address1', $_POST['address2']);
		$values['address2'] = Clean($_POST['address2']);
		$format[] = "%s";
	}else{
		$values['address2'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['city']) AND $_POST['city'] != ''){
		$layout->AddContentById('city', $_POST['city']);
		$values['city'] = Clean($_POST['city']);
		$format[] = "%s";
	}else{
		$values['city'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['state']) AND $_POST['state'] != ''){
		$layout->AddContentById('state', $_POST['state']);
		$values['state'] = Clean($_POST['state']);
		$format[] = "%s";
	}else{
		$values['state'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['zip']) AND $_POST['zip'] != ''){
		$layout->AddContentById('zip', $_POST['zip']);
		$values['zip'] = Clean($_POST['zip']);
		$format[] = "%s";
	}else{
		$values['zip'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['dob']) AND $_POST['dob'] != ''){
		$layout->AddContentById('dob', $_POST['dob']);
		$values['dob'] = Clean($_POST['dob']);
		$format[] = "%s";
	}else{
		$values['dob'] ='';
		$format[] = "%s";
	}

	/*
	$new_perms = array('can_manage_surveys'=>'n', 'can_manage_admins'=>'n');
	if(!isset($_POST['can_manage_survey']) AND !isset($_POST['can_manage_admins'])){
		$errors = true;
		$error_msg .= '{{ST:atleast_one_permission_required}} ';
	}else{
		if(isset($_POST['can_manage_survey'])){
			$new_perms['can_manage_surveys'] = 'y';
			$layout->AddContentById('can_manage_survey_state', 'checked="checked"');
		}
		if(isset($_POST['can_manage_admins'])){
			$new_perms['can_manage_admins'] = 'y';
			$layout->AddContentById('can_manage_admins_state', 'checked="checked"');
		}
	
	}
	*/
	$values['permissions'] = serialize($new_perms);
	$format[] = "%s";
	
	if(isset($_POST['deny_access'])){
		$values['status'] = 'banned';
		$format[] = "%s";
		$layout->AddContentById('deny_access_state', 'checked="checked"');
	}else{
		$values['status'] = 'active';
		$format[] = "%s";
	}
	
	if(!$errors){
		$db->update(TABLES_PREFIX . "admin", $values, array('id'=>$id), $format);
		$layout->AddContentById('alert', $layout->GetContent('alert'));
		$layout->AddContentById('alert_nature', ' alert-success');
		$layout->AddContentById('alert_heading', '{{ST:success}}!');
		$layout->AddContentById('alert_message', '{{ST:the_item_has_been_saved}}');
	}else{
		$layout->AddContentById('alert', $layout->GetContent('alert'));
		$layout->AddContentById('alert_nature', ' alert-danger');
		$layout->AddContentById('alert_heading', '{{ST:error}}!');
		$layout->AddContentById('alert_message', $error_msg);
	}
}else{
	
	$layout->AddContentById('name', $admin->name);
	$layout->AddContentById('firstname', $admin->firstname);
	$layout->AddContentById('lastname', $admin->lastname);
	$layout->AddContentById('role', $admin->role);
	$layout->AddContentById('email', $admin->email);
	$layout->AddContentById('dob', $admin->dob);
	$layout->AddContentById('address1', $admin->address1);
	$layout->AddContentById('address2', $admin->address2);
	$layout->AddContentById('city', $admin->city);
	$layout->AddContentById('state', $admin->state);
	$layout->AddContentById('zip', $admin->zip);
	
	// Get list of clinicians
	$clinicians = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 2 ORDER BY lastname ASC");
	$temp = '';
	//if($clinicians){
		$temp = '<option>- Please select --</option>';
		
		foreach($clinicians as $clinician){
			if ($admin->clinician == $clinician->id) {
				$temp .= '<option value="' . $clinician->id . '" selected>' . $clinician->lastname . ', ' . $clinician->firstname . '</option>';
			} else {
				$temp .= '<option value="' . $clinician->id . '">' . $clinician->lastname . ', ' . $clinician->firstname . '</option>';
			}
			
		}
	//}
	
	$layout->AddContentById('clinician_select', $temp);
	$layout->AddContentById('clinician', $admin->clinician);
	
	// Get lists of relations
	$relations = $db->get_results("SELECT * FROM junction_relationships WHERE patient_id = " . $id . " ORDER BY id ASC");
	
	if($relations){
		$links = '';
		foreach($relations as $relation){
			// Get 
			$record_id = $relation->id;
			$relation_id = $relation->relationship_id;
			$patient_id = $relation->patient_id;
			// Now look up stuff
			$name = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "admin WHERE id = $relation_id ORDER BY lastname ASC LIMIT 0,1");
			
			//$links = "SELECT * FROM " . TABLES_PREFIX . "admin WHERE id = $relation_id ORDER BY lastname ASC LIMIT 0,1";		
			$links .= '<span class="link"><a href="/survey/patient.php?id=' . $relation_id . '">' . $name->lastname . ', ' . $name->firstname . '</a> <a href="patient.php?id=14" class="delete" data-delete="' . $record_id . '"><span class="glyphicon lyphicon glyphicon-remove red" style="font-size: 12px;margin-left:5px;"></span></a></span>';
		}
	}
	$layout->AddContentById('relations', $links);
	
	
		
	if($admin->status == 'banned'){
		$layout->AddContentById('deny_access_state', 'checked="checked"');
	}
	
	$permissions = unserialize($admin->permissions);
	if(is_array($permissions)){
		if($permissions['can_manage_surveys'] == 'y'){
			$layout->AddContentById('can_manage_survey_state', 'checked="checked"');
		}
		if($permissions['can_manage_admins'] == 'y'){
			$layout->AddContentById('can_manage_admins_state', 'checked="checked"');
		}
	}
}

$layout->RenderViewAndExit();
