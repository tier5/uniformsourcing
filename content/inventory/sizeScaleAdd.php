<?php require('Application.php');
	require('../jsonwrapper/jsonwrapper.php');
	require('../header.php');	
	
	$isEdit = 0;
	if(isset($_GET["type"]))
	{
		switch($_GET["type"])
		{
			case "a":
			case "A":
			{
				$sql='select (Max("scaleId")+1) as "scaleId" from "tbl_invScaleName"';
				if(!($result_cnt=pg_query($connection,$sql))){
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row_cnt = pg_fetch_array($result_cnt))
				{
					$data_cnt=$row_cnt;
				}
				pg_free_result($result_cnt);
				if(!$data_cnt['scaleId']) { $data_cnt['scaleId']=1; }
				$scaleId = $data_cnt['scaleId'];
				break;
			}
			case "E":
			case "e":
			{
				$isEdit = 1;
				$sql = 'select * from "tbl_invScaleName" where "scaleId"=\''.$_GET["id"].'\' order by "scaleId"';
				if(!($result=pg_query($connection,$sql))){
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row = pg_fetch_array($result))
				{
					$nameData=$row;
				}
				pg_free_result($result);
				$sql = 'select "sizeScaleId","scaleId","scaleSize","mainOrder" from "tbl_invScaleSize" where "scaleId"=\''.$_GET["id"].'\' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\' order by "sizeScaleId", "scaleId"';
				if(!($result=pg_query($connection,$sql)))
				{
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row = pg_fetch_array($result))
				{
					$mainSize[]=$row;
				}
				pg_free_result($result);
				$sql = 'select "scaleId","sizeScaleId","opt1Size","opt1Order" from "tbl_invScaleSize" where "scaleId"=\''.$_GET["id"].'\' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\'  order by "sizeScaleId", "scaleId"';
				if(!($result=pg_query($connection,$sql)))
				{
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row = pg_fetch_array($result))
				{
					$opt1Size[]=$row;
				}
				pg_free_result($result);
				$sql = 'select "sizeScaleId","scaleId","opt2Size","opt2Order" from "tbl_invScaleSize" where "scaleId"=\''.$_GET["id"].'\' and "opt2Size" IS NOT NULL  and "opt2Size" <>\'\' order by "sizeScaleId", "scaleId"';
				if(!($result=pg_query($connection,$sql)))
				{
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row = pg_fetch_array($result))
				{
					$opt2Size[]=$row;
				}
				pg_free_result($result);
				break;	
			}
		}
	}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
