<?php
require('Application.php');
extract($_POST);
$sql = '';
$sql = "SELECT * FROM \"locationDetails\" WHERE \"locationId\"='".$id."'";
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt)) {
    $location[]=$row_cnt;
}
pg_free_result($result_cnt);
$result = '';
if(count($location) > 0){
    foreach ($location as $key => $value){
        if($value['warehouse'] != ''){
            $result .= '<option value="'.$value['id'].'">'.$value['warehouse'].'</option>';
        }
        if($value['container'] != ''){
            $result .= '<option value="'.$value['id'].'">'.$value['container'].'</option>';
        }
        if($value['conveyor'] != ''){
            $result .= '<option value="'.$value['id'].'">'.$value['conveyor'].'</option>';
        }
    }
    echo $result;
    return;
} else {
    echo '0';
    return;
}

?>