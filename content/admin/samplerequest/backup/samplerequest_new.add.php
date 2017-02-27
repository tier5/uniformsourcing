<?php
require('Application.php');
require('../../header.php');
$isEdit = 0;
$id = 0;
$is_session = 0;
$emp_type = 0;
$emp_join ="";
$emp_id= "";
$emp_sql = "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_id =  $_SESSION['employee_type_id'];
	$emp_type = $_SESSION['employeeType'] ;
	$emp_sql = ' and vendor."vendorID" ='.$emp_id;
	$is_session = 1;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_id =$_SESSION['employee_type_id'];
	$emp_type = $_SESSION['employeeType'] ;
	$emp_sql = ' and c."ID" ='.$emp_id;
	$is_session = 1; 
}
if(isset($_GET['id']))
{
	$isEdit = 1;
	$id = $_GET['id'];
	
	$sql = "Select \"tbl_sampleRequest\".*,vendor.\"vendorName\",vendor.address,cl.client from \"tbl_sampleRequest\" inner join \"clientDB\" as cl on cl.\"ID\"= \"tbl_sampleRequest\".cid inner join vendor on vendor.\"vendorID\"=\"tbl_sampleRequest\".vid where id = $id";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_sample = $row;
	}
	$sql = "Select * from tbl_sample_uploads where status =1 and sampleid = $id";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_Uploads[] =$row;
	}
	$imageArr = array();
	$fileArr = array();
	for($i = 0, $img= 0, $file = 0; $i < count($data_Uploads); $i++)
	{
		if(trim($data_Uploads[$i]['uploadtype']) == 'I')
		{
			$imageArr[$img]['id'] = $data_Uploads[$i]['uploadid'];
			$imageArr[$img++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
		else if(trim($data_Uploads[$i]['uploadtype']) == 'F')
		{
			$fileArr[$file]['id'] = $data_Uploads[$i]['uploadid'];
			$fileArr[$file++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
	}
	pg_free_result($result);
	
		$sql ='select "notesId", notes, "createdDate", e.firstname as "firstName", e.lastname as "lastName" from "tbl_sampleNotes" as n inner join "employeeDB" as e on e."employeeID" =n."createdBy" where "sampleId"='.$id.' order by "notesId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_notes[]=$row;
	}
}
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}

$sql='select (Max("id")+1) as "id" from "tbl_sampleRequest" ';
if(!($result_cnt=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
	$data_cnt=$row_cnt;
}
if(! isset($data_cnt['id'])) { $data_cnt['id']=1; }
if(!$data_cnt['id']) { $data_cnt['id']=1;  }

$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
} 


/* carrier values*/
$sql = "select carrier_id,carrier_name  from  tbl_carriers where status=1";
if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_carrier[]=$row;
}
pg_free_result($result);	
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
<?php
	if($_GET['page'] == 'arc')
	{
?>
    <input type="button" value="Back" onclick="location.href='samplerequest.archive.list.php';" />
<?php 
	}
	else
	{
?>
     <input type="button" value="Back" onclick="location.href='oldsamplerequest.list.php';" />
<?php
	}
?>
     </td>
    <td>&nbsp;</td>
  </tr>
</table>
<center><div align="center" style="width:50%" id="message"></div></center>
<center>
	<form action="samplerequest.add1.php" method="post" enctype="multipart/form-data" id="validationForm" name="validationForm">
	<table width="100%">
		<tbody>
            <tr>
		  <td valign="top" align="center"><font face="arial">
			  <font size="5">Sample Request form <br>
			  <br>
			  </font>
        
