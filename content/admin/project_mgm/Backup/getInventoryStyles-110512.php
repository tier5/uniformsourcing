<?php

require('Application.php');
extract($_POST);

$ret_arr = array();
$ret_arr['error'] = "";
$ret_arr['success'] = "";
$ret_arr['color'] = '';
$ret_arr['size'] = '';
$ret_arr['length'] = '';
$ret_arr['height'] = '';
$ret_arr['pid'] = 0;
$ret_arr['pid'] = $pid;
switch ($_GET['opt']) {
    case "style":
        $query1 = 'SELECT "styleId","styleNumber" from "tbl_invStyle" where "isActive"=1 order by "styleNumber"';
        if (!($result_cnt = pg_query($connection, $query1))) {
            echo json_encode("Failed query style inv: " . pg_last_error($connection));
            exit;
        }
        while ($row_cnt = pg_fetch_array($result_cnt)) {
            echo "<option value='" . $row_cnt['styleId'] . "'>" . $row_cnt['styleNumber'] . "</option>";
        }
        pg_free_result($result_cnt);
        break;

    case "all":
        if ($styleid > 0) {
            $query1 = 'SELECT "scaleNameId" from "tbl_invStyle" where "styleId"=' . $styleid;
            if (!($result_cnt = pg_query($connection, $query1))) {
                echo json_encode("Failed query style inv: " . pg_last_error($connection));
                exit;
            }
            $row2 = pg_fetch_array($result_cnt);
            $scaleId = $row2['scaleNameId'];
            pg_free_result($result_cnt);

            $query2 = 'Select "colorId",name from "tbl_invColor" where "styleId"=' . $styleid;
            // echo $query2;
            if (!($result2 = pg_query($connection, $query2))) {
                echo json_encode("Failed Option Query color: " . pg_last_error($connection));
                exit;
            }

            while ($row2 = pg_fetch_array($result2)) {
                $ret_arr['color'] .= "<option value='" . $row2['colorId'] . "'>" . $row2['name'] . "</option>";
            }
            pg_free_result($result2);

            $sql = 'select "sizeScaleId","scaleId","scaleSize" from "tbl_invScaleSize" where "scaleId"=\'' . $scaleId . '\' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\' order by "sizeScaleId", "scaleId"';
            if (!($result = pg_query($connection, $sql))) {
                print("Failed query1: " . pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result)) {
                $ret_arr['size'] .= "<option value='" . $row['sizeScaleId'] . "'>" . $row['scaleSize'] . "</option>";
            }
            pg_free_result($result);
            $sql = 'select "scaleId","sizeScaleId","opt1Size" from "tbl_invScaleSize" where "scaleId"=\'' . $scaleId . '\' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\'  order by "sizeScaleId", "scaleId"';
            if (!($result = pg_query($connection, $sql))) {
                print("Failed query1: " . pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result)) {
                $ret_arr['length'] .= "<option value='" . $row['sizeScaleId'] . "'>" . $row['opt1Size'] . "</option>";
            }
            pg_free_result($result);
            $sql = 'select "sizeScaleId","scaleId","opt2Size" from "tbl_invScaleSize" where "scaleId"=\'' . $scaleId . '\' and "opt2Size" IS NOT NULL  and "opt2Size" <>\'\' order by "sizeScaleId", "scaleId"';
            if (!($result = pg_query($connection, $sql))) {
                print("Failed query1: " . pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result)) {
                $ret_arr['height'] .= "<option value='" . $row['sizeScaleId'] . "'>" . $row['opt2Size'] . "</option>";
            }
            pg_free_result($result);
        }
        break;

    case "delete":
        $query_Name.="DELETE from tbl_prj_style_custom where style_id=" . $style_id;
        if ($query_Name != "") {
            // echo $query_Name;
            if (!($result = pg_query($connection, $query_Name))) {
                $ret_arr['error'] = "Error while mutiple custom style information to database!";
                echo json_encode($ret_arr);
                return;
            }

            $query_Name = "";
            pg_free_result($result);
              $ret_arr["success"]="Style information deleted from  project";
        }

        break;

    case "save":
        if (isset($pid) && $pid > 0) {
            $query_Name.='SELECT  "style_id" FROM "tbl_prj_style_custom"  ';

            $q_add = "";
            if (isset($style) || $style != '' || $style != null || $style != 'null') {

                if ($q_add == "")
                    $q_add.=' style=' . $style;
                else
                    $q_add.=' AND style=' . $style;
            }
            if (isset($color) || $color != '' || $color != null || $color != 'null') {

                if ($q_add == "")
                    $q_add.=' color=' . $color;
                else
                    $q_add.=' AND color=' . $color;
            }
            if ((isset($size) || $size != '' || $size != null || $size != 'null')) {

                if ($q_add == "")
                    $q_add.=' size=' . $size;
                else
                    $q_add.=' AND size=' . $size;
            }

            if (isset($length) || $length != '' || $length != null || $length != 'null') {
                // $query_Name.=' AND   length='.$length;  
                if ($q_add == "")
                    $q_add.=' length=' . $length;
                else
                    $q_add.=' AND length=' . $length;
            }

            if (isset($height) || $height != '' || $height != null || $height != 'null') {
                // $query_Name.=' AND   height='.$height;  
                if ($q_add == "")
                    $q_add.=' height=' . $height;
                else
                    $q_add.=' AND height=' . $height;
            }

            if ($q_add != "")
                $query_Name.=" WHERE " . $q_add . " AND pid=" . $pid;

            if ($query_Name != "") {
                // echo $query_Name;
                if (!($result1 = pg_query($connection, $query_Name))) {
                    $ret_arr['error'] = "Error while adding custom style information to database!";
                    echo json_encode($ret_arr);
                    return;
                }
                $query_Name = "";
                $row1 = pg_fetch_array($result1);
                //  print_r($result1);
                pg_free_result($result1);
            }
           
            if (isset($row1['style_id']) && $row1['style_id'] > 0) {
                $query_Name = 'UPDATE "tbl_prj_style_custom" SET ';
                $q_add = "";
                if ((isset($size) || $size != '' || $size != null || $size != 'null')) {
                    if ($q_add == "")
                        $q_add.=' "size"=' . $size;
                    else
                        $q_add.=' ,"size"=' . $size;
                }
                if (isset($color) || $color != '' || $color != null || $color != 'null') {
                    //$query_Name.=' "color"='.$color;
                    if ($q_add == "")
                        $q_add.=' "color"=' . $color;
                    else
                        $q_add.=' ,"color"=' . $color;
                }

                if (isset($length) || $length != '' || $length != null || $length != 'null') {
                    // $query_Name.=' "length"='.$length;
                    if ($q_add == "")
                        $q_add.=' "length"=' . $length;
                    else
                        $q_add.=' ,"length"=' . $length;
                }
                if (isset($height) || $height != '' || $height != null || $height != 'null') {
                    //  $query_Name.=' "height"='.$height;
                    if ($q_add == "")
                        $q_add.=' "height"=' . $height;
                    else
                        $q_add.=' ,"height"=' . $height;
                }

                if (isset($qty) && $qty != "") {
                    //  $query_Name.=' "height"='.$height;
                    if ($q_add == "")
                        $q_add.=' "quantity"=' . $qty;
                    else
                        $q_add.=' ,"quantity"=' . $qty;
                }

                if ($q_add != "")
                    $query_Name.=$q_add;
                $query_Name.=' ,"updateddate"=' . date('U') . ' WHERE "style_id"=' . $row1['style_id'];
                // echo $query_Name;
                $ret_arr["success"]="Style information updated with new quantity";
            }
            else {
                $query_Name = "INSERT INTO tbl_prj_style_custom (";
                $query_Name.=" pid";
                $query_Name.= ", style";
                if (!isset($size) || $size == '' || $size == null || $size == 'null')
                    $size = 0;
                if (!isset($length) || $length == '' || $length == null || $length == 'null')
                    $length = 0;
                if (!isset($height) || $height == '' || $height == null || $height == 'null')
                    $height = 0;
                if ($color != "") {

                    $query_Name.= ", color ";
                }
                if ($size > 0) {

                    $query_Name.= ", size";
                }
                if ($length > 0) {

                    $query_Name.= ", length";
                }

                if ($height > 0) {

                    $query_Name.= ", height";
                }


                if ($qty != "") {
                    $query_Name.= ", quantity";
                }

                $query_Name.=" ,createddate";
                $query_Name.=" ) VALUES(";
                $query_Name.=" '$pid' ";
                $query_Name.=", '$style' ";
                if ($color != "")
                    $query_Name.=", '$color' ";
                if ($size != "")
                    $query_Name.=", '$size' ";
                if ($length != "")
                    $query_Name.=", '$length' ";
                if ($height != "")
                    $query_Name.=", '$height' ";

                if ($qty != "") {
                    $query_Name.=", '$qty' ";
                }

                $query_Name.=" ," . date('U');
                $query_Name.=" );";
                $ret_arr["success"]="Style information added to project";
            }

            if ($query_Name != "") {
                //  echo $query_Name;
                if (!($result = pg_query($connection, $query_Name))) {
                    $ret_arr['error'] = "Error while adding custom style information to database!";
                    $ret_arr["success"]="";
                    echo json_encode($ret_arr);
                    return;
                }                
                $query_Name = "";
                pg_free_result($result);
            }
        }
        else
            $ret_arr["error"] = "Please save the project before adding style information.";
        break;
        
        
    case "pull_from_inv":
        
        $sql = 'select style1."scaleNameId",style_cust.length as length_id ,style_cust.height  as height_id ,style_cust.size as size_id , style_cust.style_id as cust_id,style."styleNumber" as style,style1."styleNumber",color."name", color."colorId",'
        . '(select "scaleSize" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.size) as size, '
        . '(select "opt1Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.length) as length, '
        . '(select "opt2Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.height) as height, style_cust.quantity'
        . ' from "tbl_prj_style_custom" as style_cust left join "tbl_invStyle" as style on style."styleId"=style_cust."style" '
        . ' left join "tbl_invStyle" as style1  on style1."styleId"=style_cust."style" left join "tbl_invColor" as color on color."colorId" =style_cust."color" where style_cust.style_id=' . $style_id;
//echo $sql;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_cust_inv = $row;
}

