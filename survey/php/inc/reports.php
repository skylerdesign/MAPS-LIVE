<?php
function getQuestionsPicklist($id) {

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	// Get list of questions
	$questions = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "question WHERE survey_id = " . $id . " ORDER BY order_by ASC LIMIT 14");

	$temp = '';
	if($questions){
		$temp = '<option value="" selected>All Outcomes</option>';

		foreach($questions as $question){
			if ($_GET['question'] == $question->id) {
				$temp .= '<option value="' . $question->id . '" selected>' . $question->order_by . '. ' . $question->question . '</option>';
			} else {
				$temp .= '<option value="' . $question->id . '">' . $question->order_by . '. ' . $question->question . '</option>';
			}
		}
	}
	return $temp;
}

function countBaselineSurveys($survey_id = 2, $gender_id = 0, $substance_id = 0) {

	// Get a count of all Baseline surveys for comparison purposes

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	$result = $db->get_var("SELECT COUNT(*) FROM survey_results WHERE survey_id = " . $survey_id . " ORDER BY id DESC");

	return $result;

}

function getSurveyBaselineAnswers($survey_id, $patient_id, $question_id, $gender_id, $substance_id) {

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	if ($patient_id == 0 ) {

		// No patient
		$results = $db->get_results("SELECT id FROM survey_results WHERE survey_id = " . $survey_id . " ORDER BY id DESC");

		$getgender = '';
		if ( $gender_id = 0 ) {
			$getgender = ' AND ques';
		}
		// SELECT * FROM `survey_answers` WHERE `results_id` = '151' AND `question_id` = 1


		if ( $results ) {
			$temp = '';
			$calculator = array();
			foreach ( $results as $result ) {
				// Get survey ID
				$id = $result->id;
				// Now get answer
				$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id DESC LIMIT 1");
				$answer = $answers->answer;
				$option = $answers->choice_id;
				if ($option != NULL) {
					// Need to look up option
					$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
					$answer = $options->choice;
				}

				// Add to calculator
				$calculator[] = $answer;
				$sum = array_sum ( $calculator );
				$baselineCount = countBaselineSurveys();
				$answer = $sum / $baselineCount;

			}


		} else {
			$answer = 0;
		}

		return $answer;


	} else {
		// Patient
		$result = $db->get_row("SELECT id FROM survey_results WHERE survey_id = " . $survey_id . " AND patient_id = " . $patient_id . " ORDER BY id DESC");

		if ( $result ) {
			// Get survey ID
			$id = $result->id;
			// Now get answer
			$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id DESC LIMIT 1");
			$answer = $answers->answer;
			$option = $answers->choice_id;
			if ($option != NULL) {
				// Need to look up option
				$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
				$answer = $options->choice;
			}
		} else {
			$answer = 0;
		}

		return $answer;
	}



}