<table>
<tr>
<td>
<table width="100%" cellspacing="1" cellpadding="1" border="0">
				<tbody><tr>
				    <td height="25" valign="top" align="right">Choose Client:</td>
				    <td>&nbsp;</td>
				    <td valign="top" align="left"><select name="clientID" style="width:240px"><?php for($i=0; $i < count($data1); $i++){
	if($data_sample['cid']==$data1[$i]['ID'])
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
		}?></select></td>
			      </tr>
                  <tr>
				  <td height="25" valign="top" align="right">Brand/Manufacture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="brand_manufac" id="brand_manufac" value="<?php echo htmlspecialchars(stripslashes($data_sample['brand_manufct']));?>"></td>
				</tr>
				  <tr>
				  <td height="25" valign="top" align="right">Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="<?php echo  htmlspecialchars(stripslashes($data_sample['srID']));?>"></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Sample Type:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">
                  <select name="sampletype">

                  <option value="Stock" <?php if($data_sample['sampletype'] == "Stock"){?> selected="selected" <?php }?>>Stock</option>
                  <option value="Custom"<?php if($data_sample['sampletype'] == "Custom"){?> selected="selected" <?php }?>>Custom</option>
                  </select></td>
				</tr>
                  <tr>
				  <td height="25" valign="top" align="right">Sample Name:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sample_name" value="<?php echo  htmlspecialchars(stripslashes($data_sample['sample_name']));?>"></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Ordered Date:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="ordered_date" id="ordered_date" value="<?php if($data_sample['ordered_date'] !=""){ echo date('m/d/y',$data_sample['ordered_date']);}?>"></td>
				</tr>
                 
                <tr>
				  <td height="25" valign="top" align="right">Style Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="style" value="<?php echo htmlspecialchars(stripslashes($data_sample['styleNo']));?>" ></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Brief Sample Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="briefdesc" name="briefdesc" cols="30" rows="4"><?php echo htmlspecialchars(stripslashes($data_sample['brief_desc']));?></textarea></td>
    			</tr>
                <tr>
				  <td height="25" valign="top" align="right">Size Requested:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sizerequest" id="size" value="<?php echo htmlspecialchars(stripslashes($data_sample['size']));?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Date Needed:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="dateneeded" id="dateneeded" value="<?php echo $data_sample['dateneeded'];?>"></td>
				</tr>
				
				<tr>
				  <td height="25" valign="top" align="right">Picture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" id="picture1" name="picture1">
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload1" onclick="javascript:if(confirm('Save other informations before uploading')) { return ajaxFileUpload('picture1','I',document.getElementById('id')); } else { return false; }"></td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">File:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" id="file1" name="file1">
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload2" onclick="javascript:if(confirm('Save other informations before uploading')) { ajaxFileUpload('file1','F',document.getElementById('id')); } else { return false; }"></td>
					</tr>
				  </tbody></table></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Detailed Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="detaildesc" name="detaildesc" cols="50" rows="8"><?php echo htmlspecialchars(stripslashes($data_sample['detail_description']));?></textarea></td>
    			</tr> 
                <?php 
				if($emp_type !=2)
				{
				?>
				<tr>
				  <td height="25" valign="top" align="right">Vendor</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><select name="vendorID" style="width:240px">
				   <?php for($i=0; $i <count($data_Vendr); $i++)
				   {
					if($data_sample['vid']==$data_Vendr[$i]['vendorID'])
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
					else 
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
                   }?> 
			      </select></td>
				  </tr>
<?php 
				}
?>
                   <tr>
				  <td height="25" valign="top" align="right">Send Mail to Vendor:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="checkbox" name="mailvendor_check"<?php if($data_sample['mailvendor_check'] == "on") {?> checked="checked" <?php }?> ></td>
				</tr>
                   <tr>
				  <td height="25" valign="top" align="right">Generate PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" readonly="readonly" value="<?php echo stripslashes($data_sample['po_sequence']);?>" name="generate_po" id="generate_po" >
				      <input type="button" name="generate_po_btn" id="generate_po_btn" value="Generate PO" onclick="javascript:GenerateInvoice('generate_po');" /></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Color:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="color" value="<?php echo htmlspecialchars(stripslashes($data_sample['color']));?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  value="<?php echo htmlspecialchars(stripslashes($data_sample['fabricType']));?>" ></td>
				</tr>
<?php 
				if($emp_type !=2)
				{
?>
                <tr>
				  <td height="25" valign="top" align="right">Cost :</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="cost"  value="<?php echo $data_sample['cost'];?>"></td>
				</tr>
<?php 
				}

				if($emp_type !=1)
				{
?>  
				<tr>
				  <td height="25" valign="top" align="right">Customer Target Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerTargetprice" id="customerTargetprice" value="<?php echo stripslashes($data_sample['customerTargetprice']);?>" ></td>
				</tr>
<?php 
				}
