
<table>
<tr>
<td>
<table width="85%" cellspacing="1" cellpadding="1" border="0">
				<tbody><tr>
				    <td height="25" valign="top" align="right">Choose Client:</td>
				    <td>&nbsp;</td>
				    <td valign="top" align="left"><?php echo $data_sample['brand_manufct'];?></td>
			      </tr>
                  <tr>
				  <td height="25" valign="top" align="right">Brand/Manufacture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="brand_manufac" id="brand_manufac" value="<?php echo $data_sample['brand_manufct'];?>"></td>
				</tr>
				  <tr>
				  <td height="25" valign="top" align="right">Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="<?php echo $data_sample['srID'];?>"></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Style Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="style" value="<?php echo $data_sample['styleNo'];?>" ></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Brief Sample Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="briefdesc" name="briefdesc" cols="30" rows="4"><?php echo $data_sample['brief_desc'];?></textarea></td>
    			</tr>
                <tr>
				  <td height="25" valign="top" align="right">Size Requested:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sizerequest" id="size" value="<?php echo $data_sample['size'];?>"></td>
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
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload1" onclick="javascript:return ajaxFileUpload('picture1','I',document.getElementById('id'));"></td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">File:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" id="file1" name="file1">
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload2" onclick="javascript:return ajaxFileUpload('file1','F',document.getElementById('id'));"></td>
					</tr>
				  </tbody></table></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Detailed Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="detaildesc" name="detaildesc" cols="50" rows="8"><?php echo $data_sample['detail_description'];?></textarea></td>
    			</tr>                           
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
				<tr>
				  <td height="25" valign="top" align="right">Color:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="color" value="<?php echo $data_sample['color'];?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  value="<?php echo $data_sample['fabricType'];?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Cost :</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="cost"  value="<?php echo $data_sample['cost'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Customer Target Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerTargetprice" id="customerTargetprice" value="<?php echo $data_sample['customerTargetprice'];?>" ></td>
				</tr>
				
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
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerpo" id="customerpo" value="<?php echo $data_sample['customer_po'];?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Internal PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" readonly="readonly" value="<?php echo $data_sample['internal_po'];?>" name="internalpo" id="internalpo" >
				      <input type="button" name="invoice_btn" id="invoice_btn" value="Generate Invoice" onclick="javascript:GenerateInvoice();" /></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Invoice Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="invoiceno" id="invoiceno" value="<?php echo $data_sample['invoicenumber'];?>" ></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Client Shipper Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="shipperno" id="shipperno" value="<?php echo $data_sample['clientshipper_no'];?>" ></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Return Authorization:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="returnauth" id="returnauth" value="<?php echo $data_sample['returnauthor'];?>" ></td>
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
            echo " <td width=\"150px\" >".$limitNotes."</td>";
			 echo " <td width=\"150px\" ><a style=\"cursor:hand;cursor:pointer;\" onclick=\"javascript:popOpen('".txtAreaId.($i+1)."', '".($i+1)."' );\">Read more...</a></td>";
			echo " <td >&nbsp;</td>";
			echo " <td ><textarea id=\"txtAreaId".($i+1)."\" name=\"textAreaName[]\" style=\"display:none\">".stripslashes($data_notes[$i]['notes'])."</textarea>
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
        	<img src="<?php echo ($upload_dir.$imageArr[$i]['file']);?>" width="101" height="89" id="thumb_image3" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
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
?>
<?php
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