<?php require('Application.php');
require('../jsonwrapper/jsonwrapper.php');
require('../header.php');
$isEdit=0;
$styleId=0;
if(isset($_GET['type']))
{
	switch($_GET['type'])
	{
		case "a":
		case "A":
		{
			$isEdit=0;
			$sql='select (Max("styleId")+1) as "styleId" from "tbl_invStyle"';
			if(!($result=pg_query($connection,$sql))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row = pg_fetch_array($result)){
				$data=$row;
			}
			pg_free_result($result);
			if(! $data['styleId']) { $data['styleId']=1; }
			$styleId = $data['styleId'];
			break;
		}
		case "E":
		case "e":
		{
			$isEdit=1;
			$sql='select * from "tbl_invStyle" where "styleId"='.$_GET['ID'];
			if(!($result=pg_query($connection,$sql))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row = pg_fetch_array($result)){
				$data_style=$row;
			}
			pg_free_result($result);
			
			$sql='select * from "tbl_invColor" where "styleId"='.$_GET['ID'];
			if(!($result=pg_query($connection,$sql))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row = pg_fetch_array($result)){
				$data_color[]=$row;
			}
			pg_free_result($result);
			
			$multipleLocation=$data_style['locationIds'];
			$styleId=$data_style['styleId'];
			$location=explode(',',$data_style['locationIds']);
			break;
		}
	}
}	
$query1='Select Distinct "scaleName","scaleId" from "tbl_invScaleName" where "isActive"=1';
if(!($result_cnt=pg_query($connection,$query1))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row_cnt = pg_fetch_array($result_cnt)){
				$data_scaleN[]=$row_cnt;
			}
			pg_free_result($result_cnt);
$query2='Select "garmentID","garmentName" from "tbl_garment" where status=1';
if(!($result_cnt1=pg_query($connection,$query2))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row_cnt = pg_fetch_array($result_cnt1)){
				$data_garment[]=$row_cnt;
			}
			pg_free_result($result_cnt1);
$query3='Select "fabricID","fabName" from "tbl_fabrics" where status=1';
if(!($result_cnt2=pg_query($connection,$query3))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row_cnt = pg_fetch_array($result_cnt2)){
				$data_fab[]=$row_cnt;
			}
			pg_free_result($result_cnt2);
$query4='Select "locationId","name" from "tbl_invLocation" order by "locationId"';
if(!($result_cnt3=pg_query($connection,$query4))){
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			while($row_cnt = pg_fetch_array($result_cnt3)){
				$data_location[]=$row_cnt;
			}
			pg_free_result($result_cnt3);		
$query5=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
	 "FROM \"clientDB\" ".
	 "WHERE \"active\" = 'yes' ".
	 "ORDER BY \"client\" ASC");
	if(!($result=pg_query($connection,$query5))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result)){
		$data_client[]=$row1;
	}
	pg_free_result($result);
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.validate.js"></script>';
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/ajaxfileupload.js"></script>';
?>
<?php if($isEdit==0){?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='inventory.php';" class="button_text" type="button" name="back" value="Back" /></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
<?php }else{?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='reports.php';" class="button_text" type="button" name="back" value="Back" /></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
<?php }?>
<table width="100%">
        <tr>
          <td align="left" valign="top"><center>
            <table width="100%">
                <tr>
                  <td align="center" valign="top"><font size="5"><?php if($isEdit==0){echo "ADD STYLE";}else{echo "EDIT STYLE";}?></font><font size="5"> 
                  <br />
                      <br>
                      </font>
                       <center>
                        <table><tr><td>
                        <div align="center" id="message"></div>
                        </td></tr></table>
                        </center>
                      <form name="validationForm" id="validationForm" method="post" action="">
                        <table width="80%" border="0">
                          <tr>
                            <td align="center"><p></p></td>
                            <td align="center">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                            <td align="right" valign="top">&nbsp;</td>
                          </tr>
                        </table>
                        <table width="98%" border="0" cellspacing="1" cellpadding="1">
                          <tr>
                            <td width="355" height="25" align="right" valign="top">Style Number: <br /></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            <input name="styleNumber" id="styleNumber" <?php if($isEdit==1){?>readonly="readonly" <?php }?>type="text" class="textBox" 
                            value="<?php echo $data_style['styleNumber'];?>"  /></td>
                          </tr>
                            
                          <tr>
                            
                          <td width="355" height="25" align="right">Barcode: </td>
                            <td width="10">&nbsp;</td>
        <td align="left" valign="top"><input type="file" name="barcode_image" id="barcode_image" />