?>				
				<tr>
				  <td height="25" valign="top" align="right">In Stock:</td>
				  <td>&nbsp;</td>
                  <td valign="top" align="left">
<?php
					if($data_sample['inStock']) 
					{
?>
					<input type="radio" value="1" name="inStock" checked="checked" id="inStockYes" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="inStock" id="inStockNo" />&nbsp;No 
                    <?php
					}
					else
					{
					?>

<input type="radio" value="1" name="inStock" id="inStockYes"/>&nbsp;Yes &nbsp;<input type="radio" value="0" name="inStock" checked="checked"  id="inStockNo" />&nbsp;No
<?php 
					}
?>
                  
                  
				  </td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Embroidery:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">
<?php 
				if($data_sample['embroidery_new']) 
				{
?>
                  <input type="radio" checked="checked" value="1" name="embroidery" id="embroideryYes">
					Yes	<input type="radio" value="0" name="embroidery" id="embroideryNo">	No 
<?php
				}
				else
				{
?>
                   <input type="radio" value="1" name="embroidery" id="embroideryYes">
					Yes	<input type="radio"  checked="checked" value="0" name="embroidery" id="embroideryNo">	No 
<?php
				}
?>
                    </td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Silk Screening:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">
<?php 
				if($data_sample['silkscreening']) 
				{
?>

                  <input type="radio" checked="checked" value="1" name="silkscreening" id="silkscreeningYes">
					Yes
					<input type="radio" value="0" name="silkscreening" id="silkscreeningNo">
					No 
<?php
				}
				else
				{
?>
                     <input type="radio" value="1" name="silkscreening" id="silkscreeningYes">
					Yes
					<input type="radio"  checked="checked" value="0" name="silkscreening" id="silkscreeningNo">
					No
<?php
				}
?>
                    </td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Customer PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerpo" id="customerpo" value="<?php echo htmlspecialchars(stripslashes($data_sample['customer_po']));?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Internal PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" readonly="readonly" value="<?php echo stripslashes($data_sample['internal_po']);?>" name="internalpo" id="internalpo" >
				      <input type="button" name="invoice_btn" id="invoice_btn" value="Generate Invoice" onclick="javascript:GenerateInvoice('internal_po');" /></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Invoice Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="invoiceno" id="invoiceno" value="<?php echo htmlspecialchars(stripslashes($data_sample['invoicenumber']));?>" ></td>
				</tr>
