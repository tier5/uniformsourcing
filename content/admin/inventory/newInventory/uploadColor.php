<?php
require('Application.php');
extract($_POST);
try {
    //$temp = explode(".", $_FILES["file"]["name"]);
    $temp = explode("/", $_FILES["file"]["type"]);
  

    $bytes = $_FILES["file"]['size'] / 1024;
    $imageSize = round($bytes, 0);

    if($_FILES["file"]['size'] == '')
    {
         echo json_encode([
            'message' => " Image size cannot be more than 2MB ",
            'status' => false,
            'statusCode' => 400
        ]);
        return;

    }

    if($imageSize > 2052)
    {
         echo json_encode([
            'message' => " Image size cannot be more than 2MB ",
            'status' => false,
            'statusCode' => 400
        ]);
        return;

    }
    
    //return;
    if( $temp[1] == 'gif' || $temp[1] == 'png' || $temp[1] == 'jpg' || $temp[1] == 'jpeg' ){
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["file"]["tmp_name"], "../../../uploadFiles/inventory/images/" . $newfilename);
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
            'message' => 'Please upload a proper gif/png/jpg/jpeg type image.',
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