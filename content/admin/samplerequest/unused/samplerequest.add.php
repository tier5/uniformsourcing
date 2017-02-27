<?php
require('Application.php');
require('../../header.php');
$upload_dir			= "../../projectimages/";	

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
	<form action="samplerequest.add1.php" method="post" enctype="multipart/form-data" name="frmSampleRequest">
	<table width="100%">
		<tbody><tr>
		  <td valign="top" align="center"><font face="arial">
			  <font size="5">Add Sample Request form <br>
			  <br>
			  </font>
			  <table width="85%" cellspacing="1" cellpadding="1" border="0">
				<tbody><tr>
				    <td height="25" valign="top" align="right">Choose Client:</td>
				    <td>&nbsp;</td>
				    <td valign="top" align="left"><select name="clientID" style="width:240px"><?php for($i=0; $i < count($data1); $i++){
	if($clientID==$data1[$i]['ID'])
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
		}?></select></td>
			      </tr>
				  <tr>
				  <td height="25" valign="top" align="right">Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Garment:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="garment" id="garment"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Size:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="size" id="size"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Picture 1:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td><img height="96" width="129" border="0" alt="" id="thumb_picture1" src="" style="display:none;"></td>
					  <td valign="top" align="left"><input type="file" id="picture1" name="picture1">
					  	<input type="hidden" name="hdnpicture1" id="hdnpicture1"  />
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload1" onclick="javascript:return ajaxFileUploadGeneral('picture1','<?php echo $data_cnt['id'];?>',1);"></td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Picture 2:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td><img height="96" width="129" border="0" alt="" id="thumb_picture2" src="" style="display:none;"></td>
					  <td valign="top" align="left"><input type="file" id="picture2" name="picture2">
					  	<input type="hidden" name="hdnpicture2" id="hdnpicture2"  />
						  <input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="btnUpload2" onclick="javascript:return ajaxFileUploadGeneral('picture2','<?php echo $data_cnt['id'];?>',2);"></td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Sample Garment:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sampleGarment" id="sampleGarment" ></td>
				</tr>
                           
				<tr>
				  <td height="25" valign="top" align="right">Vendor</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><select name="vendorID" style="width:240px">
				   <?php for($i=0; $i <count($data_Vendr); $i++)
				   {
					if($vendorID==$data_Vendr[$i]['vendorID'])
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
					else 
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
                   }?> 
			      </select></td>
				  </tr>
				<tr>
				  <td height="25" valign="top" align="right">Style:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="style"  ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Color:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="color" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Type of Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric code:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricCode"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Embroidery:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="embroidery" id="embroidery"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Customer Target Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerTargetprice" id="customerTargetprice" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Position:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="position" id="position"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">In Stock: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="radio" checked="checked" value="1" name="inStock" id="inStockYes">
					Yes
					<input type="radio" value="0" name="inStock" id="inStockNo">
					No </td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Project Notes:</td>
                  <td>&nbsp;</td>
                  <td valign="top">
					<table width="80%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
                    <tbody>
                      <tr>
                        
                        <td align="left" valign="top" colspan="4"><a style="cursor:hand;cursor:pointer;" name="addNotes" id="addNotes" onClick="javascript:popOpen('');"><img height="25px" width="120px" src="<?php echo $mydirectory;?>/images/addNotes.gif" alt="notes" /></a></td>
                      </tr>
                      </tbody>
                    </table>
                    </td>
				</tr>
				<tr>
				  <td height="25" align="right"><input type="submit" value="Save" onmouseover="this.style.cursor = 'pointer';" name="btnSave" onclick="javascript: return fnvalidatesamplerequest();"></td>
				  <td>&nbsp;</td>
				  <td align="left"><input type="button" value="Cancel" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='samplerequest.list.php'" name="btnCancel"></td>
				</tr>
			  </tbody></table>
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
<div id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
<tr><td width="100px" align="left"><strong>Notes : </strong></td><td>&nbsp;</td></tr>
  <tr>
    <td width="100" align="left"><p id="editPopId"></p></td>
    <td width="10">&nbsp;</td>
    
  </tr>
</table>
</div></form>
	  <p> </p>
</center>
<script type="text/javascript">
function popOpen(txtId)
{
	if(txtId == null || txtId == "")
	{
		var popID = 'textPop'; //Get Popup Name
	}
	else
	{
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML = document.getElementById(txtId).value;
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
		document.frmSampleRequest.notesId.value="";
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
<?php
require('../../trailer.php');
?>