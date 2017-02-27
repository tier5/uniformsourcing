<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";
extract($_POST);
$return_arr = array();
$return_arr['error'] = "";
$return_arr['html'] = "";

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
	 $sql = 'select "sent_date","confirm_num","vendor_po","upload_file","tbl_vendorid",pid,vid,vendor."vendorName" from tbl_prjvendor inner join vendor '
.' on vendor."vendorID"=tbl_prjvendor.vid where status =1 and pid ='. $pid;
	//echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjVendor[]=$row;
	}
	pg_free_result($result);
}
	 
  $queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
//		echo $queryVendor;
	 if(!($result=pg_query($connection,$queryVendor)))
	{
	print("Failed query1: " . pg_last_error($connection));
	exit;
    }
    while($row = pg_fetch_array($result))
	{
	$data_Vendr[]=$row;
     }



$vender='<table border="0" cellpadding="1" cellspacing="1">
<tr>
<td>
<table align="left" border="0" cellpadding="1" cellspacing="0" id="tbl_vendor">
</table>
</td>
</tr>
<tr>
<td>
<table align="left" border="0" cellpadding="1" cellspacing="0" id="tbl_vendor_edit">';
if($pid)
    {
        for($i=0; $i<count($data_prjVendor); $i++)
        {
			$vender .= '<tr class="vendor_row_'.$i.'">
							<td>Vendor Name: <input type="hidden" id="vendorinp'.$i.'" value="'.$data_prjVendor[$i]['vid'].'" </td>
							
							<td> <input type="text" disabled="disabled" value="'.$data_prjVendor[$i]['vendorName'].'" > </td>
							<td width="10px">&nbsp;</td>
							<td ';
							if($emp_type ==1){
								$vender .= 'style="visibility:hidden" ';
							}
							$vender .= '><a class="alink" href="#" onClick="DeleteVendorRow('.$i;
							$vender .=','.$data_prjVendor[$i]['vid'];
							$vender .= ','.$data_prjVendor[$i]['tbl_vendorid'].');" >Delete</a></td>
						</tr>';
					
       
        if(isset($data_prjVendor[$i]['vendor_po']) && $data_prjVendor[$i]['vendor_po']!="")
          $vender .='<tr class="vendor_row_'.$i.'"><td>Vendor PO#: </td><td>'.$data_prjVendor[$i]['vendor_po'].'</td></tr>';  
        
          if(isset($data_prjVendor[$i]['upload_file']) && $data_prjVendor[$i]['upload_file']!="")
          $vender .='<tr class="vendor_row_'.$i.'"><td>Upload PO# : </td><td><strong>'.$data_prjVendor[$i]['upload_file'].'</strong>'.
'<a href="download.php?file='.$data_prjVendor[$i]['upload_file'].'">
<img src="'.$mydirectory.'/images/Download.png" alt="download"/></a>'.
                  '</td></tr>';  
          //echo 'HHH'.$data_prjVendor[$i]['sent_date'];
         if(isset($data_prjVendor[$i]['sent_date']) && $data_prjVendor[$i]['sent_date']!="")
         {
             $ar=split('-',$data_prjVendor[$i]['sent_date']);
             $dt=$ar[1].'/'.$ar[2].'/'.$ar[0];
          $vender .='<tr class="vendor_row_'.$i.'"><td>Date: </td><td>'.$dt.'</td></tr>'; 
         }
           if(isset($data_prjVendor[$i]['confirm_num']) && $data_prjVendor[$i]['confirm_num']!="")
          $vender .='<tr class="vendor_row_'.$i.'"><td>confirmation number: </td><td>'.$data_prjVendor[$i]['confirm_num'].'</td></tr><tr><td>&nbsp;</td></tr>';
       
    }
    }
    $vender.='<tr><td id="vendor_list" colspan="2"></td></tr>';
$vender .= '<tr>
<td valign="middle" align="left">Vendor Name: </td> 
   <td align="left" valign="top" ><select name="vendorID" id="vendorID" onchange="CheckVendor();" ';


	if($emp_type >0){$vender .='disabled="disabled"';}
	$vender.='>';
	$vender.='<option value="0">----Select----</option>"';
	  $vendorIndex = "";
	for($i=0; $i <count($data_Vendr); $i++){
		if($vendorID==$data_Vendr[$i]['vendorID'])
		{
			$vender.='<option value="';
			$vender.= $data_Vendr[$i]['vendorID'];
			$vender.='" selected="selected">';
			$vender.=$data_Vendr[$i]['vendorName'];
			$vender.='</option>';
			$vendorIndex = $i;
		}
		else
		{
			$vender.='<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
		}
}

    $vender.='</select>';
   $vender.='<img '; 
   if($emp_type >0){ 
  $vender.='style="visibility:hidden"';
  } 
  $vender.='src="'.$mydirectory.'/images/bullet_add.png" alt="add" width="32" height="25" onClick="javascript:AddAnotherVendor(\'tbl_vendor\',0,\'name\',\'1\',\'0\');"/></td>';
    
	if( $vendorIndex != "")
	{
		$vender.= '<input type="hidden" id="hdnVendorid" value="'.$data_Vendr[$vendorIndex]['vendorID'].'" />';
		$vender.= '<input type="hidden" id="hdnVendorName" value="'.$data_Vendr[$vendorIndex]['vendorName'].'" />';
	}
	else
	{
		$vender.= '<input type="hidden" id="hdnVendorid" value="0"/>';
		$vender.= '<input type="hidden" id="hdnVendorName" value=""/>';
	}
	
   
       /*  $vender.='</td></tr><tr> <td valign="middle" align="left">Vendor PO#: </td> <td width="20px">&nbsp;</td>
   <td align="left" valign="top" ><input type="text" id="vendorPO" name="vendorPO">  </td></tr>'
  . '<tr> <td valign="middle" align="left">Deposit Sent: </td> <td width="20px">&nbsp;</td>
   <td align="left" valign="top" >Yes<input type="radio" id="deposit_sent_yes" name="deposit_sent" onclick="javascript:showDepositSent()">'
       .' No<input type="radio" id="deposit_sent_no" name="deposit_sent"> </td></tr>';
       $vender.='<tr><td colspan="2" ></td></tr>';*/
         
                $vender.='</table>';
//echo $vender;
$return_arr['html'] = $vender;
echo json_encode($return_arr);
return;
?>