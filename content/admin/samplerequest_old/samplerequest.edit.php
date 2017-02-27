<?php
ob_start();
require('Application.php');
require('../../header.php');
$id = $_GET['id'];
if(!isset($_GET['id'])) { header('Location:samplerequest.list.php?msg=c'); }
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
	$sql ='select "notesId", notes, "createdDate", e.firstname as "firstName", e.lastname as "lastName" from "tbl_sampleNotes" as n inner join "employeeDB" as e on e."employeeID" =n."createdBy" where "sampleId"='.$id.' order by "notesId"';
if(!($result=pg_query($connection,$sql))){
	print("Failed sql: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_notes[]=$row;
}
if(! $data_cnt['id']) { $data_cnt['id']=$_GET['id']; }
if(isset($_POST['btnSave'])) {
	if($debug == "on"){
		require('../../header.php');
		foreach($_POST as $key=>$value) {
			if($key!="submit") { echo "$key = $value<br/>"; }
		}
	}
	extract($_POST);
	/*UPDATE "public"."tbl_sampleRequest" SET "id"='5', "srID"=NULL, "garment"=NULL, "size"=NULL, "picture"='picture_5_1280549699.jpg', "sampleGarment"=NULL, "styleNo"=NULL, "color"=NULL, "fabricType"=NULL, "fabricCode"=NULL, "embroidery"=NULL, "customerTargetprice"=NULL, "position"=NULL, "inStock"='1', "comments"=NULL, "createdDate"='', "modifiedDate"='', "status"='1' WHERE "id"='5'*/
	if($inStock) { $inStock=1;} else { $inStock=0;}
	$query4="UPDATE \"tbl_sampleRequest\" set \"cid\" = '$clientID',  \"inStock\" = '$inStock', ";		 	
	if($srID) $query4.="\"srID\" = '$srID', ";
	if($garment) $query4.=" \"garment\" = '$garment', ";
	if($hdnpicture1) $query4.="\"picture1\" = '$hdnpicture1', ";
	if($hdnpicture2) $query4.="\"picture2\" = '$hdnpicture2', ";
	if($sampleGarment) $query4.="\"sampleGarment\" = '$sampleGarment', ";
	if($style) $query4.="\"styleNo\" = '$style', ";
	if($color) $query4.="\"color\" = '$color', ";
	if($fabricType) $query4.="\"fabricType\" = '$fabricType', ";
	if($fabricCode) $query4.="\"fabricCode\" = '$fabricCode', ";
	if($embroidery) $query4.="\"embroidery\" = '$embroidery', ";
	if($customerTargetprice) $query4.="\"customerTargetprice\" = '$customerTargetprice', ";
	if($position) $query4.="\"position\" = '$position', ";
	//if($inStock)  $query4.="\"inStock\" = '$inStock', ";
	if($comments)  $query4.="\"comments\" = '$comments', ";
	if($vendorID) $query4.="\"vid\" = '$vendorID', ";
	$query4.= " \"modifiedDate\" = '".date('U')."' ";
	$query4.= " WHERE \"id\" = '".$_GET['id']."'";
		if(!($result4=pg_query($connection,$query4))){
		print("Failed query4: " . pg_last_error($connection));
		exit;
	}
	for($i=0; $i< count($textAreaName); $i++)
	{
		if($hdnNotesName[$i] == 0 && $textAreaName[$i] !="")
		{
			$sql="Insert into \"tbl_sampleNotes\" (";
			if($textAreaName[$i]!="") $sql.="notes ,";
			$sql.=" \"sampleId\"" ;
			$sql .=", \"createdDate\"";
			$sql .=", \"createdBy\"";
			$sql .=" )Values(";
			if($textAreaName!="") $sql .=" '$textAreaName[$i]',";
			$sql .=" '".$data_cnt['id']."'";
			$sql .=", ".date("U");
			$sql .=", ".$_SESSION["employeeID"]."";
			$sql .=" )";
			if(!($result=pg_query($connection,$sql)))
			{
				print("Failed sql: " . pg_last_error($connection));
				exit;
			}
		}
	}
	header("location: samplerequest.list.php?msg=s");
}
$query3=("SELECT * ".
		 "FROM \"tbl_sampleRequest\" ".
		 "WHERE \"id\" ='".$_GET['id']."' ");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3=$row3;
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
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min-1.4.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/projectadd.js"></script>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='samplerequest.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center><div align="center" style="width:50%" id="message"></div></center>
<center>
	<form action="samplerequest.edit.php?id=<?php echo $_GET['id']?>" method="post" enctype="multipart/form-data">
	<table width="100%">
		<tbody>
        <tr>
		  <td valign="top" align="center"><font face="arial">
			  <font size="5">Edit Sample Request form <br>
			  <br>
			  </font>
			  <table width="85%" cellspacing="1" cellpadding="1" border="0">
				<tbody>
                <tr>
				    <td height="25" valign="top" align="right">Choose Client:</td>
				    <td>&nbsp;</td>
				    <td valign="top" align="left"><select name="clientID" style="width:240px"><?php for($i=0; $i < count($data1); $i++){
	if($data3['cid']==$data1[$i]['ID'])
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
		}?></select></td>
			      </tr>
				  <tr>
				  <td height="25" valign="top" align="right">Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="<?php echo $data3['srID'];?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Garment:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="garment" id="garment" value="<?php echo $data3['garment'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Size:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="size" id="size" value="<?php echo $data3['size'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Picture 1:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td>
					  <?php
					  	if($data3['picture1']) 
							echo '<img height="96" width="129" id="thumb_picture1" src="../../projectimages/'.$data3['picture1'].'"  />';
						else 
							echo '<img height="96" width="129" id="thumb_picture1" style="display:none;" />';
					  ?>
					  </td>
					  <td valign="top" align="left"><input type="file" id="picture1" name="picture1">
					  	<input type="hidden" name="hdnpicture1" id="hdnpicture1" value="<?php echo $data3['picture1'];?>"  />
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload" onclick="javascript:return ajaxFileUploadGeneral('picture1','<?php echo $data_cnt['id'];?>',1);">						  
						 </td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Picture 2:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td>
					  <?php
					  	if($data3['picture2']) 
							echo '<img height="96" width="129" id="thumb_picture2" src="../../projectimages/'.$data3['picture2'].'"  />';
						else 
							echo '<img height="96" width="129" id="thumb_picture2" style="display:none;" />';
					  ?>
					  </td>
					  <td valign="top" align="left"><input type="file" id="picture2" name="picture2">
					  	<input type="hidden" name="hdnpicture2" id="hdnpicture2" value="<?php echo $data3['picture2'];?>"  />
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload" onclick="javascript:return ajaxFileUploadGeneral('picture2','<?php echo $data_cnt['id'];?>',2);">						  
						 </td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Sample Garment:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sampleGarment" id="sampleGarment"
                  			value="<?php echo $data3['sampleGarment'];?>" ></td>
				</tr>                
                              
				<tr>
				  <td height="25" valign="top" align="right">V<font face="arial">e</font>ndor:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><select name="vendorID" style="width:240px">
				    <?php 
					for($i=0; $i <count($data_Vendr); $i++)
					{
						if($data3['vid']==$data_Vendr[$i]['vendorID'])
							echo '<option value="'.$data_Vendr[$i]['vendorID'].'"
							 selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
						else 
							echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
   					 }
?>
			      </select></td>
				  </tr>
				<tr>
				  <td height="25" valign="top" align="right">Style:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="style" value="<?php echo $data3['styleNo'];?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Color:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="color" value="<?php echo $data3['color'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Type of Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType" value="<?php echo $data3['fabricType'];?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric code:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricCode" value="<?php echo $data3['fabricCode'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Embroidery:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="embroidery" id="embroidery" value="<?php echo $data3['embroidery'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Customer
Target
Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerTargetprice" id="customerTargetprice" value="<?php echo $data3['customerTargetprice'];?>" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Position:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="position" id="position" value="<?php echo $data3['position'];?>"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">In Stock: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">
					<?php
					if($data3['inStock']) 
					echo '<input type="radio" value="1" name="inStock" checked="checked" id="inStockYes" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="inStock" id="inStockNo" />&nbsp;No ';
else
	echo '<input type="radio" value="1" name="inStock" id="inStockYes"/>&nbsp;Yes &nbsp;<input type="radio" value="0" name="inStock" checked="checked"  id="inStockNo" />&nbsp;No ';
					?>
					</td>
				</tr>
 <?php
				if($data3['comments'] !="")
				{
?>
                <tr>
                <td height="25" valign="top" align="right">Comments:</td>
                <td>&nbsp;</td>
                <td><textarea readonly="readonly" id="comments" name="comments" rows="7" cols="35"><?php echo $data3['comments'];?></textarea></td>
                </tr>
<?php 
				}
?>
				<tr>
				  <td height="25" valign="top" align="right">Project Notes:</td>
                  <td>&nbsp;</td>
				  <td align="left" valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
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
</tr>
				<tr>
				  <td height="25" align="right"><input type="submit" value="Save" onmouseover="this.style.cursor = 'pointer';" name="btnSave" onclick="javascript: return fnvalidatesamplerequest();"></td>
				  <td>&nbsp;</td>
				  <td align="left"><input type="button" value="Cancel" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='samplerequest.list.php'" name="btnCancel"></td>
				</tr>
			  </tbody></table>
		  </font></td>
		</tr>
	  </tbody></table></form>
	  <p> </p>
</center>
<div style="width:500" id="textPop" class="popup_block">
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
<div style="width:500" id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
<tr id="tr_popEmpId" style="display:none">
<td width="100px" align="left"><strong>Added By : </strong></td><td width="5px">&nbsp;</td><td id="td_popEmpId"></td>
</tr>
<tr id="tr_popDateTimeId" style="display:none">
<td width="100px" align="left"><strong>Added Date : </strong></td><td width="5px">&nbsp;</td><td id="td_popDateTimeId"></td>
</tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td width="100px" align="left"><strong>Notes : </strong></td><td>&nbsp;</td></tr>
  <tr>  	
    <td width="100" align="left"><p id="editPopId"></p></td>
    <td width="10">&nbsp;</td>
    
  </tr>
</table>
</div>
<script type="text/javascript">
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
	
}
function DeleteCurrentRow(obj)
{
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}
</script>
<?php
require('../../trailer.php');
ob_end_flush();
?>