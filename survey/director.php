<?php

require 'includes.php';

if(!IsLoggedIn()){
	//Leave('signin.php');
}

if(!AdminCanManageAdmins()){
	if(AdminCanManageSurvey()){
		//Leave('index.php');
	}else{
		//Leave('signin.php');
	}
}


$layout = PrivatePage('director', '{{ST:administrators}}');

$layout->AddContentById('currentusername', $_SESSION['username']);

//$layout->AddContentById('firstname', $_SESSION['username']);

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}else{
	Leave('directors.php');
}

if (getUserRole() == 4) {
	//echo 'You don\'t have permission to edit this record.' . ' <a href="directors.php">Go Back</a>';
	//exit();
	Leave('director-view.php?id=' . $id);
}

if($id == intval($_SESSION['surveyengine_admin_user_id'])){
	echo $tmpl_strings->Get('cant_edit_yourself') . ' <a href="patients.php">'.$tmpl_strings->Get('back').'</a>';
	exit();
}

$admin = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "admin WHERE id = $id ORDER BY id DESC LIMIT 0,1");

if(isset($_POST['delete'])){
	$db->query("DELETE FROM " . TABLES_PREFIX . "admin WHERE id = " . $id);
	Leave('directors.php');
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
	
	if(isset($_POST['clinic']) AND $_POST['clinic'] != ''){
		$layout->AddContentById('clinic', $_POST['clinic']);
		$values['clinic'] = Clean($_POST['clinic']);
		$format[] = "%s";
	}else{
		$values['clinic'] ='';
		$format[] = "%s";
	}
	
	
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
	$layout->AddContentById('clinic', $admin->clinic);
	
	// Get list of clinicians
	$clinics = $db->get_results("SELECT * FROM clinics ORDER BY name ASC");
	if($clinics){
		$temp = '<option value="">- Select Clinic -</option>';
		foreach($clinics as $clinic){
			if ($admin->clinic == $clinic->id) {
				$temp .= '<option value="' . $clinic->id . '" selected>' . $clinic->name . '</option>';
			} else {
				$temp .= '<option value="' . $clinic->id . '">' . $clinic->name . '</option>';
			}
			
		}
	}

	$layout->AddContentById('clinic_select', $temp);
	
		
	if($admin->status == 'banned'){
		$layout->AddContentById('deny_access_state', 'checked="checked"');
	}
	
	
}

// Get list of clinicians
	$clinics = $db->get_results("SELECT * FROM clinics ORDER BY name ASC");
	if($clinics){
		$temp = '<option value="">- Select Clinic -</option>';
		foreach($clinics as $clinic){
			if ($admin->clinic == $clinic->id) {
				$temp .= '<option value="' . $clinic->id . '" selected>' . $clinic->name . '</option>';
			} else {
				$temp .= '<option value="' . $clinic->id . '">' . $clinic->name . '</option>';
			}
			
		}
	}

$layout->RenderViewAndExit();
