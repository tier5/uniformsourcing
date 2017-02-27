<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$rowCount = 0;
$mainCount = 0;
$invId	= 0;
$styleId = $_GET['styleId'];
$colorId = $_GET['colorId'];
if(isset($_GET['row']))
	$rowCount = $_GET['row'];
if(isset($_GET['column']))
	$mainCount = $_GET['column'];
$isCellRequest = 0;
if(isset($_GET['invId']))
{
	$invId=$_GET['invId'];
	$sql = 'select inv."inventoryId",col."name",inv."columnSize",inv."sizeScaleId",inv.price,inv."styleNumber", inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId", inv.quantity, inv."mainSize", inv."rowSize" from "tbl_inventory" as inv left join "tbl_invColor" as col on col."colorId"=inv."colorId" where inv."inventoryId"='.$invId.' and inv."styleId"='.$styleId.' and inv."colorId"='.$colorId.' order by inv."inventoryId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed StorageData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;
	}
	pg_free_result($result);
	$sql='select "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, box, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "invId"='.$invId.' order by "storageId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed InvData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_storage[]=$row;
	}
	pg_free_result($result);
	$isCellRequest = 1;
	$sql='select "conveyorQty","conveyorSlotId","storageId" from "tbl_invStorage" where "invId"='.$invId.' and "conveyorQty" IS NOT NULL and "conveyorQty" > \'0\'  order by "storageId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed ConveyorData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_conveyor[]=$row;
	}
	pg_free_result($result);
	$sql='select "storageId","wareHouseQty",room,"row",rack,shelf,box from "tbl_invStorage" where "invId"='.$invId.' and "wareHouseQty" IS NOT NULL and "wareHouseQty" > \'0\'  order by "storageId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed wareData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_ware[]=$row;
	}
	pg_free_result($result);
	$sql='select "otherQty","location","storageId" from "tbl_invStorage" where "invId"='.$invId.' and "otherQty" IS NOT NULL and "otherQty" > \'0\'  order by "storageId"';
	if(!($result=pg_query($connection,$sql))){
		print("Failed otherData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_other[]=$row;
	}
	pg_free_result($result);
}
else
{
	$sql = 'select "inventoryId","styleNumber",col."name","sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty", "mainSize", "rowSize", "columnSize" from "tbl_inventory" as inv inner join "tbl_invColor" as col on col."colorId"=inv."colorId" where inv."styleId"='.$styleId.' and inv."colorId"='.$colorId.' and "isStorage"=0 order by "inventoryId"';

	if(!($result=pg_query($connection,$sql))){
		print("Failed StorageData: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;
	}
	pg_free_result($result);
	$sql = 'select "storageId", "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, box, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "styleId"='.$styleId.' and "colorId"='.$colorId.' order by "storageId"';
	
	if(!($result=pg_query($connection,$sql))){
		print("Failed Data_invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_storage[]=$row;
	}
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Global Uniform Sourcing Internal Intranet</title>
<link rel="stylesheet" type="text/css" href="<?php echo $mydirectory;?>/style.css" media="all"/>
<head>
<script type="text/JavaScript" src="<?php echo $mydirectory;?>/js/tabcontent.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>

<link href="<?php echo $mydirectory;?>/tabcontent.css" rel="stylesheet" type="text/css" />

<!--Styles needed for the LightBoxPopup -->
<style>
		.black_overlay{
			display: none;
			position: absolute;
			top: 0%;
			left: 0%;
			width: 100%;
			height: 100%;
			background-color:#000;
			z-index:1001;
			-moz-opacity: 0.8;
			opacity:.80;
			filter: alpha(opacity=80);
		}
		.white_content {
			display: none;
			position: absolute;
			top: 25%;
			left: 25%;
			width: 50%;
			height: 300px;
			padding: 16px;
			border: 16px solid grey;
			background-color: white;
			z-index:1002;
			overflow: scroll;
			text-align:center;
		}
</style>
<!--Styles needed for the ShowHide Feature of the Storage Forms -->
<script language="javascript" type="text/javascript">    
function showHideDiv(objectId)    {        
var divstyle = new String(); 
divstyle = document.getElementById(objectId).style.display;        
if(divstyle.toLowerCase()=="none" || divstyle == "")        
{document.getElementById(objectId).style.display = "block";}       
 else{document.getElementById(objectId).style.display = "none";}
}  

function showHideMB(objectId)    {        
var divstyle = new String(); 
divstyle = document.getElementById(objectId).style.display;        
if(divstyle.toLowerCase()=="block" || divstyle == "")        
{document.getElementById(objectId).style.display = "none";}       
else{document.getElementById(objectId).style.display = "block";}
}      
 </script>


</head><body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<table width="100%">
        <tr>
          <td align="left" valign="top"><center>
            <table width="100%">
                <tr>
                  <td align="center"><font size="5"><br>
                      <br>
                    </font>
			
					
	<div id="light" class="white_content">
<div id="forms"><!--All the forms like Conveyor form, Warehouse form, Other comes inside this DIV-->
<?php
if(($isCellRequest==0) && (count($data_inv) > 0))
{	
?>
<div style="overflow-x:scroll; overflow-y:hidden; width:700px; height:40px;" >
<!--the width generated in for the ul should be dynamic and it should be in the logic (no of item x 90)-->
	  <ul id="tabId" style="width:<?php echo (count($data_inv) * 150);?>px; " class="shadetabs" >
<?php
	for($i=0;$i < count($data_inv); $i++)
	{
		echo '<li id="tabliId'.($i+1).'"><a href="#" rel="tcontent'.($i+1).'">'.$data_inv[$i]['mainSize'].' - '.$data_inv[$i]['rowSize'].'</a></li>';
	}
?>
</ul></div>
<fieldset style="padding:10px; border: 1px solid gray;">
<center><table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table></center>
<?php
	$storageQty = 0;
	$location="";
	for($i=0;$i < count($data_inv); $i++)
	{
		$location="";
		for($k=0; $k<count($data_loc); $k++)
		{
			if($data_loc[$k]['locationId']==$data_inv[$i]['locationId'])
			{
				$location=$data_loc[$k]['name'];
			break;
			}
		}
		if($data_inv[$i]['quantity'] < $data_inv[$i]['newQty'])
		{
			$storageQty = $data_inv[$i]['newQty'] - $data_inv[$i]['quantity'];
?>
<!--div to be inserted here-->
<div id="tcontent<?php echo ($i+1);?>" class="tabcontent">
<table width="100%" border="0">
  <tr>
    <td align="center">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" id="mb<?php echo ($i+1);?>">
	<tr>
    <td align="center"><strong>Select Conveyor, Warehouse or Other </strong></td>
  </tr>
 			
  <tr>
    <td height="30" align="center"><a href="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Conveyor Form</a></td>
  </tr>
  <tr>
    <td height="30" align="center"><a href="javascript:showHideDiv('wf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Warehouse Form</a></td>
  </tr>
  <tr>
    <td height="30" align="center"><a href="javascript:showHideDiv('of<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Other Form</a> </td>   
  </tr>
</table>
<!--Conveyor Table starts here  -->
<form id="cForm<?php echo ($i+1);?>">
<div style="display:none;" id="cf<?php echo ($i+1);?>">
<div class="scrollVertical">

<table width="100%" border="0" cellpadding="0"  cellspacing="0" >
  <tr>
    <td width="10">&nbsp;</td>
    <td height="30"><strong>Conveyor Form </strong></td>
    <td height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
<?php 
  		for($j=0;$j < $storageQty; $j++)
		{
?>
  <tr>
    <td>&nbsp;</td>
    <td width="150">Slot ID#: </td>
    <td width="10">&nbsp;</td>
    <td height="30"><input name="slotId[]" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
<?php
		}
?>  
</table>
</div>
<div style="padding:10px;">
    <input name="invId" type="hidden" value="<?php echo $data_inv[$i]['inventoryId'];?>"/>
    <input type="button" id="cfSubmit<?php echo ($i+1);?>" name="cfSubmit<?php echo ($i+1);?>" value="Save" />
      <input type="button" name="cfCancel<?php echo ($i+1);?>" value="Cancel" onclick="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();" />
 </div>  
</div>
<!--Conveyor Table ends here  -->
</form>
<form id="wForm<?php echo ($i+1);?>">
<!--Warehouse Form Table starts here -->
<div style="display:none;" id="wf<?php echo ($i+1);?>">

<table width="100%" border="0" cellpadding="0"  cellspacing="0" >
  <tr>
    <td width="10">&nbsp;</td>
    <td height="30"><strong>Warehouse Form </strong></td>
    <td height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('wf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="150">Room#: </td>
    <td width="10">&nbsp;</td>
    <td height="30"><input name="room" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Row#: </td>
    <td>&nbsp;</td>
    <td height="30"><input name="row" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Rack#: </td>
    <td>&nbsp;</td>
    <td height="30"><input name="rack" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Shelf#: </td>
    <td>&nbsp;</td>
    <td height="30"><input name="shelf" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Box#: </td>
    <td>&nbsp;</td>
    <td height="30"><input name="box" type="text" class="textBox" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;<input name="invId" type="hidden" value="<?php echo $data_inv[$i]['inventoryId'];?>"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td height="30"><input type="button" id="wfSubmit<?php echo ($i+1);?>" name="wfSubmit<?php echo ($i+1);?>" value="Save" />
      <input type="button" name="wfCancel<?php echo ($i+1);?>" value="Cancel" onclick="javascript:showHideDiv('wf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();" /></td>
    <td>&nbsp;</td>
  </tr>
</table>

 </div>

</form>
<!--Warehouse Form Table ends here -->
<form id="oForm<?php echo ($i+1);?>">
<!--Other Form Table starts here -->
<div style="display:none;" id="of<?php echo ($i+1);?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td width="10">&nbsp;</td>
    <td height="30"><strong>Other Form </strong></td>
    <td height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('of<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;</td>
    <td width="150">Location:</td>
    <td width="10">&nbsp;</td>
    <td rowspan="3"><textarea name="location" id="locTxtAreaId<?php echo ($i+1);?>" class="textArea"></textarea></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;<input name="invId" type="hidden" value="<?php echo $data_inv[$i]['inventoryId'];?>"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="button" id="ofSubmit<?php echo ($i+1);?>" name="ofSubmit<?php echo ($i+1);?>" value="Save" />
      <input type="button" name="ofCancel<?php echo ($i+1);?>" value="Cancel" onclick="javascript:showHideDiv('of<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');ClearMessage();"/></td>
    <td>&nbsp;</td>
  </tr>
</table></div>
</form>
</td>
<!-- second td starts here-->
    <td width="300px" valign="top">
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php 
	if($data_inv[$i]['styleNumber'] !="")
	{
?>
  <tr>
    <td height="30">Style Number:<strong><?php echo $data_inv[$i]['styleNumber'];?></strong></td>
  </tr>
<?php 
	}
	if($data_inv[$i]['name']!="")
	{
?>
  <tr>
    <td height="30">Color Name:<strong><?php echo $data_inv[$i]['name'];?></strong></td>
  </tr>
<?php 
  	}
	if($data_inv[$i]['mainSize'] !="")
	{
?>
  <tr>
    <td height="30">Main Size:<strong><?php echo $data_inv[$i]['mainSize'];?></strong>
  </tr>
<?php 
	}
	if($data_inv[$i]['columnSize']!="")
	{
?>
  <tr>
   <td height="30">Column Size:<strong><?php echo $data_inv[$i]['columnSize'];?></strong> </td>
  </tr>
<?php 
	}
	if($data_inv[$i]['rowSize']!="")
	{
?>
  <tr>
   <td height="30">Row Size:<strong><?php echo $data_inv[$i]['rowSize'];?></strong></td>
  </tr>
<?php
  }
  if($location !="")
  {
?>
  <tr>
     <td height="30">Location:<strong><?php echo $location;?> </strong></td>
  </tr>
<?php 
  }
?>
</table>

</td>
  </tr>
</table>

</div>




<?php
	}//End qty add
	else if($data_inv[$i]['quantity'] > $data_inv[$i]['newQty']) // Quantity sutract
	{
			$storageQty = $data_inv[$i]['quantity'] - $data_inv[$i]['newQty'];
?>
	<div id="tcontent<?php echo ($i+1);?>" class="tabcontent">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" id="mb<?php echo ($i+1);?>">
    <tr>
    <td width="0">&nbsp;</td>
    <td ><strong>Please select the item to be deleted from the storage options below: </strong></td>
    <td width="0">&nbsp;</td>
    <td height="30">Style Number:<strong><?php echo $data_inv[$i]['styleNumber'];?></strong></td>
    <td width="0">&nbsp;</td>
  </tr>
     <tr>
    <td width="0">&nbsp;</td>
    <td ><a href="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Conveyor Form</a></td>
    <td width="0">&nbsp;</td>
    <td height="30">Color Name:<strong><?php echo $data_inv[$i]['name'];?></strong></td>
    <td width="0">&nbsp;</td>
  </tr>
     <tr>
    <td width="0">&nbsp;</td>
    <td ><a href="javascript:showHideDiv('wf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Warehouse Form</a></td>
    <td width="0">&nbsp;</td>
    <td height="30">Main Size:<strong><?php echo $data_inv[$i]['mainSize'];?></strong></td>
    <td width="0">&nbsp;</td>
  </tr>
     <tr>
    <td width="0">&nbsp;</td>
    <td ><a href="javascript:showHideDiv('of<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Other Form</a> </td>
    <td width="0">&nbsp;</td>
    <td height="30">Row Size:<strong><?php echo $data_inv[$i]['rowSize'];?></strong></td>
    <td width="0">&nbsp;</td>
  </tr>
     <tr>
    <td width="0">&nbsp;</td>
    <td ></td>
    <td width="0">&nbsp;</td>
    <td height="30">Column Size:<strong><?php echo $data_inv[$i]['columnSize'];?></strong></td>
    <td width="0">&nbsp;</td>
  </tr>
  <?php if($location!=""){?>
     <tr>
    <td width="0">&nbsp;</td>
    <td ></td>
    <td width="0">&nbsp;</td>
    <td height="30">Location:<strong><?php echo $location;?> </strong></td>
    <td width="0">&nbsp;</td>
  </tr>
  <?php }?>
</table>

<!--Conveyor Table starts here  -->

<div id="cf<?php echo ($i+1);?>" style="display:none;">
<table width="100%" border="0" cellpadding="0"  cellspacing="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td height="30"><strong>Conveyor Form </strong></td>
    <td height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');"></a> </td>
    <td align="right">&nbsp;</td>
    <td align="right"><a href="javascript:showHideDiv('cf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Go Back</a></td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" colspan="5" align="left"><strong>Items to be deleted <span id="cDeleteInfo<?php echo ($i+1);?>"><?php echo $storageQty;?></span></strong> </td>
    <td>&nbsp;</td>
  </tr>
<?php
			for($j = 0; $j < count($data_storage); $j++)
			{
				if(($data_storage[$j]['conveyorQty'] > 0 || $data_storage[$j]['conveyorQty'] != "") && ($data_inv[$i]['inventoryId'] == $data_storage[$j]['invId']))
				{
					?>
  <tr>
    <td>&nbsp;</td>
    <td width="100">Slot ID#: </td>
    <td width="10">&nbsp;</td>
    <td width="75" height="30" align="left"><?php echo $data_storage[$j]['conveyorSlotId'];?></td>
    <td>&nbsp;<input id="cqty[<?php echo $i; ?>][<?php echo $j; ?>]" type="hidden" size="10" value ="1" /></td>
    <td align="left"><input type="button" name="cnvDel<?php echo ($j+1);?>" value="Delete" onclick="javascript:StorageUpdate('<?php echo $data_storage[$j]['storageId'];?>','<?php echo $data_storage[$j]['invId'];?>','cqty[<?php echo $i; ?>][<?php echo $j; ?>]','<?php echo $data_storage[$j]['conveyorQty']; ?>','c_delete', <?php echo ($i+1);?>);"/></td>
    <td>&nbsp;</td>
  </tr>
<?php
				}
			}
?>  
</table>

</div>

<!--Conveyor Table ends here  -->
<!--Warehouse Form Table starts here -->

<div id="wf<?php echo ($i+1);?>" style="display:none;">

<table width="100%" border="0" cellpadding="0"   cellspacing="0" >
  <tr>
    <td width="10">&nbsp;</td>
    <td width="150" height="30" align="left"><strong>Warehouse Form </strong></td>
    <td width="10" height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('wf<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" colspan="3" align="left"><strong>Items to be deleted <span id="wDeleteInfo<?php echo ($i+1);?>"><?php echo $storageQty;?></span> </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" colspan="3" align="left"><strong> Please enter the value to be deducted in the 2nd text field shown straight to the quantity label. </strong></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
			for($j = 0; $j < count($data_storage); $j++)
			{
				if(($data_storage[$j]['wareHouseQty'] > 0 || $data_storage[$j]['wareHouseQty'] != "") && ($data_inv[$i]['inventoryId'] == $data_storage[$j]['invId']))
				{
?>
<table width="100%" border="0" cellpadding="0"  cellspacing="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td width="150" align="left">BOx#: </td>
    <td width="10">&nbsp;</td>
    <td height="30" colspan="3" align="left"><?php echo $data_storage[$j]['box']; ?></td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left">Quantity#: </td>
    <td>&nbsp;</td>
    <td width="50" height="30" align="left"><?php echo $data_storage[$j]['wareHouseQty']; ?></td>
    <td width="75" align="left"><input id="wqty[<?php echo $i; ?>][<?php echo $j; ?>]" type="text" class="textBoxNw" size="10" value="" /></td>
    <td align="left"><input type="button" name="Submit752223" value="Delete" onclick="javascript:StorageUpdate('<?php echo $data_storage[$j]['storageId'];?>','<?php echo $data_storage[$j]['invId'];?>','wqty[<?php echo $i; ?>][<?php echo $j; ?>]',0,'w_delete', <?php echo ($i+1);?>);"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td height="30" colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
				}
			}
?>
</div>
<!--Warehouse Form Table ends here -->
<!--Other Form Table starts here -->
<div id="of<?php echo ($i+1);?>" style="display:none;">
<table width="100%" border="0" cellpadding="0"   cellspacing="0" >
  <tr>
    <td width="10">&nbsp;</td>
    <td width="150" height="30" align="left"><strong>Other Form </strong></td>
    <td width="10" height="30">&nbsp;</td>
    <td height="30" align="right"><a href="javascript:showHideDiv('of<?php echo ($i+1);?>'); showHideMB('mb<?php echo ($i+1);?>');">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" colspan="3" align="left"><strong>Items to be deleted <span id="oDeleteInfo<?php echo ($i+1);?>"><?php echo $storageQty;?></span></strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="30" colspan="3" align="left"><strong>Please enter the value to be deducted in the 2nd text field shown straight to the quantity label. </strong></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
			for($j = 0; $j < count($data_storage); $j++)
			{
				if(($data_storage[$j]['otherQty'] > 0 || $data_storage[$j]['otherQty'] != "") && ($data_inv[$i]['inventoryId'] == $data_storage[$j]['invId']))
				{
?>
<table width="100%" border="0" cellpadding="0"  cellspacing="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td width="150" align="left">Location: </td>
    <td width="10">&nbsp;</td>
    <td height="30" colspan="3" align="left"><?php echo $data_storage[$j]['location'];?></td>
    <td width="10">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left">Quantity#: </td>
    <td>&nbsp;</td>
    <td width="50" height="30" align="left"><?php echo $data_storage[$j]['otherQty'];?></td>
    <td width="75" align="left"><input id="oqty[<?php echo $i; ?>][<?php echo $j; ?>]" type="text" class="textBoxNw" size="10" value="" /></td>
    <td align="left"><input type="button" name="Submit75222" value="Delete" onclick="javascript:StorageUpdate('<?php echo $data_storage[$j]['storageId'];?>','<?php echo $data_storage[$j]['invId'];?>','oqty[<?php echo $i; ?>][<?php echo $j; ?>]',0,'o_delete', <?php echo ($i+1);?>);"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td height="30" colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
				}
			}
?>
</div>
</div>
<?php
		}// end of else case - subtract qty
	}//end for loop of inventory
?>
<br />
</fieldset>
<?php	
}
else if($isCellRequest == 1)
{
?>
<fieldset style="padding:10px; border: 1px solid gray;">
<center><table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table></center>
<table width="100%">
<tr>
<td width="40%">
<table width="100%" border="0" cellpadding="0"   cellspacing="0" >
  <tr>
    <td height="30" align="right"><a href="javascript:Navigate();">Go Back</a> </td>
    <td width="10">&nbsp;</td>
  </tr>
  </table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="mainforms">
  <tr>
    <td align="left" valign="top">
    <!--Conveyor table -->
	 <table width="100%" border="0" cellpadding="0"  cellspacing="0">
                      <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Storage Info </strong></td>
                        <td width="150">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
					  </table>
<?php
	if(count($data_conveyor) > 0 )
	{
?>
                        <div id="conveyorDIV" style="overflow-y:scroll; height:120px;">
                        
                        <table width="100%" border="0" cellpadding="0"  cellspacing="0" id="conveyor">
<?php 
		for($i=0;$i < count($data_conveyor); $i++)
       	{
 ?>
                           <tr>
                            <td>&nbsp;</td>
                            <td height="30">Conveyor Slot ID#: </td>
                            <td><?php echo $data_conveyor[$i]['conveyorSlotId']; ?></td>
                            <td>
                            <input type="hidden" id="c_qty<?php echo ($i+1);?>" value="<?php echo $data_conveyor[$i]['conveyorQty'];?>" />
                            <input type="button" id="convDelete<?php echo ($i+1);?>" name="delete" value="Delete" onclick="javascript:StorageUpdate('<?php echo $data_conveyor[$i]['storageId'];?>','<?php echo $invId;?>','c_qty<?php echo ($i+1);?>','<?php echo $data_conveyor[$i]['conveyorQty']; ?>','c_delete',0);" /></td>
                            <td>&nbsp;</td>
                          </tr>
<?php 
		}
?>
                    </table>
					</div>
<?php 
 	}
	if(count($data_ware) > 0)
	{
?>
					<!--warehosue table -->
                    <table width="100%" border="0" cellpadding="0"  cellspacing="0" id="warehouse">
<?php
		for($i=0;$i < count($data_ware); $i++)
		{
			if($data_ware[$i]['room']!="")
			{
?>
                       <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Room#:</strong></td>
                        <td width="150"><?php echo $data_ware[$i]['room'];?></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
 <?php 
			}
			if($data_ware[$i]['row']!="")
			{
?>
                       <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Row#:</strong></td>
                        <td width="150"><input type="text" value="<?php echo $data_ware[$i]['row'];?>" class="textBox" id="row<?php echo ($i+1);?>" name="row"></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
<?php 
            }
            if($data_ware[$i]['rack']!="")
            {
?>
                       <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Rack#:</strong></td>
                        <td width="150"><input type="text" value="<?php echo $data_ware[$i]['rack'];?>" class="textBox" id="rack<?php echo ($i+1);?>" name="rack"></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
<?php 
			}
			if($data_ware[$i]['shelf']!="")
			{
?>
                       <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Shelf#:</strong></td>
                        <td width="150"><input type="text" value="<?php echo $data_ware[$i]['shelf'];?>" class="textBox" id="shelf<?php echo ($i+1);?>" name="shelf"></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
<?php
			}
?>
                       <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Box#:</strong></td>
                        <td width="150"><input type="text" value="<?php echo $data_ware[$i]['box'];?>" class="textBox" id="box<?php echo ($i+1);?>" name="box"></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="30">Quantity:</td>
                        <td><input type="text" class="textBoxNw" size="10" name="w_qty" id="w_qty<?php echo ($i+1);?>" value="<?php echo $data_ware[$i]['wareHouseQty'];?>" /></td>
                        <td>
                        <input type="button" name="updateQty" value="Update"
                         onclick="javascript:StorageUpdate('<?php echo $data_ware[$i]['storageId'];?>','<?php echo $invId;?>','w_qty<?php echo ($i+1);?>','<?php echo $data_ware[$i]['wareHouseQty'];?>','w_update',0);"/>
                            <input type="button" name="deleteQty" value="Delete" 
                            onclick="javascript:StorageUpdate('<?php echo $data_ware[$i]['storageId'];?>','<?php echo $invId;?>','w_qty<?php echo ($i+1);?>','<?php echo $data_ware[$i]['wareHouseQty'];?>','w_delete',0);" /></td>
                        <td>&nbsp;</td>
                      </tr>
<?php 
 		}// end ware for loop
?>
						</table>
<?php
	} // end ware house if
?>
<?php
	if(count($data_other) > 0 )
	{
?>
					<!--location table -->
                    <table width="100%" border="0" cellpadding="0"  cellspacing="0" id="location">
<?php 
		for($i=0;$i < count($data_other); $i++)
		{
?>
					 <tr>
                        <td width="10">&nbsp;</td>
                        <td width="125" height="30"><strong>Location: </strong></td>
                        <td width="150"><?php echo $data_other[$i]['location'];?></td>
                        <td>&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="30">Quantity:</td>
                        <td><input type="text" class="textBoxNw" size="10" id="o_qty<?php echo ($i+1);?>" name="o_qty" value="<?php echo $data_other[$i]['otherQty'];?>" /></td>   
                        <td>
                        <input type="button" name="updateQty" value="Update" 
                        onclick="javascript:StorageUpdate('<?php echo $data_other[$i]['storageId'];?>','<?php echo $invId;?>',
                        'o_qty<?php echo ($i+1);?>','<?php echo $data_other[$i]['otherQty'];?>','o_update',0);" />
                            <input type="button" name="delete" value="Delete" onclick="javascript:StorageUpdate('<?php echo $data_other[$i]['storageId'];?>','<?php echo $invId;?>','o_qty<?php echo ($i+1);?>','<?php echo $data_other[$i]['otherQty'];?>','o_delete',0);" /></td>
                        <td>&nbsp;</td>
                      </tr>
<?php 
		}
?>
                    </table>
<?php 
	}
?>
	</td>
    </tr>
</table>
</td>
<td valign="top">
<?php 
	if(count($data_inv) > 0)
	{
?>
<table cellpadding="0" cellspacing="0" width="100%">
<?php 
		$location="";
		for($i=0;$i < count($data_inv); $i++)
		{
			for($j=0; $j<count($data_loc); $j++)
			{
				if($data_loc[$j]['locationId']==$data_inv[$i]['locationId'])
				{
					$location=$data_loc[$j]['name'];
				}
			}
?>
<tr>
<td height="45">&nbsp;</td>
</tr>
<tr>
<td height="30">Style Number:<strong><?php echo $data_inv[$i]['styleNumber'];?></strong></td>
</tr>
<?php
			if($data_inv[$i]['name']!="")
			{
?>
<tr>
<td height="30">Color Name:<strong><?php echo $data_inv[$i]['name'];?></strong></td>
</tr>
<?php 
			}
			if( $data_inv[$i]['mainSize']!="")
			{
?>
<tr>
<td height="30">Main Size:<strong><?php echo $data_inv[$i]['mainSize'];?></strong></td>
</tr>
<?php 
			}
			if($data_inv[$i]['rowSize']!="")
			{
?>
<tr>
<td height="30">Row Size:<strong><?php echo $data_inv[$i]['rowSize'];?></strong></td>
</tr>
<?php
			}
			if( $data_inv[$i]['opt2ScaleId']!="")
			{
?>
<tr>
<td height="30">Column Size:<strong><?php echo $data_inv[$i]['columnSize'];?></strong></td>
</tr>
<?php 
			}
			if($location!="")
			{
?>
<tr>
<td height="30">Location:<strong><?php echo $location;?> </strong></td>
</tr>
<?php 
			}
	}//for loop ends
?>
</table>
<?php 		
	}
?>
</td>
</tr>
</table>
</fieldset>
<?php 
}
?>
<input type="hidden" id="styleId" name="styleId" value="<?php echo $styleId;?>"/>
<input type="hidden" id="colorId" name="colorId" value="<?php echo $colorId;?>"/> 
</div>
		<!--close button can be found here -->
		 <a href = "javascript:void(0)" onclick = "javascript:Navigate();">Close</a></div>
		<div id="fade" class="black_overlay"></div>				
                    </td>
                </tr>
              </table>
              <p>
          </center></td>
        </tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
<?php
if(count($data_inv) <= 0)
{
	echo 'Navigate();';
}
else if($isCellRequest==0)
{?>
var tab =new ddtabcontent("tabId"); //enter ID of Tab Container
tab.setpersist(true); //toogle persistence of the tabs' state
tab.setselectedClassTarget("link"); //"link" or "linkparent"
tab.init();
<?php } 

?>
document.getElementById('light').style.display='block';
document.getElementById('fade').style.display='block';
<?php 
if(isset($_GET['showForm']))
{
	$form = $_GET['showForm'];
	$num= substr($form,2);
	if($num <= count($data_inv)){
		if($form!="")
		{
			echo "showHideDiv('$form'); showHideMB('mb$num');";		
		}
	}
}
?>

});
/*$("#close").click({
	//document.getElementById('light').style.display='none';
	//document.getElementById('fade').style.display='none';
	$(location).attr('href',"reportViewEdit.php?styleId=< ?php echo $styleId;?>&colorId=< ?php echo $colorId;?>");   
});*/
function ClearMessage()
{
	$("#message").html("");
}
function Navigate()
{
	$(location).attr('href',"reportViewEdit.php?styleId=<?php echo $styleId;?>&colorId=<?php echo $colorId;?>");
}
</script>       
<script type="text/javascript">
function StorageUpdate(storageID,invID,itemValue,qty,type,num)
{
    t=itemValue.split("w_qty");
	$("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");
	val = "";
	if(itemValue != "")
	{
		qty=document.getElementById(itemValue).value;
	}
	dataString = '&storageID='+storageID+'&invID='+invID+'&qty='+qty+'&type='+type+'&value='+val;
        if($("#row"+t[1]).length>0)
        dataString +='&row='+document.getElementById('row'+t[1]).value;
     if($("#rack"+t[1]).length>0)
        dataString +='&rack='+document.getElementById('rack'+t[1]).value;
     if($("#shelf"+t[1]).length>0)
        dataString +='&shelf='+document.getElementById('shelf'+t[1]).value;
     if($("#box"+t[1]).length>0)
         dataString +='&box='+document.getElementById('box'+t[1]).value;
	 $.ajax
  	({
	   type: "POST",
	   url: "updateStorage.php",
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
					   $("#message").html("<div class='successMessage'><strong>Storage Updated. Thank you.</strong></div>");
					   <?php if($isCellRequest==1)
					   {
					   echo 'location.reload(true);	';
					   }
					   else
					   {?>
					   if(itemValue != "")
					   {
	 				   var type= itemValue.substr(0,1);					  
					  $(location).attr("href","storage.php?styleId=<?php echo $styleId;?>&colorId=<?php echo $colorId;?>&showForm="+type+"f"+num);
					   }
					  <?php }?>
					   
			   }
		  }
		  else
		  {
			  $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");																																																														          }
	  }
  });
  return false;
}
<?php
for($i=0;$i < count($data_inv); $i++)
{
?>
$(function(){$("#cfSubmit<?php echo ($i+1);?>").click(function(){
PostDB('cForm<?php echo ($i+1);?>');
});
});
$(function(){$("#wfSubmit<?php echo ($i+1);?>").click(function(){
PostDB('wForm<?php echo ($i+1);?>');
});
});
$(function(){$("#ofSubmit<?php echo ($i+1);?>").click(function(){
PostDB('oForm<?php echo ($i+1);?>');
});
});
<?php
}
?>
function PostDB(formId)
{
  $("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>"); 
  var styleId =  document.getElementById('styleId').value;
  var colorId = document.getElementById('colorId').value;
  dataString = $("#"+formId).serialize();  
  dataString += '&formId='+formId+'&styleId='+styleId+'&colorId='+colorId;   
  $.ajax
  ({
	   type: "POST",
	   url: "storageSubmit.php",
	   data: dataString,
	   dataType: "json",
	   success:function(data)
	   {
		   if(data!=null)
		   {	
			   if(data.name || data.error)
			   {
				   $("#message").html("<div class='errorMessage'><strong>" + data.name + data.error +"</strong></div>"); 
			   } 
			   else
			   { 
					   $("#message").html("<div class='successMessage'><strong>Storage Updated. Thank you.</strong></div>");
					   location.reload(true);					  
			   }
		  }
		  else
		  {
			  $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");																																																														            	}
	  }
  });
  return false;
}
</script>
</body></html>