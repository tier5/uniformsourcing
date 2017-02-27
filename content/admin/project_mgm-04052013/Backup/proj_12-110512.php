<?php

require('Application.php');
$is_session = 0;
$emp_type = "";
$emp_id = "";
extract($_POST);
$return_arr = array();
$data_order = array();
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['html'] = "";
if(!isset($pid) || $pid <= 0){
    $return_arr['error'] = 'Please save the project information first and click on inventory tab.';
    echo json_encode($return_arr);
    return;
}
$sql = 'select style1."scaleNameId",style_cust.length as length_id ,style_cust.height  as height_id ,style_cust.size as size_id , style_cust.style_id as cust_id,style."styleNumber" as style,style1."styleNumber",color."name", color."colorId",'
        . '(select "scaleSize" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.size) as size, '
        . '(select "opt1Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.length) as length, '
        . '(select "opt2Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.height) as height, style_cust.quantity'
        . ' from "tbl_prj_style_custom" as style_cust left join "tbl_invStyle" as style on style."styleId"=style_cust."style" '
        . ' left join "tbl_invStyle" as style1  on style1."styleId"=style_cust."style" left join "tbl_invColor" as color on color."colorId" =style_cust."color" where  pid =' . $pid;

if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_cust_inv[] = $row;
}

pg_free_result($result);

//print_r($data_cust_inv);
$sql = "";
for ($i = 0; $i < count($data_cust_inv); $i++) {
    if ($data_cust_inv[$i]['scaleNameId'] != "") {
        $sql = 'select "quantity" from "tbl_inventory" where   "scaleId"=' . $data_cust_inv[$i]['scaleNameId'];
       
        if ($data_cust_inv[$i]['size_id'] != "" && $data_cust_inv[$i]['size_id'] > 0)
            $sql.=' AND "sizeScaleId"=' . $data_cust_inv[$i]['size_id'];
       // else
        //     $sql.=' AND "sizeScaleId" = null';
        
          if ($data_cust_inv[$i]['colorId'] != "" && $data_cust_inv[$i]['colorId'] > 0)
            $sql.=' AND "colorId"=' . $data_cust_inv[$i]['colorId'];
      //  else
       //      $sql.=' AND "colorId" = null';
        
        
        if ($data_cust_inv[$i]['length_id'] != "" && $data_cust_inv[$i]['length_id'] > 0)
            $sql.=' AND "opt1ScaleId"=' . $data_cust_inv[$i]['length_id'];
      //  else
     // $sql.=' AND "opt1ScaleId" = null';
       
           
           if ($data_cust_inv[$i]['height_id'] != "" && $data_cust_inv[$i]['height_id'] > 0)
            $sql.=' AND "opt2ScaleId"=' . $data_cust_inv[$i]['height_id'];
         //  else
       // $sql.=' AND "opt2ScaleId" = null';
    }
 //   echo $sql."<br/>";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    if (isset($row['quantity']) && $row['quantity'] > 0) {
        $data_order[$i] = $row['quantity'];
    }
    else
        $data_order[$i] = 0;
//$data_order[]=$quantity-$data_cust_inv[$i]['quantity'];
}
//print_r($data_order);
$html = '<br/>';
$html .='<input type="button" value="Pull From Inventory" onclick="javascript:pullFromInvButton()"/>';
$html .= '<div id="inv_content">';
$tbl='<table cellpadding="1" cellspacing="1"  width="100%"  >';

$table .= '<tr ><th class="gridHeader">Style</th>';
$table .= '<th class="gridHeader">Color</th>';
$table .= '<th class="gridHeader">Size</th>';
$table .= '<th class="gridHeader">Length</th>';
$table .= '<th class="gridHeader">Height</th>';
$table .= '<th class="gridHeader">Quantity</th</tr>';

$count = count($data_cust_inv);
$inv_list = '';
$order_list = '';
$total_inv=0;
$total_order=0;
for ($i = 0; $i < $count; $i++) {

   
        
        $inv_list .= '<tr style="font-weight:normal;"><td class="grid001">' . $data_cust_inv[$i]['styleNumber'] . '</td>';
        $inv_list .= '<td class="grid001">' . $data_cust_inv[$i]['name'] . '</td>';
        if (isset($data_cust_inv[$i]['size']))
            $inv_list .= '<td class="grid001">' . $data_cust_inv[$i]['size'] . '</td>';
        else
            $inv_list .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['length']))
            $inv_list .= '<td class="grid001">' . $data_cust_inv[$i]['length'] . '</td>';
        else
            $inv_list .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['height']))
            $inv_list .= '<td  class="grid001">' . $data_cust_inv[$i]['height'] . '</td>';
        else
            $inv_list .= '<td class="grid001">&nbsp;</td>';
            $inv_list .= '<td class="grid001">' . $inv_qty . '</td></tr>';
    }
  $html.="<br/>".$tbl."<tr><td colspan='6'>PIECES TO BE ORDERED/SUBSTITUTED</td></tr>".$table.$inv_list;
 $html.='</div>';
//echo $production;
$return_arr['html'] = $html;
echo json_encode($return_arr);
return;
?>