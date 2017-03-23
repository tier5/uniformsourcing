<?php 
require('Application.php');
extract($_POST);

	// $sql = 'select id,warehouse_name as name from "warehouse" where "locationId"='.$id;

 //    $warehouse_info;
 //    $w_name;
 //    if (!($result = pg_query($connection, $sql))) {
 //        print("Failed invQuery: " . pg_last_error($connection));
 //        exit;
 //    }
 //    while ($row = pg_fetch_array($result)) {
 //        $warehouse_info[] = $row;
 //    }

    $sql = 'select id , warehouse from "locationDetails" where "locationId"=\''.$id.'\' and warehouse !=  \'null\'';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $location_details_id[] = $row;
    }
    echo json_encode($location_details_id);
    exit();




    foreach($warehouse_info as $key=>$val)
    {
    	$w_name[$val['id']] = $val['name']; 
    }

    echo json_encode($warehouse_info);
    exit();

    echo json_encode($w_name);
    exit();


    // $location_string = " ";
    // foreach ($all_location_inv as $key => $value) {
    //     if($location_string == " ") 
    //         $location_string .= $value['locationId'];
    //     else
    //         $location_string .= ','.$value['locationId'];
    // }
    // //echo "<pre>"; print_r($location_string);
    // //exit();

    // $sql = 'select warehouse_name,"locationId" from "warehouse" where "locationId" in ('.$location_string.')';

    // $warehouse_info;
    // if (!($result = pg_query($connection, $sql))) {
    //     print("Failed invQuery: " . pg_last_error($connection));
    //     exit;
    // }
    // while ($row = pg_fetch_array($result)) {
    //     $warehouse_info[] = $row;
    // }
    
    

    

?>