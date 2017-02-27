<?php
require('Application.php');
$itemid=$_POST['itemid'];
$description=$_POST['description'];
$price=$_POST['price'];
if($debug == "on"){
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
		require('../../header.php');
		echo "The itemid you selected is in use by another item with the description ".$data1[$i]['description']." and price ".$data1[$i]['price']." Please go back change the item id.";
		require('../../trailer.php');
		exit;
	}
}
$query2=("INSERT INTO \"billingcodes\" ".
		 "(\"itemid\", \"description\", \"price\", \"ikey\", \"active\") ".
		 "VALUES ('$itemid', '$description', '$price', 'FF', 'yes')");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
header("location: ../index.php");
?>
