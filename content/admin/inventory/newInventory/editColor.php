<?php
require('Application.php');
extract($_POST);
try {
    if($is_change == 0){
        $sql = '';
        $sql = "UPDATE \"tbl_invColor\" SET name='".$name."' WHERE \"colorId\"=".$colorId;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'status' => false,
                'statusCode' => 500,
                'message' => pg_last_error($connection)
            ]);
            return;
        }
        pg_free_result($result);
    } else {
        $temp = explode(".", $_FILES["file"]["name"]);
        if( $temp[1] == 'gif' || $temp[1] == 'png' || $temp[1] == 'jpg' ){
            $newfilename = round(microtime(true)) . '.' . end($temp);
            move_uploaded_file($_FILES["file"]["tmp_name"], "../../../uploadFiles/inventory/images/" . $newfilename);
            $sql = '';
            $sql = "UPDATE \"tbl_invColor\" SET  name='".$name."', image='".$newfilename."' WHERE \"colorId\"=".$colorId;
            if(!($result=pg_query($connection,$sql))){
                echo json_encode([
                    'status' => false,
                    'statusCode' => 500,
                    'message' => pg_last_error($connection)
                ]);
                return;
            }
            pg_free_result($result);


        } else {
            echo json_encode([
                'message' => 'Please Upload a Image',
                'status' => false,
                'statusCode' => 400
            ]);
            return;
        }
    }
    $sql = '';
    $sql = 'INSERT INTO "tbl_invUpdateLog" ('.
        '"styleId","createdBy","createdAt",type ) VALUES ('.
        "'".$styleId."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Edit Color' ) RETURNING *";
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    $log = pg_fetch_array($result);
    pg_free_result($result);


    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " '" . $styleId . "' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Edit Color:  ".$colorId."'";
    $sql .= ")";
    if(!($audit = pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    pg_free_result($audit);
    echo json_encode([
        'message' => 'Color Added Successfully',
        'status' => true,
        'statusCode' => 200
    ]);
    return;
} catch(Exception $e){
    echo json_encode([
        'message' => $e->getMessage(),
        'status' => false,
        'statusCode' => 500
    ]);
    return;
}


?>