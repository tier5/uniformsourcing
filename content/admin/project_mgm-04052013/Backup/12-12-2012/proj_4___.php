<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$html = "";
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['html'] = "";
$isEdit=0;
$pricingId =0;
$unit_retail_price =0;
$target_retail_price =0;
$is_session =0;
$emp_type ="";
$emp_id= "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =  $_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' style="visibility:hidden"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}


if(isset($_POST['pid']) && $_POST['pid']!=0){
	$isEdit=1;
	$sql = "select * from tbl_prj_style  where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_style[]=$row;
	}
	for($style_index=0;$style_index<count($data_prj_style);$style_index++)
	{
		$target_retail_price+=($data_prj_style[$style_index]['retailprice']*$data_prj_style[$style_index]['garments']);
		$unit_retail_price +=($data_prj_style[$style_index]['priceunit']*$data_prj_style[$style_index]['garments']);
	}
	pg_free_result($result);		
	
	$sql = "Select * from tbl_prjpricing where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjPricing =$row;
	}
	if($data_prjPricing['pricingId']>0)
		$pricingId = $data_prjPricing['pricingId'];
	pg_free_result($result);
	
	$sql = "Select est.* from \"projectEstimatedUnitCost\" as est left join tbl_newproject as np on np.pid=est.pid where est.pid = $pid";
       
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_estimate =$row;
	}
        
        
        
        
	if($data_prj_estimate['prj_estimate_id']!="")
	$estimate_id = $data_prj_estimate['prj_estimate_id'];
	pg_free_result($result);
        
        $sql = "Select shiponclient from \"tbl_newproject\" where pid = $pid";
if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_ship =$row['shiponclient'];
	}
        pg_free_result($result);
}


$html = '<table width="90%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
  <table cellpadding="1" cellspacing="1" border="0" width="100%">
<!--New rows added for pt invoice,taxes etc-->
<tr>
<td colspan="3">
<table cellpadding="1" cellspacing="1" border="0" width="100%" id="tbl_style">
<tr>
<td>Style :</td>
<td>Total No of Garments:</td>
<td';
if($emp_type ==1 ){
	$html .= ' style="visibility:hidden"';
}
$html .= '>Target Price Per Unit:</td>
<td';
if($emp_type>0){
	$html .= ' style="visibility:hidden"';
}
$html .= '>Target Retail Price:</td>
</tr>
<tr>
<td >
<input type="text" id="style1" name="style[]"';
if($emp_type ==2){
	$html .= 'disabled="disabled"';
}
$html .= 'value="'.$data_prj_style[0]['style'].'" onchange="javascript:calculateProjectCost(\'1\');" />
</td>
<td>
<input type="text" id="garments1" name="garments[]"';
if($emp_type ==2){
	$html .= ' disabled="disabled"';
}
$html .= 'value="'.$data_prj_style[0]['garments'].'" onchange="javascript:isNumeric(this);calculateProjectCost(\'1\');  " />
</td>
<td>
<input type="text" id="priceunit1" name="priceunit[]" value="'.$data_prj_style[0]['priceunit'].'"'.$style_price.'onchange="javascript:isNumeric(this);calculateProjectCost(\'1\');  "  />
</td>
<td>
<input type="text" id="retailprice1" name="retailprice[]" value="'.$data_prj_style[0]['retailprice'].'" ';
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= 'onchange="javascript:isNumeric(this); calculateProjectCost(\'1\'); " />
</td>
<td>
<input type="hidden" id="rowSum1" value="';
if($data_prj_style[0]['priceunit']>0 && $data_prj_style[0]['garments'] >0){
	$html .= ($data_prj_style[0]['priceunit'] * $data_prj_style[0]['garments']);
}
else{
	$html .= '0';
}
	$html .= '"  /> 
<input type="hidden" id="row_sum_target1" value="';
if($data_prj_style[0]['retailprice']>0 && $data_prj_style[0]['garments'] >0){
	$html .= ($data_prj_style[0]['retailprice'] * $data_prj_style[0]['garments']);
}
else{
	$html .= '0';
}
	$html .= '"  /> 