<input type="button" id="upload1" value="Upload" onmouseover="this.style.cursor = 'pointer';" style="cursor: pointer;" onclick="javascript:barCode();"/>
<img id="bar_img" width="50" height="50" src="../uploadFiles/inventory/images/thumbs/<?php echo $data_style['barcode'] ; ?>" onclick="PopEx(this, null,  null, 0, 0, 50,'PopBoxImageLarge');" pbsrc="../uploadFiles/inventory/images/<?php echo $data_style['barcode'];?>">
<input type="text" name="barcode_name" id="barcode_name" style="display:none">
<?php if($data_style['barcode']!=""){ ?> <a href="<?php echo $mydirectory;?>/download.php?filename=<?php echo $data_style['barcode'] ; ?>" >
    <img src="<?php echo $mydirectory;?>/images/b_download.jpg" width="30" height="30">
</a><?php } ?></td>
       
                          </tr>
                          
                          <tr>
                            <td height="25" align="right" valign="top">Size Scale: </td>
                            <td>&nbsp;</td>
                            <td align="left" valign="top"><select name="sizeScale" id="sizeScale" <?php if($isEdit==1){?> disabled="disabled"<?php }?> style="width:160px">
							<?php 
                            for($i=0; $i < count($data_scaleN); $i++){
                              if($data_style['scaleNameId']!=$data_scaleN[$i]['scaleId']){
                            echo '<option value="'.$data_scaleN[$i]['scaleId'].'">'.$data_scaleN[$i]['scaleName'].'</option>';}
                            else{ echo '<option value="'.$data_scaleN[$i]['scaleId'].'" selected="selected">'.$data_scaleN[$i]['scaleName'].'</option>';}
                            }
                            ?>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Garment:</td>
                            <td>&nbsp;</td>
                            <td align="left" valign="top"><select name="garment" id="garment">
                              <option value="0">--- Select Garment ------</option>
<?php 
for($i=0; $i < count($data_garment); $i++){
  if($data_style['garmentId']!=$data_garment[$i]['garmentID']){
echo '<option value="'.$data_garment[$i]['garmentID'].'">'.$data_garment[$i]['garmentName'].'</option>';}
else{ echo '<option value="'.$data_garment[$i]['garmentID'].'" selected="selected">'.$data_garment[$i]['garmentName'].'</option>';}}
?>
                            </select></td>
                          </tr>
                          <tr>
                            <td  height="25" align="right" valign="top">Colors Available:</td>
                            <td>&nbsp;</td>
                            </tr>
                            <tr>
                            <td colspan="3" align="left" valign="top"><fieldset style="padding:5px;</h5>">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="imageView">  
                              </table>
                              </fieldset>
                              <br />
                              <fieldset style="padding:5px;</h5>">
                              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="50">Name: </td>
                                  <td width="10">&nbsp;</td>
                                  <td><input name="color" id="color" type="text" class="textBox"/></td>
                                  <td width="50">Image:</td>
                                  <td width="10">&nbsp;</td>
                                  <td><input type="file" name="inv_image" id="inv_image" />
                                    <input type="button" id="upload" value="Upload" onmouseover="this.style.cursor = 'pointer';" style="cursor: pointer;" onclick="javascript:ColorCheck();"/></td>
                                </tr>
                              </table>
                            </fieldset></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Fabric:</td>
                            <td>&nbsp;</td>
                            <td align="left" valign="top"><select name="fabric" id="fabric">
                              <option value="0">--- Select Fabric ------</option>
