<?php

if(isset($_GET['survey']) AND $_GET['survey'] != ''){
	$requestURI = explode('/', $_GET['survey']);
	$function = $requestURI[2];
	
	echo '$function: ' . $function;
	
	switch($function){
		case 't':
			 if($requestURI[2]){
			 	$_GET['id'] = intval($requestURI[2]);
			 	require 'take_survey.php';
                 break;
			 }
	}
}

header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
