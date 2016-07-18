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

$id = null;
$layout = PrivatePage('analysis', '{{ST:all_surveys}}');

$layout->AddContentById('currentusername', $_SESSION['username']);

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}else{
	$page = 1;
}
$rows = ROWS_PER_PAGE;

if(isset($_GET['message']) AND $_GET['message'] != ''){
	
	if($_GET['message'] == 'deleted'){
		$layout->AddContentById('alert', $layout->GetContent('alert'));
		$layout->AddContentById('alert_nature', ' alert-success');
		$layout->AddContentById('alert_heading', '{{ST:success}}!');
		$layout->AddContentById('alert_message', '{{ST:the_item_has_been_deleted}}');
	}

    if($_GET['message'] == 'duplicated'){
        $layout->AddContentById('alert', $layout->GetContent('alert'));
        $layout->AddContentById('alert_nature', ' alert-success');
        $layout->AddContentById('alert_heading', '{{ST:success}}!');
        $layout->AddContentById('alert_message', '{{ST:the_survey_has_been_duplicated}}');
    }
}

$offset = ($page - 1) * $rows;

$layout->AddContentById('firstname', $_SESSION['username']);

// Populate Gender picklist
$layout->AddContentById('dropdown-gender', getGenderPicklist());

// Populate Bar Type picklist
$layout->AddContentById('dropdown-charttype', getChartTypePicklist());


$quizs = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "survey ORDER BY id DESC LIMIT $offset, $rows");
$number_of_records = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "survey" ));


// Get total number of patients
$number_of_patients = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 3 ORDER BY id"));
$layout->AddContentById('number_of_patients', $number_of_patients);
// Get total number of clinicians
$number_of_clinicians = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 2 ORDER BY id"));
$layout->AddContentById('number_of_clinicians', $number_of_clinicians);
// Get total number of staff
$number_of_staff = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 4 ORDER BY id"));
$layout->AddContentById('number_of_staff', $number_of_staff);
///Number of directors
$num_directors = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 1" ));
$layout->AddContentById('num_directors', $num_directors);
// Get total number of clinics
$number_of_clinics = count($db->get_results("SELECT * FROM clinics" ));
$layout->AddContentById('number_of_clinics', $number_of_clinics);
// Get total number of users
$num_users = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin ORDER BY id"));
$layout->AddContentById('num_users', $num_users);

// Events
$num_events = count($db->get_results("SELECT * FROM events ORDER BY id "));
$layout->AddContentById('num_events', $num_events);


$number_of_pages = ceil( $number_of_records / $rows );

$rows_html = '';
if($quizs){
	foreach($quizs as $quiz){
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('home-rows');
		$row_layout->AddContentById('id', $quiz->id);
		$row_layout->AddContentById('name', $quiz->title);
		$row_layout->AddContentById('description', TrimText($quiz->description, 50));
		
		$rows_html .= $row_layout-> ReturnView();
	}
	
	if($number_of_records>$rows){
		if(isset($_GET['id'])){
			$pagination = Paginate('index.php?id=' . $id, $page, $number_of_pages, true, 3);
		}else{
			$pagination = Paginate('index.php', $page, $number_of_pages, false, 3);
		}
		$layout->AddContentById('pagination', $pagination);
	}
	
}else{
	$rows_html = '<tr><td colspan="3">{{ST:no_items}}</td></tr>';
}

// PATIENTS DROPDOWN
$patients = $db->get_results("SELECT * FROM survey_admin WHERE role = 3 ORDER BY id DESC LIMIT $offset, $rows");
$rows_html = '';
if ( $patients ) {
	foreach ( $patients as $patient ) {
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('dropdown-patients');
		$row_layout->AddContentById('id', $patient->id);
		$row_layout->AddContentById('name', $patient->name);
		$row_layout->AddContentById('firstname', $patient->firstname);
		$row_layout->AddContentById('lastname', $patient->lastname);
		
		$rows_html .= $row_layout-> ReturnView();
	}
} else {
	$rows_html = '<tr><td colspan="3">{{ST:no_items}}</td></tr>';
}

//$layout->AddContentById('dropdown-patients', $rows_html);
$layout->AddContentById('dropdown-patients', getPatientPicklist());

// Get Baseline Survey results
if(isset($_GET['id']) AND $_GET['id'] != ''){
	// Lookup by patient
	$baseline_results = $db->get_results("SELECT * FROM  survey_results WHERE survey_id=4 AND patient_id = $id ORDER BY id DESC");
} else {
	// Lookup all patients
	$baseline_results = $db->get_results("SELECT * FROM  survey_results WHERE survey_id=4 AND ORDER BY id DESC");
	//$baseline_results = $db->get_results("SELECT * FROM  survey_results WHERE survey_id=4 ORDER BY id, patient_id DESC");
}


if ( $baseline_results ) {
	$templinks = '';
	foreach ( $baseline_results as $baseline_result ) {
		// Get title 
		$survey_title = $db->get_row("SELECT * FROM survey_survey WHERE id = $survey_result->survey_id ORDER BY id DESC LIMIT 0,1");
		$templinks .= '<a href="' . $survey_result->id . '" class="btn btn-primary" onclick="return GetAnswersTemp(' . $baseline_result->id . ', ' . $baseline_result->survey_id . ');">' . $baseline_result->title . '</a>&nbsp;&nbsp;';
	}
	$layout->AddContentById('baseline_result', $templinks);
}
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
 
$layout->AddContentById('questions',  getSurveyQuestions(4));
$layout->AddContentById('dropdown-questions', getQuestionsPicklist(4));
$layout->AddContentById('dropdown-substance', getSubstancePicklist(4));


$layout->AddContentById('rows', $rows_html);



$layout->RenderViewAndExit();
