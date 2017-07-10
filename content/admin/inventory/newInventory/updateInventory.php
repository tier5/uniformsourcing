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
    foreach ($qty as $q){
        if($q < 0){
            echo json_encode([
                'message' => 'Negative values not allowed',
                'success' => false,
                'code' => 400
            ]);
            return;
        }
    }
    $count = 0;
    foreach ($is_change as $key=>$change){
        if($change == 1){
            $mainSize = $mainSizeId[$key];
            $optSize = $optSizeId[$key];
            $sql = '';
            $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$unit['id'].' and "mainSizeId"='.$mainSize.' and "optSizeId"='.$optSize;
            if(!($result=pg_query($connection,$sql))){
                echo json_encode([
                    'message' => pg_last_error($connection),
                    'success' => false,
                    'code' => 500
                ]);
                return;
            }
            $quantity = pg_fetch_array($result);
            if($quantity != null){
                if($qty[$key] != $quantity['qty']){
                    $sql = '';
                    $sql = 'UPDATE "tbl_invQuantity" SET ';
                    $sql .= ' qty='.$qty[$key];
                    $sql .= ' WHERE id='.$quantity['id'];
                    $count++;
                }
            } else {
                $sql = '';
                $sql = 'INSERT INTO "tbl_invQuantity" ( ';
                $sql .= '"boxId","mainSizeId","optSizeId","qty" ) VALUES (';
                $sql .= " '".$unit['id']."','".$mainSize."','".$optSize."','".$qty[$key]."' ) ";
                $count++;
            }
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
    if($count > 0 ){
        echo json_encode([
            'message' => 'Box Updated Successfully',
            'success' => true,
            'code' => 200
        ]);
        return;
    } else {
        echo json_encode([
            'message' => 'Box Already Updated',
            'success' => true,
            'code' => 202
        ]);
        return;
    }
} else {
    echo json_encode([
        'message' => 'Box Not Found',
        'success' => false,
        'code' => 500
    ]);
    return;
}
?>