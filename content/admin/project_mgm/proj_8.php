<?php

require('Application.php');
require($JSONLIB . 'jsonwrapper.php');
$error      = "";
$msg        = "";
$html       = "";
$return_arr = array();
extract($_POST);
$tx='';
if(isset($close))
$tx='_closed';

$return_arr['error'] = "";
$return_arr['name']  = "";
$return_arr['html']  = "";
$isEdit              = 0;
$is_session          = 0;
$emp_type            = "";
$emp_id              = "";
if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1))
{
    $emp_type    = $_SESSION['employeeType'];
    $emp_id      = $_SESSION['employee_type_id'];
    $is_session  = 1;
    $style_price = ' style="visibility:hidden"';
}
else if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 2))
{
    $emp_type    = $_SESSION['employeeType'];
    $emp_id      = $_SESSION['employee_type_id'];
    $is_session  = 1;
    $style_price = ' disabled="disabled"';
}

if (isset($_POST['pid']) && $_POST['pid'] != 0)
{
    $isEdit = 1;
    $sql    = "Select * from tbl_newproject$tx where  pid = $pid";
    //echo $sql;
    if (!($result = pg_query($connection, $sql)))
    {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result))
    {
        $data_prj = $row;
    }
    pg_free_result($result);
    $sql      = "select tbl_prjorder_shipping$tx.*,tbl_carriers.weblink from  tbl_prjorder_shipping$tx left join tbl_carriers on tbl_carriers.carrier_id = tbl_prjorder_shipping$tx.carrier_id where tbl_prjorder_shipping$tx.status=1 and pid = $pid";
    //echo $sql;
    //$queryTester = $sql;
    if (!($result   = pg_query($connection, $sql)))
    {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result))
    {
        $data_order_shipping[] = $row;
    }
    pg_free_result($result);

    $track_no = array();

    for ($i = 0; $i < count($data_order_shipping); $i++)
    {//echo $i."<br/>";
        if (isset($data_order_shipping[$i]['shipping_id']) && $data_order_shipping[$i]['shipping_id'] != "")
        {

            $track_no[$i] = array();
            $q      = 'select tracking_no,track_id from tbl_prjorder_track_no'.$tx.' where shipping_id=' . $data_order_shipping[$i]['shipping_id'];
            $result = pg_query($connection, $q);
            while ($row    = pg_fetch_array($result))
            {
                $track_no[$i][] = $row;
            }
        }
    }
    //print_r($track_no);  
    
    
    
    
    $datalist = array();

    for ($i = 0; $i < count($data_order_shipping); $i++)
    {//echo $i."<br/>";
        if (isset($data_order_shipping[$i]['shipping_id']) && $data_order_shipping[$i]['shipping_id'] != "")
        {

            $datalist[$i] = array();
            $q      = 'select * from tbl_qty_shipped'.$tx.' where shipping_id=' . $data_order_shipping[$i]['shipping_id'].' order by prj_style_id';
            $result = pg_query($connection, $q);
            while ($row    = pg_fetch_array($result))
            {
                $datalist[$i][] = $row;
            }
        }
    }   
    
    
}

$sql    = "select carrier_id,carrier_name  from  tbl_carriers where status=1";
if (!($result = pg_query($connection, $sql)))
{
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result))
{
    $data_carrier[] = $row;
}
pg_free_result($result);


 $sql2 = "select * from tbl_prj_style$tx  where status =1 and pid = $pid order by prj_style_id";
	if(!($result=pg_query($connection,$sql2))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_style[]=$row;
	}
        


$sql    = "select shiponclient  from  tbl_prj_sample$tx where pid = $pid";
$result = pg_query($connection, $sql);
$row    = pg_fetch_array($result);
$shipon = $row['shiponclient'];


$html = '<table width="80%" cellspacing="1" cellpadding="1" border="0">
			<tr>
			<strong>Left To Ship</strong>
				<td class="gridHeader">Style:</td>
				<td class="gridHeader">Size:</td>
				<td class="gridHeader">Qty Ordered:</td>
				<td class="gridHeader">Left To Ship:</td>
				</tr>';
	for($i=0; $i<count($data_prj_style); $i++){
$html .='<tr>
				<td class="grid001">'.htmlentities( $data_prj_style[$i]['style']).'</td>
				<td class="grid001">'.htmlentities( $data_prj_style[$i]['vendor_style']).'</td>
				<td class="grid001">'.htmlentities( $data_prj_style[$i]['garments']).'</td>
				<td id="left_ship_'.$i.'" class="grid001"></td>
		</tr>';
	}
		'</table><br><br>';



