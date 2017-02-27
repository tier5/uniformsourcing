<?php
require('Application.php');
$qr='select ship.shipping_id,ship.pid from tbl_newproject as np
 left join  tbl_prjorder_shipping as ship on np.pid=ship.pid ';
//echo $qr;
  if (!($r = pg_query($connection, $qr))) {
            print("Failed query1: " . pg_last_error($connection));
            exit();
              }
 $proj_list=array();             
    while($row2 = pg_fetch_array($r))
    { 
   $proj_list[]=$row2;     
    }         
 pg_free_result($r);
 
 
  if(count($proj_list)>0)
 {
      $qName=''; 
 for($i=0;$i<count($proj_list);$i++)
 {
 if($proj_list[$i]['pid']!=''&&$proj_list[$i]['shipping_id']!='')    
  $qName.=';update tbl_prjorder_track_no  set pid='.$proj_list[$i]['pid'].' where shipping_id='.$proj_list[$i]['shipping_id'];   
 }
  }
 
  
 // echo $qName;
  if (!($result = pg_query($connection, $qName)))
        {
      echo pg_last_error($connection);
           // return;
        } 
        else echo 'OK'; 
?>