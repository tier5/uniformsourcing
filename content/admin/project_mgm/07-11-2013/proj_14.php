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

$query1 = "SELECT prch.bid,qt.project_name FROM tbl_prjpurchase as prch left join tbl_quote as qt on qt.qid=prch.project_name where prch.pid=".$pid;
//echo $query1 ;
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$row1 = pg_fetch_array($result1);
	$bid_num=$row1;


pg_free_result($result1);

if($pid){
$bid ='Choose BID</em>: <select name="bid" id="bid">'.
							'<option value="">------------SELECT-------------</option>';
	for($i=0; $i < count($data1); $i++){
	$bid .="<option value=\"".$data1[$i]['qid']."\"   ";
	if($bid_num["bid"]==$data1[$i]['qid']) 
	$bid.=" selected='selected' ";
	$bid.=">".$data1[$i]['po_number']." - ".$data1[$i]['project_name']."</option>";
	}
$bid .='</select>';
}

if(!$pid){
	$bid .='Choose BID</em>: <select name="bid" id="bid">'.
							'<option value="">------------SELECT-------------</option>';
							$bid .='</select>';
}				 

//echo $notification;
$return_arr['html'] = $bid;
echo json_encode($return_arr);
return;
?>

