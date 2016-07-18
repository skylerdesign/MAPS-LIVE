<?php

require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

if(!AdminCanManageAdmins()){
	if(AdminCanManageSurvey()){
		Leave('index.php');
	}else{
		Leave('signin.php');
	}
}

$layout = PrivatePage('appointment-new', 'Appointments');


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
		$values['title'] = Clean($_POST['name']);
		$format[] = "%s";
	}else{
		$values['name'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['start']) AND $_POST['start'] != ''){
		$layout->AddContentById('start', $_POST['start']);
		$values['start'] = date('Y-m-d H:i', strtotime( $_POST['start'] ) );
		$format[] = "%s";
	}else{
		$values['start'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['end']) AND $_POST['end'] != ''){
		$layout->AddContentById('end', $_POST['end']);
		$values['end'] = date('Y-m-d H:i', strtotime( $_POST['end'] ) );
		$format[] = "%s";
	}else{
		$values['end'] ='';
		$format[] = "%s";
	}
		
	if(isset($_POST['user']) AND $_POST['user'] != ''){
		$layout->AddContentById('user', $_POST['user']);
		$values['user'] = Clean($_POST['user']);
		$format[] = "%s";
	}else{
		$values['user'] ='';
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
	
	if(!$errors){
		
		if($db->insert("events", $values, $format)){
			
			$layout->AddContentById('alert', $layout->GetContent('alert'));
			$layout->AddContentById('alert_nature', ' alert-success');
			$layout->AddContentById('alert_heading', '{{ST:success}}!');
			$layout->AddContentById('alert_message', '{{ST:the_admin_has_been_saved}}');
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


$offset = ($page - 1) * $rows;
$admins = $db->get_results("SELECT * FROM events ORDER BY id DESC LIMIT $offset, $rows");
$number_of_records = count($db->get_results("SELECT * FROM events" ));
$number_of_pages = ceil( $number_of_records / $rows );

$rows_html = '';
if($admins){
	foreach($admins as $admin){
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('admins-rows');
		$row_layout->AddContentById('id', $admin->id);
		$row_layout->AddContentById('name', $admin->title);
		$row_layout->AddContentById('end', $admin->end);
		$row_layout->AddContentById('start', $admin->start);
		$row_layout->AddContentById('user', $admin->user);
		$row_layout->AddContentById('clinician', $admin->clinician);
		
		$rows_html .= $row_layout-> ReturnView();
	}
	
	if($number_of_records>$rows){
		$pagination = Paginate('appointments.php', $page, $number_of_pages, false, 3);
		$layout->AddContentById('pagination', $pagination);
	}
	
}else{
	$rows_html = '<tr><td colspan="3">{{ST:no_items}}</td></tr>';
}



$layout->AddContentById('rows', $rows_html);



$layout->RenderViewAndExit();