<?php 
for($i=0; $i < count($data_fab); $i++){
   if($data_style['fabricId']!=$data_fab[$i]['fabricID']){
echo '<option value="'.$data_fab[$i]['fabricID'].'">'.$data_fab[$i]['fabName'].'</option>';}
else {echo '<option value="'.$data_fab[$i]['fabricID'].'" selected="selected">'.$data_fab[$i]['fabName'].'</option>';}}
?>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Sex:</td>
                            <td>&nbsp;</td>
                            <td align="left" valign="top"><select name="sex" id="sex">
                              <option value="">--- Select Gender------</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="unisex">Unisex</option>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Price:</td>
                            <td>&nbsp;</td>
                            <td align="left"><input name="price" id="price" type="text" class="textBox" value="<?php echo $data_style['price'];?>" /></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Locations:</td>
                            <td>&nbsp;</td>
                            <td align="left"><select name="locations" id="locations" size="5" multiple="multiple" 
                            onblur="MultipleSelection(document.getElementById('locations'))">
                            <option value="0">--- Select Locations ---</option>
<?php 
if($isEdit==1){
  for($i=0; $i<count($data_location); $i++)
  {
	  $done=0;
	  if(count($location)>0)
	  {
		for($j=0; $j<count($location); $j++)
		{
			if($location[$j]==$data_location[$i]['locationId']){
			 echo '<option value="'.$data_location[$i]['locationId'].'" selected ="selected">'.$data_location[$i]['name'].'</option>';
			 $done=1;
			break;}
		}if(!$done)
		 echo '<option value="'.$data_location[$i]['locationId'].'">'.$data_location[$i]['name'].'</option>';

	  }
	else { echo '<option value="'.$data_location[$i]['locationId'].'">'.$data_location[$i]['name'].'</option>';}
  }
}
else{
for($i=0; $i < count($data_location); $i++){
echo '<option value="'.$data_location[$i]['locationId'].'">'.$data_location[$i]['name'].'</option>';}
}
?>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Client:</td>
                            <td>&nbsp;</td>
                            <td align="left"><select name="client" id="client">
                              <option value="0">--- Select Client-----</option>
<?php for($i=0; $i < count($data_client); $i++){
	  if($data_style['clientId']!=$data_client[$i]['ID']){
echo '<option value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';}
else { echo '<option value="'.$data_client[$i]['ID'].'" selected="selected">'.$data_client[$i]['client'].'</option>';}
}
?>
                            </select></td>
                          </tr>
                          <tr>
                            <td height="25" align="right" valign="top">Notes:</td>
                            <td>&nbsp;</td>
                            <td align="left"><textarea name="notes" id="notes" type="text" class="textArea" c><?php echo $data_style['notes'];?></textarea></td>
                          </tr>
                          <tr>
                            <td height="25" align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="left">
                            <input id="multipleLocation" name="multipleLocation" type="hidden" value="<?php echo $multipleLocation;?>" />
                            <input id="styleId" name="styleId" type="hidden" value="<?php echo $styleId;?>"/>
                            <input id="isEdit" type="hidden" value="<?php echo $isEdit;?>"/>
                           <?php if($isEdit){?> <input name="submit" type="submit" onMouseOver="this.style.cursor = 'pointer';" value="Edit Style"  /><?php }else{ ?>  <input name="submit" type="submit" onMouseOver="this.style.cursor = 'pointer';" value="Save Style"  /><?php }?>
                           <input name="cancel" onclick="javascript:location.href='reports.php';" type="button" onMouseOver="this.style.cursor = 'pointer';" value="Cancel"  />
                           </td>
                          </tr>
                        </table>
                      </form></td>
                </tr>
              </table>
              <p>
          </center></td>
        </tr>
      </table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script type="text/javascript">
