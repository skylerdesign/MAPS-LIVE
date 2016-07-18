<?php

require 'includes.php';

if(!IsLoggedIn()){
	Leave('signin.php');
}

if(!AdminCanManageSurvey()){
	if(AdminCanManageAdmins()){
		//Leave('admins.php');
	}else{
		//Leave('signin.php');
	}
}

$id = null;
$layout = PrivatePage('clinics', 'Clinics');
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

$clinics = $db->get_results("SELECT * FROM clinics ORDER BY id DESC LIMIT $offset, $rows");

$number_of_records = count($db->get_results("SELECT * FROM clinics" ));

$number_of_pages = ceil( $number_of_records / $rows );

$rows_html = '';

if($clinics){
	foreach($clinics as $clinic){
		$row_layout = new Layout('html/','str/');
		$row_layout->SetContentView('clinic-rows');
		$row_layout->AddContentById('id', $clinic->id);
		$row_layout->AddContentById('name', $clinic->name);
		
		$row_layout->AddContentById('clinic', $clinic->clinic);
		$row_layout->AddContentById('phone', $clinic->phone);
		
		
		$address = $clinic->address1;
		if ($clininc->address2 != '' ) {
			$address .= '<br> ' . $clinic->address2;
		}
		$address .= '<br>' . $clinic->city . ', ' . $clinic->state . ' ' . $clinic->zip;
		if ($clinic->phone != '' ) {
			$address .= '<br> ' . $clinic->phone;
		}
		
		$row_layout->AddContentById('address', $address);
		
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
