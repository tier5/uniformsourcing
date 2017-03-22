<?php
require('Application.php');
extract($_POST);

$location_name;
$conveyor_name;

$sql = 'select identifier from "tbl_invLocation" where "locationId"='.$locationId;
if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$location_name = pg_fetch_array($result);


$sql = 'select name from "tbl_conveyor" where "conveyorId"='.$conveyorId;
if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$conveyor_name = pg_fetch_array($result);

$new_slot = $location_name['identifier'].'_'.$conveyor_name['name'].'_'.$slot;

// print_r($new_slot);
// exit();

$sql = 'select count(*) from tbl_conveyor where slot=\''.$new_slot.'\'';

if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$myres = pg_fetch_array($result);

if($myres['count'] >0 )
{
	echo json_encode('slot not available');
	exit();
}







$sql = 'UPDATE "tbl_conveyor" SET slot = \''.$new_slot.'\' WHERE "conveyorId"='.$conveyorId;

// var_dump($query);
// exit();
if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
echo "1";
exit();


?>