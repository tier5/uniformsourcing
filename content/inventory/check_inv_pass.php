<?php
require('Application.php');
$pass=$_REQUEST['pass'];
$ret=array('stat'=>'');
//echo 'HH'.$pass.' '.$inv_pass;
if($pass==$inv_pass)
{
$ret['stat']='true';
$_SESSION['inv_pass']=$inv_pass;
}
header('content-type:application/json');
echo json_encode($ret);
?>