<?php 
				if($emp_type !=2)
				{
?>                
                 <tr>
				  <td height="25" valign="top" align="right">Client Shipper Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="shipperno" id="shipperno" value="<?php echo htmlspecialchars(stripslashes($data_sample['clientshipper_no']));?>" ></td>
				</tr>
             
                 <tr>
				  <td height="25" valign="top" align="right">Return Authorization:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="returnauth" id="returnauth" value="<?php echo htmlspecialchars(stripslashes($data_sample['returnauthor']));?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Order/Confirmation #:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="order_confirmation"  value="<?php echo  htmlspecialchars(stripslashes($data_sample['order_confirmation']));?>"></td>
				</tr>
                
                       <tr>
				  <td height="25" valign="top" align="right">Order Placed On:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="order_on" onclick="javascript:showDate(this);" value="<?php echo  htmlspecialchars(stripslashes($data_sample['order_on']));?>"></td>
				</tr>
                
                 <tr>
				  <td height="25" valign="top" align="right">Bid Number:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="bid_number"  value="<?php echo  htmlspecialchars(stripslashes($data_sample['bid_number']));?>"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Project Budget:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="project_budget"  value="<?php echo  htmlspecialchars(stripslashes($data_sample['project_budget']));?>"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Carrier:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><select name="carrier_shipping" onchange="javascript:show_weblink(this, <?php echo $i; ?>);">
                <option value="0">----- select -----</option>
                <?php
				for($index=0; $index<count($data_carrier); $index++)
				{
					if($data_carrier[$index]['carrier_id'] == $data_sample['carrier_shipping'])
					{
?>
                <option value="<?php echo $data_carrier[$index]['carrier_id'];?>" selected="selected"><?php echo $data_carrier[$index]['carrier_name'];?></option>
                <?php 
					}
					else
					{
?>
                <option value="<?php echo $data_carrier[$index]['carrier_id'];?>"><?php echo $data_carrier[$index]['carrier_name'];?></option>
                <?php
					}
				}
?>
              </select></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Tracking Number:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="tracking_number" value="<?php echo  htmlspecialchars(stripslashes($data_sample['tracking_number']));?>"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Shipped On:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="shipped_on" onclick="javascript:showDate(this);" value="<?php echo  htmlspecialchars(stripslashes($data_sample['shipped_on']));?>"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Shipping Notes:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><textarea name="shipping_notes" cols="30" rows="4"><?php echo  htmlspecialchars(stripslashes($data_sample['shipping_notes']));?></textarea></td>
				</tr>
            	<tr>
				  <td height="25" valign="top" align="right">Add Notes:</td>
                  <td>&nbsp;</td>
                  <td valign="top">
					<table width="80%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
                    <tbody>
                      <tr>
                        
<?php 
	echo "<td align=\"left\" valign=\"top\" colspan=\"4\"><a style=\"cursor:hand;cursor:pointer;\" name=\"addNotes\" id=\"addNotes\" onClick=\"javascript:popOpen('');\"><img height=\"25px\" width=\"120px\"src=\"$mydirectory/images/addNotes.gif\" alt=\"notes\"/></a></td></tr>";
	 
    if($id)
    {
        for($i=0; $i<count($data_notes); $i++)
        {
?>   
        <tr>
<?php
			$limitNotes = substr($data_notes[$i]['notes'],0,10);
			echo " <td width=\"100px\">Notes ".($i+1).": </td>";
			echo " <td >&nbsp;</td>";
            echo " <td width=\"150px\" >".htmlspecialchars(stripslashes($limitNotes))."</td>";
			 echo " <td width=\"150px\" ><a style=\"cursor:hand;cursor:pointer;\" onclick=\"javascript:popOpen('".txtAreaId.($i+1)."', '".($i+1)."' );\">Read more...</a></td>";
			echo " <td >&nbsp;</td>";
			echo " <td ><textarea id=\"txtAreaId".($i+1)."\" name=\"textAreaName[]\" style=\"display:none\">".htmlspecialchars(stripslashes($data_notes[$i]['notes']))."</textarea>
			       <input type='hidden' id=\"dateTimeId".($i+1)."\" value=\"".date("d-m-Y g:i A", $data_notes[$i]['createdDate'])."\" />
				   <input type='hidden'  id=\"hdnNotesId".($i+1)."\" name=\"hdnNotesName[]\" value=\"".$data_notes[$i]['notesId']."\" />
				   <input type='hidden' id=\"empNameId".($i+1)."\" value=\"".$data_notes[$i]['firstName']." ".$data_notes[$i]['lastName']. "\" /></td>";
?>
        </tr>
      
 <?php
        }
    }
?>          
                      </tbody>
                    </table>
                    </td>
				</tr>
<?php
				}
?>   
                <tr>
                <td align="right">Send Email to Client:</td>
                <td>&nbsp;</td>
                <td align="left"><input name="is_mail" type="radio" value="1" />Yes &nbsp;<input name="is_mail" type="radio" value="0" checked="checked"/>No
                </td>
                </tr>
				<tr>
				  <td height="25" align="right"><input type="hidden" id="id" name="id" value="<?php echo $id;?>" /><input type="hidden" id="isEdit" name="isEdit" value="<?php echo $isEdit;?>" /><input type="submit" value="Save" onmouseover="this.style.cursor = 'pointer';" name="btnSave"></td>
				  <td>&nbsp;</td>
				  <td align="left"><input type="button" value="Cancel" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='samplerequest.list.php'" name="btnCancel"></td>
				</tr>
			  </tbody></table>