pg_free_result($result);
        
           if ($data_cust_inv['scaleNameId'] != "") {
        $sql = 'select "quantity" from "tbl_inventory" where   "scaleId"=' . $data_cust_inv['scaleNameId'];
       
        if ($data_cust_inv['size_id'] != "" && $data_cust_inv['size_id'] > 0)
            $sql.=' AND "sizeScaleId"=' . $data_cust_inv['size_id'];
       // else
        //     $sql.=' AND "sizeScaleId" = null';
        
          if ($data_cust_inv['colorId'] != "" && $data_cust_inv['colorId'] > 0)
            $sql.=' AND "colorId"=' . $data_cust_inv['colorId'];
      //  else
       //      $sql.=' AND "colorId" = null';
        
        
        if ($data_cust_inv['length_id'] != "" && $data_cust_inv['length_id'] > 0)
            $sql.=' AND "opt1ScaleId"=' . $data_cust_inv['length_id'];
      //  else
     // $sql.=' AND "opt1ScaleId" = null';
       
           
           if ($data_cust_inv['height_id'] != "" && $data_cust_inv['height_id'] > 0)
            $sql.=' AND "opt2ScaleId"=' . $data_cust_inv['height_id'];
         //  else
       // $sql.=' AND "opt2ScaleId" = null';
    }
 //   echo $sql."<br/>";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    pg_free_result($result);
 $inv_quantity=$row['quantity'];
 
 
 
       
        $sql = 'select "inv_pull_qty" from "tbl_prj_style_custom" where ';
       $sql_add="";
        if ($data_cust_inv['size_id'] != "" && $data_cust_inv['size_id'] > 0)
        {
            if($sql_add=="")
                $sql_add.='  "size"=' . $data_cust_inv['size_id'];
                else
            $sql_add.=' AND "size"=' . $data_cust_inv['size_id'];
        }
        
          if ($data_cust_inv['colorId'] != "" && $data_cust_inv['colorId'] > 0)
          {
               if($sql_add=="")
                $sql_add.='  "color"=' . $data_cust_inv['colorId'];
                else
            $sql_add.=' AND "color"=' . $data_cust_inv['colorId'];
          }
        
        
        if ($data_cust_inv['length_id'] != "" && $data_cust_inv['length_id'] > 0)
        {
             if($sql_add=="")
                $sql_add.=' "length"=' . $data_cust_inv['length_id'];
                else
            $sql_add.=' AND "length"=' . $data_cust_inv['length_id'];
    
        }
           
           if ($data_cust_inv['height_id'] != "" && $data_cust_inv['height_id'] > 0)
           {
                if($sql_add=="")
                $sql_add.='  "height"=' . $data_cust_inv['height_id'];
                else
            $sql_add.=' AND "height"=' . $data_cust_inv['height_id'];
           }
      
