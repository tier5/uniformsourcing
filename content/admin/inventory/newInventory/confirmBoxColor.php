<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);

$sql = '';
$sql = 'SELECT * FROM "tbl_invStyle" WHERE "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$style = pg_fetch_array($result);
pg_free_result($result);
$sql = '';
$sql = 'SELECT * FROM "tbl_invColor" WHERE "colorId" = '.$colorId;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$color = pg_fetch_array($result);
pg_free_result($result);
if($color['styleId'] == $style['styleId']){
    if(isset($boxId) && $boxId != 0){
        $sql = '';
        $sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$boxId;
        $sql = 'SELECT * FROM "tbl_invColor" WHERE "colorId" = '.$colorId;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        $box = pg_fetch_array($result);
        if($box['styleId'] != $style['styleId']){
            echo json_encode([
                'message' => 'Box is not Available for this Style',
                'success' => false,
                'code' => 400
            ]);
            return;
        }
    }
    echo json_encode([
        'message' => 'Box and Color Available',
        'success' => true,
        'code' => 200
    ]);
    return;
} else {
    echo json_encode([
        'message' => 'Color Not Found For this Style Please Change The Color',
        'success' => false,
        'code' => 400
    ]);
    return;
}
?>