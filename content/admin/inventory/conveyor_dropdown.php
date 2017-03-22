<?php
require('Application.php');
extract($_POST);


	$sql = 'select distinct name, "conveyorId" from "tbl_conveyor" where "locationId"=\''.$id.'\' order by "name" ';
    $conveyor_names;
    if (!($result = pg_query($connection, $sql))) 
    {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) 
    {
        $conveyor_names[] = $row;
    }

    echo json_encode($conveyor_names);
    exit();



?>