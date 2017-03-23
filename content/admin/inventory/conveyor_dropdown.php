<?php
require('Application.php');
extract($_POST);


	// $sql = 'select distinct name, "conveyorId" from "tbl_conveyor" where "locationId"=\''.$id.'\' order by "name" ';
 //    $conveyor_names;
 //    if (!($result = pg_query($connection, $sql))) 
 //    {
 //        print("Failed invQuery: " . pg_last_error($connection));
 //        exit;
 //    }
 //    while ($row = pg_fetch_array($result)) 
 //    {
 //        $conveyor_names[] = $row;
 //    }

 //    echo json_encode($conveyor_names);
 //    exit();

$sql = 'select id , conveyor from "locationDetails" where "locationId"=\''.$id.'\' and conveyor !=  \'null\'';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $location_details_id[] = $row;
    }
    echo json_encode($location_details_id);
    exit();



?>