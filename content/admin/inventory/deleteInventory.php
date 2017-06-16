<?php
require('Application.php');

$sql = "SELECT * FROM \"tbl_invStorage\" WHERE unit='".$_POST['unitId']."' and \"styleId\"='".$_POST['styleId']."' ";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_storage[] = $row;
}
if(count($data_storage) > 0){
    $count = 0;
    foreach ($data_storage as $key => $val){
        $count = $count+$val['wareHouseQty'];
    }
    if($count == 0){
        for ($i=0;$i<count($data_storage);$i++){
            $query = 'DELETE FROM "tbl_invStorage" WHERE "storageId"='.$data_storage[$i]['storageId'];
            if (!($resultProduct = pg_query($connection, $query))) {
                print("Failed invQuery: " . pg_last_error($connection));
                exit;
            }
            $row = pg_fetch_array($resultProduct);
            $data_str = $row;
        }
        $sql = '';
        $sql = "INSERT INTO \"audit_logs\" (";
        $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
        $sql .= " \"log\") VALUES (";
        $sql .= " '".$_POST['styleId']."' ";
        $sql .= ", '". $_SESSION['employeeID'] ."'";
        $sql .= ", '". date('U') ."'";
        $sql .= ", 'Delete Box: ".$_POST['unitId']."'";
        $sql .= ")";
        if(!($audit = pg_query($connection,$sql))){
            $return_arr['error'] = pg_last_error($connection);
        }
        echo 1;//Deleted
        exit;
    } else {
        echo 2;//Please Empty the table first
        return;
    }
} else {
    echo 3;//No Result Found
    return;
}
?>