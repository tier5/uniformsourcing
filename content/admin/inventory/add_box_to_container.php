<?php 
require('Application.php');
extract($_POST);



$location_name;
$container_name;

$sql = 'select identifier from "tbl_invLocation" where "locationId"='.$locationId;
if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$location_name = pg_fetch_array($result);


$sql = 'select name from "tbl_container" where "containerId"='.$containerId;
if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$container_name = pg_fetch_array($result);

$new_box = $location_name['identifier'].'_'.$container_name['name'].'_'.$box;

$sql = 'select count(*) from tbl_container where box=\''.$new_box.'\'';

if(!($result=pg_query($connection,$sql)))
{
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$myres = pg_fetch_array($result);

if($myres['count'] >0 )
{
	echo json_encode('box not available');
	exit();
}







$sql = 'UPDATE "tbl_container" SET box = \''.$new_box.'\' WHERE "containerId"='.$containerId;

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