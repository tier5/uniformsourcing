<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";
extract($_POST);
$tx='';
if(isset($close))
$tx='_closed';
$return_arr = array();
$return_arr['error'] = "";
$return_arr['html'] = "";
$isEdit=0;

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
   	$sql = "Select * from tbl_prjsample$tx where status =1 and pid = $pid";	
        echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjSample =$row;
	}
        //print_r($data_prjSample);
	if($data_prjSample['sampleId']!="")
	$sampleId = $data_prjSample['sampleId'];
	pg_free_result($result);

}
$sql = 'select id, "srID" from "tbl_sampleRequest'.$tx.'" where status=1';
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_sample[]=$row1;
}
pg_free_result($result1);

if($emp_type ==1)
{ 
$sample='<table cellpadding="1" cellspacing="1" border="0">'.
    '<tr>'.
      '<td align="right">Samples Provided:</td>'.
'<td>'; 
if($isEdit &&  $data_prjSample['sampleprovided']==0  )
{

 $sample.='&nbsp;No';

}
else
{
   $sample.='&nbsp;Yes';

}
 $sample.='</td>
<td>&nbsp;</td>
    </tr>
  <td align="right">Embroidery:</td>
  <td align="left">';
    
if($isEdit &&  $data_prjSample['embroidery'] ==0)
 {

	$sample.='&nbsp;Yes &nbsp;';
    
 }
else
 { 
	$sample.='&nbsp;No';
 }
	$sample.='</td>
  </tr>
  <tr>
    <td align="right">Silk Screening:</td>
    <td align="left">';
if($isEdit &&  $data_prjSample['silkscreening'] == 0)
{
   $sample.='&nbsp;Yes &nbsp';
}
else
{
 $sample.='&nbsp;No'; 
}

  $sample.='</td>
  </tr>
  <tr>
    <td align="right">ETA Production:</td>
    <td align="left">';
	
	 $sample.='<input ';
	 if($emp_type ==1){ 
	  $sample.='disabled="disabled"';
	  }
	  $sample.='type="text" name="etaPrdctn" id="etaPrdctn" value="'.$data_prjSample['etaproduction'].'"/>';
      $sample.='<input'; 
	  $sample.='type="hidden" id="sampleId" name="sampleId" value="'.$sampleId.'"/></td>
  </tr>
  </table>';
}
$sample.='<title>Untitled Document</title>';
$sample.='<table cellpadding="1" cellspacing="1" border="0"'; 
if($emp_type ==1){ 
 $sample.='style="display:none;"'; }
 $sample.='>';
$sample.='<tr>
<td align="right">Samples Provided:</td>';
if($isEdit && $data_prjSample['sampleprovided']  == 0  )
{
    $sample.='<td align="left"> <input type="radio" value="1" id="samplesProvided" name="samplesProvided"  onClick="setVisibility(\'rowId\',\'1\'); if(document.getElementById(\'production\').value == 1){ setVisibility(\'prodId\',\'1\'); } ;';
	$sample.= '" />&nbsp;Yes &nbsp;';
	$sample.='<input type="radio" value="0" id="samplesProvided" name="samplesProvided" checked="checked" onClick="setVisibility(\'rowId\',\'0\');setVisibility(\'prodId\',\'0\');"/>&nbsp;No </td>';
}
else
{

$sample.='<td align="left"><input type="radio" value="1"  id="samplesProvided" name="samplesProvided" checked="checked" onClick="setVisibility(\'rowId\',\'1\');Prdtnvisible();"/>&nbsp;Yes &nbsp;<input type="radio" value="0" id="samplesProvided" name="samplesProvided"  onclick="setVisibility(\'rowId\',\'0\');setVisibility(\'prodId\',\'0\');"/>&nbsp;No'; 

}

 $sample.='</td>

</tr>
<tr id="rowId"';
if($isEdit &&  $data_prjSample['sampleprovided']  == 0 ){
	$sample.='style="display:none;';
}
$sample.='color:#FF00FF"><td>&nbsp;</td>';

