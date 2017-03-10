<?php
require('Application.php');
$query = 'UPDATE "tbl_invStorage" SET ';
$query .="room='".$_REQUEST['room']."'";
$query .=",rack='".$_REQUEST['rack']."'";
$query .=",shelf='".$_REQUEST['self']."'";
$query .=",row='".$_REQUEST['row']."' ";
$query .=",\"updatedDate\"='".date('U')."' ";
$query .=" where box='".$_REQUEST['boxId']."' and \"styleId\"='".$_REQUEST['styleId']."' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit;
?>