$scaleNum = 0;
$opt1Num = 0;
$opt2Num = 0;
	?>
    <table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='./sizeScaleList.php'" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
    
    <center>
    <table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table>
    </center>
    <form id="validationForm" name="scaleSize">
    <table width="100%">
                <tr>
                  <td align="center" valign="top"><font size="5">Size Scale</font><font size="5">  <br />
                      <br>
                      </font>
                      <table width="95%" border="0">
                        <tr>
                          <td align="center"><p></p></td>
                          <td align="center">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <td align="right" valign="top">&nbsp;</td>
                        </tr>
                      </table>
                      <table width="95%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td width="355" height="25" align="right" valign="top">Name:  </td>
                        <td width="10">&nbsp;</td>
                        <td align="left" valign="top"><input name="scaleName" type="text" class="textBox" <?php if($isEdit) echo 'value="'.$nameData['scaleName'].'"';?> /> </td>
                         </tr>
                      <tr>
                        <td height="25" align="right" valign="top">Main Sizes:</td>
                        <td>&nbsp;</td>
                        <td align="center" valign="top">
                         <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tblScaleSize">
                          <tbody>                     
                          <tr>
                            <td height="30px" valign="middle" width="150"><input name="scaleSize[]" type="text" class="textBox" value="<?php if($isEdit ==1){ echo $mainSize[0]['scaleSize'];} else {echo "";} ?>" /> </td><td>&nbsp;</td>
                            <td height="30px" valign="middle" width="150"><input name="mainOrder[]" id="mainOrder[0]" type="text" class="orderTextBox" value="<?php if($isEdit ==1){ echo $mainSize[0]['mainOrder'];} else {echo "";} ?>" onblur="javascript:IsNumeric('mainOrder[0]');" /> </td>
                            <?php if($isEdit ==1){ ?>
                            <td width="10"><input name="scaleSizeId[]" type="hidden" class="textBox" value="<?php echo $mainSize[0]['sizeScaleId'];?>" /></td>
                            <?php } else { ?>
                            <td width="10">&nbsp;</td>
                            <?php } ?>  
                            <td><img src="<?php echo $mydirectory;?>/images/bullet_add.png" alt="add" width="32" height="25" onclick="javascript:AddRow('tblScaleSize','mainOrder[]', 'scaleSize[]'<?php if($isEdit){?>, 0, 'scale', '','', 0<?php } ?>);"/></td>
                          </tr>
                          </tbody>
                        </table></td>
                      </tr>
                      <tr>
                        <td height="25" align="right" valign="top">Row Name:</td>
                        <td>&nbsp;</td>
                        <td align="left" valign="top"><input name="opt1Name" type="text" class="textBox" <?php if($isEdit) echo 'value="'.$nameData['opt1Name'].'"';?>/></td>
                      </tr>
                      <tr>
                        <td height="25" align="right" valign="top">Row Sizes:</td>
                        <td>&nbsp;</td>
                        <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" id="tblOpt1Size">
                          <tbody>   
                          <tr>
                            <td height="30px" valign="middle" width="150"><input name="opt1Size[]" type="text" class="textBox" value="<?php if($isEdit ==1){ echo $opt1Size[0]['opt1Size'];} else {echo "";} ?>" /></td>
                            <td>&nbsp;</td>
                            <td height="30px" valign="middle" width="150"><input name="opt1Order[]" id="opt1Order[0]" type="text" class="orderTextBox" value="<?php if($isEdit ==1){ echo $opt1Size[0]['opt1Order'];} else {echo "";} ?>" onblur="javascript:IsNumeric('opt1Order[0]');" /> </td>
                                 <?php
							if($isEdit ==1){ ?>
                            <td width="10"><input name="opt1SizeId[]" type="hidden" class="textBox" value="<?php echo $opt1Size[0]['sizeScaleId'];?>" /></td>
                            <?php } else { ?>
                            <td width="10">&nbsp;</td>
                            <?php } ?>  
                            <td><img src="<?php echo $mydirectory;?>/images/bullet_add.png" alt="add" width="32" height="25" onclick="javascript:AddRow('tblOpt1Size','opt1Order[]', 'opt1Size[]'<?php if($isEdit){?> , 0, 'opt1', '','',0<?php } ?>);"/></td>
                          </tr>
                          </tbody>
                        </table></td>
                      </tr>
                      <tr>
                        <td height="25" align="right" valign="top">Column Name:</td>
                        <td>&nbsp;</td>
                        <td align="left"><input name="opt2Name" type="text" class="textBox" <?php if($isEdit) echo 'value="'.$nameData['opt2Name'].'"';?> /></td>
                      </tr>
                      <tr>
                        <td height="25" align="right" valign="top">Column Sizes:</td>
                        <td>&nbsp;</td>
                        <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tblOpt2Size">
                          <tbody>
                          <tr>
                            <td height="30px" valign="middle" width="150"><input name="opt2Size[]" type="text" class="textBox" value="<?php if($isEdit ==1){ echo $opt2Size[0]['opt2Size'];} else {echo "";} ?>" /></td>
                            <td>&nbsp;</td>
                            <td height="30px" valign="middle" width="150"><input name="opt2Order[]" id="opt2Order[0]" type="text" class="orderTextBox" value="<?php if($isEdit ==1){ echo $opt2Size[0]['opt2Order'];} else {echo "";} ?>" onblur="javascript:IsNumeric('opt2Order[0]');" /> </td>
                            <?php
							if($isEdit ==1){ ?>
                            <td width="10"><input name="opt2SizeId[]" type="hidden" class="textBox" value="<?php echo $opt2Size[0]['sizeScaleId'];?>" /></td>
                            <?php } else { ?>
                            <td width="10">&nbsp;</td>
                            <?php } ?>                            
                            <td><img src="<?php echo $mydirectory;?>/images/bullet_add.png" alt="add" width="32" height="25" onclick="javascript:AddRow('tblOpt2Size','opt2Order[]','opt2Size[]'<?php if($isEdit){?> , 0, 'opt2', '','',0<?php } ?>);"/></td>
                          </tr>
                          </tbody>
                        </table></td>
                      </tr>
                      <tr>
                        <td height="25" align="right" valign="top">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="left">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="left"><input name="submit" type="submit" onmouseover="this.style.cursor = 'pointer';" <?php if($isEdit){?>value="Edit"<?php } else { ?>value="Add"<?php }?>/></td>
                      </tr>
                  </table></td>
                </tr>
              </table>                           
              <input name="scaleId" type="hidden" value="<?php echo $nameData['scaleId'];?>"/>              
              </form>      
              <input id="isEdit" type="hidden" value="<?php echo $isEdit;?>"/>
