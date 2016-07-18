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

$layout = PrivatePage('clinic', 'Update Clinic');

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}else{
	Leave('clinics.php');
}

$admin = $db->get_row("SELECT * FROM clinics WHERE id = $id ORDER BY id DESC LIMIT 0,1");

if(isset($_POST['delete'])){
	$db->query("DELETE FROM clinics WHERE id = " . $id);
	Leave('clinics.php');
}

$layout->AddContentById('id', $admin->id);

if(isset($_POST['submit'])){
	
	$errors = false;
	$values = array();
	$format = array();
	$error_msg = '';
	
	if(isset($_POST['name']) AND $_POST['name'] != ''){
		$layout->AddContentById('name', $_POST['name']);
		$values['name'] = Clean($_POST['name']);
		$format[] = "%s";
	}else{
		$values['name'] ='';
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
	
	if(isset($_POST['phone']) AND $_POST['phone'] != ''){
		$layout->AddContentById('phone', $_POST['phone']);
		$values['phone'] = Clean($_POST['phone']);
		$format[] = "%s";
	}else{
		$values['phone'] ='';
		$format[] = "%s";
	}

	if(!$errors){
		$db->update("clinics", $values, array('id'=>$id), $format);
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
	$layout->AddContentById('address1', $admin->address1);
	$layout->AddContentById('address2', $admin->address2);
	$layout->AddContentById('city', $admin->city);
	$layout->AddContentById('state', $admin->state);
	$layout->AddContentById('zip', $admin->zip);
	$layout->AddContentById('phone', $admin->phone);
	
}

$layout->RenderViewAndExit();
