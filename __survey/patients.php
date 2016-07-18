<?php

require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

if(!AdminCanManageSurvey()){
	if(AdminCanManageAdmins()){
		//Leave('admins.php');
	}else{
	//	Leave('signin.php');
	}
}

$id = null;
$layout = PrivatePage('patients', '{{ST:patients_title}}');
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

$patients = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 3 ORDER BY id DESC LIMIT $offset, $rows");

$number_of_records = count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE role = 3" ));

$number_of_pages = ceil( $number_of_records / $rows );

$rows_html = '';

if($patients){
	foreach($patients as $patient){
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('patient-rows');
		$row_layout->AddContentById('id', $patient->id);
		$row_layout->AddContentById('name', $patient->name);
		$row_layout->AddContentById('firstname', $patient->firstname);
		$row_layout->AddContentById('lastname', $patient->lastname);
		
		
		
		// Clinician lookup
		$clinicians = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "admin WHERE ID=" . $patient->clinician );
		foreach($clinicians as $clinician){ 
			if ($patient->clinician) {
				$row_layout->AddContentById('clinician', $clinician->firstname . ', ' . $clinician->lastname);
			} else {
				$row_layout->AddContentById('clinician', 'No clinician specified');
			}

		}
		
				
		
		
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