</td>
<td valign="top">

<table  border="0" cellspacing="0" cellpadding="0">
<?php
if($isEdit)
{
if(count($imageArr))
{
	for($i=0; $i<count($imageArr); $i++)
	{
?>

      <tr>
        <td height="25">Image</td>
      </tr>
      <tr>
        <td>
        
<?php
		if($imageArr[$i] != "" )
		{
?>         
        	<img src="<?php echo ($upload_dir.$imageArr[$i]['file']);?>" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
             <a style="cursor:hand;cursor:pointer;" onClick="javascript:return DeleteUploads('<?php echo $imageArr[$i]['id'];?>','<?php echo addslashes($imageArr[$i]['file']);?>','<?php echo $id;?>');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
    </a> 
<?php
		}
?>
     
        </td>
      </tr>
<?php
	}
}
if($data_sample['picture1'] !="")
{
?>	
	 <tr>
        <td height="25">Image</td>
      </tr>
      <tr>
        <td>
	<img src="<?php echo ($upload_dir.stripslashes($data_sample['picture1']));?>" width="101" height="89" id="pic1" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
     </td>
    </tr>
<?php         
}
if($data_sample['picture2'] !="")
{
?>
	 <tr>
        <td height="25">Image</td>
      </tr>
      <tr>
        <td>
	<img src="<?php echo ($upload_dir.stripslashes($data_sample['picture2']));?>" width="101" height="89" id="pic2" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
   
    </td>
    </tr>
<?php 	
}
if(count($fileArr))
{
	for($i=0; $i<count($fileArr); $i++)
	{
?>

      <tr>
        <td height="25">Files</td>
      </tr>
      <tr>
        <td>
        
<?php
		if($fileArr[$i] != "")
		{ ?>    
			<strong><?php echo (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-")+1))); ?></strong>
            <a href="download.php?file=<?php echo $fileArr[$i]['file'];?>"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download"/></a>
             <a href="javascript:void(0);" onClick="javascript:return DeleteUploads('<?php echo $fileArr[$i]['id'];?>','<?php echo addslashes($fileArr[$i]['file']);?>','<?php echo $id;?>');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a>
<?php		
        }
?>
      </td>
      </tr>
<?php
	}
}
}
?>
</table>


</td>
</tr>
</table>

			  
		  </font></td>
		</tr>
	  </tbody></table>
      
      
        <div id="textPop" class="popup_block">
<center><div ><strong>Sample Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><textarea id="notes" name="notesId" cols="60" rows="10"></textarea></td>
    </tr>
    <tr>
        <td align="center"><input type="button" name="notesSubmit" id="notesSubmit" value="Submit" onclick="javascript:onNotesSubmit('prjNotes',document.getElementById('notes'));Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="textPop" class="popup_block">

<center><div><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><textarea id="notes" name="notesId" cols="60" rows="10"></textarea></td>
    </tr>
    <tr>
        <td align="center"><input type="button" name="notesSubmit" id="notesSubmit" value="Submit" onClick="javascript:onNotesSubmit('prjNotes',document.getElementById('notes'));Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">


<tr id="tr_popEmpId" style="display:none">
<td width="100px" align="left"><strong>Added By : </strong></td>
<td width="5px">&nbsp;</td><td align="left" id="td_popEmpId"></td>
</tr>
<tr id="tr_popDateTimeId" style="display:none">
<td width="100px" align="left">
<strong>Added Date : </strong></td>
<td width="5px">&nbsp;</td>
<td align="left" id="td_popDateTimeId"></td>
</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>



<tr>
<td width="100px" align="left"><strong>Notes :</strong></td>
<td>&nbsp;</td>
</tr>
  <tr>
    <td width="100"  align="left"><p id="editPopId"></p></td>
    <td width="10">&nbsp;</td>
    
  </tr>
</table>
</div>
</div>
</div></form>
	  <p> </p>
</center>

<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min-1.4.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
<script src="<?php echo $mydirectory;?>/js/PopupBox.js" type="text/javascript"></script>
<script type="text/javascript">
if($("#dateneeded"))
{
	$(function() 
	 {
	  $("#dateneeded").datepicker();
	 });
}
$('#ordered_date').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });

