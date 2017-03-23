<?php 
require('Application.php');
extract($_POST);

// $sql = 'select distinct name, "containerId" from "tbl_container" where "locationId"=\''.$id.'\' order by "name" ';
//     $container_names;
//     if (!($result = pg_query($connection, $sql))) {
//         print("Failed invQuery: " . pg_last_error($connection));
//         exit;
//     }
//     while ($row = pg_fetch_array($result)) {
//         $container_names[] = $row;
//     }

//     echo json_encode($container_names);
//     exit();

$sql = 'select id , container from "locationDetails" where "locationId"=\''.$id.'\' and container !=  \'null\'';
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