<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['notesId']))
{
	$notesId = $_POST['notesId'];	
	
	if($notesId > 0)
	{
		$query = "DELETE from \"tbl_sampleNotes\" where \"notesId\"='$notesId'";	
		if(!($result=pg_query($connection,$query))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
	}
}
echo json_encode($return_arr);
exit;
?>