$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center" scope="col"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="3" align="center"><table id="tbl_carrier" width="80%" cellpadding="0" cellspacing="0">
            <tr>
              <td width="50%" height="25" align="right">Order Placed On:</td>
              <td>&nbsp;</td>
              <td width="49%" align="left" valign="top"><input type="text" id="order_on" onclick="javascript:showDate(this);" ';
if ($emp_type > 0)
{
    $html .= 'disabled="disabled"';
}
$html .= 'name="order_on" value="' . $data_prj['order_placeon'] . '"/></td>
              <td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td>
            </tr>
              <tr>
              <td width="50%" height="25" align="right">Bid Number:</td>
              <td width="1%">&nbsp;</td>
              <td width="49%" align="left" valign="top"><input type="text"';
if ($emp_type > 0)
{
    $html .= 'disabled="disabled"';
}
$html .= 'name="bid_number" value="';
$html .= $data_prj['bid_number'];
$html .= '"/></td>
              <td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td>
            </tr>';
/* <tr>
  <td width="50%" height="25" align="right">Project Budget:</td>
  <td width="1%">&nbsp;</td>
  <td width="49%" align="left" valign="top"><input type="text"';
  if($emp_type >0){$html .= 'disabled="disabled"'; }
  $html .= 'name="project_budget" value="';
  $html .= $data_prj['project_budget'];
  $html .= '"/></td>
  <td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td>
  </tr> */
$html .= ' </table></td></tr>';


$html.= '<tr>
          <td align="center">
          <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" id="dataTable">';
