<?php require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
	require('../../header.php');	
	
	$sql1='SELECT "colorID","colorName" from tbl_color where status=1';
	if(!($result1=pg_query($connection,$sql1)))
	{
		print("Failed Color: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data1[]=$row1;
	}
	pg_free_result($result1);
	$sql2='SELECT "fabricID","fabName" from tbl_fabrics where status=1';
	if(!($result1=pg_query($connection,$sql2)))
	{
		print("Failed Fabric: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data2[]=$row1;
	}
	pg_free_result($result1);
	$sql3='SELECT "garmentID","garmentName" from tbl_garment where status=1';
	if(!($result1=pg_query($connection,$sql3)))
	{
		print("Failed Garment: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data3[]=$row1;
	}
	pg_free_result($result1);
	$sql4='SELECT "sizeID","sizeName" from tbl_size where status=1';
	if(!($result1=pg_query($connection,$sql4)))
	{
		print("Failed Size: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data4[]=$row1;
	}
	pg_free_result($result1);
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
	echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
	?> 
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='newInventory/inventoryManagement.php';" class="button_text" type="submit" name="back" value="Back" /></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
  <table width="100%">
      <tr>
        <td align="center" valign="top"><font size="5">DATABASE<br/>
            <br/>
            </font>
            <div id="selectBox_Div">
            <table width="80%" border="0">
              <tr>
                <td align="left" valign="top">Colors</td>
                <td align="left" valign="top">Description </td>
                <td align="left" valign="top">Fabrics</td>
                <td align="left" valign="top">Size</td>
              </tr>
              <tr>
                <td align="left" valign="top"><select  class="description" id="colorSel" size="15">
                  <optgroup label="Color Description">
                   <?php 
                for($i=0; $i < count($data1); $i++){
					if($i > 0)
					{
                ?>
                	
                  		<option value="<?php echo $data1[$i]['colorID'];?>"><?php echo $data1[$i]['colorName'];?></option>
                   
                 <?php
					}
					else
					{
						$colorId = $data1[$i]['colorID'];
						?>
                    	<option selected="selected" value="<?php echo $data1[$i]['colorID'];?>"><?php echo $data1[$i]['colorName'];?></option>
                    	
                <?php
					}
				}
                ?> 
                  </optgroup>
                </select></td>
                <td align="left" valign="top"><select class="description"  id="garmentSel" size="15">
                  <optgroup label="Garment Description">
                     <?php 
                for($i=0; $i < count($data3); $i++){
					if($i > 0)
					{
                ?>               
                  <option value="<?php echo $data3[$i]['garmentID'];?>"><?php echo $data3[$i]['garmentName'];?></option>
                   <?php
					}
					else
					{
						$garmentId = $data3[$i]['garmentID'];
						?>
                    <option selected="selected" value="<?php echo $data3[$i]['garmentID'];?>"><?php echo $data3[$i]['garmentName'];?></option>
                 <?php
					}
                }
                ?> 
                  </optgroup>
                </select></td>
                <td align="left" valign="top"><select class="description"  id="fabricSel" size="15">
                  <optgroup label="Fabrics Description">
                    <?php 
                for($i=0; $i < count($data2); $i++){
                	if($i > 0)
					{
                ?>     
                  <option value="<?php echo $data2[$i]['fabricID'];?>"><?php echo $data2[$i]['fabName'];?></option>
                  <?php
					}
					else
					{
						$fabricId = $data2[$i]['fabricID'];
						?>
                    <option selected="selected" value="<?php echo $data2[$i]['fabricID'];?>"><?php echo $data2[$i]['fabName'];?></option>
                    
                 <?php
					}
                }
                ?> 
                  </optgroup>
                </select></td>
                <td align="left" valign="top"><select  class="description" id="sizeSel" size="15">
                  <optgroup label="Size Description">
                    <?php 
                for($i=0; $i < count($data4); $i++){
                	if($i > 0)
					{
                ?>     
                  <option value="<?php echo $data4[$i]['sizeID'];?>"><?php echo $data4[$i]['sizeName'];?></option>
                  <?php
					}
					else
					{
						$sizeId = $data4[$i]['sizeID'];
						?>
                    <option selected="selected" value="<?php echo $data4[$i]['sizeID'];?>"><?php echo $data4[$i]['sizeName'];?></option>
                 <?php
					}
                }				
                ?>                                              
                  </optgroup>
                </select></td>
              </tr>
              <tr>
                <td align="left" valign="top">&nbsp;</td>
                <td align="left" valign="top">&nbsp;</td>
                <td align="left" valign="top">&nbsp;</td>
                <td align="left" valign="top">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="top"><input onClick="Hide();visible('colorAdd',true);" name="button34" type="button" onmouseover="this.style.cursor = 'pointer';" value="Add" />
                  <input id="colorEdit_But" type="button" onmouseover="this.style.cursor = 'pointer';" value="Edit" /></td>
                <td align="left" valign="top"><input onClick="Hide();visible('garmentAdd',true);" name="button35" type="button" onmouseover="this.style.cursor = 'pointer';" value="Add" />
                  <input id="garmentEdit_But" type="button" onmouseover="this.style.cursor = 'pointer';" value="Edit" /></td>
                <td align="left" valign="top"><input name="button36"  onClick="Hide();visible('fabricAdd',true);" type="button" onmouseover="this.style.cursor = 'pointer';" value="Add" />
                  <input id="fabricEdit_But" type="button" onmouseover="this.style.cursor = 'pointer';" value="Edit" /></td>
                <td align="left" valign="top"><input name="button37" onClick="Hide();visible('sizeAdd',true);" type="button" onmouseover="this.style.cursor = 'pointer';" value="Add" />
                  <input id="sizeEdit_But" type="button" onmouseover="this.style.cursor = 'pointer';" value="Edit" /></td>
              </tr>
          </table>
          </div>
      </td>
    </tr>
    </table>
    <br /> 
    <center>
    <table width="50%" border="0" cellspacing="0" cellpadding="0">
    <tr><td>
    <div align="center" id="message"></div>
    </td></tr></table>  
   	<table border="0" cellspacing="0" cellpadding="0">                                       
      <tr>
        <td>          
        <div id="colorAdd_Div" style="float:left; width:300px;display:none">    
        <form id="colorAdd_Frm">    
      <table width="100%" border="0" cellspacing="0" cellpadding="0">                      
         <tr>
          <td align="center" height="35" colspan="3"><strong>Add Color </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="cNameA" type="text" class="textBox" id="colorName" /></td>
        </tr>
        <tr>
          <td height="35">C unicode:</td>
          <td>&nbsp;</td>
          <td><input name="cUnicodeA" type="text" class="textBox" id="cUnicode" /></td>
        </tr>
        <tr>
          <td height="35">Hex:</td>
          <td>&nbsp;</td>
          <td><input name="cHexA" type="text" class="textBox"  id="hex"/></td>
        </tr>
        <tr>
          <td height="35">pms:</td>
          <td>&nbsp;</td>
          <td><input name="cpmsA" type="text" class="textBox" id="pms" /></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Save"/></td>                             
        </tr>
      </table>
      </form>
      </div>
    
    
   <div id="colorEdit_Div" style="float:left; width:300px;display:none">
    <form id="colorEdit_Frm"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Edit Color </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="cNameE" id="cNameE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">C unicode:</td>
          <td>&nbsp;</td>
          <td><input name="cUnicodeE" id="cUnicodeE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Hex:</td>
          <td>&nbsp;</td>
          <td><input name="chexE" id="chexE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">pms:</td>
          <td>&nbsp;</td>
          <td><input name="cpmsE" id="cpmsE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td><input name="colorEditId" id="colorEditId" type="hidden" value= "<?php echo $colorId;?>"/></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Update" />
          <input type="button" name="colorDel_But" id="colorDel_But" onmouseover="this.style.cursor = 'pointer';" value="Delete" /></td>
          </tr>
      </table>
      </form>
    </div>
    
    
    
    <div id="garmentAdd_Div" style="float:left; width:300px;display:none">
    <form id="garmentAdd_Frm"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Add Garments </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="gNameA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="gDescA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">G unicode:</td>
          <td>&nbsp;</td>
          <td><input name="gUnicodeA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Img_url:</td>
          <td>&nbsp;</td>
          <td><input name="gImg_urlA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Save" /></td>
        </tr>
      </table>
      </form>
    </div>
    
    <div id="garmentEdit_Div" style="float:left; width:300px;display:none">
    <form id="garmentEdit_Frm"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Edit Garments </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="gNameE" id="gNameE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="gDescE" id="gDescE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">G unicode:</td>
          <td>&nbsp;</td>
          <td><input name="gUnicodeE" id="gUnicodeE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Img_url:</td>
          <td>&nbsp;</td>
          <td><input name="gImg_urlE" id="gImg_urlE" type="text" class="textBox" /></td>
        </tr>
         <tr>         
          <td><input name="garmentEditId" id="garmentEditId" type="hidden" value= "<?php echo $garmentId;?>"/></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Update" />
          <input type="button" name="garmentDel_But" id="garmentDel_But" onmouseover="this.style.cursor = 'pointer';" value="Delete" /></td>
        </tr>
      </table>
      </form>
    </div>
    <div id="fabricAdd_Div" style="float:left; width:300px;display:none">
    <form id="fabricAdd_Frm"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Add Farbic </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="fNameA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="fDescA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">F unicode:</td>
          <td>&nbsp;</td>
          <td><input name="fUnicodeA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Img_url:</td>
          <td>&nbsp;</td>
          <td><input name="fImg_urlA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Save" /></td>
        </tr>
      </table>
      </form>
    </div>
    <div id="fabricEdit_Div" style="float:left; width:300px;display:none">
    <form id="fabricEdit_Frm">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Edit Farbic </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="fNameE" id="fNameE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="fDescE"  id="fDescE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">F unicode:</td>
          <td>&nbsp;</td>
          <td><input name="fUnicodeE" id="fUnicodeE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Img_url:</td>
          <td>&nbsp;</td>
          <td><input name="fImg_urlE" id="fImg_urlE" type="text" class="textBox" /></td>
        </tr>
         <tr>          
          <td><input name="fabricEditId" id="fabricEditId" type="hidden" value= "<?php echo $fabricId;?>"/></td>
        </tr>
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Update" />
          <input type="button" name="fabricDel_But" id="fabricDel_But" onmouseover="this.style.cursor = 'pointer';" value="Delete" /></td>
        </tr>
      </table>
      </form>
    </div>
    <div id="sizeAdd_Div" style="float:left; width:300px;display:none">
    <form id="sizeAdd_Frm">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Add Size </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="sNameA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="sDescA" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">S unicode:</td>
          <td>&nbsp;</td>
          <td><input name="sUnicodeA" type="text" class="textBox" /></td>
        </tr>       
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Save" /></td>
        </tr>
      </table>
      </form>
    </div>
    <div id="sizeEdit_Div" style="float:left; width:300px;display:none">
    <form id="sizeEdit_Frm">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" height="35" colspan="3"><strong>Edit Size </strong></td>
          </tr>
        <tr>
          <td height="35">Name:</td>
          <td>&nbsp;</td>
          <td><input name="sNameE" id="sNameE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">Description:</td>
          <td>&nbsp;</td>
          <td><input name="sDescE" id="sDescE" type="text" class="textBox" /></td>
        </tr>
        <tr>
          <td height="35">S unicode:</td>
          <td>&nbsp;</td>
          <td><input name="sUnicodeE" id="sUnicodeE" type="text" class="textBox" /></td>
        </tr>    
        <tr>        
          <td><input name="sizeEditId" id="sizeEditId" type="hidden" value= "<?php echo $sizeId;?>"/></td>
        </tr>       
        <tr>
          <td height="35">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" onmouseover="this.style.cursor = 'pointer';" value="Update" />
          <input type="button" name="sizeDel_But" id="sizeDel_But" onmouseover="this.style.cursor = 'pointer';" value="Delete" /></td>
        </tr>
      </table>
      </form>
    </div>                      
    </td>
    </tr>                       
    </table>    
    </center> 
    <input type="hidden" id="hdnFrmType" value=""/> 
    <script>
function visible(id,status){var e = document.getElementById(id+'_Div');	if(status==true){e.style.display = "";document.getElementById('hdnFrmType').value = id;window.scrollBy(0,250);}else e.style.display = 'none';}
function Hide(){document.getElementById('colorAdd_Div').style.display='none';document.getElementById('colorEdit_Div').style.display='none';document.getElementById('garmentAdd_Div').style.display='none';document.getElementById('garmentEdit_Div').style.display='none';document.getElementById('fabricAdd_Div').style.display='none';document.getElementById('fabricEdit_Div').style.display='none';document.getElementById('sizeAdd_Div').style.display='none';document.getElementById('sizeEdit_Div').style.display='none';$("#message").empty();}
$(document).ready(function(){$("#colorDel_But").click(function(){if(document.getElementById('colorEditId').value != ""){if(confirm('Are you sure you want to delete the record')) {DeleteRecords('colorEdit',"colorDel"); }}});$("#garmentDel_But").click(function(){if(document.getElementById('garmentEditId').value != ""){if(confirm('Are you sure you want to delete the record')) {DeleteRecords('garmentEdit',"garmentDel"); }}});$("#fabricDel_But").click(function(){if(document.getElementById('fabricEditId').value != ""){if(confirm('Are you sure you want to delete the record')) {DeleteRecords('fabricEdit',"fabricDel"); }}});$("#sizeDel_But").click(function(){if(document.getElementById('sizeEditId').value != ""){if(confirm('Are you sure you want to delete the record')) {DeleteRecords('sizeEdit',"sizeDel"); }}});$("#colorSel").change(function(){if(document.getElementById('colorEdit_Div').style.display == 'none'){document.getElementById('colorEditId').value = $("#colorSel").val();}else{$("#colorSel option[value='"+document.getElementById('colorEditId').value+"']").attr('selected', 'selected');$("#message").html("<div class='errorMessage'><strong>Waring ! Updation is pending on existing item. Please Update.</strong></div>");}});$("#garmentSel").change(function(){if(document.getElementById('garmentEdit_Div').style.display == 'none'){document.getElementById('garmentEditId').value = $(this).val();}else{$("#garmentSel option[value='"+document.getElementById('garmentEditId').value+"']").attr('selected', 'selected');$("#message").html("<div class='errorMessage'><strong>Waring ! Updation is pending on existing item. Please Update.</strong></div>");}});$("#fabricSel").change(function(){if(document.getElementById('fabricEdit_Div').style.display == 'none'){document.getElementById('fabricEditId').value = $(this).val();}else{$("#fabricSel option[value='"+document.getElementById('fabricEditId').value+"']").attr('selected', 'selected');$("#message").html("<div class='errorMessage'><strong>Waring ! Updation is pending on existing item. Please Update.</strong></div>");}});$("#sizeSel").change(function(){if(document.getElementById('sizeEdit_Div').style.display == 'none'){document.getElementById('sizeEditId').value = $(this).val();}else{$("#sizeSel option[value='"+document.getElementById('sizeEditId').value+"']").attr('selected', 'selected');$("#message").html("<div class='errorMessage'><strong>Waring ! Updation is pending on existing item. Please Update.</strong></div>");}});$("#colorEdit_But").click(function(){if(document.getElementById('colorEditId').value != ""){GetEditDetails("colorEdit");}});$("#garmentEdit_But").click(function(){if(document.getElementById('garmentEditId').value != ""){GetEditDetails("garmentEdit");}});$("#fabricEdit_But").click(function(){if(document.getElementById('fabricEditId').value != ""){GetEditDetails("fabricEdit");}});$("#sizeEdit_But").click(function(){if(document.getElementById('sizeEditId').value != ""){GetEditDetails("sizeEdit");}});});$(function(){$("#colorAdd_Frm").submit(function(){PostDB();return false;});$("#colorEdit_Frm").submit(function(){PostDB();return false;});$("#garmentAdd_Frm").submit(function(){PostDB();return false;});$("#garmentEdit_Frm").submit(function(){PostDB();return false;});$("#fabricAdd_Frm").submit(function(){PostDB();return false;});$("#fabricEdit_Frm").submit(function(){PostDB();return false;});$("#sizeAdd_Frm").submit(function(){PostDB();return false;});$("#sizeEdit_Frm").submit(function(){PostDB();return false;});});
function GetEditDetails(type){var id = document.getElementById(type+"Id").value;dataString = "type="+type+"&id="+id;$.ajax({type: "POST",url: "editDetails.php",data: dataString,dataType: "json",timeout:60000,success: function(data){if(data!=null){if(data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " +data.error +"</strong></div>");} else{if(data != null){switch(type){case 'colorEdit':{document.getElementById('cNameE').value = data['colorName'];document.getElementById('cUnicodeE').value = data['cUnicode'];document.getElementById('chexE').value = data['hex'];document.getElementById('cpmsE').value = data['pms'];Hide();visible('colorEdit',true);break;}case 'garmentEdit':{document.getElementById('gNameE').value = data['garmentName'];document.getElementById('gDescE').value = data['gdescription'];document.getElementById('gUnicodeE').value = data['gUnicode'];document.getElementById('gImg_urlE').value = data['imgURL'];Hide();visible('garmentEdit',true);break;}case 'fabricEdit':{document.getElementById('fNameE').value = data['fabName'];document.getElementById('fDescE').value = data['fabDescription'];document.getElementById('fUnicodeE').value = data['fUnicode'];document.getElementById('fImg_urlE').value = data['imgURL'];Hide();visible('fabricEdit',true);break;}case 'sizeEdit':{document.getElementById('sNameE').value = data['sizeName'];document.getElementById('sDescE').value = data['sizeDescription'];document.getElementById('sUnicodeE').value = data['sUnicode'];Hide();visible('sizeEdit',true);break;}}}/*else alert('data null');*/}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process. Please try again later...</strong></div>");}},error:function(objAJAXRequest,strError){$("#message").html("<div class='errorMessage'><strong>Failed to load data."+strError+"Please try again later.</strong></div>");}});}
function PostDB(){var type = document.getElementById("hdnFrmType").value;dataString = $("#"+type+"_Frm").serialize();dataString += "&submitType="+type;$.ajax({type: "POST",url: "database1.php",	data: dataString,dataType: "json",timeout:60000,success: function(data){if(data!=null){if(data.name || data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");} else {$("#message").html("<div class='successMessage'><strong>New Color details Added. Thank you.</strong></div>");location.reload(true);}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process. Please try again later...</strong></div>");}},error:function(objAJAXRequest,strError){$("#message").html("<div class='errorMessage'><strong>Failed to load data."+strError+"Please try again later.</strong></div>");}});}
function DeleteRecords(val,type){var id = document.getElementById(val+"Id").value;dataString = "type="+type+"&id="+id;$.ajax({type: "POST",url: "editDetails.php",data: dataString,dataType: "json",success: function(data){if(data!=null){if (data.error){$("#message").html("<div class='errorMessage'><strong>Sorry, " +data.error +"</strong></div>");}else {$("#message").html("<div class='successMessage'><strong>Deleted one record. Thank you.</strong></div>");	location.reload(true);}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process. Please try again later...</strong></div>");}}});}
</script>	
 <?php  require('../../trailer.php');
?>