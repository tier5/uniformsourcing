<?php
require('Application.php');
require('../../header.php');
$isEdit = 0;
$id = 0;
if(isset($_GET['id']))
{
	$isEdit = 1;
	$id = $_GET['id'];
	
	$sql = "Select tbl_sample_database.*,vendor.\"vendorName\",vendor.address from tbl_sample_database  left join vendor on vendor.\"vendorID\"=tbl_sample_database.vid where status =1 and sample_id = $id";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_sample = $row;
	}
	$sql = "Select * from tbl_sample_database_uploads where status =1 and sample_id = $id";
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
			$imageArr[$img]['id'] = $data_Uploads[$i]['upload_id'];
			$imageArr[$img++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
		else if(trim($data_Uploads[$i]['uploadtype']) == 'F')
		{
			$fileArr[$file]['id'] = $data_Uploads[$i]['upload_id'];
			$fileArr[$file++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
	}
	pg_free_result($result);
	
}

$sql='select (Max("sample_id")+1) as "sample_id" from tbl_sample_database ';
if(!($result_cnt=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
	$data_cnt=$row_cnt;
}
if(! isset($data_cnt['sample_id'])) { $data_cnt['sample_id']=1; }
if(!$data_cnt['sample_id']) { $data_cnt['sample_id']=1;  }

$sql = "select * from tbl_sample_db_type where status =1";
	if(!($result=pg_query($connection,$sql))){
	print("Failed sample_type_sql: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_sample_type[]=$row;
} 
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
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='database_sample_list.php';" /></td>
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
			  <font size="5"><?php if($isEdit){?>Edit <?php }else {?> Add<?php }?> Database Samples <br>
			  <br>
			  </font>
        
<table>
<tr>
<td>
<table width="85%" cellspacing="1" cellpadding="1" border="0">
				<tbody>
                  <tr>
                    <td height="25" valign="top" align="right">Brand/Manufacture:</td>
                    <td>&nbsp;</td>
                    <td valign="top" align="left"><input type="text" class="textBox" name="brand_manufac" id="brand_manufac" value="<?php echo htmlentities($data_sample['brand_manufct']);?>"></td>
                  </tr>
				  <tr>
				  <td height="25" valign="top" align="right">Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="<?php echo htmlentities($data_sample['sample_id_val']);?>"></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Style Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="style" value="<?php echo htmlentities($data_sample['style_number']);?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Type of samples:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><select name="sample_type" style="width:240px">
				   <?php for($i=0; $i <count($data_sample_type); $i++)
				   {
					if($data_sample['sample_type_id']==$data_sample_type[$i]['type_id'])
						echo '<option value="'.$data_sample_type[$i]['type_id'].'" selected="selected">'.$data_sample_type[$i]['sample_type'].'</option>';
					else 
						echo '<option value="'.$data_sample_type[$i]['type_id'].'">'.$data_sample_type[$i]['sample_type'].'</option>';
                   }?> 
			      </select></td>
				  </tr>
                 <tr>
                 <td height="25" valign="top" align="right">Brief Sample Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="briefdesc" name="briefdesc" cols="30" rows="4"><?php echo htmlentities($data_sample['brief_desc']);?></textarea></td>
    			</tr>
				
				<tr>
				  <td height="25" valign="top" align="right">Picture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
				    <tbody><tr>
				      <td valign="top" align="left"><input type="file" id="picture1" name="picture1">
				        <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload1" onclick="javascript:if(confirm('Enter other information before uploading')) { return ajaxFileUpload('picture1','I',document.getElementById('id')); } else { return false; }"></td>
				      </tr>
				      </tbody></table></td>
				  </tr>
				<tr>
				  <td height="25" valign="top" align="right">File:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" id="file1" name="file1">
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload2" onclick="javascript:if(confirm('Enter other information before uploading')) { return ajaxFileUpload('file1','F',document.getElementById('id')); } else { return false; }"></td>
					</tr>
				  </tbody></table></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Detailed Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="detaildesc" name="detaildesc" cols="50" rows="8"><?php echo htmlentities($data_sample['detail_description']);?></textarea></td>
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
				  <td valign="top" align="left"><input type="text" class="textBox" name="color" value="<?php echo htmlentities($data_sample['color']);?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  value="<?php echo htmlentities($data_sample['fabric']);?>" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Cost :</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="cost"  value="<?php echo $data_sample['samplecost'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right"><font face="arial">Re</font>tail Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="retailprice" id="retailprice" value="<?php echo $data_sample['retailprice'];?>" ></td>
				</tr>
				
				<tr>
				  <td height="25" valign="top" align="right">In Stock:</td>
				  <td>&nbsp;</td>
                  <td valign="top" align="left">
<?php
					if($data_sample['instock']) 
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
	</div>
</div></form>
	  <p> </p>
</center>

<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min-1.4.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script type="text/javascript">
$("#validationForm").validate({
	rules: {
			cost:  {number: true},
			retailprice:  {number: true}
		},
		messages: {
			cost: "Please enter in digits",
			retailprice : "Please enter in digits"
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
						$(location).attr("href","database_sample_add.php?id="+data.id);
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
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
							$("#message").html("<div class='successMessage'><strong>Database Sample Updated. Thank you.</strong></div>");
							
						}
						else
						{
							$("#message").html("<div class='successMessage'><strong>New Database Sample Added. Thank you.</strong></div>");
						}
						$(location).attr("href","database_sample_add.php?id="+data.id);
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
							$(location).attr("href","database_sample_add.php?id="+data.id);							
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
</script>
<?php
require('../../trailer.php');
?>