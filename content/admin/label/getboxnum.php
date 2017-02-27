<?php
require('Application.php');
$return_arr['box_num'] ="";
$query=("SELECT nextval('box_number_seq') as seq");
if(!($result=pg_query($connection,$query))){
	print("Failed sequence query : " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$seq=$row['seq'];
}
$return_arr['box_num']=$seq;
pg_free_result($result);	 

/*if(isset($seq))
	echo $seq;
else 
	echo '0';*/
	echo json_encode($return_arr);
exit;
?>