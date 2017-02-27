<?php
require('Application.php');
$ID=$_POST['ID'];
$itemid=$_POST['itemid'];
$description=$_POST['description'];
$price=$_POST['price'];
if($debug == "on"){
	echo "ID IS $ID<br>";
	echo "itemid IS $itemid<br>";
	echo "description IS $description<br>";
	echo "price IS $price<br>";
}
$query1=("SELECT * ".
		 "FROM \"billingcodes\" ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
for($i=0; $i < count($data1); $i++){
	if($itemid == $data1[$i]['itemid']){
		if($ID != $data1[$i]['ID']){
			require('../../header.php');
			echo "The itemid you selected is in use by another item with the description ".$data1[$i]['description']." and price ".$data1[$i]['price']." Please go back change the item id.";
			require('../../trailer.php');
			exit;
		}
	}
}
$query2=("UPDATE \"billingcodes\" ".
		 "SET ".
		 "\"itemid\" = '$itemid', ".
		 "\"description\" = '$description', ".
		 "\"price\" = '$price', ".
		 "\"active\" = 'yes' ".
		 "WHERE \"ID\" = '$ID'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
header("location: ../index.php");
?>
