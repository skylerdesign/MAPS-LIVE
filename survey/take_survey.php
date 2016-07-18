<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require 'includes.php';

$layout = new Layout('html/','str/');
$layout->SetContentView('take-survey');

if(isset($_GET['x'])){
	$id = intval(base64_decode($_GET['x'])) - 54321;
	$layout->AddContentById('id_query', '?x='.$_GET['x']);
}elseif(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$event = intval($_GET['event']);
	$patient = intval($_GET['patient']);
	$layout->AddContentById('id_query', '?id='.$_GET['id'] . '&event=' . $_GET['event'] . '&patient=' . $_GET['patient']);
}else{
	$id = null;
}

if(isset($_GET['appointment_id'])){
	$appointment_id = intval($_GET['appt']);
	$layout->AddContentById('appointment_id', $_GET['appointment_id']);
}else{
	$appointment_id = null;
}

if(isset($_GET['event'])){
	$event = intval($_GET['event']);
}else{
	$event = null;
}


if(isset($_POST)){
	$_POST = NewCleanXSS($_POST);
}

$layout->AddContentById('current_year', date('Y'));
$layout->AddContentById('site_url', SITE_URL);

$survey = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "survey WHERE id = $id AND status = 'published' ORDER BY id DESC LIMIT 0,1");
if($survey){
	$layout->AddContentById('id', $id);
	$layout->AddContentById('name', $survey->title);
	$layout->AddContentById('description', preg_replace('/\v+|\\\[rn]/','<br/>',$survey->description));
	$layout->AddContentById('survey_submission_alert', '{{ST:survey_alert_missing_answers}}' );
	
	if($survey->daily_limit AND intval($survey->daily_limit) > 0){
		if(count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "results WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' AND survey_id = $id AND YEAR(date_taken)=".date('Y')." AND MONTH(date_taken)=".date('n')." AND DAY(date_taken)=".date('j')."")) >= intval($survey->daily_limit)){
			$layout->AddContentById('questions', '<h3 style="color:red">{{ST:taken_maximum_times_day}}</h3>');
			$layout->AddContentById('hide_buttons', 'style="display:none;"');
			$layout->RenderViewAndExit();
		}
	}
	
	if($survey->total_limit AND intval($survey->total_limit) > 0){
		if(count($db->get_results("SELECT * FROM " . TABLES_PREFIX . "results WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' AND survey_id = $id")) >= intval($survey->total_limit)){
			$layout->AddContentById('questions', '<h3 style="color:red">{{ST:taken_maximum_times}}</h3>');
			$layout->AddContentById('hide_buttons', 'style="display:none;"');
			$layout->RenderViewAndExit();
		}
	}
	
	$questions = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "question WHERE survey_id = $id ORDER BY order_by ASC, id ASC");
	
	$questions_rows_html = '';
	if($questions){
		$count = 1;
		foreach($questions as $question){
			$cookie_name = 'surveyengine_'.$id.'_name_'.$question->id;
			$row_layout = new Layout('html/','str/');
			$row_layout->SetContentView('take-survey-piece');
			$row_layout->AddContentById('number', $count);
			$row_layout->AddContentById('question_id', $question->id);
			$count++;
			
			if($question->is_required == 'y'){
				$row_layout->AddContentById('is_required', ' <span style="font-size: 0.6em; color: red;">{{ST:required}}</span>');
			}  else {
				$row_layout->AddContentById('is_required', '');
			}
			
			$row_layout->AddContentById('question', $question->question);
			
				
			if($question->attachment != ''){
				$files = unserialize($question->attachment);
				if(count($files) > 0 AND is_array($files)){
					$files_lists = '<div><ol>';
					$files_count = 1;
					foreach($files as $f){
						if(is_image_file($f)){
							$files_lists .= '<li><a target="_blank" href="'. BASE_URL . 'uploads/' . $f.'" rel="prettyPhoto[gal'.$question->id.']">{{ST:attachment}} '.$files_count.'</a></li>';
						}else{
							$files_lists .= '<li><a target="_blank" href="'. BASE_URL . 'uploads/' . $f.'">{{ST:attachment}} '.$files_count.'</a></li>';
						}
						$files_count++;
					}
					$files_lists .= '</ol></div>';
					$row_layout->AddContentById('files', $files_lists);
				}
			}
			
			if($question->question_type == 'tf'){
				$choice_layout = new Layout('html/','str/');
				$choice_layout->SetContentView('take-survey-tf');
				$choice_layout->AddContentById('name', 'name_'.$question->id);
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					if($_POST['name_'.$question->id] == 't'){
						$choice_layout->AddContentById('t_state', 'checked="checked"');
					}elseif($_POST['name_'.$question->id] == 'f'){
						$choice_layout->AddContentById('f_state', 'checked="checked"');
					}
				}elseif(isset($_COOKIE[$cookie_name])) {
					if($_COOKIE[$cookie_name] == 't'){
						$choice_layout->AddContentById('t_state', 'checked="checked"');
					}elseif($_COOKIE[$cookie_name] == 'f'){
						$choice_layout->AddContentById('f_state', 'checked="checked"');
					}
				}
				$row_layout->AddContentById('choices', $choice_layout->ReturnView());
			}elseif($question->question_type == 'mp'){
				$choices_html = '';
				$choices = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "choices WHERE question_id = " . $question->id . " ORDER BY id ASC");
				$saved_choice = null;
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					$saved_choice = $_POST['name_'.$question->id];
				}elseif(isset($_COOKIE[$cookie_name])) {
					$saved_choice = $_COOKIE[$cookie_name];
				}
					
				foreach($choices as $choice){
					$choice_layout = new Layout('html/','str/');
					$choice_layout->SetContentView('take-survey-mp');
					$choice_layout->AddContentById('name', 'name_'.$question->id);
					$choice_layout->AddContentById('choice', $choice->choice);
					$choice_layout->AddContentById('value', $choice->id);
					if($saved_choice == $choice->id){
						$choice_layout->AddContentById('state', 'checked="checked"');
					}
					$choices_html .= $choice_layout->ReturnView();
				}
				$choices_html = '<ul class="select-scale">' . $choices_html . '</ul>';
				$row_layout->AddContentById('choices', $choices_html);
			}elseif($question->question_type == 'ma'){
				$choices_html = '';
				$choices = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "choices WHERE question_id = " . $question->id . " ORDER BY id ASC");
				$saved_choices = array();
				if(isset($_POST['name_'.$question->id]) AND count($_POST['name_'.$question->id]) > 0){
					foreach($_POST['name_'.$question->id] as $s){
						$saved_choices[] = $s;
					}
					setcookie($cookie_name, serialize($saved_choices), time() + COOKIE_TIME);	
				}elseif(isset($_COOKIE[$cookie_name])) {
					$saved_choices = unserialize($_COOKIE[$cookie_name]);
					$old_saved_choices = $saved_choices;
					$saved_choices = array();
					if(is_array($old_saved_choices)){
						foreach($old_saved_choices as $oc){
							$saved_choices[] = $oc;
						}
					}
				}
					
				foreach($choices as $choice){
					$choice_layout = new Layout('html/','str/');
					$choice_layout->SetContentView('take-survey-ma');
					$choice_layout->AddContentById('name', 'name_'.$question->id);
					$choice_layout->AddContentById('choice', $choice->choice);
					$choice_layout->AddContentById('value', $choice->id);
					if(in_array($choice->id, $saved_choices)){
						$choice_layout->AddContentById('state', 'checked="checked"');
					}
					$choices_html .= $choice_layout->ReturnView();
				}
				$row_layout->AddContentById('choices', $choices_html);
			}elseif($question->question_type == 'st'){
				$choice_layout = new Layout('html/','str/');
				$choice_layout->SetContentView('take-survey-st');
				$choice_layout->AddContentById('name', 'name_'.$question->id);
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					$choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
				}elseif(isset($_COOKIE[$cookie_name])) {
					$choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
				}
				$row_layout->AddContentById('choices', $choice_layout->ReturnView());
			}elseif($question->question_type == 'nd'){
				$choice_layout = new Layout('html/','str/');
				$choice_layout->SetContentView('take-survey-nd');
				$choice_layout->AddContentById('name', 'name_'.$question->id);
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					$choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
				}elseif(isset($_COOKIE[$cookie_name])) {
					$choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
				}
				$row_layout->AddContentById('choices', $choice_layout->ReturnView());
			}elseif($question->question_type == 'nc'){
				$choice_layout = new Layout('html/','str/');
				$choice_layout->SetContentView('take-survey-nc');
				$choice_layout->AddContentById('name', 'name_'.$question->id);
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					$choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
				}elseif(isset($_COOKIE[$cookie_name])) {
					$choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
				}
				$row_layout->AddContentById('choices', $choice_layout->ReturnView());
				
				
			}elseif($question->question_type == 'lt'){
				$choice_layout = new Layout('html/','str/');
				$choice_layout->SetContentView('take-survey-lt');
				$choice_layout->AddContentById('name', 'name_'.$question->id);
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
					$choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
				}elseif(isset($_COOKIE[$cookie_name])) {
					$choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
				}
				$row_layout->AddContentById('choices', $choice_layout->ReturnView());
			}elseif($question->question_type == 'em'){
                $choice_layout = new Layout('html/','str/');
                $choice_layout->SetContentView('take-survey-em');
                $choice_layout->AddContentById('name', 'name_'.$question->id);
                if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
                    setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
                    $choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
                }elseif(isset($_COOKIE[$cookie_name])) {
                    $choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
                }
                $row_layout->AddContentById('choices', $choice_layout->ReturnView());
            }elseif($question->question_type == 'dt'){
                $choice_layout = new Layout('html/','str/');
                $choice_layout->SetContentView('take-survey-dt');
                $choice_layout->AddContentById('name', 'name_'.$question->id);
                if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
                    setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
                    $choice_layout->AddContentById('value', $_POST['name_'.$question->id]);
                }elseif(isset($_COOKIE[$cookie_name])) {
                    $choice_layout->AddContentById('value', $_COOKIE[$cookie_name]);
                }
                $row_layout->AddContentById('choices', $choice_layout->ReturnView());
            }elseif($question->question_type == 'dd'){
                $choices_html = '';
                $choices = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "choices WHERE question_id = " . $question->id . " ORDER BY id ASC");
                $saved_choice = null;
                if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
                    setcookie($cookie_name, $_POST['name_'.$question->id], time() + COOKIE_TIME);
                    $saved_choice = $_POST['name_'.$question->id];
                }elseif(isset($_COOKIE[$cookie_name])) {
                    $saved_choice = $_COOKIE[$cookie_name];
                }

                $choice_layout = new Layout('html/','str/');
                $choice_layout->SetContentView('take-survey-dd');
                $choice_layout->AddContentById('name', 'name_'.$question->id);
                $option_html = '';
                foreach($choices as $choice){
                    $option_html .= '<option value="'.$choice->id.'" {{ID:selected_type_tf}}';
                    if($saved_choice == $choice->id){
                        $option_html .= ' selected="selected"';
                    }
                    $option_html .= '>'.$choice->choice.'</option>';

                }
                $choice_layout->AddContentById('options', $option_html);
                $choices_html .= $choice_layout->ReturnView();
                $row_layout->AddContentById('choices', $choices_html);
            }
			
			$questions_rows_html .= $row_layout->ReturnView();
		}
	}else{
		$questions_rows_html = '<p>{{ST:no_items}}</p>';
	}
	
	
	if(isset($_POST['submit']) && isset($_POST['ts']) && isset($_COOKIE['token']) && $_COOKIE['token'] == sha1('dfgkjsh456dsght4'.$_POST['ts'])){
		$errors = false;
		$values = array();
		$format = array();
		$error_msg = '';
		$post_value = null;
		$answers = array();
		
		if($questions){
			$countX = 1;
			foreach($questions as $question){
				$cookie_name = 'surveyengine_'.$id.'_name_'.$question->id;
				setcookie($cookie_name, '', time() - COOKIE_TIME);
				
				
				if(isset($_POST['name_'.$question->id]) AND $_POST['name_'.$question->id] != ''){
					$post_value = $_POST['name_'.$question->id];

                    if($question->question_type == 'em' AND !ValidateEmail($post_value)){
                        $errors = true;
                        $error_msg .= ' {{ST:question}} ' . $countX . ' {{ST:is_invalid_email}}';
                    }else{
                        if($question->question_type == 'tf'){
                            if($post_value == 'f' OR $post_value == 't'){
                                $answers[$question->id] = $post_value;
                            }
                        }else{
                            $answers[$question->id] = $post_value;
                        }
                    }

				}elseif($question->is_required == 'y'){
					$errors = true;
					$error_msg .= ' {{ST:question}} ' . $countX . ' {{ST:is_required2}}';
				}
				$countX = $countX + 1;
			}
			
			if(count($answers) == 0){
				$errors = true;
				$error_msg .= ' {{ST:no_answers}}';
			}
			
			if($errors == false){
				//$values['answers'] = serialize($answers);
				//$format[] = "%s";
				$values['date_taken'] = date('Y-m-d H:i:s');
				$format[] = "%s";
				$values['ip_address'] = $_SERVER['REMOTE_ADDR'];
				$format[] = "%s";
				$values['survey_id'] = $id;
				$format[] = "%d";
				$values['event_id'] = $event;
				$format[] = "%d";
				$values['patient_id'] = $patient;
				$format[] = "%d";
				if($db->insert(TABLES_PREFIX . "results", $values, $format)){
					$new = $db->insert_id;
					
					foreach($answers as $k=>$v){
						$q_ans = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "question WHERE id = $k ORDER BY id DESC LIMIT 0,1");
						if($q_ans->question_type == 'ma'){
							foreach($v as $vv){
							$db->insert(TABLES_PREFIX . "answers", array('survey_id'=>$id,'question_id'=>$k,'results_id'=>$new,'choice_id'=>$vv), array("%d","%d","%d","%d"));
							}
						}elseif($q_ans->question_type == 'mp' OR $q_ans->question_type == 'dd'){
							$db->insert(TABLES_PREFIX . "answers", array('survey_id'=>$id,'question_id'=>$k,'results_id'=>$new,'choice_id'=>$v), array("%d","%d","%d","%d"));
						}else{
							$db->insert(TABLES_PREFIX . "answers", array('survey_id'=>$id,'question_id'=>$k,'results_id'=>$new,'answer'=>$v), array("%d","%d","%d","%s"));
						}
					}
					
					if($survey->email == 'y'){
						$to = WEBMASTER_EMAIL;
						$subject = "[" . $_SERVER['SERVER_NAME'] . "]".$tmpl_strings->Get('app_name')." - ".$tmpl_strings->Get('survey_taken');
						$message = $survey->title."

".$tmpl_strings->Get('survey_taken_msg')."

" . BASE_URL;
						$from = WEBMASTER_EMAIL;
						$headers = "Content-type: text/html; charset=UTF-8\r\n";
                        $headers .= "From: $from\r\n";
						mail($to,$subject,$message,$headers);


					}
					
					foreach($questions as $question){
						if($question->question_type == 'nc' OR 
						   $question->question_type == 'nd' OR 
						   $question->question_type == 'tf' OR  
						   $question->question_type == 'mp' OR
                           $question->question_type == 'dd' OR
                           $question->question_type == 'ma'){
						if(isset($answers[$question->id])){
							$q_stats = array();
							
							$s_index = $answers[$question->id];
							
							if($question->stats){
								$q_stats = unserialize($question->stats);
								if($question->question_type == 'mp'){
									$s_index = Slug($s_index);
								}
							}else{
								if($question->question_type == 'tf'){
									$q_stats = array('t'=>0,'f'=>0);
								}elseif($question->question_type == 'dd' OR $question->question_type == 'mp' OR $question->question_type == 'ma'){
									$choices = $db->get_results("SELECT * FROM " . TABLES_PREFIX . "choices WHERE question_id = " . $question->id . " ORDER BY id ASC");
									if($question->question_type == 'mp'){
										$s_index = Slug($s_index);
									}
									foreach($choices as $c){
										$q_stats[$c->id] = 0;
									}
									if($question->question_type == 'ma'){
										$q_stats['_total_selects'] = 0;
									}
								}elseif($question->question_type == 'nc' OR $question->question_type == 'nd'){
									$q_stats = array('total'=>0,'n'=>0,'max'=>0,'min'=>9999999999);
								}
							}
							
							if($question->question_type == 'ma'){
								if(count($answers[$question->id]) > 0){
									foreach($answers[$question->id] as $mp_a){
										$q_stats[Slug($mp_a)] = $q_stats[Slug($mp_a)] + 1;
									}
									$q_stats['_total_selects'] = $q_stats['_total_selects'] + 1;
								}
							}elseif($question->question_type == 'nc' OR $question->question_type == 'nd'){
								$q_stats['total'] = $q_stats['total'] + floatval($answers[$question->id]);
								$q_stats['n'] = $q_stats['n'] + 1;
								if(floatval($answers[$question->id]) > $q_stats['max']){
									$q_stats['max'] = floatval($answers[$question->id]);
								}
								if(floatval($answers[$question->id]) < $q_stats['min']){
									$q_stats['min'] = floatval($answers[$question->id]);
								}
							}else{
								$q_stats[$s_index] = $q_stats[$s_index] + 1;
							}
							
							$db->update(TABLES_PREFIX . "question", array('stats'=>serialize($q_stats)), array('id'=>$question->id), array("%s"));
						}
						}
					}
					
					if($survey->redirect_url){
						Leave($survey->redirect_url);
					}else{
						if(ALLOW_PUBLIC_VIEW_RESULTS == true){
							Leave('survey_results.php?x='.$_GET['x'].'&submitted=1');
						}else{
							$layout->AddContentById('alert', $layout->GetContent('alert'));
							$layout->AddContentById('alert_nature', ' alert-success');
							$layout->AddContentById('alert_heading', '{{ST:thank_you}}!');
							$layout->AddContentById('alert_message', '{{ST:thanks_for_taking_survey}}');
							$layout->AddContentById('hide_buttons', 'style="display:none;"');
						}
					}
				}
			}else{
				$layout->AddContentById('alert', $layout->GetContent('alert'));
				$layout->AddContentById('alert_nature', ' alert-danger');
				$layout->AddContentById('alert_heading', '{{ST:error}}!');
				$layout->AddContentById('alert_message', $error_msg);
				
				$layout->AddContentById('questions', $questions_rows_html);
			}
		}else{
			$layout->AddContentById('questions', '<h3 style="color:red">{{ST:survey_not_available}}</h3>');
			$layout->AddContentById('hide_buttons', 'style="display:none;"');
		}
	}else{
		
		
		
		
		if(isset($_POST['save'])){
			$layout->AddContentById('alert', $layout->GetContent('alert'));
			$layout->AddContentById('alert_nature', ' alert-success');
			$layout->AddContentById('alert_heading', '{{ST:success}}!');
			$layout->AddContentById('alert_message', '{{ST:saved}}');
		}
	
		$layout->AddContentById('questions', $questions_rows_html);
	
	}
}else{
	$layout->AddContentById('name', '{{ST:survey_not_available}}');
	$layout->AddContentById('hide_buttons', 'style="display:none;"');
}


$layout->RenderViewAndExit();