if ($isEdit)
{
    for ($i = 0; $i < count($data_order_shipping); $i++)
    {
        $html .= '<tr><td>
    <fieldset><legend>&nbsp;Order Shipping - ' . ($i + 1) . '&nbsp;</legend>
            <table width="100%">
            <tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Carrier:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>';

        for ($index = 0; $index < count($data_carrier); $index++)
        {
            if ($data_carrier[$index]['carrier_id'] == $data_order_shipping[$i]['carrier_id'])
            {
                $html .= '<td><input type="text" READONLY  value="' . $data_carrier[$index]['carrier_name'] . '" />';
                $html .= '<input type="hidden" READONLY name="carrier_shipping_select[]" value="' . $data_order_shipping[$i]['carrier_id'] . '" /></td>';
            }
        }
        /*   <td width="49%"><select READONLY ';
          if($emp_type >0){$html .= 'disabled="disabled"'; }
          $html .= 'name="carrier_shipping_select[]" onchange="javascript:show_weblink(this, ';
          $html .= $i;
          $html .= ');">
          <option value="0">----- select -----</option>';
          for($index=0; $index<count($data_carrier); $index++)
          {
          if($data_carrier[$index]['carrier_id'] == $data_order_shipping[$i]['carrier_id'])
          {
          $html .= '<option value="';
          $html .= $data_carrier[$index]['carrier_id'];
          $html .= '" selected="selected">';
          $html .= $data_carrier[$index]['carrier_name'];
          $html .= '</option>';
          }
          else
          {
          $html .= '<option value="';
          $html .= $data_carrier[$index]['carrier_id'];
          $html .= '">';
          $html .= $data_carrier[$index]['carrier_name'];
          $html .= '</option>';
          }
          }
          $html .= '</select></td> */

        $html .= ' <td align="left" valign="top" height="25px">&nbsp;<input type="hidden" name="hdn_shipping_id[]" value="';
        $html .= $data_order_shipping[$i]['shipping_id'];
        $html .= '"/></td>
            </tr>';

        if (isset($data_order_shipping[$i]['carrier_id']) && $data_order_shipping[$i]['carrier_id'] == 6)
        {
            $html .= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Date Delivered:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%"><input type="text"';
            $html .= 'name="delivered_date[]" onclick="javascript:showDate(this);" value="';
            if ($data_order_shipping[$i]['deliv_date'] != "")
                $html .= date('m/d/Y', $data_order_shipping[$i]['deliv_date']);
            $html .= '"/></td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
            </tr>';

            $html .= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Delivered By:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%"><input type="text"';
            $html .= 'name="delivered_by[]"  value="';
            $html .= $data_order_shipping[$i]['deliv_by'];
            $html .= '"/></td>
              <td align="left" valign="top" height="25px">&nbsp;'
.'<input type="hidden" name="track_shipping[' . $i . '][]" value="" /><input type="hidden" name="hdn_track_id[' . $i . '][]" value="-1" />'
                  .'<input type="hidden" name="shipon[]" value="" /> '            
.'</td></tr>';
        }
        else
        {

            $html .='<tr><td></td><td></td><td align="left" >'
                    . '<input type="hidden" name="delivered_date[]" value="" /><input type="hidden" name="delivered_by[]" value="" />'
                    . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add New Tracking Number:'
                    . '<img src="' . $mydirectory . '/images/bullet_add.png" alt="Add" onclick="javascript:AddTrackingNum(' . $i . ');"/></td></tr>';
            if ($data_order_shipping[$i]['tracking_number'] != '' && $data_order_shipping[$i]['tracking_number'] != 'Array')
            {
                $html .='<tr><td width="50%" height="25" align="right">Tracking Number:</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"> <input  value="' . $data_order_shipping[$i]['tracking_number'] . '" id="track_num' . $i . '" type="text" value=""> </td>';
                $html .= '<td><div id="weblink_id' . $i . '"><a href="javascript:void(0);" onclick="javascript:popupWindow(\'' .
                        $data_order_shipping[$i]['weblink'] . $data_order_shipping[$i]['tracking_number'] . '\');"><img src="' . $mydirectory .
                        '/images/courier_man.jpg" width="50" height="30"/></a></div></td>' .
                        '</tr>';
            }
            for ($j = 0; isset($track_no[$i][$j]); $j++)
            {

                $html .='<tr><td width="50%" height="25" align="right">Tracking Number:</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"><input type="hidden" name="hdn_track_id[' . $i . '][]" value="' . $track_no[$i][$j]['track_id'] . '" /><input  value="' . $track_no[$i][$j]['tracking_no'] . '" name="track_shipping[' . $i . '][]" id="track_num' . $i . '" type="text" value="">&nbsp;&nbsp;'
                        . '<img src="' . $mydirectory . '/images/x.jpg" border="0" alt="delete" onclick="javascript:delete_trackno('. $track_no[$i][$j]['track_id'] .');" '
                        . ' onmouseover="this.style.cursor = \'pointer\';" /></td>' .
                        '<td><div id="weblink_id' . $i . '"><a href="javascript:void(0);" onclick="javascript:popupWindow(\'' .
                        $data_order_shipping[$i]['weblink'] . $track_no[$i][$j]['tracking_no'] . '\');"><img src="' . $mydirectory .
                        '/images/courier_man.jpg" width="50" height="30"/></a></div></td>' .
                        '</tr>';

                /* if($j==0)
                  $html .='<img src="'.$mydirectory.'/images/bullet_add.png" alt="Add" onclick="javascript:AddTrackingNum('.$i.');"/>'; */
            }
            $html .='</td></tr>';
            $html .='<tr ><td align="center" colspan="3"  ><table width="100%" id="track_' . $i . '"></table></td></tr>';
            $html .= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Shipped On:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%"><input type="text"';
            if ($emp_type > 0)
            {
                $html .= 'disabled="disabled"';
            }
            $html .= 'name="shipon[]" onclick="javascript:showDate(this);" value="';
            $html .= $data_order_shipping[$i]['shippedon'];
            $html .= '"/></td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
            </tr>';
        }

        $html .= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Shipped From:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%">';
        //echo "ffffffff". $data_prj['ship_from'];
        $html .= '<select name="shiped_from[]">
         
<option value="Vendor" ';
        if (isset($data_order_shipping[$i]['ship_from']) && $data_order_shipping[$i]['ship_from'] == "Vendor")
            $html.=' selected="selected" ';
        $html.= '>Vendor</option>
<option value="Client"';
        if (isset($data_order_shipping[$i]['ship_from']) && $data_order_shipping[$i]['ship_from'] == "Client")
            $html.=' selected="selected" ';
        $html.= '>Client</option>
<option value="Office"';
        if (isset($data_order_shipping[$i]['ship_from']) && $data_order_shipping[$i]['ship_from'] == "Office")
            $html.=' selected="selected" ';
        $html.= '>Office</option>
         </select>';

        $html .= '</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
            </tr>';


        $html .= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Shipped To:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%">';

        $html .= '<select name="shiped_to[]">
         
<option value="Vendor" ';
        if (isset($data_order_shipping[$i]['ship_to']) && $data_order_shipping[$i]['ship_to'] == "Vendor")
            $html.=' selected="selected" ';
        $html.= '>Vendor</option>
<option value="Client"';
        if (isset($data_order_shipping[$i]['ship_to']) && $data_order_shipping[$i]['ship_to'] == "Client")
            $html.=' selected="selected" ';
        $html.= '>Client</option>
<option value="Office"';
        if (isset($data_order_shipping[$i]['ship_to']) && $data_order_shipping[$i]['ship_to'] == "Office")
            $html.=' selected="selected" ';
        $html.= '>Office</option>
         </select>';

        $html .= '</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
            </tr>';
        
        $html .= '<tr>
            <td>Style:</td>
            <td>Size:</td>
            <td>Qty Ordered:</td>
            <td>Qty Shipped:</td>
            </tr>';
       
        
      
$script="" ; 
                for($j=0; $j<count($data_prj_style); $j++){

$script.= '<tr>
            <td><input type="text" readonly="readonly" ';
        
                $script .= 'value="'.htmlentities( $data_prj_style[$j]['style']).'" id="styles" /></td>
       
           <td><input type="text" readonly="readonly" ';
               $script .= 'value="'.htmlentities( $data_prj_style[$j]['vendor_style']).'" /></td>';
            
			
			
           $script .= ' <td><input type="text" id="qty_order" class="qty_order_'.$j.'" readonly="readonly" ';
           $script .= 'value="'.htmlentities( $data_prj_style[$j]['garments']).'" />';

	
        
        $script .='<input type="hidden" name="prj_style_id['.$i.']['.$j.']"';
	$script .= 'value="'.$data_prj_style[$j]['prj_style_id'].'"  /></td>';
	//if($data_prj_style[$j]['prj_style_id']==$datalist[$i][$j]['prj_style_id'])
        			
        $script .= '<td>';
        $flag=0;
        for($k=0;$k<count($datalist[$i]);$k++)
        {
           if($data_prj_style[$j]['prj_style_id']==$datalist[$i][$k]['prj_style_id'])  
           {
         $script .= '<input type="text" name="shipping['.$i.']['.$j.']" id="qty_ship" value="';
        $script .=stripslashes($datalist[$i][$k]['qty_ship']);
        $script .= '" onchange="javascript:left_ship();" class="shipping_'.$j.'" />';
        $script .='<input type="hidden" name="qty_id['.$i.']['.$k.']" value="';
	$script .= $datalist[$i][$j]['qty_id'];
        $script .='"  />';
        $flag=1;
           }
        }
        if($flag==0)
        {
        $script .= '<input type="text" name="shipping['.$i.']['.$j.']" id="qty_ship" value="" onchange="javascript:left_ship();" class="shipping_'.$j.'" />';
        $script .='<input type="hidden" name="qty_id['.$i.']['.$j.']" value=""  />';        
        }  
        
       $script .=' </td>';
        
	$script .= ' </tr>';	

        }

        
        $html .=$script;

        $html.= '<tr style="bottom:10px">
              <td width="50%" height="25" align="right" >Shipping Notes:</td>
              <td align="left" valign="top" height="25px">&nbsp;</td>
              <td width="49%"><textarea name="order_shipping_notes[]" ';
        if ($emp_type > 0)
        {
            $html .= 'disabled="disabled"';
        }
        $html .= 'cols="25" rows="5">';
        $html .= $data_order_shipping[$i]['shipping_notes'];
        $html .= '</textarea></td>
              <td align="center" valign="top" height="25px">';
        if ($emp_type > 0)
        {
            $html .= '&nbsp;';
        }
        else
        {
            $html .= '<img  src="';
            $html .= $mydirectory;
            $html .= '/images/delete.png" onclick="javascript:DeleteRows(\'';
            $html .= $data_order_shipping[$i]['shipping_id'];
            $html .= '\',\'';
            $html .= $data_order_shipping[$i]['pid'];
            $html .= '\',\'order_shipping\')" />';
        }
        $html .= '</td>
            </tr>';


        $html .= ' <tr>
             <td width="50%" height="25" align="right">Shipped On Clients Carrier Account:
             
             </td><td width="1%">&nbsp;</td>
            <td ><input type="checkbox" name="shippedonclient['.$i.']" id="shipped_on_client"  onclick="javascript:shippingOn()" ';
        if (isset($data_order_shipping[$i]['shiponclient']) && $data_order_shipping[$i]['shiponclient'] == 1)
            $html .= ' checked="checked" ';

        $html .= '"/></td>
             <td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td>
            </tr>';

        $html .= '</table></fieldset></td></tr>';
    }
}
$html .= '</table>
            <table width="80%" align="center">
              <tr>
                <td width="49%" align="center" valign="top"><input type="button" value="Add new tracking info" onclick="javascript:addRow(\'dataTable\');"';
if ($emp_type > 0)
{
    $html .= ' disabled="disabled"';
}
$html .= ' /></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
    </tr>
    </table>';
$html .= '<textarea id="txt_cnt" style="display:none;">'.$script.'</textarea>';
//echo $html;

$return_arr['html'] = $html;
//$return_arr['queryTester'] = $queryTester;
echo json_encode($return_arr);
return;
?>