function getSurveyFollowupAnswers($survey_id, $patient_id = 0, $question_id, $gender_id = 0, $substance_id = 0) {

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	if ($patient_id == 0 ) {

		$followupQuestions = array(
			0 => 295,
			1 => 92,
			2 => 93,
			3 => 94,
			4 => 95,
			5 => 96,
			6 => 97,
			7 => 98,
			8 => 99,
			9 => 100,
			10 => 101,
			11 => 102,
			12 => 103,
			13 => 104
			/*14 => 310,
			15 => 311,
			16 => 312,
			17 => 313,
			18 => 314,
			19 => 315,
			20 => 316,
			21 => 317,
			22 => 318,
			23 => 319*/
		);

		$answersArray = array();

		// Loop through each question and get all values
		foreach ( $followupQuestions as $question ) {

			$answers = $db->get_results("SELECT * FROM survey_answers WHERE question_id = " . $question_id . " ORDER BY id ASC");
			$answersCount = count($answers);
			//$dates = array();
			$temp = '';
			$calculator = array();
			$dates = array();
			//$answersArray = array();

			foreach ($answers as $answer) {
				// Retrieve the survey id for each answer
				$survey_id = $answer->results_id;
				// Now get the date
				$results = $db->get_row("SELECT * FROM survey_results WHERE id = " . $survey_id . " ORDER BY id ASC");

				// Get survey ID
				$id = $results->id;
				//date_taken
				$date = date_create($results->date_taken);
				$date = date_format($date, 'm/d/Y');


				// Now get answer
				$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id DESC");


				$answersCount = $db->get_var("SELECT COUNT(*) FROM survey_answers WHERE question_id = " . $question_id . " ORDER BY id DESC");

				$answer = $answers->answer;
				$option = $answers->choice_id;
				if ($option != NULL) {
					// Need to look up option
					$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
					$answer = $options->choice;
				}

				// Update arrays
				$answersArray[$question_id][$date][] = $answer;

			}

			// loop through and create array with dates
			foreach ($answersArray as $key1 => $start) {
			    foreach ($start as $key2 => $result) { // result
				    $date = $key2;
				    $calc  = array();
			        foreach ($result as $key3 => $content) {
				        $calc[] = $content;
			        }
			        $answer = array_sum($calc) / count($calc);
			        // Construct chart data
			        $temp .= ", {
							        y: '" . $date . "',
							        a: '" . round(($answer/7) * 100) . "'
						        }";
			    }
			}


			return $temp;

		}


		// No patient
		$results = $db->get_results("SELECT * FROM survey_results WHERE survey_id = " . $survey_id . " ORDER BY id ASC");

		if ( $results ) {
			$temp = '';
			$calculator = array();


			// Now get the answers
			$answers = $db->get_row("SELECT * FROM survey_answers WHERE question_id = " . $question_id . " ORDER BY id DESC");

		}

	} else {

		$results = $db->get_results("SELECT * FROM survey_results WHERE survey_id = " . $survey_id . " AND patient_id = " . $patient_id . " ORDER BY id ASC");


		if ( $results ) {
			$temp = '';
			$calculator = array();

			// Loop through each survey to retrieve the answers
			foreach ( $results as $result ) {
				// Get date
				$date = date_create($result->date_taken);
				$date = date_format($date, 'm/d/Y');

				// Get survey ID
				$id = $result->id;
				// Now get answer
				$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id DESC");


				$answersCount = $db->get_var("SELECT COUNT(*) FROM survey_answers WHERE question_id = " . $question_id . " ORDER BY id DESC");

				//echo $answersCount;

				//SELECT * FROM `survey_answers` WHERE `results_id` = 142 AND `question_id` = 295

				$answer = $answers->answer;
				$option = $answers->choice_id;
				if ($option != NULL) {
					// Need to look up option
					$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
					$answer = $options->choice;
				}

				$temp .= ", {
					        y: '" . $date . "',
					        a: '" . round(($answer/7) * 100) . "'
				        }";


				$x = $x + 1;
			}

			return $temp;
		}

		if ( $results ) {
			// Get survey ID
			$id = $result->id;
			// Now get answer
			$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id ASC");
			$answer = $answers->answer;
			$option = $answers->choice_id;
			if ($option != NULL) {
				// Need to look up option
				$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
				$answer = $options->choice;
			}


		}

		return $answer;

	}
}

function getSurveyFollowupAnswersLine($survey_id, $patient_id = 0, $question_id) {

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	if ($patient_id == 0 ) {

		$results = $db->get_results("SELECT * FROM survey_results WHERE survey_id = " . $survey_id . " ORDER BY id ASC");

	} else {

		$results = $db->get_results("SELECT * FROM survey_results WHERE survey_id = " . $survey_id . " AND patient_id = " . $patient_id . " ORDER BY id ASC");
	}

	if ( $results ) {
		$temp = '';
		foreach ( $results as $result ) {
			// Get date
			$date = date_create($result->date_taken);
			$date = date_format($date, 'm/d/Y');

			// Get survey ID
			$id = $result->id;
			// Now get answer
			$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id DESC");

			//SELECT * FROM `survey_answers` WHERE `results_id` = 142 AND `question_id` = 295

			$answer = $answers->answer;
			$option = $answers->choice_id;
			if ($option != NULL) {
				// Need to look up option
				$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
				$answer = $options->choice;
			}

			// Date conversion to YYYY-MM-DD
			$newdate = date("Y-m-d", strtotime($date));

			$temp .= ", {
				        month: '" . $newdate . "',
				        average: " . round(($answer/7) * 100) . ",
				        label: '" . round(($answer/7) * 100) . "%'
			        }";

			$x = $x + 1;
		}
		return $temp;
	}

	if ( $results ) {
		// Get survey ID
		$id = $result->id;
		// Now get answer
		$answers = $db->get_row("SELECT * FROM survey_answers WHERE results_id = " . $id . " AND question_id = " . $question_id . " ORDER BY id ASC");
		$answer = $answers->answer;
		$option = $answers->choice_id;
		if ($option != NULL) {
			// Need to look up option
			$options = $db->get_row("SELECT * FROM survey_choices WHERE question_id = " . $question_id . " AND id = " . $option );
			$answer = $options->choice;
		}
	}

	return $answer;
}


