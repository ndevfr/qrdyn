<?php 
include("functions.php");include("session.php");
if(connected()){
	$id = addslashes($_POST['id']);
	if(in_array($id, userLinks())){
		sql_exec("DELETE FROM `links` WHERE id = '$id';");
	}
}
?>