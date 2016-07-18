<?php 	

require 'includes.php';


$id = $_POST['id'];
// Get patients
if ($id) {
	
	$db->get_results("DELETE FROM junction_relationships WHERE id=" . $id );

}
?>