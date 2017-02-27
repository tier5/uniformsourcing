<?php
require('Application.php');
$return_arr=array();
$return_arr['pack_id']="";
extract($_POST);
  header('Content-type: application/json'); 
$q_add='';
if(isset($pack_id)&&$pack_id!=""&&$pack_id!='undefined')
    $q_add.=' and pack_id!='.$pack_id;
$q= 'SELECT  count(*) as count  FROM "img_file_pack" WHERE "pack_name"=\''.$pack_name.'\' '.$q_add ;
            if ($q != "") {
      
                if (!($result = pg_query($connection, $q))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                $row2=  pg_fetch_array($result);
                pg_free_result($result);
                $q = "";
            
       if($row2['count']>0)         
       {
     $return_arr['error'] = 'This package name is already exist.Please chose another one and continue...';
     echo json_encode($return_arr);
                
                    return;      
       }   
            }    


if(isset($pack_id) && trim($pack_id)!="" &&  $pack_id!=0 )
{
    
      $query= 'SELECT  count(*) as count FROM "img_file_pack" WHERE "pack_id"='.$pack_id ;
            if ($query != "") {
      
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                $row2=  pg_fetch_array($result);
                pg_free_result($result);
                $query = "";
            }     
    
    if($row2['count']>0)
    {
   $query.= 'UPDATE "img_file_pack" SET' ;
                if (isset($pack_name )&&$pack_name != "")
                    $query.="\"pack_name\"='".$pack_name."'";
                $query.=' WHERE "pack_id"='.$pack_id;
            if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }     
    }
    
    
}
    else{
 $query.= 'INSERT INTO "img_file_pack" (  "createddate" ';
                if (isset($pack_name )&&$pack_name != "")
                    $query.=' ,"pack_name"';
               
              
                $query.=") Values( ".date('U');
                 if (isset($pack_name )&&$pack_name != "")
                    $query.=" ,'".$pack_name."'";
               $query.=")";
                
    }
            if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
            if(isset($pack_id) && $pack_id!=""&& $pack_id!=0)
               $pack_id2= $pack_id;
            else
            {
            $query='SELECT max("pack_id") as pack_id FROM "img_file_pack" ';
             if ($query != "") {
               // echo $query;
                if (!($result1 = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                $row1=  pg_fetch_array($result1);
                pg_free_result($result1);
                $query = "";
            }
            
            
     $pack_id2=$row1['pack_id'];
            }
           $return_arr['pack_id']=$pack_id2;
    
    //delete the file items when update
       $query.= 'DELETE from "img_file_items" WHERE   "pack_id"='.$pack_id2;
                    
         //   echo $query;
            if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
    
            for($i=0;$i<count($upload_file);$i++)
            {
          $query.= 'INSERT INTO "img_file_items" (  "pack_id" ';
                if (isset($upload_file[$i] )&& $upload_file[$i] != "")
                    $query.=' ,"filename"';
                   if (isset($upload_type[$i] )&& $upload_type[$i] != "")
                    $query.=' ,"type"';
               
              
                $query.=") Values( '".$pack_id2."'";
               if (isset($upload_file[$i] )&& $upload_file[$i] != "")
                    $query.=" ,'".$upload_file[$i] ."'";
                   if (isset($upload_type[$i] )&& $upload_type[$i] != "")
                    $query.=" ,'".$upload_type[$i] ."'";
               $query.=")";
                
         //   echo $query;
            if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
            }

        
          if(isset($client)&& count($client)>0)  
          {
    $query= 'DELETE from "img_file_clients" WHERE   "pack_id"='.$pack_id2;
                    
        
    
            for($i=0;$i<count($client);$i++)
            {
              
           $query.= ';INSERT INTO "img_file_clients" (  "pack_id" ';
                if (isset($client[$i] )&& $client[$i] != "")
                    $query.=' ,"cid"';         
              
                $query.=") Values( '".$pack_id2."'";
               if (isset($client[$i] )&& $client[$i] != "")
                    $query.=" ,'".$client[$i] ."'";   
               $query.=")";
                
            
          
            }        
          //  echo $query;
              if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
          }
           
          
          if(isset($style)&& count($style)>0)  
          {
    $query= 'DELETE from "img_file_styles" WHERE   "pack_id"='.$pack_id2;
                    
        
    
            for($i=0;$i<count($style);$i++)
            {
              
           $query.= ';INSERT INTO "img_file_styles" (  "pack_id" ';
                if (isset($style[$i] )&& $style[$i] != "")
                    $query.=' ,"style"';         
              
                $query.=") Values( '".$pack_id2."'";
               if (isset($style[$i] )&& $style[$i] != "")
                    $query.=" ,'".$style[$i] ."'";   
               $query.=")";
                
            
          
            }        
          //  echo $query;
              if ($query != "") {
                //echo $query;
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
          }     
          
     
  echo json_encode($return_arr);          
?>