$sql.=$sql_add;
//echo $sql;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while($row_inv = pg_fetch_array($result))
    {
  $inv_qty[]= $row_inv;    
    }
     pg_free_result($result);
   // for($i=0; $inv_qty[$i]['quantity']!=""; $i++){
        for($i=0; $i< count($inv_qty); $i++){
        $inv_quantity-=$inv_qty[$i]['inv_pull_qty'];
  // echo $inv_qty[$i]['quantity']."hh<br/>";
    
    }
 // echo $inv_quantity;
        if (isset($inv_quantity) && $inv_quantity > 0) {
            $qty=$inv_quantity - $data_cust_inv['quantity'];
            if($qty>0)
        $data_qty = $data_cust_inv['quantity'];
    else 
        $data_qty=$data_cust_inv['quantity']+$qty;
    }
    else
        $data_qty = 0;
                
      echo "inv->".$qty;  
        
        
        
        
        
         if (isset($style_id) && $style_id != "") {
             
                $query_Name = 'UPDATE "tbl_prj_style_custom" SET ';
               $query_Name .= ' pull_from_inv=1';
                
             $query_Name .= ' ,	inv_pull_qty='.$data_qty;
              
              $query_Name .= ' where style_id='.$style_id;
                
            if ($query_Name != "") {
                //  echo $query_Name;
                if (!($result = pg_query($connection, $query_Name))) {
                    $ret_arr['error'] = "Error while adding custom style information to database!";
                    $ret_arr["success"]="";
                    echo json_encode($ret_arr);
                    return;
                }                
                $query_Name = "";
                pg_free_result($result);
            }        
         }
        
        break;
        
            case "leave_from_inv":
        
         if (isset($style_id) && $style_id != "") {
                $query_Name = 'UPDATE "tbl_prj_style_custom" SET ';
               $query_Name .= ' pull_from_inv=0';
                
             $query_Name .= ' ,	inv_pull_qty=0';
              
              $query_Name .= ' where style_id='.$style_id;
                
            if ($query_Name != "") {
                //  echo $query_Name;
                if (!($result = pg_query($connection, $query_Name))) {
                    $ret_arr['error'] = "Error while adding custom style information to database!";
                    $ret_arr["success"]="";
                    echo json_encode($ret_arr);
                    return;
                }                
                $query_Name = "";
                pg_free_result($result);
            }        
         }
        
        break;
}
echo json_encode($ret_arr);
return;
?>