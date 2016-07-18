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

if(isset($_GET['duplicate']) AND intval($_GET['duplicate']) != 0){
    DuplicateSurvey(intval($_GET['duplicate']));
    Leave('index.php?message=duplicated');
}

$id = null;
$layout = PrivatePage('setup', '{{ST:all_surveys}}');

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



$layout->AddContentById('rows', $rows_html);



$layout->RenderViewAndExit();
