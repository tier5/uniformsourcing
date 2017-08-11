<?php
require('Application.php');
extract($_POST);
try {
    $temp = explode(".", $_FILES["file"]["name"]);
    if( $temp[1] == 'gif' || $temp[1] == 'png' || $temp[1] == 'jpg' ){
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["file"]["tmp_name"], "../../../uploadFiles/inventory/images/ " . $newfilename);
        $sql = '';
        $sql = 'INSERT INTO "tbl_colorTemp" ('.
            'name, path ) VALUES (\''.$name.'\',\''.$newfilename.'\') RETURNING *';
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'status' => false,
                'statusCode' => 500
            ]);
            return;
        }
        $data=pg_fetch_array($result);
        pg_free_result($result);
        echo json_encode([
            'message' => 'Color Uploaded',
            'status' => true,
            'data' => $data,
            'statusCode' => 200
        ]);
        return;
    } else {
        echo json_encode([
            'message' => 'Please Upload a Image',
            'status' => false,
            'statusCode' => 400
        ]);
        return;
    }
} catch(Exception $e){
    echo json_encode([
        'message' => $e->getMessage(),
        'status' => false,
        'statusCode' => 500
    ]);
    return;
}


?>