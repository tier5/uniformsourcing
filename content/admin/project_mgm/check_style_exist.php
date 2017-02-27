<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['style']) && isset($_POST['pid']))
{
	$style = $_POST['style'];
	$pid = $_POST['pid'];
	if($style !="")
	{
		$query = "Select style from tbl_prj_style where pid = $pid and style = '".$style."'";
		if(!($result=pg_query($connection,$query))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		while($row = pg_fetch_array($result)){
			$data_style=$row;
		}	
		if(count($data_style)>1)
		{
			$return_arr['error'] = "Style Already Exist";
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
	}
}
echo json_encode($return_arr);
exit;
?>