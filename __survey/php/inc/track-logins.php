<?php
function confirmIPAddress($value) {

  $q = "SELECT login_attempts, (CASE when last_login is not NULL and DATE_ADD(last_login, INTERVAL ".TIME_PERIOD. " MINUTE)>NOW() then 1 else 0 end) as denied FROM ".TBL_ATTEMPTS." WHERE ip_address = '$value'";
  
  $db = new Db;
  
  $data = $db->get_results( $q );

  //Verify that at least one login attempt is in database
  if (!$data) {
	  addLoginAttempt($value);
	  return 0;
  }
  if ($data[0]->login_attempts >= ATTEMPTS_NUMBER)
  {
	echo $data[0]->login_attempts;
	
    if($data[0]->denied == 1)
    {
      return 1;
    }
    else
    { 
      //$this->clearLoginAttempts($value);
      clearLoginAttempts($value);
      return 0; 
    }
  }
  return 0;
}

function addLoginAttempt($value) {

   //Increase number of attempts. Set last login attempt if required.

   $q = "SELECT * FROM ".TBL_ATTEMPTS." WHERE ip_address = '$value'";

   $db = new Db;
   
   $data = $db->get_results( $q );
  
   if($data)
   {
     $attempts = $data[0]->login_attempts+1;        
     if($attempts >= ATTEMPTS_NUMBER) {
       $q = "UPDATE ".TBL_ATTEMPTS." SET login_attempts=".$attempts.", last_login=NOW() WHERE ip_address = '$value'";
       //$result = mysql_query($q, $this->connection);
       $db->query( $q );
     }
     else {
       $q = "UPDATE ".TBL_ATTEMPTS." SET login_attempts=".$attempts." WHERE ip_address = '$value'";
       //$result = mysql_query($q, $this->connection);
       $db->query( $q );
     }
   }
   else {
	 
     $q = "INSERT INTO ".TBL_ATTEMPTS." (login_attempts,ip_address,last_login) values (1, '$value', NOW())";
	 
     // $result = mysql_query($q, $this->connection);
     $db->query( $q );
   }
}

function clearLoginAttempts($value) {
	$db = new Db;
	$q = "UPDATE ".TBL_ATTEMPTS." SET login_attempts = 0 WHERE ip_address = '$value'";
	return $db->query( $q );
}
?>