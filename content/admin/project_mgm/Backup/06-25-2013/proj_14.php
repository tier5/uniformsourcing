<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";
$pid=$_POST['pid'];
$query1 = "SELECT * FROM tbl_quote";
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}

pg_free_result($result1);

$query1 = "SELECT bid FROM tbl_prjpurchase where pid=".$pid;
//echo $query1 ;
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$row1 = pg_fetch_array($result1);
	$bid_num=$row1["bid"];


pg_free_result($result1);



$bid ="Choose Quote Number: <select name=\"bid\" id=\"bid\">";
	for($i=0; $i < count($data1); $i++){
	$bid .="<option value=\"".$data1[$i]['qid']."\"   ";
	if($bid_num==$data1[$i]['qid']) $bid.=" selected='selected' ";
	$bid.=">".$data1[$i]['po_number']."</option>";
	}
$bid .='</select>';

//echo $notification;
$return_arr['html'] = $bid;
echo json_encode($return_arr);
return;
?>