<script type="text/javascript">
var hasLoaded = false;
$(function(){$("#validationForm").submit(function(){$("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");dataString = $("#validationForm").serialize();if(document.getElementById('isEdit').value==1)dataString += "&type=e";else dataString += "&type=a";$.ajax({type: "POST",url: "sizeScaleSubmit.php",data: dataString,dataType: "json",success:function(data){if(data!=null){	if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); } else {	if(document.getElementById('isEdit').value==1){$("#message").html("<div class='successMessage'><strong>Size Scale Updated. Thank you.</strong></div>");}else{$("#message").html("<div class='successMessage'><strong>New Scale Information Added. Thank you.</strong></div>");																																																													}}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");																																																													}}});return false;});});
</script>
<script type="text/javascript">
function IsNumeric(obj)
{
	var val=document.getElementById(obj).value; 
	if(isNaN(val))
	{
		alert('Please enter only digits');
		document.getElementById(obj).value="";
		document.getElementById(obj).focus();
	}
}
function init()
{
	hasLoaded = true;	
}
window.onload=init;
function AddRow(tableID,orderName,txtName<?php if($isEdit){?>, sizeId, type, size,order,isDb<?php }?>) {

	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);

	var cell1 = row.insertCell(0);
	cell1.style.height = "30px";
	var element1 = document.createElement("input");
	element1.type = "text";	
	element1.className = "textBox";
	element1.name = txtName;
	
	var cell2 = row.insertCell(1);
	cell2.width="20px";
	var cell3 = row.insertCell(2);
	cell3.style.height = "20px";
	var element3 = document.createElement("input");
	element3.type = "text";	
	element3.className = "orderTextBox";
	element3.name = orderName;
	var id= orderName.substr(0,(orderName.length - 1));
	id =  id+rowCount+']';
	element3.id = id;
	element3.setAttribute('onblur','IsNumeric(id)');
	<?php if($isEdit){?>
	if(isDb){
	element1.value = size;
	element3.value = order;
	}
	var element2 = document.createElement("input");
	element2.type = "hidden";
	element2.value = sizeId;
	switch(type)
	{
		case "scale":
		{
			element2.name = "scaleSizeId[]";
			break;
		}
		case "opt1":
		{
			element2.name = "opt1SizeId[]";
			break;
		}
		case "opt2":
		{
			element2.name = "opt2SizeId[]";
			break;
		}
	}	
	cell2.appendChild(element2);
	<?php } else {?>
	cell2.innerHTML = "&nbsp;";
	<?php }?>
	cell1.appendChild(element1);
	cell3.appendChild(element3);
	var cell4 = row.insertCell(3);
	cell4.width="10px";
	cell4.innerHTML = "&nbsp;";
	var cell5 = row.insertCell(4);	
	cell5.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentRow(this<?php if($isEdit){?>,"+sizeId+",'"+type+"','"+size+"',"+isDb+"<?php } ?>);\"><img style=\"width:32px;height:25px;\" src=\"<?php echo $mydirectory;?>/images/delete.png\" ></a>";
}
function DeleteCurrentRow(obj<?php if($isEdit) {?>,sizeId,type,size,isDb<?php }?>)
{	//alert("sizeId= "+sizeId+" type= "+type+" size= "+size+" isDb= "+isDb);
	if (hasLoaded) {
		
		var delRow = obj.parentNode.parentNode;
		var tbl = delRow.parentNode.parentNode;
		var rIndex = delRow.sectionRowIndex;		
		var rowArray = new Array(delRow);
		<?php if($isEdit) { ?>
		if(isDb){
		var dataString = "sizeId="+sizeId+"&type="+type+"&size="+size;	
		$.ajax({type: "POST",url: "sizeScaleDelete.php",data: dataString,dataType: "json",																																																													success:																																																													function(data){if(data!=null){	if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); } else {	if(document.getElementById('isEdit').value==1){$("#message").html("<div class='successMessage'><strong>One record deleted...</strong></div>");}else{$("#message").html("<div class='successMessage'><strong>One record deleted...</strong></div>");																																																													}}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");																																																													}}});}
		<?php }?>
		DeleteRow(rowArray);		
	}
}
function DeleteRow(rowObjArray)
{
	if (hasLoaded) {
		for (var i=0; i<rowObjArray.length; i++) {
			var rIndex = rowObjArray[i].sectionRowIndex;
			rowObjArray[i].parentNode.deleteRow(rIndex);
		}
	}
}
</script>
<script type="text/javascript">
<?php if($isEdit)
{
	for($i=1; $i < count($mainSize); $i++)
	{
		if($mainSize[$i]['scaleSize'] != "")
			echo "AddRow('tblScaleSize','mainOrder[]','scaleSize[]','".$mainSize[$i]['sizeScaleId']."', 'scale', '".$mainSize[$i]['scaleSize']."','".$mainSize[$i]['mainOrder']."', 1);";
	}
	for($j=1; $j < count($opt1Size); $j++)
	{
		if($opt1Size[$j]['opt1Size'] != "")
			echo "AddRow('tblOpt1Size','opt1Order[]','opt1Size[]','".$opt1Size[$j]['sizeScaleId']."', 'opt1', '".$opt1Size[$j]['opt1Size']."','".$opt1Size[$j]['opt1Order']."', 1);";
	}
	for($k=1; $k < count($opt2Size); $k++)
	{
		if($opt2Size[$k]['opt2Size'] != "")
			echo "AddRow('tblOpt2Size','opt2Order[]','opt2Size[]','".$opt2Size[$k]['sizeScaleId']."', 'opt2', '".$opt2Size[$k]['opt2Size']."','".$opt2Size[$k]['opt2Order']."', 1);";
	}
}
	?>
</script>
 <?php  require('../trailer.php');
?>