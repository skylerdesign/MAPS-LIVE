<?php

require 'includes.php';

$output = array();
$output['status'] = 1;

if(isset($_GET['id']) AND $_GET['id'] != ''){
	$id = intval($_GET['id']);
}else{
	$output['status'] = 0;
	$output['error'] = $tmpl_strings->Get('error');
	echo json_encode($output);
	exit();
}

// Get all events by user id 	
$events = $db->get_results("SELECT * FROM events WHERE user = " . $id . " ORDER BY id ASC");

$temp = '';
if($events){
	foreach($events as $event){
		
		$event_id =  $event->id;
		$patient_id = $event->user;	
		$survey_id = 100;
		
		//echo "SELECT * FROM " . TABLES_PREFIX . "results WHERE event_id = $event_id AND patient_id = $patient_id AND survey_id = $survey_id ORDER BY id DESC LIMIT 0,1";

		

		$result = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "results WHERE  event_id = " . $event_id . " AND  patient_id = " . $patient_id . " AND  survey_id = " . $survey_id . " ORDER BY  survey_id DESC ");
		
		echo $result;
		
		if($result){
			
			echo 'results';
			
			// Now get thqe questions
			$questions = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "question WHERE survey_id = ". $result->survey_id ." ORDER BY id ASC");
			//$answers = unserialize($result->answers);
			
			
		
			if($questions){
				
				$output['answers'] = '';
				
				foreach($questions as $q){
					$answer = '';
					
					echo "SELECT * FROM " . TABLES_PREFIX . "answers WHERE results_id = " . $result->id . " AND question_id = ".$q->id." ORDER BY id ASC" . '<br>';
					
					$ans = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "answers WHERE results_id = " . $result->id . " AND question_id = ".$q->id." ORDER BY id ASC");
										
					if($ans){
						
						
						if($q->question_type == 'ma'){
							$ma_ans = array();
							foreach($ans as $a){
								$ma_ans[] = $db->get_var("SELECT choice FROM " . TABLES_PREFIX . "choices WHERE id = ".$a->choice_id);
							}
							$answer = implode(", ", $ma_ans);
						}elseif($q->question_type == 'tf'){
							if($ans[0]->answer == 'f'){
								$answer = $tmpl_strings->Get('false');
							}else{
								$answer = $tmpl_strings->Get('true');
							}
						}elseif($q->question_type == 'mp' OR $q->question_type == 'dd'){
							$answer = $db->get_var("SELECT choice FROM " . TABLES_PREFIX . "choices WHERE id = ".$ans[0]->choice_id);
						}else{
							$answer = $ans[0]->answer;	
						}
					} else {
						echo 'No answers';
					}
					$output['answers'] .= $q->question.' '.$answer.'';
				}
				
				
				//echo json_encode($output);
					
			} else {
				// No questions
				$output['status'] = 0;
				$output['error'] = $tmpl_strings->Get('error');
				echo json_encode($output);
				exit();
			}		
		} else {
			// No results
			$output['status'] = 0;
			$output['error'] = $tmpl_strings->Get('error');
			echo json_encode($output);
			exit();
		}
	
	}
} else {
	// No events
	$output['status'] = 0;
	$output['error'] = $tmpl_strings->Get('error');
	echo json_encode($output);
	exit();
}

echo json_encode($output);
exit();