<input type="hidden" name="prjstyle_id[]" value="'.$data_prj_style[0]['prj_style_id'].'"  />
<img ';
if($emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= 'src="'.$mydirectory.'/images/bullet_add.png" alt="add" height="25" onclick="javascript:multipleStyle(\'tbl_style\',\'prjstyle\');"/></td>
</tr>';



$total_garment += (float)$data_prj_style[0]['garments'];
if($isEdit){
	for($i=1,$j=2; $i<count($data_prj_style); $i++,$j++){
		$total_garment+=(float)$data_prj_style[$i]['garments'];
		$html .= '<tr>
		<td>
		<input id="style'.$j.'" name="style[]"';
		if($emp_type ==2){
			$html .= 'disabled="disabled"';
		}
		$html .= 'type="text" value="'.htmlentities( $data_prj_style[$i]['style']).'" onchange="javascript:calculateProjectCost(\''.$j.'\');"  />
		
		</td>
		<td>
		<input id="garments'.$j.'" name="garments[]"';
		if($emp_type ==2){
			$html .= 'disabled="disabled"';
		}
		$html .= 'type="text" value="'.htmlentities( $data_prj_style[$i]['garments']).'" onchange="javascript:isNumeric(this);calculateProjectCost(\''.$j.'\');"  />
		</td>
		<td>
		<input id="priceunit'.$j.'" '.$style_price.'  name="priceunit[]" type="text" value="'.htmlentities( $data_prj_style[$i]['priceunit']).'" onchange="javascript:isNumeric(this);calculateProjectCost(\''.$j.'\');" />
		</td>
		<td>
		<input id="retailprice'.$j.'" name="retailprice[]"';
		if($emp_type ==1 || $emp_type ==2){
			$html .= 'style="visibility:hidden"';
		}
		$html .= 'type="text" value="'.htmlentities( $data_prj_style[$i]['retailprice']).'" onchange="javascript:isNumeric(this);calculateProjectCost(\''.$j.'\');" />
		</td>
		<td> 
		<input type="hidden" id="rowSum'.$j.'"';
		if($emp_type ==2){
			$html .= 'disabled="disabled"';
		}
		$html .= 'value="';
		if($data_prj_style[$i]['priceunit']>0 && $data_prj_style[$i]['garments'] >0){
			$html .= ($data_prj_style[$i]['priceunit'] * $data_prj_style[$i]['garments']);
		}
		else{
			$html .= '0';
		}
		$html .= '"   /> 
		<input type="hidden" id="row_sum_target'.$j.'"';
		if($emp_type ==2){
			$html .= 'disabled="disabled"';
		}
		$html .= 'value="';
		if($data_prj_style[$i]['retailprice']>0 && $data_prj_style[$i]['garments'] >0){
			$html .= ($data_prj_style[$i]['retailprice'] * $data_prj_style[$i]['garments']);
		}
		else{
			$html .= '0';
		}
		$html .= '"   /> 
		<input type="hidden" name="prjstyle_id[]" value="'.$data_prj_style[$i]['prj_style_id'].'"  />
		<a ';
		if($emp_type ==1 || $emp_type ==2){
			$html .= 'style="visibility:hidden" ';
		}
		$html .= 'href="javascript:void(0);" class="deleteTd" onclick="javascript:DeleteRows(\''.$data_prj_style[$i]['prj_style_id'].'\',\''.$data_prj_style[$i]['pid'].'\',\'prjstyle\')">Delete</a>
		</td>
	</tr>';
	}
}

$html .= '</table>
<input type="hidden" id="total_garment" value="'.$total_garment.'" />
</td>
</tr>
<tr '.$style_price.' id="shipping_cost_row" ';

if(isset($data_prj_ship) && $data_prj_ship==1) 
   $html .=' style = "display:none;" ';

$html .='>
<td align="right" width="50%" height="25">Shipping Cost:<strong>$</strong></td>
<td width="1%">&nbsp;</td>
<td align="left" height="25"><input type="text" name="shipping_cost" id="shipping_cost" value="'.$data_prjPricing['shipping_cost'].'" '.$style_price.' onchange="javascript:isNumeric(this);calculateProjectComCost();  "  /></td>
</tr>
<tr ';
//echo "status ";//.$_POST['shipping_status'];
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="display:none;"';
}
$html .= '>
	<td align="right" height="25">Pt Invoice:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"><input type="text" name="pt_invoice" value="'.$data_prjPricing['pt_invoice'].'" onchange="javascript:isNumeric(this);" /></td>
</tr>

<tr '.$style_price.'>
	<td align="right" height="25">Taxes:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"><input type="text" name="taxes" id="taxes" value="'.$data_prjPricing['taxes'].'" '.$style_price.' onchange="javascript:isNumeric(this);calculateProjectComCost(); " /></td>
</tr>



<tr '.$style_price.'>
	<td align="right" height="25">Project Quote:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"><input type="text" name="projectQuote" id="projectQuote" '.$style_price.' value="'.$data_prjPricing['prjquote'].'" onchange="javascript:isNumeric(this);" /></td>
</tr>
<tr ';
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= '>
	<td align="right" height="25">Project Cost:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"> <input type="text" name="pcost" readonly="readonly" id="pcost"   value="'.$data_prjPricing['prjcost'].'" /></td>
