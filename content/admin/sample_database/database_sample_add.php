<?php
require('Application.php');
require('../../header.php');
$isEdit = 0;
$id = 0;
if(isset($_GET['id']))
{
	$isEdit = 1;
	$id = $_GET['id'];
	
	$query3 = "SELECT tbl_sample_database.*,".  // was sql1
		"vendor.\"vendorName\",".
		"vendor.address ".
		"FROM tbl_sample_database  ".
		"LEFT JOIN vendor ON vendor.\"vendorID\" = tbl_sample_database.vid WHERE status = 1 AND sample_id = $id";
	if(!($result3 = pg_query($connection,$query3))){
		print("Failed query3:<br> $query3 <br><br> " . pg_last_error($connection));
		exit;
	}
	while($row3 = pg_fetch_array($result3)){
		$data3 = $row3;
	}

	$query4 = "SELECT * ".  // was sql2
		"FROM tbl_sample_database_uploads ".
		"WHERE status = 1 AND sample_id = $id";
	if(!($result4 = pg_query($connection,$query4))){
		print("Failed query4:<br> $query4 <br><br> " . pg_last_error($connection));
		exit;
	}
	while($row4 = pg_fetch_array($result4)){
		$data4[] = $row4;
	}

	$imageArr = array();
	$fileArr = array();
	for($i = 0, $img= 0, $file = 0; $i < count($data4); $i++)
	{
		if(trim($data4[$i]['uploadtype']) == 'I')
		{
			$imageArr[$img]['id'] = $data4[$i]['upload_id'];
			$imageArr[$img++]['file'] = stripslashes($data4[$i]['filename']);
		}
		else if(trim($data4[$i]['uploadtype']) == 'F')
		{
			$fileArr[$file]['id'] = $data4[$i]['upload_id'];
			$fileArr[$file++]['file'] = stripslashes($data4[$i]['filename']);
		}
	}
	pg_free_result($result4);


}

$query5 = "SELECT (Max(\"sample_id\")+1) AS \"sample_id\" ". // was sql3
	"FROM tbl_sample_database ";
if(!($result5 = pg_query($connection,$query5))){
	print("Failed query5:<br> $query5 <br><br> " . pg_last_error($connection));
	exit;
}
while($row5 = pg_fetch_array($result5)){
	$data5 = $row5;
}

if(! isset($data5['sample_id'])) { 
	$data5['sample_id']=1; 
}

if(!$data5['sample_id']) { 
	$data5['sample_id']=1;  
}

$query6 = "SELECT * ". // was sql4
	"FROM tbl_sample_db_type ".
	"WHERE status = 1";
if(!($result6 = pg_query($connection,$query6))){
	print("Failed query6:<br> $query6 <br><br> " . pg_last_error($connection));
	exit;
}
while($row6 = pg_fetch_array($result6)){
	$data6[] = $row6;
} 

$query8 = "SELECT \"vendorID\", \"vendorName\", \"active\" ". // used to be queryVendor
	"FROM \"vendor\" ".
	"WHERE \"active\" = 'yes' ".
	"ORDER BY \"vendorName\" ASC ";
if(!($result8 = pg_query($connection,$query8))){
	print("Failed query8:<br> $query8 <br><br> " . pg_last_error($connection));
	exit;
}
while($row8 = pg_fetch_array($result8)){
	$data8[] = $row8;
}

$query1 = ("SELECT \"ID\", \"clientID\", \"client\", \"active\" " .
	"FROM \"clientDB\" " .
	"WHERE \"active\" = 'yes' " .
	"ORDER BY \"client\" ASC");
if (!($result1 = pg_query($connection, $query1))) {
	print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
	exit;
}
while ($row1 = pg_fetch_array($result1)) {
	$data1[] = $row1;
}
pg_free_result($result1);

$query7 = "SELECT DISTINCT \"locationId\",\"name\" ".
	"FROM \"tbl_invLocation\" ";
if(!($result7 = pg_query($connection,$query7))){
	print("Failed query7:<br> $query7 <br><br> " . pg_last_error($connection));
	exit;
}
while($row7 = pg_fetch_array($result7)){
	$data7[] = $row7;
}

