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
$layout = PrivatePage('analysis-by-gender', '{{ST:all_surveys}}');

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

if(isset($_GET['gender'])){
	$gender = $_GET['gender'];
	$layout->AddContentById('gender', 'GENDER: ' . $gender);
}

// Populate Gender picklist
$layout->AddContentById('dropdown-gender', getGenderPicklist());


// Get All Baseline Surveys By Gender
switch ($gender) {
    case 'male':
        $$choice_id = 1;
        break;
    case 'Female':
        $choice_id = 2;
        break;
    case 'Transgender (male to female)':
        $choice_id = 3;
        break;
    case 'Transgender (female to male)':
    	$choice_id = 4;
    	break;

    default:
        '';
} 
$baseline_results_by_gender = $db->get_results("SELECT * FROM survey_answers WHERE survey_id = 2 AND question_id = 1 AND choice_id = " . $gender );

if ( $baseline_results_by_gender ) {
	// Now look up each value
	foreach ( $baseline_results_by_gender as $baseline_result ) {
		// Get Answers
	}
	$layout->AddContentById('baseline_result', $templinks);
}


 
$layout->AddContentById('questions',  getSurveyQuestions(4, 1));
$layout->AddContentById('dropdown-questions', getQuestionsPicklist(4));
$layout->AddContentById('rows', $rows_html);
$layout->RenderViewAndExit();
