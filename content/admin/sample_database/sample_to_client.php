<?php
require('Application.php');

$id = $_POST['id'];
$atclient = $_POST['atclient'];

if($id != "" AND $atclient != ""){
	if($atclient == '0'){
		$query1 = ("UPDATE tbl_sample_database ".
			"SET ".
			"atclient = '$atclient' ".
			"WHERE sample_id = '$id' ");
		if(!($result1 = pg_query($connection,$query1))){
			print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
			exit;
		}

		header("Location: database_sample_add.php?id=$id");
		exit();
	}else{
		$query1 = ("UPDATE tbl_sample_database ".
			"SET ".
			"conveyor = null, ".
			"slot = null, ".
			"atclient = '$atclient' ".
			"WHERE sample_id = '$id' ");
		if(!($result1 = pg_query($connection,$query1))){                      
			print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
			exit;
		}

		header("Location: database_sample_add.php?id=$id");
		exit();
	}
}

?>
