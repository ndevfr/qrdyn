<?php 
include("functions.php");include("session.php");
if(connected()){
	$id = addslashes($_POST['id']);
	if(in_array($id, userLinks())){
		removeLink($id);
	}
}
?>