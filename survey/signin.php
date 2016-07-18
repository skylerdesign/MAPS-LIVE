<?php
require 'includes.php';

if(IsLoggedIn()){
	Leave('index.php');
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$layout = PublicPage('signin', '{{ST:signin}}');

if(isset($_POST['submit'])){
	if(isset($_POST['email']) AND isset($_POST['password'])){
		
		$user = $db->get_row("SELECT * FROM " . TABLES_PREFIX . "admin WHERE email = '" . Clean($_POST['email']) . "' ORDER BY id DESC LIMIT 0,1");
		
		// Confirm IP address
		if (confirmIPAddress($ip_address) > 0) {
			$layout->AddContentById('alert', $layout->GetContent('alert'));
			$layout->AddContentById('alert_nature', ' alert-danger');
			$layout->AddContentById('alert_heading', 'Account locked!');
			$layout->AddContentById('alert_message', 'Your account has been locked for ' . TIME_PERIOD . ' minutes due to ' . ATTEMPTS_NUMBER . ' or more incorrect login attempts.');
		} else {		
			if($user AND ($user->password == encode_password(Clean($_POST['password'])))){
				
				$layout->AddContentById('email', $user->username);
				
				if($user->status == 'active'){
					$_SESSION['surveyengine_admin_logged_in'] = true;
					$_SESSION['surveyengine_admin_user_id'] = $user->id;
					$_SESSION['username'] = $user->name;
					//$layout->AddContentById('firstname', 'eee');
					$layout->AddContentById('loginemail', $user->username);
			
					if(isset($_POST['remember_me'])){
						setcookie(COOKIE_NAME, 'email='.$user->username.'&hash='.$user->password, time() + COOKIE_TIME);
					}
					
					// Clear login block
					clearLoginAttempts($ip_address);
			
					header('Location: index.php');
				
					exit();
				
				}else{
					// Add attempt
					//addLoginAttempt($ip_address);
					
					$layout->AddContentById('alert', $layout->GetContent('alert'));
					$layout->AddContentById('alert_nature', ' alert-danger');
					$layout->AddContentById('alert_heading', '{{ST:error}}!');
					$layout->AddContentById('alert_message', '{{ST:access_denied_contact_admin}}');
				}
			}else{
				
				// Track incorrect login
				$values['user_id'] = Clean($_POST['email']);
				$values['ip_address'] = $ip_address;
				$values['attempted_at'] = date("Y-m-d H:i:s");
				$format[] = "%s";
				
				$db->insert("user_failed_logins", $values, $format);
				
				// Add attempt
				//addLoginAttempt($ip_address);
				
				$layout->AddContentById('alert', $layout->GetContent('alert'));
				$layout->AddContentById('alert_nature', ' alert-danger');
				$layout->AddContentById('alert_heading', '{{ST:error}}!');
				$layout->AddContentById('alert_message', '{{ST:the_info_is_not_correct}}');
			}
		}
	}else{
		// Add attempt
		//addLoginAttempt($ip_address);
		$layout->AddContentById('alert', $layout->GetContent('alert'));
		$layout->AddContentById('alert_nature', ' alert-danger');
		$layout->AddContentById('alert_heading', '{{ST:error}}!');
		$layout->AddContentById('alert_message', '{{ST:the_info_is_not_correct}}');
	}
}

$layout->RenderViewAndExit();