function getSurveyQuestions($id, $isBaseline=0) {

	if(!IsLoggedIn()){
		return false;
	}

	$db = new Db;

	$single = $_GET['question'];

	// Get list of questions
	if ($single) {
		$questions = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "question WHERE survey_id = " . $id . " AND id = " . $single . " ORDER BY order_by ASC LIMIT 14");
	} else {
		$questions = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "question WHERE survey_id = " . $id . " ORDER BY order_by ASC LIMIT 14");
	}

	$temp = '';

	// First get baseline values.  These are the baseline questions mapped to the follow up questions.  Unfortunately this is manual
	$baseline = array(
		0 => 27,
		1 => 28,
		2 => 29,
		3 => 31,
		4 => 32,
		5 => 39,
		6 => 36,
		7 => 30,
		8 => 82,
		9 => 85,
		10 => 40,
		11 => 41,
		12 => 42,
		13 => 43
		/*14 => 300,
		15 => 301,
		16 => 302,
		17 => 303,
		18 => 304,
		19 => 305,
		20 => 306,
		21 => 307,
		22 => 308,
		23 => 309*/
	);

	$followup = array(
		0 => 295,
		1 => 92,
		2 => 93,
		3 => 94,
		4 => 95,
		5 => 96,
		6 => 97,
		7 => 98,
		8 => 99,
		9 => 100,
		10 => 101,
		11 => 102,
		12 => 103,
		13 => 104
		/*14 => 310,
		15 => 311,
		16 => 312,
		17 => 313,
		18 => 314,
		19 => 315,
		20 => 316,
		21 => 317,
		22 => 318,
		23 => 319*/
	);

	if($questions){
		$x = 0;
		foreach($questions as $question){
            $survey_id = 2;
            $question_id = $baseline[$x];
            $followup_question_id = $followup[$x];
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $patient_id =  $_GET['id'];
            $gender_id =  $_GET['gender'];
            $charttype =  $_GET['charttype'];

            if ($isBaseline == 0) {
                $answer = getSurveyBaselineAnswers($survey_id, $patient_id, $question_id, $gender_id, $substance_id);
            } else {
                $answer = getSurveyBaselineAnswers( 2 , 0, $question_id, $gender_id, $substance_id);
            }

            $x = $x+1;
            $temp .= '<div class="col-lg-12">';
            $temp .= '<!-- QUESTION -->';
            $temp .= '<div class="panel panel-default">';
            $temp .= '<div class="panel-heading">';

            $temp .= $question->order_by . ". " . $question->question;

            //$temp .= '<br>KEY: ' . $question_id ;
            //$temp .= '<br>patient id: ' . $patient_id;
            //$temp .= '<br>question id: ' . $question_id;
            //$temp .= '<br>survey id: ' . $survey_id;
            //$temp .= '<br>answer value: ' . $answer;
            //$temp .= '<br>chart type: ' . $charttype;

            $temp .= '</div>';
            $temp .= '<!-- /.panel-heading -->';
            $temp .= '<div class="panel-body">';
            if ($charttype == 'Line') {
                $temp .= '<div id="morris-line-chart-' . $question->id . '"></div>';
            } else {
                $temp .= '<div id="morris-bar-chart-' . $question->id . '"></div>';
            }
            $temp .= '</div>';
            $temp .= '<!-- /.panel-body -->';
            $temp .= '</div>';
            $temp .= '<!-- /.panel -->';
            $temp .= '</div><!-- /.col-lg-12 -->';

            // Chart Script
            $temp .= '<script>';

            // Now loop through each matching survey
            // But first determine which chart type
            if ($charttype == 'Line') {

                $calc = round(($answer / 90) * 100);
                // Line Chart
                $temp .= "$(function() {

					    Morris.Line({
					        element: 'morris-line-chart-" . $question->id . "',
					        data: [
					        {
					            month: '2016-02-08',
					            average: " . $calc . ",
					            label: '" . $calc . "%'

					        }" . getSurveyFollowupAnswersLine( 4 , $patient_id, $followup_question_id) . "
					        ],

					        hoverCallback: function(index, options, content) {
						        var data = options.data[index];
						        return(content);
						    },
						    postUnits: '%',
						    xLabelAngle: 45,
						    xLabels: 'month',
						    xkey: 'month',
						    ymax: 100,
						    ykeys: ['average'],
						    labels: ['Average']

					    });

					});";

            } else {
                // Bar Chart
                $calc = round(($answer / 90) * 100);
                $temp .= "$(function() {

					    Morris.Bar({
					        element: 'morris-bar-chart-" . $question->id . "',
					        data: [
					        {
					            y: 'Baseline',
					            a: " . $calc . "
					        }" . getSurveyFollowupAnswers( 4 , $patient_id, $followup_question_id) . "
					        ],
					        xkey: 'y',
					        xLabelAngle: 45,
					        ykeys: ['a'],
					        ymax: 100,
					        yLabelFormat: function(y){return y != Math.round(y)?'':y;},
					        //labels: ['Baseline', 'Follow Up'],
					        barColors: ['#f0ad4e'],
					        hideHover: 'auto',
					        resize: true,
					        hoverCallback: function(index, options, content) {
						        var data = options.data[index];
						        return(content);
						    },
						    postUnits: '%',
						    labels: ['Value', ''],
						    gridIntegers: true

					    });

					});";
				}

				$temp .= '</script>';

		} // End foreach

	}
	return $temp;
}
?>