$query2 = "SELECT \"garmentID\",\"garmentName\" ".
	"FROM \"tbl_garment\" ".
	"WHERE status = 1";
if(!($result2 = pg_query($connection,$query2))){
	print("Failed query2:<br> $query2 <br><br> " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[] = $row2;
}
pg_free_result($result2);

$query9 = "SELECT * ".
	"FROM \"sample_conveyor\" ".
	"WHERE active = 1 ";
if(!($result9 = pg_query($connection,$query9))){
	print("Failed query9:<br> $query9 <br><br> " . pg_last_error($connection));
	exit;
}
while($row9 = pg_fetch_array($result9)){
	$data9[] = $row9;
}
pg_free_result($result9);

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
					<font size="5"><?php if($isEdit){?>Edit <?php }else {?> Add<?php }?> Database Samples <br><br></font>

					<table>
						<tr>
							<td valign="top">
								<table width="100%" cellspacing="1" cellpadding="1" border="0">
									<tbody>
										<tr>
											<td height="25" valign="top" align="right">Client:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="clientname">                   
													<option value="0">-- NA --</option>
<?php 
for($i=0; $i < count($data1); $i++){
	if($data3['client'] == $data1[$i]['ID']){
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	}else{ 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
	}
}
?>  
												</select>
											</td>
										</tr>
		
										<tr>
											<td height="25" valign="top" align="right">Department:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="department" value="<?php echo htmlentities($data3['department']);?>" ></td>
										</tr>              
										<tr>
											<td height="25" valign="top" align="right">Type of samples:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="sample_type" style="width:240px">
<?php 
for($i=0; $i < count($data6); $i++){
	if($data3['sample_type_id'] == $data6[$i]['type_id']){
		echo '<option value="'.$data6[$i]['type_id'].'" selected="selected">'.$data6[$i]['sample_type'].'</option>';
	}else{
		echo '<option value="'.$data6[$i]['type_id'].'">'.$data6[$i]['sample_type'].'</option>';
	}
}
?> 
												</select>
											</td>
										</tr>                 
										<tr>
											<td height="25" valign="top" align="right">Sample Name:</td>
											<td width="10">&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="<?php echo htmlentities($data3['sample_id_val']);?>"></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Vendor</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="vendorID" style="width:240px">
													<option value="0">---SELECT---</option>
<?php 
for($i=0; $i < count($data8); $i++){
	if($data3['vid'] == $data8[$i]['vendorID']){
		echo '<option value="'.$data8[$i]['vendorID'].'" selected="selected">'.$data8[$i]['vendorName'].'</option>';
	}else{
		echo '<option value="'.$data8[$i]['vendorID'].'">'.$data8[$i]['vendorName'].'</option>';
	}
}
?> 
												</select>
											</td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Style Number:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="style" value="<?php echo htmlentities($data3['style_number']);?>" ></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Size:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="size_field" value="<?php echo htmlentities($data3['size_field']);?>" ></td>
										</tr>  
										<tr>
											<td height="25" valign="top" align="right">Garment:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="garment" id="garment">
													<option value="">--- Select Garment ------</option>
<?php 
for($i=0; $i < count($data2); $i++){
	if($data3['garment'] != $data2[$i]['garmentID']){
		echo '<option value="'.$data2[$i]['garmentID'].'">'.$data2[$i]['garmentName'].'</option>';
	}else{ 
		echo '<option value="'.$data2[$i]['garmentID'].'" selected="selected">'.$data2[$i]['garmentName'].'</option>';
	}
}
?>
												</select>                                 
											</td>
										</tr>                              
								
										<tr>
											<td height="25" valign="top" align="right">Description:</td>
											<td width="10">&nbsp;</td>
											<td align="left"><textarea id="detaildesc" name="detaildesc" cols="30" rows="4"><?php echo htmlentities($data3['detail_description']);?></textarea></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Color:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="color" value="<?php echo htmlentities($data3['color']);?>" ></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Fabric: </td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  value="<?php echo htmlentities($data3['fabric']);?>" ></td>
										</tr>
										<tr>
											<td height="25" width="300px" valign="top" align="right">Embroidery/Silk Screening:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
<?php 
if($data3['embroidery_new']){
?>
												<input type="radio" checked="checked" value="1" name="embroidery" id="embroideryYes">Yes
												<input type="radio" value="0" name="embroidery" id="embroideryNo">No 
<?php
}else{
?>
												<input type="radio" value="1" name="embroidery" id="embroideryYes">Yes	
												<input type="radio"  checked="checked" value="0" name="embroidery" id="embroideryNo">No
<?php
}
?>
											</td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Cost :</td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="cost"  value="<?php echo $data3['samplecost'];?>"></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right"><font face="arial">Retail Price:</font></td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="retailprice" id="retailprice" value="<?php echo $data3['retailprice'];?>" ></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right"><font face="arial">Conveyor:</font></td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="conveyor">
													<option value="0">--SELECT--</option>
<?php
for($i=0; $i < count($data9); $i++){
	if($data3['conveyor'] == $data9[$i]['id']){
		echo '<option value="'.$data9[$i]['id'].'" selected="selected">'.$data9[$i]['name'].'</option>';
	}else{
		echo '<option value="'.$data9[$i]['id'].'">'.$data9[$i]['name'].'</option>';
        }
}
?>
												</select>
											</td>
										</tr>
											<td height="25" valign="top" align="right"><font face="arial">Slot:</font></td>
											<td>&nbsp;</td>
											<td valign="top" align="left"><input type="text" class="textBox" name="slot" id="slot" value="<?php echo $data3['slot'];?>" ></td>
										</tr>
										<tr>
											<td height="25" valign="top" align="right">Notes:</td>
											<td width="10">&nbsp;</td>
											<td align="left"><textarea id="notes" name="notes" cols="30" rows="4"><?php echo htmlentities($data3['notes']);?></textarea></td>
										</tr>
<?php
if($isEdit){
?>
										<tr>
											<td height="25" valign="top" align="right">Picture:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<table width="250" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="top" align="left">
																<input type="file" name="picture1" id="picture1" onchange="javascript:ajaxFileUpload('picture1','I',document.getElementById('id'));" />
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
		  
										<tr>
											<td height="25" valign="top" align="right">File:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<table width="250" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="top" align="left">
																<input type="file" id="file1" name="file1">
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload2" onclick="javascript:if(confirm('Enter other information before uploading')) { return ajaxFileUpload('file1','F',document.getElementById('id')); } else { return false; }">
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
<?php 
} 
?>
										<tr>
											<td height="25" valign="top" align="right">Location:</td>
											<td>&nbsp;</td>
											<td valign="top" align="left">
												<select name="location">
													<option value="">--- Select Locations-----</option>
<?php 
for($i=0; $i < count($data7); $i++){
	if($data3['location'] == $data7[$i]['locationId']){
		echo '<option value="'.$data7[$i]['locationId'].'" selected="selected">'.$data7[$i]['name'].'</option>';
	}else{
		echo '<option value="'.$data7[$i]['locationId'].'">'.$data7[$i]['name'].'</option>';
	}
}
?>  
		  										</select>
											</td>
										</tr>

										<tr>
											<td height="25" align="right">
												<input type="hidden" id="id" name="id" value="<?php echo $id;?>" />
												<input type="hidden" id="isEdit" name="isEdit" value="<?php echo $isEdit;?>" />
												<input type="submit" value="Save" onmouseover="this.style.cursor = 'pointer';" name="btnSave">
											</td>
											<td>&nbsp;</td>
											<td align="left">
												<input type="button" value="Cancel" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='samplerequest.list.php'" name="btnCancel">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="10px;" valign="top">&nbsp;</td>
							<td valign="top" width="100%">
								<table  border="0" cellspacing="0" cellpadding="0" width="100%">
<?php 
if(count($imageArr)){
?>   
									<tr>
										<td>Images</td>
									</tr> 
<?php 
}
?>
									<tr>
										<td>
<?php
if($isEdit){
	if(count($imageArr)){
		for($i=0; $i < count($imageArr); $i++){
?>

											<div style="width:150px;height:100px;float:left;">
<?php
			if($imageArr[$i] != "" ){
?>
												<img src="<?php echo ($upload_dir.$imageArr[$i]['file']);?>" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
												<a style="cursor:hand;cursor:pointer;" onClick="javascript:return DeleteUploads('<?php echo $imageArr[$i]['id'];?>','<?php echo addslashes($imageArr[$i]['file']);?>','<?php echo $id;?>');">
													<img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
													</a> 
<?php
			}
?>
     
											</div>
<?php
		}
	}
} // martin added this
?>
										</td>
									</tr>
<?php 
if(count($fileArr)){
?>
									<tr>
										<td>Files</td>
									</tr>
<?php 
}
?>
									<tr>
										<td>
<?php	
if(count($fileArr)){
	for($i=0; $i<count($fileArr); $i++){
?>

											<div style="width:150px;height:100px;float:left;">
	     
<?php
		if($fileArr[$i] != ""){ 
?>    
												<strong><?php echo (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-")+1))); ?></strong>
												<a href="download.php?file=<?php echo $fileArr[$i]['file'];?>"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download"/></a>
												<a href="javascript:void(0);" onClick="javascript:return DeleteUploads('<?php echo $fileArr[$i]['id'];?>','<?php echo addslashes($fileArr[$i]['file']);?>','<?php echo $id;?>');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a>

<?php
		}
?>
											</div>
<?php
	}
} 
?>
										</td>
									</tr>
<?php 
//}
?>
								</table>
							</td>
						</tr>
					</table>
				</font></td>
			</tr>
		</tbody>
	</table>
	</div>
	</div>
	</form>
	<p></p>
	<br><br>
<?php if($isEdit == '1'){ ?>
	<form action="sample_to_client.php" method="POST">
<?php	echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\">"; ?>
	<table align="left">
		<tr>
			<td width="400px">&nbsp;</td>
			<td height="25" valign="top" align="right">Send to Client:</td>
			<td>&nbsp;</td>
			<td valign="top" align="left">
				<select name="atclient" onchange="this.form.submit()">
					<option value="0">---In Inventory---</option>

<?php
for($i=0; $i < count($data1); $i++){
	if($data3['atclient'] == $data1[$i]['ID']){
					echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	}else{
					echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
	}
}
?>
				</select>
			</td>
		</tr>
	</table>
<?php } ?>
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

function DeleteUploads(id,filename,sample_id){
	var dataString = "filename="+filename+"&tableid="+id+"&sample_id="+sample_id;
	$.ajax({
		type: "POST",
		url: "delete_uploads.php",
		data: dataString,
		dataType: "json",
		timeout : 60000,
		success:function(data){
			if(data!=null){
				if(data.name || data.error){
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

function DeleteCurrentRow(obj){
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}

function DeleteRow(rowObjArray){
	for (var i=0; i<rowObjArray.length; i++) {
		var rIndex = rowObjArray[i].sectionRowIndex;
		rowObjArray[i].parentNode.deleteRow(rIndex);
	}
}

</script>

<script type="text/javascript">
$(function(){
	$("#validationForm").submit(function(){
		if($("#validationForm").valid()){
			var pid = document.getElementById('pid');
			dataString = $("#validationForm").serialize();
			$.ajax({
				type: "POST",
				url: "samplerequest.add1.php",
				data: dataString,
				dataType: "json",
				timeout : 60000,
				success:function(data){
					if(data!=null){
						if(data.name || data.error){
							$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						}
						else
						{
							if(document.getElementById('isEdit').value==1){
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
function ajaxFileUpload(fileId,type,samplerequestId){
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
	$.ajaxFileUpload({
		url:'doajaxfileupload.php',
		secureuri:false,
		fileElementId:fileId,
		dataType: 'json',
		data:{uploadpicture:uploadpicture.value,uploadfile:uploadfile.value,fileId:fileId, id:id,type:type,srID:sample_id},
		success: function (data, status){
			if(typeof(data.error) != 'undefined'){
				if(data.error != ''){
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
		error: function (data, status, e){
			$("#message").html("<div class='errorMessage'><strong>"+e+"</strong></div>");
		}
	})
	return false;
}
</script>
<?php
require('../../trailer.php');
?>
