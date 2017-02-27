<?php
require('Application.php');
extract($_POST);
$ID=$_POST['ID'];
$client=$_POST['client'];
$contact=$_POST['contact'];
$phone=$_POST['phone'];
$fax=$_POST['fax'];
$address=$_POST['address'];
$address2=$_POST['address2'];
$city=$_POST['city'];
$state-$_POST['state'];
$zip=$_POST['zip'];
$country=$_POST['country'];
$email=$_POST['email'];
$www=$_POST['www'];
$clientID=$_POST['clientID'];
$class=$_POST['class'];
$intranet=$_POST['intranet'];
$accountmanager=$_POST['accountmanager'];
$query1=("SELECT \"ID\", \"clientID\" ".
		 "FROM \"clientDB\"");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
for($i=0; $i < count($data1); $i++){
	if($clientID == $data1[$i]['clientID'] AND $ID != $data1[$i]['ID']){
		require('../../header.php');
		echo "The client ID you put in is in use by another client. Please select another client ID.<br>";
		require('../../trailer.php');
		exit;
	}elseif($clientID != ""){
	}else{
		$query4=("SELECT MAX(\"ID\") as maxid ".
				 "FROM \"clientDB\" ");
		if(!($result4=pg_query($connection,$query4))){
			print("Failed query4: " . pg_last_error($connection));
			exit;
		}
		while($row4 = pg_fetch_array($result4)){
			$data4[]=$row4;
		}
		$clientID=($data4[0]['maxid'] + 1);
	}
}
if(isset($_POST['update'])){
	$query2="UPDATE \"clientDB\" ".
			 "SET ".
			 "\"client\" = '$client', ".
			 "\"contact\" = '$contact', ".
			 "\"phone\" = '$phone', ".
			 "\"fax\" = '$fax', ".
			 "\"address\" = '$address', ".
			 "\"address2\" = '$address2', ".
			 "\"city\" = '$city', ".
			 "\"state\" = '$state', ".
			 "\"zip\" = '$zip', ".
			 "\"country\" = '$country', ".
			 "\"email\" = '$email', ".
			 "\"www\" = '$www', ".
			 "\"clientID\" = '$clientID', ".
			 "\"class\" = '$class', ".
                         "\"intranet\" = '$intranet', ".
			 "\"accountmanager\" = '$accountmanager', ";
			 if($shipperno!="")$query2.="shipperno = '$shipperno',";
			 else $query2.="shipperno = null,";
			 if($carrier>0)$query2.="carrier = '$carrier',";
			 $query2.="\"notes\" = '$notes', ".
			 "\"active\" = 'yes' ".
			 "WHERE \"ID\" = '$ID'";
			 if(!($result2=pg_query($connection,$query2))){
				print("Failed query2: " . pg_last_error($connection));
				exit;
			}
	header("location: ../index.php");
}
if(isset($_POST['deactivate'])){
	$query3="UPDATE \"clientDB\" ".
			 "SET ".
			 "\"client\" = '$client', ".
			 "\"contact\" = '$contact', ".
			 "\"phone\" = '$phone', ".
			 "\"fax\" = '$fax', ".
			 "\"address\" = '$address', ".
			 "\"address2\" = '$address2', ".
			 "\"city\" = '$city', ".
			 "\"state\" = '$state', ".
			 "\"zip\" = '$zip', ".
			 "\"country\" = '$country', ".
			 "\"email\" = '$email', ".
			 "\"www\" = '$www', ".
			 "\"clientID\" = '$clientID', ".
			 "\"class\" = '$class', ".
                         "\"intranet\" = '$intranet', ".
			 "\"accountmanager\" = '$accountmanager', ";
			 if($shipperno!="")$query3.="shipperno = '$shipperno',";
			 else $query3.="shipperno = null,";
			 if($carrier>0)$query2.="carrier = '$carrier',";
			 $query3.="\"notes\" = '$notes', ".
			 "\"active\" = 'no' ".
			 "WHERE \"ID\" = '$ID'";
	if(!($result3=pg_query($connection,$query3))){
		print("Failed query3: " . pg_last_error($connection));
		exit;
	}
	header("location: ../index.php");
}
?>