function ajaxFileUpload(filefield,filetype,id<?php if($isEdit){?>,clrId<?php } ?>){$("#loading").ajaxStart(function(){$(this).show();}).ajaxComplete(function(){$(this).hide();});$.ajaxFileUpload({url:'doajaxfileupload.php?field='+filefield+'&fileType='+filetype+'&projectId='+id<?php if($isEdit) {?>+'&colorId='+clrId<?php }?>,secureuri:false,fileElementId:filefield,dataType: 'json',success: function (data, status){if(typeof(data.error) != 'undefined'){if(data.error != ""){alert(data.error);}else{alert("File content is uploaded successfully");document.getElementById(filefield).value="";<?php if($isEdit){?>if(clrId >0){if(data.imageSrc !="")document.getElementById('image_'+clrId).src=data.imageSrc;if(data.imageName !="")document.getElementById("imagename"+"_"+clrId).value = data.imageName;}else{
AddRow('imageView',document.getElementById('color').value,data.msg,0);document.getElementById('color').value="";var query="styleAdd.php?ID="+document.getElementById('styleId').value;}<?php } else { ?>AddRow('imageView',document.getElementById('color').value,data.msg,0);document.getElementById('color').value="";var query="styleAdd.php?ID="+document.getElementById('styleId').value;<?php } ?>/*$(location).attr('href',query+"&submitType=edit");*/return true;}}},error: function (data, status, e){alert(e);}})
return false;}



function ajaxBarcodeFileUpload(filefield,filetype,id<?php if($isEdit){?>,clrId<?php } ?>){
$("#loading").ajaxStart(function(){$(this).show();}).ajaxComplete(function(){
   $(this).hide();});
$.ajaxFileUpload({url:'doajaxfileupload.php?field='+filefield+'&fileType='+filetype+'&projectId='+id<?php if($isEdit) {?>+'&colorId='+clrId<?php }?>,secureuri:false,fileElementId:filefield,dataType: 'json',
     data:{width:"960", height:"720"},
   success: function (data, status){
       if(typeof(data.error) != 'undefined'){if(data.error != ""){alert(data.error);}
else{
   var imgName=$("#barcode_image").val();
    $("#barcode_name").val(imgName);
    
    ext = '';
if(imgName != '')
imgName = imgName.split('.');ext = imgName.pop().toLowerCase();
    $("#bar_img").attr("src","<?php echo $upload_dir_image.'thumbs/';?>"+imgName+"."+ext);
     $("#bar_img").attr("pbsrc","<?php echo $upload_dir_image;?>"+imgName+"."+ext);
   
    alert("File content is uploaded successfully"); 
 //document.getElementById(filefield).value="";
return true;}}},error: function (data, status, e){alert(e);}})
return false;}
function EnableUploadField()
{
	var image = document.getElementById('inv_image1');
	image.style.display = "block";
	var fileInput = document.getElementById('upload1');
	fileInput.style.display = "block"; 
}
function ColorCheck()
{
	if(document.getElementById('color').value=="")
	{
		alert('Enter a color name before uploading');
		document.getElementById('color').focus();
	}
	else
	{
		ajaxFileUpload('inv_image','image',<?php echo $styleId;?>);
	}
}

