<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$currentBox;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$current = pg_fetch_array($result);
pg_free_result($result);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$targetBox;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$target = pg_fetch_array($result);
pg_free_result($result);
if($target != '' && $current != ''){
    /*
     * Code for Merge 2 Box
     */
} else {
    echo json_encode([
        'message' => 'Box Not Found',
        'success' => false,
        'code' => 400
    ]);
    return;
}
?>