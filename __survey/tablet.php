<?php

require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

$layout = PrivatePage('tablet', 'Tablet');

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}else{
	//Leave('tablets.php');
}

$admin = $db->get_row("SELECT * FROM tablets WHERE id = $id ORDER BY id DESC LIMIT 0,1");

$layout->AddContentById('id', $admin->id);

$layout->AddContentById('firstname', $_SESSION['username']);

if(isset($_POST['delete'])){
	$db->query("DELETE FROM tablets WHERE id = " . $id);
	Leave('tablets.php');
}

if(isset($_POST['submit'])){
	
	$errors = false;
	$values = array();
	$format = array();
	$error_msg = '';
	
	if(isset($_POST['name']) AND $_POST['name'] != ''){
		$layout->AddContentById('name', $_POST['name']);
		$values['name'] = Clean($_POST['name']);
		$format = "%s";
	}else{
		$values['name'] ='';
		$format = "%s";
	}
	
	if(isset($_POST['address']) AND $_POST['address'] != ''){
		$layout->AddContentById('address', $_POST['address']);
		$values['address'] = Clean($_POST['address']);
		$format = "%s";
	}else{
		$values['address'] ='';
		$format = "%s";
	}

	if(isset($_POST['password']) AND $_POST['password'] != ''){
		if($_POST['password'] != $_POST['cpassword']){
			$errors = true;
			$error_msg .= '{{ST:password_not_confirmed}} ';
		}else{
			$values['password'] = encode_password($_POST['password']);
			$format = "%s";
		}
	}
	
	if(!$errors){
		$db->update("tablets", $values, array('id'=>$id), $format);
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
	$layout->AddContentById('address', $admin->address);
}

$layout->RenderViewAndExit();
