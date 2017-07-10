<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$boxId.' and "colorId"='.$colorId.' and "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$unit = pg_fetch_array($result);
pg_free_result($result);
if($unit != ''){
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$unit['id'];
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    while ($row = pg_fetch_array($result)){
        $quantity[] = $row;
    }
    pg_free_result($result);
    $total = 0;
    foreach ($quantity as $value){
        $total = $total + $value['qty'];
    }
    if($total > 0){
        echo json_encode([
            'message' => 'Box is not empty ! Please empty the box First..',
            'success' => false,
            'code' => 400
        ]);
        return;
    } else {
        if(count($quantity) > 0){
            foreach ($quantity as $qty){
                $sql = '';
                $sql = 'DELETE FROM "tbl_invQuantity" WHERE id='.$qty['id'];
                if(!($result=pg_query($connection,$sql))){
                    echo json_encode([
                        'message' => pg_last_error($connection),
                        'success' => false,
                        'code' => 500
                    ]);
                    return;
                }
            }
        }
        $sql = '';
        $sql = 'DELETE FROM "tbl_invUnit" WHERE id='.$boxId.' and "colorId"='.$colorId.' and "styleId"='.$styleId;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        echo json_encode([
            'message' => 'Unit Deleted Successfully',
            'success' => true,
            'code' => 202
        ]);
        return;
    }
} else {
    echo json_encode([
        'message' => 'Box Not Found',
        'success' => false,
        'code' => 404
    ]);
    return;
}
?>