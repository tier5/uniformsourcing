<?php
require('Application.php');
require('../../header.php');
//$sql='select st."styleId" st."styleNumber",st."scaleNameId",st."scaleNameId" from "tbl_invStyle" st left join tbl_inventory inv on st."styleId"=inv."styleId" where st."styleId"='.$_GET['StyleId'];
$search = "";
if(isset($_GET['styleId']))
{
	$styleId 	= $_GET['styleId'];	
	if(isset($_GET['colorId']))
	{
		$clrId 		= $_GET['colorId'];
		$opt1Id 	= $_GET['opt1Id'];
		$opt2Id 	= $_GET['opt2Id'];
	}
	else
	{
		$clrId 		= 0;
		$opt1Id 	= 0;
		$opt2Id 	= 0;
	}
	if($clrId > 0)
	{
		$search = " and \"colorId\"=$clrId ";
		if($opt1Id > 0)
		 	$search .= "and \"opt1ScaleId\"=$opt1Id ";
		if($opt2Id > 0)
		 	$search .= "and \"opt2ScaleId\"=$opt2Id ";
	}
	else
		$search = "";	
}
$sql ='select "styleId","barcode", "styleNumber", "scaleNameId", price, "locationIds" from "tbl_invStyle" where "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
	print("Failed StyleQuery: " . pg_last_error($connection));
	exit;
}
$row = pg_fetch_array($result);
$data_style=$row;
pg_free_result($result);
$query2='Select * from "tbl_invColor" where "styleId"='.$data_style['styleId'];
if(!($result2=pg_query($connection,$query2))){
	print("Failed OptionQuery: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data_color[]=$row2;}
pg_free_result($result2);
if($data_style['scaleNameId']!="" )
{	

	$query2='Select "opt1Name","opt2Name" from "tbl_invScaleName" where "scaleId"='.$data_style['scaleNameId'];
	if(!($result=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_optionName=$row;
	pg_free_result($result);
	
	$query2='Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_mainSize[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt1Size[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt2Size[]=$row2;}
	pg_free_result($result2);
}

$totalScale = count($data_mainSize);
$tableWidth = 0;

$tableWidth = $totalScale * 100;

$sql='select "inventoryId",quantity,"newQty","isStorage" from "tbl_inventory"';
if(!($result=pg_query($connection,$sql))){
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;}
		for($i=0; $i<count($data_inv); $i++)
		{
			if($data_inv[$i]['newQty']>0)
			{
				if(($data_inv[$i]['quantity']!="" && $data_inv[$i]['quantity']>0))
				{
					$sql='update "tbl_inventory" set "isStorage"=1 ,"newQty"=0';
						if(!($result=pg_query($connection,$sql))){
						print("Failed invUpdateQuery: " . pg_last_error($connection));
						exit;
					}
				}
				else if(($data_inv[$i]['quantity']=="" || $data_inv[$i]['quantity']==0))
				{
					$sql='Delete from "tbl_inventory" where "inventoryId"='.$data_inv[$i]['inventoryId'];
						if(!($result=pg_query($connection,$sql))){
						print("Failed deleteInvQuery: " . pg_last_error($connection));
						exit;
					}
				}
			}
		}
if(count($data_color) > 0)
{	
	if($search != "")
	{
		$query='select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "isActive"=1'.$search.' order by "inventoryId"';
	}
	else
	{
		$clrId = $data_color[0]['colorId'];
		$query='select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$data_color[0]['colorId'].'  and "isActive"=1 order by "inventoryId"';
	}	
	if(!($result=pg_query($connection,$query))){
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;}
	pg_free_result($result);
}
$query='select * from "tbl_invLocation" order by "locationId"';
if(!($result=pg_query($connection,$query))){
	print("Failed invQuery: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_loc[]=$row;}
pg_free_result($result);
$locArr = array();
if($data_style['locationIds'] != "")
{
	$locArr = explode(",",$data_style['locationIds']);
}
?>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>

<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='reports.php'" /></td>
    <td>&nbsp;</td>
    <td align="right"><label>
      <input type="button" name="send-email" id="send-email" value="Send Email" />
    </label></td>
  </tr>
</table>
<table width="100%">
<tr><td><center><table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table></center></td></tr>
<tr>
<td align="center"><font size="5">Report</font><font size="5"> View/Edit   <br>
    <br>
  </font>
  <fieldset style="margin:10px;">
  <table width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
       <td>&nbsp;</td>
       <td>&nbsp;</td>
    
    </tr>
    <tr>
    <form id="optForm" method="post">
      <td>Style:</td>
     <td><h1><?php echo $data_style['styleNumber'];?></h1></td>
     <?php if($data_style['barcode']!=""){ ?>
      <td width="60">Barcode:</td>
    <td><h1><img width="100" height="100" src="../../uploadFiles/inventory/images/<?php echo $data_style['barcode'];?>"></h1></td>
      <?php } ?>
      <td>
      <div class="color">Color:&nbsp;
          <select name="color" id="color">                      	
 <?php 
for($i=0; $i < count($data_color); $i++){
	if($data_color[$i]['name']!="")
	{
		if($data_color[$i]['colorId'] == $clrId)
		{
			$imageName = $data_color[$i]['image'];
			echo '<option selected="selected" value="'.$data_color[$i]['colorId'].'">'.$data_color[$i]['name'].'</option>';
			continue;
		}
	  echo '<option value="'.$data_color[$i]['colorId'].'">'.$data_color[$i]['name'].'</option>';
	}
}?>
    </select></div></td>
    <td>&nbsp;</td></form>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
  </tr>
</table>
</fieldset>
<form id="inventoryForm">
<div id="scrollLinks">
<fieldset style="margin:10px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
	  <td width="10"></td>
	  	<td width="170" align="left" valign="top" style="padding:5px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img id="imgView" src="<?php echo $upload_dir_image.$imageName;?>" alt="thumbnail" width="150" height="230" border="1" class="mouseover_left" /></td>
            </tr>
          <tr>
            <td height="100">&nbsp;</td>
            </tr>
          <tr>
            <td><input width="117" height="98" type="image" src="<?php echo $mydirectory;?>/images/updtInvbutton.jpg" alt="Submit button"/></td>
            </tr>
          
        </table></td>
	<td width="10"></td>
        <td><div id="header" style="float:left; width:100%;">      
        <div id="scrollLinks4">
<div id="scrollLinks2">
<div id="scrollLinks">
<div id="scrollLinks3">
<table class="HD001" width="250px" style="float:left;" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td class="gridHeaderReportGrids3">&nbsp;</td>
    <td class="gridHeaderReport">sizes </td>
  </tr>
  <tr>
    <td class="gridHeaderReportGrids3"><a class="mouseover_left" href="#"><img src="<?php echo $mydirectory;?>/images/leftArrw.gif" alt="lft" width="33" height="26" border="0" /></a><a class="mouseover_right" href="#"><img src="<?php echo $mydirectory;?>/images/rightArrw.gif" alt="lft" width="30" height="26" border="0" /></a></td>
    <td class="gridHeaderReport">prices</td>
  </tr>
  <tr>
    <td class="gridHeaderReportGrids3">&nbsp;</td>
    <td class="gridHeaderReportGrids2">&nbsp;</td>
  </tr>
  <tr>
    <td class="gridHeaderReportGrids3">&nbsp;</td>
    <td colspan="2" class="gridHeaderReportGrids4"><?php echo $data_optionName['opt1Name'];?></td>
  </tr>
  <tr>
    <td class="gridHeaderReportGrids3">&nbsp;</td>
    <td class="gridHeaderReportGrids2"><span class="gridHeaderReportGrids3"><a class="mouseover_up" href="#"><img src="<?php echo $mydirectory;?>/images/upArrw.gif" alt="lft" width="33" height="26" border="0" /></a><a class="mouseover_down" href="#"><img src="<?php echo $mydirectory;?>/images/dwnArrw.gif" alt="lft" width="33" height="26" border="0" /></a></span></td>
  </tr>
</table>
</div> 
</div> 
</div>
</div>		
<div class="TopDiv">
<!-- window 1 starts here -->
<div id="wn">
  <div id="lyr1">
    <table  style="float:left;" width="<?php echo $tableWidth."px";?>" border="0" cellspacing="1" cellpadding="1">
      <tr id="mainSizeTop">
	</tr>
      <tr id="priceTop">       
      </tr>
      <tr id="dummy1">      
      </tr>
      <tr id="dummy2">       
      </tr>
      <tr id="columnSize">      
      </tr>
    </table>
  </div>
</div>
</div>
</div>
<div id="wn3">
  <div id="lyr3">
    <div id="rowSize" style="float:left; width:250px;">
      <table width="250" border="0" cellspacing="1" cellpadding="1">
<?php
if($locArr[0] > 0 && $locArr[0] != "")
{	
	$loc_i = 0;
	for($i=0;$i<count($locArr);$i++, $loc_i++)
	{ 	
		for(;$loc_i < count($data_loc);$loc_i++)
		{
			if($locArr[$i] == $data_loc[$loc_i]['locationId'])
				break;
		}
?>
    <tr>
      <td class="gridHeaderReportGrids3"><?php echo $data_loc[$loc_i]['name'];?></td>
			<?php
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{			
				if($j != 0)
				{
            ?>
    	<tr>
		<td class="gridHeaderReportGrids3">&nbsp;</td>
		<td class="gridHeaderReportalt"><?php echo $data_opt1Size[$j]['opt1Size']; ?></td>
        </tr>	
				
	<?php	
				}
				else
				{
	?>				
        <td class="gridHeaderReportalt"><?php echo $data_opt1Size[$j]['opt1Size']; ?></td>
        </tr>			
	<?php			
				}
			}
		}
		else
		{
		?>
        <td style="visibility:hidden;" class="gridHeaderReportGrids2">&nbsp;</td>
        </tr>
    	<?php		
		}
	?> 
      <tr>
        <td class="gridHeaderReportGrids3">&nbsp;</td>
        <td class="gridHeaderReportGrids2">&nbsp;</td>
      </tr>       
<?php 
	}//LocArr for
}//locArr if
?>  
</table>
  </div>
  <?php if(count($data_opt1Size) > 0){?>
<div id="wn2" style="position:relative; width:600px; height:<?php echo ((count($data_opt1Size)*count($locArr) * 32) + (count($data_loc) * 32));?>px;  overflow:hidden; float:left;">
<?php }else{?>
<div id="wn2" style="position:relative; width:600px; height:<?php echo (count($locArr) * 64);?>px;  overflow:hidden; float:left;">
<?php }?>
  <div id="lyr2">
    <div id="values" >
      <table id="values" width="<?php echo $tableWidth."px";?>" border="0" cellspacing="1" cellpadding="1">
<?php
if($locArr[0] > 0 && $locArr[0] != "")
{		
	for($i=0;$i<count($locArr);$i++)
	{		
		$rowIndex=0;
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{
				echo '<tr id="qty_'.$i.'_'.$rowIndex.'"></tr>';				
				$rowIndex++;
			}
		}
		else
		{
			echo '<tr id="qty_'.$i.'_'.$rowIndex.'"></tr>';		
		}		
		echo '<tr id="qtyDummy'.$i.'"></tr>';	
	}	
}?>
</table>
    </div>
  </div>
</div>
</div>
</div>
<div id="footer" style="width:100%; float:left;">
    <table class="HD001" style="float:left; width:250px;" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td class="gridHeaderReportGrids3">&nbsp;</td>
        <td class="gridHeaderReport">sizes </td>
      </tr>
    </table>
  <div id="wn4">
    <div id="lyr4">
        <table class="TopValues" style="float:left; width:<?php echo $tableWidth."px";?>;"  border="0" cellspacing="1" cellpadding="1">
          <tr id="mainSizeBottom">            
          </tr>
          <tr id="adjBottom" ></tr>
        </table>
    </div>
  </div>
</div></td>
</tr>
<tr>
<td id="hdnVar">
	<input type="hidden" name="scaleNameId" value="<?php echo $data_style['scaleNameId'];?>"/>
      <input type="hidden" id="styleId" name="styleId" value="<?php echo $styleId;?>"/>
      <input type="hidden" id="colorId" name="colorId" value="<?php echo $clrId;?>"/> 
      <input type="hidden" id="locCount" name="locCount" value="0"/> 
      <input type="hidden" id="rowCount" name="rowCount" value="0"/>
      <input type="hidden" id="mainCount" name="mainCount" value="0"/>       
</td></tr>
</table>
<br />
</fieldset> 
</div></form>
</td>
</tr>
</table>
  <div id="dialog-form" title="Submit By Email">
			<p class="validateTips">All form fields are required.</p>       	
			<form action='reportMail.php?styleId=<?php echo $styleId;?>&colorId=<?php if(isset($_GET['colorId'])){ echo $clrId;} else{ echo $data_color[0]['colorId'];}?>' id="frmmailsendform" method="POST">
			<fieldset>				
				<label for="email">Email</label>
				<input type="text" name="email" id="email"  value="" class="text ui-widget-content ui-corner-all" />
				<label for="subject">Subject:</label>
				<input type="text" name="subject" id="subject" class="text ui-widget-content ui-corner-all" />				
			</fieldset>
		</form>
		</div>        
<script type="text/javascript">
function AddRow(type,cellId,value)
{
	switch(type)
	{
		case 'main':
		{
			var trTop = document.getElementById('mainSizeTop');
			var trBottom = document.getElementById('mainSizeBottom');
			var tr2Bottom = document.getElementById('adjBottom');
			var cell = trTop.insertCell(cellId);
			cell.className = 'gridHeaderReport';
			cell.innerHTML=value;
			cell = trBottom.insertCell(cellId);
			cell.className = 'gridHeaderReport';
			cell.innerHTML=value;			
			cell = tr2Bottom.insertCell(cellId);
			cell.className = 'txBxWhite';
			var txtBox = document.createElement("input");
			txtBox.type = "text";	
			txtBox.className = "txBxWhite";			
			txtBox.value = "";
			cell.appendChild(txtBox);
			break;
		}
		case 'price':
		{
			var trPrice = document.getElementById('priceTop');
			var cell = trPrice.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			var txtBox = document.createElement("input");
			txtBox.type = "text";	
			txtBox.className = "txBxWhite";
			txtBox.name = 'price[]';
			txtBox.value = value;
			cell.appendChild(txtBox);
			break;
		}		
		case 'column':
		{
			var trc = document.getElementById('columnSize');
			var cell = trc.insertCell(cellId);
			cell.className = 'gridHeaderReportBlue';
			cell.innerHTML=value;
			break;
		}
		case 'dummy':
		{	
			var trd = document.getElementById('dummy1');
			var cell = trd.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			cell.innerHTML=value;
			
			trd = document.getElementById('dummy2');
			cell = trd.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			cell.innerHTML=value;
			break;
		}		
	}	
}
function AddQty(trId,type,cellId,locIndex,rowIndex,qty,invIdValue)
{
	switch(type)
	{
		case 'qty':
		{
			var tr = document.getElementById(trId);		
			var cell = tr.insertCell(cellId);
			var txtBox = document.createElement("input");
			cell.className = 'gridHeaderReportGrids';
			txtBox.type = "text";
			txtBox.name = "qty["+locIndex+"]["+rowIndex+"][]";
			txtBox.className = "txBxGrey";			
			txtBox.value = qty;
			cell.appendChild(txtBox);
			
			txtBox = document.createElement("input");			
			txtBox.type = "hidden";
			txtBox.name = "invId["+locIndex+"]["+rowIndex+"][]";
			txtBox.value = invIdValue;
			cell.appendChild(txtBox);	
			if(invIdValue > 0 && qty > 0)
			{
				a = document.createElement("a");
				a.setAttribute("href", "#");
				img = document.createElement("img");
				img.width="15px";
				img. height="14px";
				img.className = "imgRght";
				img.src="<?php echo $mydirectory;?>/images/Btn_edit.gif";				
				img.setAttribute("onclick",'QtyDblClick('+invIdValue+');');						
				a.appendChild(img);
				cell.appendChild(a);
			}
			break;
		}
		case 'qtyDummy':
		{
			var trd = document.getElementById('qtyDummy'+locIndex);
			var cell = trd.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			cell.innerHTML="&nbsp;";
			
			break;
		}
	}
}
function StoreInitialValues(locIndex,rowIndex,txtValue, newQty)
{
	var td = document.getElementById('hdnVar');
	var element1 = document.createElement("input");
	element1.type = "hidden";
	element1.name = "hdnqty["+locIndex+"]["+rowIndex+"][]";
	element1.value = txtValue;
	var element2 = document.createElement("input");
	element2.type = "hidden";
	element2.name = "hdnNewQty["+locIndex+"]["+rowIndex+"][]";
	element2.value = newQty;
	td.appendChild(element1);
	td.appendChild(element2);
	
}
function QtyDblClick(inventoryId)
{	
	$(location).attr('href',"storage.php?styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value+"&invId="+inventoryId);
}
</script>
<script type="text/javascript">	   
$(document).ready(function(){
<?php
if($data_style['scaleNameId']!="")
{
	$sizeIndex = 0;
	$columnSize = 0;
		
	for($i=0; $i<count($data_mainSize); $i++)
  	{
		$invPrice = 0;
		$found = 0;
		
		echo 'AddRow("main",'.$sizeIndex.',"'.$data_mainSize[$i]['scaleSize'].'");';
		for($j=0;$j<count($data_inv);$j++)
		{
			if($data_inv[$j]['sizeScaleId'] == $data_mainSize[$i]['mainSizeId'])
			{
				if($data_inv[$j]['price'] != "" || $data_inv[$j]['price'] > 0)
				{
					$invPrice = 1;
					echo 'AddRow("price",'.$sizeIndex.',"'.$data_inv[$j]['price'].'");';
				}
				break;
			}
		}		
		if(!$invPrice)
		{
			echo 'AddRow("price",'.$sizeIndex.',"'.$data_style['price'].'");';
		}			
		if($i < count($data_opt2Size))
		{
			echo 'AddRow("column",'.$columnSize++.',"'.$data_opt2Size[$i]['opt2Size'].'");';
		}
		echo 'AddRow("dummy",'.$sizeIndex.',"&nbsp;");';
		$sizeIndex++;		
	}
	if($sizeIndex)
		echo "document.getElementById('mainCount').value = $sizeIndex;";
}
$locIndex = 0;
$rowIndex = 0;
$mainIndex = 0;
if($locArr[0] > 0 && $locArr[0] != "")
{	
	for($i=0;$i<count($locArr);$i++,$locIndex++)
	{ 
		$rowIndex = 0;
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{			
				InsertQty($data_mainSize,$data_inv,$data_opt1Size[$j]['opt1SizeId'],$locArr[$i],$locIndex,$rowIndex);			
				$rowIndex++;
			}		
		}
		else
		{
			InsertQty($data_mainSize,$data_inv,0,$locArr[$i],$locIndex,$rowIndex);			
			$rowIndex++;		
		}
		echo 'AddQty("dummy","qtyDummy",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
	}
}
if($locIndex)
	echo "document.getElementById('locCount').value = $locIndex;";
if($rowIndex)
	echo "document.getElementById('rowCount').value = $rowIndex;";
function InsertQty($data_mainsize,$data_inv,$rowSizeId,$locId,$locIndex, $rowIndex)
{
	$mainIndex = 0;
	for($i=0;$i < count($data_mainsize);$i++)
	{
		$invFound=0;
		for($j=0; $j < count($data_inv);$j++)
		{
			if($rowSizeId > 0)
			{
				if(($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId']) && ($locId == $data_inv[$j]['locationId']) && ($rowSizeId == $data_inv[$j]['opt1ScaleId']))
				{
					$invFound = 1;
					if($data_inv[$j]['inventoryId'] != "")
					{
						if($data_inv[$j]['quantity'] != "" )
						{
							echo "StoreInitialValues($locIndex,$rowIndex,'".$data_inv[$j]['quantity']."','".$data_inv[$j]['newQty']."');";
							echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';
						}
						else
						{
							echo "StoreInitialValues($locIndex,$rowIndex,0,'".$data_inv[$j]['newQty']."');";
							echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';
						}
						
					}
					else
					{
						echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
						echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
					}
					break;
				}
			}
			else
			{
				if($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId'] && ($locId == $data_inv[$j]['locationId']) && ("" == $data_inv[$j]['opt1ScaleId']))
				{
					$invFound = 1;
					if($data_inv[$j]['inventoryId'] != "")
					{
						if($data_inv[$j]['quantity'] != "" )
						{
							echo "StoreInitialValues($locIndex,$rowIndex,'".$data_inv[$j]['quantity']."','".$data_inv[$j]['newQty']."');";
							echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';
						}
						else
						{
							echo "StoreInitialValues($locIndex,$rowIndex,0,'".$data_inv[$j]['newQty']."');";
							echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';
						}
						
					}
					else
					{
						echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
						echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
					}
					break;
				}
			}			
		}
		if(!$invFound)
		{
			echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
			echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
		}
		$mainIndex++;
	}		
}
?> 
$("#color").change(function()
	{
		PostRequest();		
});
function PostRequest()
{
	var stylId = document.getElementById('styleId').value;
	var clrId = 0;	
	if($("#color").val() != undefined)
	{
		clrId = $("#color").val();
	}	
	var dataString = 'styleId='+stylId+'&colorId='+ clrId;
	//alert(dataString);
	$.ajax
	({
		type: "POST",
		url: "reportOptSubmit.php",
		data: dataString,
		dataType: "json",
		success: function(data)
		{
			if(data!=null){	
				if(data.styleId != null){$(location).attr('href','reportViewEdit.php?'+dataString);}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>No Inventory found with sample color selected!</strong></div>");
				}
			}
		} 
	});	
}
});
</script>
<script type="text/javascript">
function init_dw_Scroll() {

var wndo = new dw_scrollObj('wn', 'lyr1');
wndo.setUpScrollControls('scrollLinks');

var wndo = new dw_scrollObj('wn2', 'lyr2');
wndo.setUpScrollControls('scrollLinks2');

var wndo1 = new dw_scrollObj('wn3', 'lyr3');
wndo1.setUpScrollControls('scrollLinks3');

var wndo1 = new dw_scrollObj('wn4', 'lyr4');
wndo1.setUpScrollControls('scrollLinks4');

}

// if code supported, link in the style sheet and call the init function onload
if ( dw_scrollObj.isSupported() ) {
    //dw_Util.writeStyleSheet('css/scroll.css');
    dw_Event.add( window, 'load', init_dw_Scroll);
}
</script>
<script type="text/javascript">
$(function(){$("#inventoryForm").submit(function(){$("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");dataString = $("#inventoryForm").serialize();dataString += "&type=e";$.ajax({type: "POST",url: "invReportSubmit.php",data: dataString,dataType: "json",success:function(data){if(data!=null){	if(data[0].name || data[0].error){$("#message").html("<div class='errorMessage'><strong>Sorry, " + data[0].name + data[0].error +"</strong></div>"); if(data[0].flag){$(location).attr('href',"storage.php?type=a&styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value);} } else {if(data[0].flag){$("#message").html("<div class='successMessage'><strong>Inventory Quantity Updated. Thank you.</strong></div>");$(location).attr('href',"storage.php?type=a&styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value);}else{$("#message").html("<div class='successMessage'><strong> All inventorys are uptodate...</strong></div>");}}}else{$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");}}});return false;});});
</script>
<?php require('../../trailer.php');?>