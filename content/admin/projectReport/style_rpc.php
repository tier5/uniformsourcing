<?php
// Is there a posted query string?
require('Application.php');
if(isset($_POST['queryString'])) 
{
	$queryString = pg_escape_string($_POST['queryString']);
	
	// Is the string length greater than 0?
	
	if(strlen($queryString) >0) 
	{	
		// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
		// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$queryString%' LIMIT 10
		
		$query = "SELECT style FROM tbl_prj_style WHERE status=1 and LOWER(style) LIKE LOWER('$queryString%') LIMIT 10";
		if(!($result=pg_query($connection,$query))){
			print("Failed query1: " . pg_last_error($connection));
			exit;
	
		}
		while($row = pg_fetch_array($result)){
				echo '<li onClick="fill(\''.$row['style'].'\');">'.$row['style'].'</li>';
		}		
	} 
	else {
		// Dont do anything.
	} // There is a queryString.
} 
else {
	echo 'There should be no direct access to this script!';
}

?>