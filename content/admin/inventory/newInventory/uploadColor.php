<?php
require('Application.php');
extract($_POST);
try {
            dd($_FILES);
    $temp = explode(".", $_FILES["file"]["name"]);
    $newfilename = round(microtime(true)) . '.' . end($temp);
    move_uploaded_file($_FILES["file"]["tmp_name"], "../img/imageDirectory/" . $newfilename);
    $sql = '';
    $sql = 'INSERT INTO "tbl_colorTemp" ('.
        'name, path ) VALUES (\''.$name.'\',\''.$file.'\')';
    if(!($result=pg_query($connection,$sql))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    $data=$row;
    return 1;
} catch(Exception $e){
    return $e->getMessage();
}


?>