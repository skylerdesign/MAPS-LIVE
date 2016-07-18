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
	
// List of events
 $json = array();

 // Query that retrieves events
$sql = $db->get_results("SELECT * FROM events ORDER BY id ");
//$sql = "SELECT * FROM events ORDER BY id";

$out = array();

foreach($sql as $row) {
	
	$id = $row->id;
	//echo $id;
	
	$title = $row->title;
	$url = $row->url;
	$start = strtotime($row->start) . '000';
	$end = strtotime($row->end) . '000';
	
    $out[] = array(
        'id' => $id,
        'title' => $title,
        'url' => $url,
        'start' => $start,
        'end' => $end
    );
}

echo json_encode(array('success' => 1, 'result' => $out));

?>