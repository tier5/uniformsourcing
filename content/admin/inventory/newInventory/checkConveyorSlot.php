<?php
require('Application.php');
//$returnArray['name'] = '';
extract($_POST);
$sql = '';
if($mode == 'add'){
    $sql = "SELECT id FROM \"tbl_invQuantity\" WHERE conveyor_slot='".$conveyorSlot."'";
    $result=pg_query($connection,$sql);
    $conveyorSlotResult = pg_fetch_array($result);
    pg_free_result($result);

    if(isset($conveyorSlotResult['id']) && !empty($conveyorSlotResult['id'])){
        echo json_encode([
            'message' => 'This Conveyor slot is taken',
            'success' => false,
            'info' => '',
            'code' => 500
        ]);
        return;
    }else{
        echo json_encode([
            'message' => 'This Conveyor slot is not taken',
            'success' => true,
            'info' => '',
            'code' => 200
        ]);
        return;
    }
}else if($mode == 'edit'){
    $sql = "SELECT id FROM \"tbl_invQuantity\" WHERE conveyor_slot='".$conveyorSlot."'";
    $result=pg_query($connection,$sql);
    $conveyorSlotResult = pg_fetch_array($result);
    pg_free_result($result);

    if(isset($conveyorSlotResult['id']) && !empty($conveyorSlotResult['id'])){
        echo json_encode([
            'message' => 'This Conveyor slot is taken',
            'success' => false,
            'info' => '',
            'code' => 500
        ]);
        return;
    }else{
        echo json_encode([
            'message' => 'This Conveyor slot is not taken',
            'success' => true,
            'info' => '',
            'code' => 200
        ]);
        return;
    }
}else{
    $sql = "SELECT id FROM \"tbl_invQuantity\" WHERE conveyor_slot='".$conveyorSlot."'";
    $result=pg_query($connection,$sql);
    $conveyorSlotResult = pg_fetch_array($result);
    pg_free_result($result);

    if(isset($conveyorSlotResult['id']) && !empty($conveyorSlotResult['id'])){
        echo json_encode([
            'message' => 'This Conveyor slot is taken',
            'success' => false,
            'info' => '',
            'code' => 500
        ]);
        return;
    }else{
        echo json_encode([
            'message' => 'This Conveyor slot is not taken',
            'success' => true,
            'info' => '',
            'code' => 200
        ]);
        return;
    }
}

?>