$("#validationForm").validate({
	rules: {
			cost:  {number: true},
			dateneeded:  {date: true},
			ordered_date:  {date: true},
			customerTargetprice:  {number: true},
			project_budget:  {number: true}
		},
		messages: {
			cost: "Please enter in digits",
			dateneeded : "Please select a valid date",
			ordered_date : "Please select a valid date",
			customerTargetprice : "Please enter in digits",
			project_budget : "Please enter in digits"
			}
	});
function DeleteUploads(id,filename,sample_id)
{
	var dataString = "filename="+filename+"&tableid="+id+"&sample_id="+sample_id;
	$.ajax({
		   type: "POST",
		   url: "delete_uploads.php",
		   data: dataString,
		   dataType: "json",
		   timeout : 60000,
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>File Removed...</strong></div>");
						$(location).attr("href","samplerequest_new.add.php?id="+data.id);
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
function GenerateInvoice(type)
{
	var invoiceval = document.getElementById('internalpo');
	var po_sequence_val = document.getElementById('generate_po');
	var dataString ='type='+type;
	$.ajax({
		   type: "POST",
		   url: "invoicegenerate.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Invoice Generated.</strong></div>");
						if(type == 'generate_po')
						po_sequence_val.value = data.value;
						else if(type == 'internal_po')
						invoiceval.value = data.value;
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}

function popOpen(txtId, rowIndex)
{
	if(txtId == null || txtId == "")
	{
		var popID = 'textPop'; //Get Popup Name
	}
	else
	{
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML =document.getElementById(txtId).value;
		if(rowIndex != null && rowIndex != "")
		{
			document.getElementById('td_popEmpId').innerHTML =document.getElementById('empNameId'+rowIndex).value;
			document.getElementById('td_popDateTimeId').innerHTML =document.getElementById('dateTimeId'+rowIndex).value;
			document.getElementById('tr_popEmpId').style.display = 'block';
			document.getElementById('tr_popDateTimeId').style.display = 'block';
		}
		else
		{
			document.getElementById('td_popEmpId').innerHTML = "";
			document.getElementById('td_popDateTimeId').innerHTML = "";
			document.getElementById('tr_popEmpId').style.display = 'none';
			document.getElementById('tr_popDateTimeId').style.display = 'none';
		}
		
	}
	
	popWidth = '500'; $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
			
	//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;
			
	//Apply Margin to Popup
	$('#' + popID).css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});	
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 		
	}
	//Close Popups and Fade Layer
	$('span.close, #fade, #cancel').live('click', function() { //When clicking on the close or fade layer...
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
	return false;
});
	function Fade()
	{
		$('#fade , .popup_block').fadeOut();
		document.getElementById('notes').value="";
	}
function onNotesSubmit(tableId,textId)
{
	var table = document.getElementById(tableId);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	cell1.width="50px";		
	cell1.innerHTML = "Notes "+rowCount+":";	
	var cell2 = row.insertCell(1);
	cell2.width="10px";		
	cell2.innerHTML = "&nbsp;";	
	
	var noteslimit=textId.value;
	
	if(noteslimit.length > 10)
	{
	 noteslimit= noteslimit.substr(0,10);
	}
	var cell3 = row.insertCell(2);
	cell3.width="150px";		
	cell3.innerHTML = noteslimit;	
	
	var cell7 = row.insertCell(3);
	cell7.width ="150px";
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "Read more...";
	element1.onclick = function(){popOpen('txtAreaId'+rowCount+'');};
	cell7.appendChild(element1);
	var cell4 = row.insertCell(4);
	cell4.width="10px";
	cell4.innerHTML = "&nbsp;";
	
	var cell5 = row.insertCell(5);
	var element2 = document.createElement("textarea");
	element2.name = "textAreaName[]";
	element2.id = 'txtAreaId'+rowCount;
	element2.value = textId.value;
	element2.style.display = "none";
	var element3 = document.createElement("input");
	element3.name = "hdnNotesName[]";
	element3.id = 'hdnNotesId'+rowCount;
	element3.value = 0;
	element3.style.display = "none";
	cell5.appendChild(element2);
	cell5.appendChild(element3);
	var cell6 = row.insertCell(6);	
	cell6.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentRow(this);\"><img style=\"width:32px;height:25px;\" src=\"<?php echo $mydirectory;?>/images/delete.png\" ></a>";
	
	
}
function DeleteCurrentRow(obj)
{
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}
function DeleteRow(rowObjArray)
{	
	for (var i=0; i<rowObjArray.length; i++) {
		var rIndex = rowObjArray[i].sectionRowIndex;
		rowObjArray[i].parentNode.deleteRow(rIndex);
	}	
}

</script>
<script type="text/javascript">
$(function(){$("#validationForm").submit(function(){
if($("#validationForm").valid())
	{
	  var pid = document.getElementById('pid');
	  dataString = $("#validationForm").serialize();
	  $.ajax({
			 type: "POST",
			 url: "samplerequest.add1.php",
			 data: dataString,
			 dataType: "json",
			 timeout : 60000,
			 success:function(data)
			 {
				 if(data!=null)
				 {
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					}
					else
					{
						if(document.getElementById('isEdit').value==1)
						{
							$("#message").html("<div class='successMessage'><strong>Sample Requset Updated. Thank you.</strong></div>");
						}
						else
						{
							$("#message").html("<div class='successMessage'><strong>New Project Management Information Added. Thank you.</strong></div>");
						}
						//$(location).attr("href","samplerequest_new.add.php?id="+data.id);
					}
				  } 
				  else
				  {
				 	$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				  }
			 }
			});
	  return false;
	  }
	  });
});
</script>
<script type="text/javascript" >
function ajaxFileUpload(fileId,type,samplerequestId)
{
	$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});
		var id = document.getElementById('id').value;
		var sample_id = document.getElementById('srID').value;		
		var uploadpicture = document.getElementById('picture1');
		var uploadfile = document.getElementById('file1');
		$.ajaxFileUpload
		(
			{
				url:'doajaxfileupload.php',
				secureuri:false,
				fileElementId:fileId,
				dataType: 'json',
				data:{uploadpicture:uploadpicture.value,uploadfile:uploadfile.value,fileId:fileId, id:id,type:type,srID:sample_id},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							$("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
						}
						else
						{
							$("#message").html("<div class='successMessage'><strong>"+data.msg +"</strong></div>");
							document.getElementById(fileId).value="";
							$(location).attr("href","samplerequest_new.add.php?id="+data.id);							
						}
					}
				},
				error: function (data, status, e)
				{
					$("#message").html("<div class='errorMessage'><strong>"+e+"</strong></div>");
				}
			}
		)
		
		return false;
}
function show_weblink(obj, index)
{
	var sel = obj.options[obj.selectedIndex].value;
	var dataString = "carrier_id="+sel+'&index='+index;
	$.ajax({
	 type: "POST",
	 url: "prj_carrier_weblink.php",
	 data: dataString,
	 dataType: "json",
	 success:
	 function(data)
	 {
		 if(data!=null)
		 {
			 if(data.error)
			 {
				 $("#message").html("<div class='errorMessage'><strong>Sorry, "+data.error +"</strong></div>");
			 }
			 else
			 {
				  $("#weblink_id"+data.index).html("");
				 if(data.weblink != "" && data.index >= 0)
				 {					
					  $("#weblink_id"+data.index).html('<div><a href="javascript:void(0);" onClick="javascript:popupWindow(\''+data.weblink+document.getElementById('track_num'+data.index).value+'\');" ><img src="<?php echo $mydirectory; ?>/images/courier_man.jpg" width="50"/></a></div>');
				 }
			 }
		 }
		 else								   
		 {
			 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		 }
	 }
	 });
	return false;
}
function showDate(obj)
{
	$(obj).datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(obj).datepicker('show'); });
	$(obj).datepicker('show');
}
</script>
<?php
require('../../trailer.php');
?>