if(isset($isEdit) && $data_prjSample['product_client'] == 1)
{

$sample.='<td align="left"><input name="production" id="production" type="radio" value="1" checked="checked" onClick="setVisibility(\'prodId\',\'1\');document.getElementById(\'production\').value=1;"/>Production
<input name="production" type="radio" value="0" 
onclick="setVisibility(\'prodId\',\'0\');document.getElementById(\'production\').value=0;" />Client</td>';
}
else
{

$sample.='<td align="left"><input name="production"  type="radio" value="1" onClick="setVisibility(\'prodId\',\'1\'); document.getElementById(\'production\').value=1;"/>Production
<input name="production" id="production" type="radio" value="0" checked="checked" 
onclick="setVisibility(\'prodId\',\'0\');document.getElementById(\'production\').value=0;" />Client';

}
	
$sample.='</td>
</tr>
<tr id="prodId"';
if($data_prjSample['product_client']  ==0 || $data_prjSample['sampleprovided']  == 0){
	$sample.='style="display:none;"';
}
$sample.='color:#FF00FF">
  <td>&nbsp;</td>
  <td><table align="left" class="prjctVenorTable">
  <tr>
    <td align="left"><table align="left" class="prjctVenorTable">
      <tr>
        <td align="left">Date:</td>
        <td align="left"><input type="text" name="prdctDate" id="sampleprdctDate" onclick="javascript:showDate(this);" value="'.$data_prjSample['sampledate'].'"/></td>
      </tr>
      <tr>
        <td height="30" align="left">Sample Number: </td>
        <td align="left"><select name="sampleNmbr" id="sampleNmbr" style="width:50;" onChange="sampleChange();" >
          <option value="">---Select---</option>';

$sample.=$sampleIndex = "";
for($i=0; $i < count($data_sample); $i++){
	if($data_prjSample['samplenumberId']==$data_sample[$i]['id'])
	{
		 $sample.='<option value="';
		 $sample.=$data_sample[$i]['id'];
		 $sample.='"selected="selected">';
		 $sample.=$data_sample[$i]['srID'];
		 $sample.='</option>';
		$sampleIndex = $i;
	}
	else 
		$sample.='<option value="';
		$sample.=$data_sample[$i]['id'];
		$sample.='">';
		$sample.=$data_sample[$i]['srID'];
		$sample.='</option>';
}

        $sample.='</select>
          <a id="sample_a"';
		  if($sampleIndex == "")
		  {
			$sample.='style="display:none;"';
		 } $sample.='href="javascript:void(0);" onClick="popupWindow(\'sample\');"><img src="';
		 $sample.=$mydirectory;
		 $sample.='/images/reportviewEdit.png" alt="" width="20px" height="25px" border="0"></a>';
       if($sampleIndex != "")
	$sample.='<input type="hidden" id="hdn_sampleNum" value="'.$data_sample[$sampleIndex]['id'].'"/> ';
else
	$sample.='<input type="hidden" id="hdn_sampleNum" value="0"/> ';
$sample.='</td>
      </tr>
  
    </table></td>
  </tr>
</table></td>
  </tr>
<td align="right">Embroidery:</td>';

if($data_prjSample['embroidery'])
{

$sample.='<td align="left"><input  type="radio" value="1" name="embroidery" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" />&nbsp;No </td>';
}
else
{ 
  $sample.='<td align="left"><input type="radio" value="1" name="embroidery" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" checked="checked" />&nbsp;No</td>';
}
$sample.='</tr>
  <tr>
    <td align="right">Silk Screening:</td>';
if($data_prjSample['silkscreening'])
{

$sample.='<td align="left"><input type="radio" value="1" name="silkScreening" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening"/>
      &nbsp;No </td>';

}
else
{

    $sample.='<td align="left"><input type="radio" value="1" name="silkScreening" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening" checked="checked"/>
      &nbsp;No </td>';
}

$sample.='</tr>
  <tr>
    <td align="right">ETA Production:</td>
    <td align="left">';
	$sample.='<input type="text" name="etaPrdctn" id="etaPrdctn" onclick="javascript:showDate(this);" value="'.$data_prjSample['etaproduction'].'"/>';
    $sample.='<input type="hidden" id="sampleId" name="sampleId" value="'.$sampleId.'"/>';
    $sample.='</td>
  </tr>

</table>';

$return_arr['html'] = $sample;
echo json_encode($return_arr);
return;
?>