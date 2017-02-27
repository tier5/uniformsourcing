<?php
require('Application.php');
require 'curl_command.php';
$pid=$_POST['pid'];
$param=array();
//$param['project']=array();
$sql='select c.intranet from "clientDB" as c left join tbl_newproject as p on p.client=c."ID" where p.pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$r=pg_fetch_array($result);
$intranet=$r['intranet'];
$sql='select * from tbl_newproject where pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['project'] = pg_fetch_array($result);
pg_free_result($result);

if(isset($param['project']['client'])&&$param['project']['client']!='')
{
$sql='select * from "clientDB" where "ID"='.$param['project']['client']; 
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['client'] = pg_fetch_array($result);
pg_free_result($result);
    
}

if(isset($param['project']['project_manager1'])&&$param['project']['project_manager1']!='')
{
$sql='select * from "employeeDB" where "employeeID"='.$param['project']['project_manager1']; 
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['prj_mng1'] = pg_fetch_array($result);
pg_free_result($result);
    
}

if(isset($param['project']['project_manager2'])&&$param['project']['project_manager2']!='')
{
$sql='select * from "employeeDB" where "employeeID"='.$param['project']['project_manager2']; 
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['prj_mng2'] = pg_fetch_array($result);
pg_free_result($result);
    
}

if(isset($param['project']['project_manager'])&&$param['project']['project_manager']!='')
{
$sql='select * from "employeeDB" where "employeeID"='.$param['project']['project_manager']; 
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['prj_mng'] = pg_fetch_array($result);
pg_free_result($result);
    
}

//print_r($param);
$sql='select * from tbl_prjpurchase where pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['purchase'] = pg_fetch_array($result);
pg_free_result($result);

$sql='select * from tbl_prmilestone where pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$param['milestone'] = pg_fetch_array($result);
pg_free_result($result);

$sql='select * from tbl_mgt_notes where pid='.$pid.' and (prj_note_type=\'ext\' or prj_note_type=\'e\')';
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind=0;
while($row=pg_fetch_array($result)){
$param['note_'.$ind] = $row;
$ind+=1;
}
pg_free_result($result);

$sql='select * from tbl_prj_style where pid='.$pid.' and status =1';
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind=0;
while($row=pg_fetch_array($result)){
$param['style_'.$ind] = $row;
$ind+=1;
}
pg_free_result($result);

$sql='select * from tbl_prjorder_shipping where pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind=0;
while($row=pg_fetch_array($result)){
$param['ship_'.$ind] = $row;
$q = 'select tracking_no,track_id from tbl_prjorder_track_no where shipping_id=' . $row['shipping_id'];
if (!($r = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind2=0;
 while ($r1=pg_fetch_array($r))
            {
  $param['track_'.$ind.'_'.$ind2]=$r1;  
  $ind2+=1;
            }

$ind+=1;
}
pg_free_result($result);


$sql='select * from tbl_prjimage_file where pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind=0;
while($row=pg_fetch_array($result)){
$param['img_'.$ind] = $row;
$ind+=1;
}
pg_free_result($result);

$sql='SELECT  list.pack_id,pack.* from "tbl_upload_pack" as list left join img_file_pack as pack on pack.pack_id=list.pack_id  where  upload_pack_u=1 AND pid='.$pid;
if (!($result = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind=0;
while($row=pg_fetch_array($result)){
$param['upld_'.$ind] = $row;
$q = 'select * from img_file_items where pack_id=' . $row['pack_id'];
if (!($r = pg_query($connection, $q))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$ind2=0;
 while ($r1=pg_fetch_array($r))
            {
  $param['upldfl_'.$ind.'_'.$ind2]=$r1;  
  $ind2+=1;
            }

$ind+=1;
}
pg_free_result($result);


//print_r($param);



//print_r($param['note']);
//if($intranet=='')$intranet='http://internaldev.ariauniforms.com';
if($intranet!=''){
 if(trim($intranet)=='http://internaldev.ariauniforms.com' || trim($intranet)=='http://internaldev.ariauniforms.com/'||
   trim($intranet)=='http://internaldev.mbuniforms.com' || trim($intranet)=='http://internaldev.mbuniforms.com/'||
   trim($intranet)=='http://internal.mbuniforms.com' || trim($intranet)=='http://internal.mbuniforms.com/'||
   trim($intranet)=='http://internal.ariauniforms.com' || trim($intranet)=='http://internal.ariauniforms.com/')  
 {   
  $res= curl_command($intranet.'/curl_socket/push.php', 50, $param) ;
  echo $res;
 }
 else
 {
 echo '<div class=\'errorMessage\'><strong>Push Feature Not Implemented For This Client...</strong></div>';    
 }
  
}
else{ echo '<div class=\'errorMessage\'><strong>Fill the intranet field in client section...</strong></div>';}
?>