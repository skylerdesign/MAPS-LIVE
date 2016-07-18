<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

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

$layout = PrivatePage('appointment', 'Appointment');

$layout->AddContentById('firstname', $_SESSION['username']);
$layout->AddContentById('currentusername', $_SESSION['username']);


if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}else{
	Leave('appointments.php');
}

if(isset($_GET['create']) AND intval($_GET['create']) != 0){
    $survey = intval($_GET['create']);
    $event = $id;
    $patient = intval($_GET['user']);
    $results = "";
    $clinician = "";
    $tablet = "";
    CreateSurvey( $survey, $event, $patient, $results, $clinician, $tablet);

    Leave('appointment.php?id=' . $event );
}

$admin = $db->get_row("SELECT * FROM events WHERE id = $id ORDER BY id DESC LIMIT 0,1");

if(isset($_POST['delete'])){
	$db->query("DELETE FROM events WHERE id = " . $id);
	Leave('appointments.php');
}

$layout->AddContentById('id', $admin->id);

if ($admin->user) {
	// Get user value 
	$patients = $db->get_results("SELECT * FROM survey_admin WHERE id = " . $admin->user);
	if ( $patients ) {
		foreach ( $patients as $patient ) {
			$layout->AddContentById('firstname', $patient->firstname);
			$layout->AddContentById('lastname', $patient->lastname);
			$layout->AddContentById('patient', $patient->name);
		}
	}
}
if ($admin->clinician) {
	// Get clinician value 
	$clins = $db->get_results("SELECT * FROM survey_admin WHERE id = " . $admin->clinician);

	if ( $clins ) {
		foreach ( $clins as $clin ) {
			$layout->AddContentById('clinician', $clin->name);
			$layout->AddContentById('firstname', $clin->firstname);
			$layout->AddContentById('lastname', $clin->lastname);
		}
	}
}

// Get user DOB
$layout->AddContentById('dob', getUserDOB($admin->user));

// Get nowshow info
if (isNoShow($id)==1) {
	$layout->AddContentById('noshow', '<input type="checkbox" name="noshow" value="1" checked>');
	$layout->AddContentById('noshowalert', '<h2 style="color:red;position: absolute;right:20px;top:0;">NO SHOW</h2>');
} else {
	$layout->AddContentById('noshow', '<input type="checkbox" name="noshow" value="1">');
}

// Get Survey results
	$survey_results = $db->get_results("SELECT * FROM  survey_results WHERE  event_id = $id ORDER BY id DESC");
	if ( $survey_results ) {
		$templinks = '';
		foreach ( $survey_results as $survey_result ) {
			// Get title 
			$survey_title = $db->get_row("SELECT * FROM survey_survey WHERE id = $survey_result->survey_id ORDER BY id DESC LIMIT 0,1");
			$templinks .= '<a href="' . $survey_result->id . '" class="btn btn-primary" onclick="return GetAnswersTemp(' . $survey_result->id . ', ' . $survey_result->survey_id . ');">' . $survey_title->title . '</a>&nbsp;&nbsp;';
		}
		$layout->AddContentById('survey_links', $templinks);
	}


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
		$values['title'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['start']) AND $_POST['start'] != ''){
		// 08/31/2015 6:52 AM
		$date = date('m/d/Y H:i', strtotime( $_POST['start'] ) );
		$layout->AddContentById('start', $date );
		$values['start'] = date('Y-m-d H:i', strtotime( $_POST['start'] ) );
		//$values['start'] = Clean($_POST['start']);
		$format[] = "%s";
	}else{
		$values['start'] ='';
		$format[] = "%s";
	}
	
	if(isset($_POST['end']) AND $_POST['end'] != ''){
		// 08/31/2015 6:52 AM
		$date = date('m/d/Y H:i', strtotime( $_POST['end'] ) );
		$layout->AddContentById('end', $date );
		$values['end'] = date('Y-m-d H:i', strtotime( $_POST['end'] ) );
		//$values['start'] = Clean($_POST['start']);
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
	
	if(isset($_POST['noshow']) AND $_POST['noshow'] != ''){
		$layout->AddContentById('noshow', $_POST['noshow']);
		$values['noshow'] = Clean($_POST['noshow']);
		$format[] = "%s";
	}else{
		$values['noshow'] ='';
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
	
	// Get the survey links
	
	// . TABLES_PREFIX . "survey WHERE id = " . $id);
	//$db->query("DELETE FROM " . TABLES_PREFIX . "results WHERE survey_id = " . $id);
	//$db->query("DELETE FROM " . TABLES_PREFIX . "question WHERE survey_id = " . $id);
	//$db->query("DELETE FROM " . TABLES_PREFIX . "choices WHERE survey_id = " . $id);
	//$db->query("DELETE FROM " . TABLES_PREFIX . "answers WHERE survey_id = " . $id);
	
	$layout->AddContentById('link_survey_test', $survey_test);
	
	if(!$errors){
		$db->update("events", $values, array('id'=>$id), $format);
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
	
	$layout->AddContentById('name', $admin->title);
	$layout->AddContentById('start', date('m/d/Y H:i', strtotime( $admin->start ) ));
	$layout->AddContentById('end', date('m/d/Y H:i', strtotime( $admin->end ) ));
	$layout->AddContentById('user', $admin->user);
	$layout->AddContentById('clinician', $admin->clinician);
	
	
}

$layout->RenderViewAndExit();