function barCode()
{
    if($('#styleNumber').val() != ''){
    ajaxBarcodeFileUpload('barcode_image','image',<?php echo $styleId;?>);
    }
    else{alert('Please enter Style Number and hit Upload');}
	
} 
function AddRow(tableID,clrName,imgName,clrId) {

var table = document.getElementById(tableID);
var rowCount = table.rows.length;
var row = table.insertRow(rowCount);

var cell1 = row.insertCell(0);
cell1.width="50px";		
cell1.innerHTML = "Name:";	
var cell2 = row.insertCell(1);
cell2.width="10px";		
cell2.innerHTML = "&nbsp;";	

var cell3 = row.insertCell(2);
var element1 = document.createElement("input");
element1.type = "text";	
element1.className = "txtColor";
element1.name = "clolorName[]";
element1.value = clrName;
var element2 = document.createElement("input");
element2.type = "hidden";	
element2.name = "imageName[]";
if(clrId > 0)
{
	element2.id = "imagename"+"_"+clrId;
}
imgName2=imgName;
element2.value = imgName;
var element3 = document.createElement("input");
element3.type = "hidden";	
element3.name = "colorId[]";
element3.value = clrId;
cell3.appendChild(element1);	
cell3.appendChild(element2);

var cell4 = row.insertCell(3);
cell4.width="50px";		
cell4.innerHTML = "Image:";	
var cell5 = row.insertCell(4);
cell5.width="10px";		
cell5.innerHTML = "&nbsp;";	
cell5.appendChild(element3);
var cell6 = row.insertCell(5);
cell6.width="50px";
ext = '';
if(imgName != '')
imgName = imgName.split('.');ext = imgName.pop().toLowerCase();
imgName=encodeURIComponent(imgName);
var str=String(imgName);
        
        str=str.replace("121212090909567845689","%26");
        str=str.replace("QM98712412qr","%2E");
        imgName=str;
if(clrId > 0){
    
    cell6.innerHTML = "<img id=\"image_"+clrId+"\" src=\"<?php echo $upload_dir_image.'thumbs/';?>"+imgName+'.'+ext+"\" alt=\"thumbnail\" width=\"64\" height=\"48\" border=\"0\"  onclick=\"PopEx(this, null,  null, 0, 0, 50,'PopBoxImageLarge');\" pbsrc=\"<?php echo $upload_dir_image;?>"+imgName+'.'+ext+"\" /> ";
}
else
{
	cell6.innerHTML = "<img src=\"<?php echo $upload_dir_image.'thumbs/';?>"+imgName+'.'+ext+"\" alt=\"thumbnail\" width=\"64\" height=\"48\" border=\"0\" onclick=\"PopEx(this, null,  null, 0, 0, 50,'PopBoxImageLarge');\" pbsrc=\"<?php echo $upload_dir_image;?>"+imgName+'.'+ext+"\"/>";
}
var cell7 = row.insertCell(6);
cell7.width="10px";		
cell7.innerHTML = "&nbsp;";	
<?php if($isEdit)
{?>
if(clrId > 0)
{
var cell8 = row.insertCell(7);
var element4 = document.createElement("input");
element4.type = "file";	
element4.name = "inv_"+clrId;
element4.id = "inv_"+clrId;
var element5 = document.createElement("input");
element5.type = "button";	
element5.id = "upload";
element5.style.width = "100px";
element5.value = "Upload";
element5.setAttribute("onClick","ajaxFileUpload('inv_"+clrId+"','image',<?php echo $styleId;?>,"+clrId+");");
cell8.appendChild(element4);	
cell8.appendChild(element5);
var cell9 = row.insertCell(8);
}
else
var cell9 = row.insertCell(7);
<?php }
else
{
	?>	
	var cell9 = row.insertCell(7);
<?php 
}
?>
cell9.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentRow(this<?php if($isEdit){?>,'"+imgName+"',"+clrId+"<?php } ?>);\">Delete</a>";

}
function DeleteCurrentRow(obj<?php if($isEdit) {?>,imgName,clrId<?php }?>)
{	//alert("sizeId= "+sizeId+" type= "+type+" size= "+size+" isDb= "+isDb);	

var delRow = obj.parentNode.parentNode;
var tbl = delRow.parentNode.parentNode;
var rIndex = delRow.sectionRowIndex;		
var rowArray = new Array(delRow);
<?php if($isEdit) { ?>		
var dataString = "colorId="+clrId+"&imgName="+imgName;$.ajax({type: "POST",url: "colorDelete.php",data: dataString,dataType: "json",success:
function(data){if(data!=null){	if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); } else {	if(document.getElementById('isEdit').value==1){$("#message").html("<div class='errorMessage'><strong>Color Removed...</strong></div>");}else{$("#message").html("<div class='errorMessage'><strong>Color Removed...</strong></div>");}}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");}}});
<?php }?>
DeleteRow(rowArray);
}
function DeleteRow(rowObjArray)
{	
for (var i=0; i<rowObjArray.length; i++) {
	var rIndex = rowObjArray[i].sectionRowIndex;
	rowObjArray[i].parentNode.deleteRow(rIndex);
}	
}
function HighlightSelectBox(selBoxId, value)
{
	sel = document.getElementById(selBoxId);
	for(var i=0; i<sel.options.length; i++)
	{
		if(sel.options[i].value==value)
		{
		sel.options.selectedIndex=i;
		}
	}
}
$().ready(function() {
<?php 
if($isEdit){
	for($i=0;$i < count($data_color);$i++)
		echo "AddRow('imageView',\"".trim($data_color[$i]['name'])."\",\"".trim($data_color[$i]['image'])."\",".$data_color[$i]['colorId']." );";
}
?>

$("#validationForm").validate({
rules: {
//price:{digits : true}
},
messages: {
//price: "Please enter in digits"
}
});
<?php if($isEdit==1)echo "HighlightSelectBox('sex','".$data_style['sex']."');";?>
});
function MultipleSelection(obj)
{
var location="";
for (var i = 0; i < obj.options.length; i++) {
    if (obj.options[i].selected) {
       location+=obj.options[i].value+',';
    }
}
/*while (obj.selectedIndex != -1)
{
if (obj.selectedIndex != 0) 
{
location+=obj.options[obj.selectedIndex].value+',';
}
obj.options[obj.selectedIndex].selected = false;
}*/
document.getElementById('multipleLocation').value=location.substr(0,(location.length-1));
}
  <?php if(($_SESSION['perm_admin']=='on') || (isset($_SESSION['inv_pass']) && $_SESSION['inv_pass']==$inv_pass)){ ?>  
 $("#validationForm").submit(function(){dataString = $("#validationForm").serialize();if(document.getElementById('isEdit').value==1)dataString += "&type=e";else dataString += "&type=a";$.ajax({type: "POST",url: "styleSubmit.php",data: dataString,dataType: "json",success:
function(data){if(data!=null){if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");}else{if(document.getElementById('isEdit').value==1){$("#message").html("<div class='successMessage'><strong>Style details Updated. Thank you.</strong></div>");$(location).attr('href',"reports.php");}else{$("#message").html("<div class='successMessage'><strong>New Style Information Added. Thank you.</strong></div>");ClearAllFields();$(location).attr('href',"reports.php?ID="+data.id+"&type=a");} } } else{ $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");}} });return false;}); 
    <?php }else{?>   
$("#validationForm").submit(function(){
  var d = prompt("Enter password"); 
  var invdata='pass='+d;
   $.ajax({
 type:'post',
 url:'check_inv_pass.php',
 data:invdata,
 datatype:'json',
 success:function(res){
     
     if(res.stat=='true'){
var dataString = $("#validationForm").serialize();if(document.getElementById('isEdit').value==1)dataString += "&type=e";else dataString += "&type=a";$.ajax({type: "POST",url: "styleSubmit.php",data: dataString,dataType: "json",success:
function(data){if(data!=null){if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");}else{if(document.getElementById('isEdit').value==1){$("#message").html("<div class='successMessage'><strong>Style details Updated. Thank you.</strong></div>");
$(location).attr('href',"reports.php");
//location.reload();
}else{$("#message").html("<div class='successMessage'><strong>New Style Information Added. Thank you.</strong></div>");ClearAllFields();$(location).attr('href',"reports.php?ID="+data.id+"&type=a");} } } else{ $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");}} });
      }
	  else{
	  alert('Wrong password!!!');
	  }
	  
  }
  });  
return false;   
 });
        	
<?php }?> 	

function ClearAllFields()
{
document.getElementById('styleNumber').value="";
document.getElementById('sizeScale').selectedIndex=0;
document.getElementById('garment').value="";
document.getElementById('price').value="";
document.getElementById('fabric').value="";
document.getElementById('sex').value="";
document.getElementById('notes').value="";
document.getElementById('locations').selectedIndex=0;
document.getElementById('client').selectedIndex=0;
}
</script>
<?php  require('../trailer.php');
?>