</tr>
<tr';
if($emp_type ==1 || $emp_type ==2){
	$html .= ' style="visibility:hidden"';
}
$html .= '>
	<td align="right" height="25">Job Costing:</td>
	<td>&nbsp;</td>
	<td align="left" height="25"> <input type="button" value="Click for job costing"    onClick="javascript:popOpen(0,\'PEC\');"  /></td>
</tr>
<tr ';
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= '>
	<td align="right" height="25">Project Estimated Unit Cost:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"><input type="text" name="pestimate" id="pestimate" readonly="readonly"  value="'.$data_prjPricing['prj_estimatecost'].'" /></td>
</tr>
<tr ';
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= '>
	<td align="right" height="25">Project Completion Cost:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"> <input type="text" name="pcompcost" id="pcompcost" value="'.$data_prjPricing['prj_completioncost'].'" />
		<input type="hidden" id="pricingId" name="pricingId" value="'.$pricingId.'"/>
	</td>
</tr>

<tr ';
if($emp_type ==1 || $emp_type ==2){
	$html .= 'style="visibility:hidden"';
}
$html .= '>
	<td align="right" height="25">Project Estimated Profit:<strong>$</strong></td>
	<td>&nbsp;</td>
	<td align="left" height="25"><input type="text" name="pestprofit" id="pestprofit" value="'.$data_prjPricing['prj_est_profit'].'" readonly="readonly" />
	<input type="hidden" id="hdn_target_mulprice" value="'.$target_retail_price.'" />
	<input type="hidden" id="hdn_unit_mulprice" value="'.$unit_retail_price.'" />
	<input type="hidden" id="hdn_garmenttotal" value="'.$garment_total.'" />
	</td>
</tr>
</table>
  </td>
</tr>
</table>
<div id="prj_estimatecost" class="popup_block">

<table width="100%">
    <tr>
      	<td align="center"><font size="5">P</font><font size="5">roject Estimated Unit Cost
        	</font>
        	<table width="90%" border="0" cellspacing="1" cellpadding="1">
            	<tr>
              		<td height="25" align="right" valign="top">Pattern Set-Up:</td>
              		<td width="10">&nbsp;</td>
              		<td align="left" valign="top"><input id="ptrnsetup" name="ptrnsetup" type="text"';
			  		if($emp_type ==2){
				  		$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['ptrnsetup'].'" /></td>
            	</tr>
            	<tr>
              		<td height="25" align="right" valign="top">Grading Set-Up:</td>
              		<td>&nbsp;</td>
              		<td align="left" valign="top"><input id="grdngsetup" name="grdngsetup" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['grdngsetup'].'" /></td>
            	</tr>
            	<tr>
              		<td height="25" align="right" valign="top">Sample Fee Set Up:</td>
              		<td>&nbsp;</td>
              		<td align="left" valign="top"><input id="smplefeesetup" name="smplefeesetup" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['smplefeesetup'].'" /></td>
            	</tr>
            	<tr>
              		<td height="25" align="right" valign="top">Fabric:</td>
              		<td>&nbsp;</td>
					<td align="left" valign="top"><input id="fabric" name="fabric" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['fabric'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right" valign="top">Trim:</td>
				 	<td>&nbsp;</td>
				  	<td align="left" valign="top"><input id="trimfee" name="trimfee" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['trimfee'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right" valign="top">Labor:</td>
				  	<td>&nbsp;</td>
				  	<td align="left" valign="top"><input id="labour" name="labour" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['labour'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right" valign="top">Duty: </td>
				  	<td>&nbsp;</td>
				  	<td align="left" valign="top"><input id="duty" name="duty" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['duty'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right" valign="top">Freight:</td>
				  	<td>&nbsp;</td>
				  	<td align="left" valign="top"><input id="frieght" name="frieght" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['frieght'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right" valign="top">Other: </td>
				  	<td>&nbsp;</td>
				  	<td align="left" valign="top"><input id="other" name="other" type="text"';
					if($emp_type ==2){
						$html .= ' disabled="disabled"';
					}
					$html .= 'value="'.$data_prj_estimate['other'].'"/></td>
				</tr>
				<tr>
				  	<td height="25" align="right"><input type="hidden" name="prj_estimate_id" value="'.$estimate_id.'" /><input name="sbtbutton"  id="sbtbutton" type="button" onMouseOver="this.style.cursor = \'pointer\';" value="Save" onclick="javascript:FillEstimatedCost();Fade();" /></td>
				  	<td>&nbsp;</td>
				  	<td align="left"><input name="button2" type="button" onMouseOver="this.style.cursor = \'pointer\';"  value="Cancel" onclick="javascript:ClearAllFields();" /></td>
				</tr>
      </table></td>
    </tr>
  </table>
</div>';
//echo $html;

$return_arr['html'] =$html;
echo json_encode($return_arr);
return;
?>

