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

$layout = PrivatePage('add-clinic', '{{ST:administrators}}');
$layout->AddContentById('currentusername', $_SESSION['username']);

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}else{
	$page = 1;
}
$rows = ROWS_PER_PAGE;


if(isset($_POST['submit'])){
	
	$errors = false;
	$values = array();
	$format = array();
	$error_msg = '';
	
	if(isset($_POST['name']) AND $_POST['name'] != ''){
		$layout->AddContentById('name', $_POST['name']);
		$values['name'] = Clean($_POST['name']);
		$format[] = "%s";
	}
	if(isset($_POST['address1']) AND $_POST['address1'] != ''){
		$layout->AddContentById('address1', $_POST['address1']);
		$values['address1'] = Clean($_POST['address1']);
		$format[] = "%s";
	}
	
	if(isset($_POST['address2']) AND $_POST['address2'] != ''){
		$layout->AddContentById('address2', $_POST['address2']);
		$values['address2'] = Clean($_POST['address2']);
		$format[] = "%s";
	}
	
	if(isset($_POST['city']) AND $_POST['city'] != ''){
		$layout->AddContentById('city', $_POST['city']);
		$values['city'] = Clean($_POST['city']);
		$format[] = "%s";
	}
	
	if(isset($_POST['state']) AND $_POST['state'] != ''){
		$layout->AddContentById('state', $_POST['state']);
		$values['state'] = Clean($_POST['state']);
		$format[] = "%s";
	}
	
	if(isset($_POST['zip']) AND $_POST['zip'] != ''){
		$layout->AddContentById('zip', $_POST['zip']);
		$values['zip'] = Clean($_POST['zip']);
		$format[] = "%s";
	}
	
	if(isset($_POST['phone']) AND $_POST['phone'] != ''){
		$layout->AddContentById('phone', $_POST['phone']);
		$values['phone'] = Clean($_POST['phone']);
		$format[] = "%s";
	}

	
	if(!$errors){
		
		if($db->insert("clinics", $values, $format)){
			$layout->AddContentById('alert', $layout->GetContent('alert'));
			$layout->AddContentById('alert_nature', ' alert-success');
			$layout->AddContentById('alert_heading', '{{ST:success}}!');
			$layout->AddContentById('alert_message', '{{ST:the_clinic_has_been_saved}}');
		}else{
			$layout->AddContentById('alert', $layout->GetContent('alert'));
			$layout->AddContentById('alert_nature', ' alert-danger');
			$layout->AddContentById('alert_heading', '{{ST:error}}!');
			$layout->AddContentById('alert_message', '{{ST:unknow_error_try_again}}');
		}
	}else{
		$layout->AddContentById('alert', $layout->GetContent('alert'));
		$layout->AddContentById('alert_nature', ' alert-danger');
		$layout->AddContentById('alert_heading', '{{ST:error}}!');
		$layout->AddContentById('alert_message', $error_msg);
	}
}

$layout->AddContentById('firstname', $_SESSION['username']);

$offset = ($page - 1) * $rows;
$admins = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin ORDER BY id DESC LIMIT $offset, $rows");
$number_of_records = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin" ));
$number_of_pages = ceil( $number_of_records / $rows );

$rows_html = '';
if($admins){
	foreach($admins as $admin){
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('admins-rows');
		$row_layout->AddContentById('id', $admin->id);
		$row_layout->AddContentById('name', $admin->name);
		$row_layout->AddContentById('firstname', $admin->firstname);
		$row_layout->AddContentById('lastname', $admin->lastname);
		$row_layout->AddContentById('role', $admin->role);
		$row_layout->AddContentById('email', $admin->email);
		$row_layout->AddContentById('prefix', $admin->prefix);
		$row_layout->AddContentById('dob', $admin->dob);
		
		$permissions = unserialize($admin->permissions);
		
		$perm_html = '';
		if(is_array($permissions)){
			if($permissions['can_manage_surveys'] == 'y' AND $permissions['can_manage_admins'] == 'y'){
				$perm_html .= '{{ST:surveys_and_admins}}';
			}elseif($permissions['can_manage_surveys'] == 'y'){
				$perm_html .= '{{ST:surveys_only}}';
			}elseif($permissions['can_manage_admins'] == 'y'){
				$perm_html .= '{{ST:admins_only}}';
			}
		}
		$row_layout->AddContentById('permissions', $perm_html);
		
		$rows_html .= $row_layout-> ReturnView();
	}
	
	if($number_of_records>$rows){
		$pagination = Paginate('admins.php', $page, $number_of_pages, false, 3);
		$layout->AddContentById('pagination', $pagination);
	}
	
}else{
	$rows_html = '<tr><td colspan="3">{{ST:no_items}}</td></tr>';
}



$layout->AddContentById('rows', $rows_html);



$layout->RenderViewAndExit();
