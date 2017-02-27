<?php

require('Application.php');
$is_session = 0;
$emp_type = "";
$emp_id = "";
$ret=array();
$ret['html']="";
extract($_POST);



$sql = 'select style_cust.pull_from_inv,style_cust.inv_pull_qty,style1."scaleNameId",style_cust.length as length_id ,style_cust.height  as height_id ,style_cust.size as size_id , style_cust.style_id as cust_id,style."styleNumber" as style,style1."styleNumber",color."name", color."colorId",'
. '(select "scaleSize" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.size) as size, '
. '(select "opt1Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.length) as length, '
. '(select "opt2Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.height) as height, style_cust.quantity'
. ' from "tbl_prj_style_custom" as style_cust left join "tbl_invStyle" as style on style."styleId"=style_cust."style" '
. ' left join "tbl_invStyle" as style1  on style1."styleId"=style_cust."style" left join "tbl_invColor" as color on color."colorId" =style_cust."color" where style_cust.pid='.$pid;
//echo $sql;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_cust_inv[] = $row;
}
pg_free_result($result);
//print_r($data_cust_inv);
       


//echo "cnt->".count($data_cust_inv);
$table_inv="";
$table_order="";
$tbl = '<table cellpadding="1" cellspacing="1"  width="100%"  > ';

$table .= '<tr ><td class="gridHeader">Style1</td>';
$table .= '<td class="gridHeader">Color</td>';
$table .= '<td class="gridHeader">Size</td>';
$table.= '<td class="gridHeader">Length</td>';
$table .= '<td class="gridHeader">Height</td>';
$table.= '<td class="gridHeader">Quantity</td></tr>';
//$table=$table_inv;

for($i=0;$i<count($data_cust_inv);$i++)
   {
  //echo "hh".$data_cust_inv[$i]['pull_from_inv'];
if($data_cust_inv[$i]['pull_from_inv']==1 )
{
    
if( $data_cust_inv[$i]['inv_pull_qty']>0) 
{
    
 $table_inv.= '<tr><td class="grid001">' . $data_cust_inv[$i]['styleNumber'] . '</td>';
        $table_inv .= '<td class="grid001">' . $data_cust_inv[$i]['name'] . '</td>';
        if (isset($data_cust_inv[$i]['size']))
            $table_inv .= '<td class="grid001">' . $data_cust_inv[$i]['size'] . '</td>';
        else
            $table_inv .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['length']))
            $table_inv .= '<td class="grid001">' . $data_cust_inv[$i]['length'] . '</td>';
        else
            $table_inv .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['height']))
            $table_inv .= '<td  class="grid001">' . $data_cust_inv[$i]['height'] . '</td>';
        else
            $table_inv .= '<td class="grid001">&nbsp;</td>';
            $table_inv .= '<td class="grid001">' .$data_cust_inv[$i]['inv_pull_qty'] . '</td></tr>';
            
  if($data_cust_inv[$i]['inv_pull_qty']<$data_cust_inv[$i]['quantity'])          
  {
     
       $table_order.= '<tr><td class="grid001">' . $data_cust_inv[$i]['styleNumber'] . '</td>';
        $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['name'] . '</td>';
        if (isset($data_cust_inv[$i]['size']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['size'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['length']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['length'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['height']))
            $table_order .= '<td  class="grid001">' . $data_cust_inv[$i]['height'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
            $table_order .= '<td class="grid001">' .($data_cust_inv[$i]['quantity']-$data_cust_inv[$i]['inv_pull_qty']) . '</td></tr>';
      
  }
  
  
}
else
{
   $table_order.= '<tr><td class="grid001">' . $data_cust_inv[$i]['styleNumber'] . '</td>';
        $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['name'] . '</td>';
        if (isset($data_cust_inv[$i]['size']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['size'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['length']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['length'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['height']))
            $table_order .= '<td  class="grid001">' . $data_cust_inv[$i]['height'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
            $table_order .= '<td class="grid001">' .$data_cust_inv[$i]['quantity'] . '</td></tr>';
}
}
else
{
 $table_order.= '<tr><td class="grid001">' . $data_cust_inv[$i]['styleNumber'] . '</td>';
        $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['name'] . '</td>';
        if (isset($data_cust_inv[$i]['size']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['size'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['length']))
            $table_order .= '<td class="grid001">' . $data_cust_inv[$i]['length'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_cust_inv[$i]['height']))
            $table_order .= '<td  class="grid001">' . $data_cust_inv[$i]['height'] . '</td>';
        else
            $table_order .= '<td class="grid001">&nbsp;</td>';
            $table_order .= '<td class="grid001">' .$data_cust_inv[$i]['quantity'] . '</td></tr>';
}
  
}
$html="";
if($table_inv!="")
    $html.=$tbl."<tr><td colspan='6'> PULL FROM INVENTORY</td></tr>".$table.$table_inv."</table>";
if($table_order!="")
    $html.="<br/>".$tbl."<tr><td colspan='6'> PIECES TO BE ORDERED/SUBSTITUTED</td></tr>".$table.$table_order."</table>";

$ret['html']=$html;
header('Content-type: application/json'); 
echo json_encode($ret);
?>