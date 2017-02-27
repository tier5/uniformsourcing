<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');?>
<?php 
if(isset($_GET['close']))
{
	$sql='Update "tbl_invStyle" SET "isActive"=0 where "styleId"='.$_GET['close'];
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
}
require('../../header.php');
$query1='SELECT "scaleNameId","styleId","styleNumber","garmentId" from "tbl_invStyle" where "isActive"=1 order by "styleNumber"';
if(!($result_cnt=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt))
{$data_style[]=$row_cnt;}
pg_free_result($result_cnt);
$query2='Select "garmentID","garmentName" from tbl_garment where status=1';
if(!($result2=pg_query($connection,$query2))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
$data_garment[]=$row2;}
pg_free_result($result2);
$query3='Select * from "tbl_invScaleName" where "isActive"=1';
if(!($result_cnt3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt3 = pg_fetch_array($result_cnt3)){
	$data_scale[]=$row_cnt3;}

pg_free_result($result_cnt3);
/*$query4='Select * from "tbl_invScaleSize"';
if(!($result_cnt4=pg_query($connection,$query4))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt4 = pg_fetch_array($result_cnt4)){
	$data_size[]=$row_cnt4;}

pg_free_result($result_cnt4);*/
$query5='Select "fabricID","fabName" from "tbl_fabrics" where status=1 ';
if(!($result_cnt5=pg_query($connection,$query5))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt5 = pg_fetch_array($result_cnt5)){
	$data_fab[]=$row_cnt5;
}
pg_free_result($result_cnt5);	
$query6=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
 "FROM \"clientDB\" ".
 "WHERE \"active\" = 'yes' ".
 "ORDER BY \"client\" ASC");
if(!($result=pg_query($connection,$query6))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result)){
	$data_client[]=$row1;
}
pg_free_result($result);
$query7='Select DISTINCT "locationId","name" from "tbl_invLocation" ';
if(!($result_cnt7=pg_query($connection,$query7))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt7 = pg_fetch_array($result_cnt7)){
	$data_location[]=$row_cnt7;
}
pg_free_result($result_cnt7);
$query8='Select * from "tbl_invColor" ';
	if(!($result_cnt8=pg_query($connection,$query8))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
while($row_cnt8 = pg_fetch_array($result_cnt8)){
		  $data_color[]=$row_cnt8;
}
pg_free_result($result_cnt8);
include('../../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['styleNumber']) && $_REQUEST['styleNumber']!="") {
	$search_sql.=' and st."styleId" ='.$_REQUEST['styleNumber'].' ';
	$search_uri.="?styleNumber=".$_REQUEST['styleNumber'];
}
if(isset($_REQUEST['scale']) && $_REQUEST['scale']!="") {
	$search_sql.=' and st."scaleNameId" ='.$_REQUEST['scale'].' ';
	if($search_uri)  {
		 $search_uri.="&scale=".$_REQUEST['scale'];
	} else {
		$search_uri.="?scale=".$_REQUEST['scale'];
	}
}
if(isset($_REQUEST['garment']) && $_REQUEST['garment']!="") {
	$search_sql.=' and st."garmentId" ='.$_REQUEST['garment'].' ';
	if($search_uri)  {
		 $search_uri.="&garment=".$_REQUEST['garment'];
	} else {
		$search_uri.="?garment=".$_REQUEST['garment'];
	}
}
if(isset($_REQUEST['price']) && $_REQUEST['price']!="") {
	$search_sql .=' and st."price" ILIKE \'%' .$_REQUEST['price'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&price=".$_REQUEST['price'];
	} else {
		$search_uri.="?price=".$_REQUEST['price'];
	}
}
if(isset($_REQUEST['fabric']) && $_REQUEST['fabric']!="") {
	$search_sql.=' and st."fabricId" ='.$_REQUEST['fabric'].' ';
	if($search_uri)  {
		 $search_uri.="&fabric=".$_REQUEST['fabric'];
	} else {
		$search_uri.="?fabric=".$_REQUEST['fabric'];
	}
}
if(isset($_REQUEST['sex']) && $_REQUEST['sex']!="") {
	$search_sql.=' and st."sex" =\''.$_REQUEST['sex'].'\' ';
	if($search_uri)  {
		 $search_uri.="&sex=".$_REQUEST['sex'];
	} else {
		$search_uri.="?sex=".$_REQUEST['sex'];
	}
}
if(isset($_REQUEST['location']) && $_REQUEST['location']!="") {
	$search_sql.=' and st."locationIds" ILIKE \'%' .$_REQUEST['location'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&location=".$_REQUEST['location'];
	} else {
		$search_uri.="?location=".$_REQUEST['location'];
	}
}
if(isset($_REQUEST['client']) && $_REQUEST['client']!="") {
	$search_sql.=' and st."clientId" ='.$_REQUEST['client'];
	if($search_uri)  {
		 $search_uri.="&client=".$_REQUEST['client'];
	} else {
		$search_uri.="?client=".$_REQUEST['client'];
	}
}
if(isset($_REQUEST['notes']) && $_REQUEST['notes']!="") {
	$search_sql .=' and st."notes" ILIKE \'%' .$_REQUEST['notes'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&notes=".$_REQUEST['notes'];
	} else {
		$search_uri.="?notes=".$_REQUEST['notes'];
	}
}
$sql='select DISTINCT st."styleNumber", sn."scaleId",sn."scaleName",st.*,g."garmentID",g."garmentName" from "tbl_invStyle" st left join tbl_garment g on g."garmentID"=st."garmentId" left join "tbl_invScaleName" sn on st."scaleNameId"= sn."scaleId" left join "tbl_invColor" col on col."styleId"=st."styleId" where st."isActive"=1'.$search_sql.' order by st."styleId" desc';
if(!($result=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($result);
if($items > 0) {
	$p = new pagination;
	$p->items($items);
	$p->limit(10); // Limit entries per page
	//$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
	//die($_SERVER['REQUEST_URI']);
	$uri= substr($_SERVER['REQUEST_URI'], 0,strpos($_SERVER['REQUEST_URI'], '&paging'));
	if(!$uri) {
		$uri=$_SERVER['REQUEST_URI'].$search_uri;
	}
	$p->target($uri);
	$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
	$p->calculate(); // Calculates what to show
	$p->parameterName('paging');
	$p->adjacents(1); //No. of page away from the current page
	
	if(!isset($_GET['paging'])) {
	$p->page = 1;
	} else {
	$p->page = $_GET['paging'];
	}
	//Query for limit paging
	$limit = "LIMIT " . $p->limit." OFFSET ".($p->page - 1) * $p->limit;
}
$sql = $sql. " ". $limit;
if(!($result=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$datalist[]=$row;
}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
?>
<script type="text/javascript">
$(document).ready(function()
{
	$("#cancel").click(function(){$(location).attr('href',"reports.php");
					 });
	$("#styleNumber").change(function()
	{
		document.getElementById('fabric').disabled=true;
		document.getElementById('scale').disabled=true;
		document.getElementById('client').disabled=true;
		document.getElementById('garment').disabled=true;
		document.getElementById('sex').disabled=true;
		PopulateColor();
	});
	$("#location").change(function()
	{
		if($("#styleNumber").val()=="")
		PopulateListFunction();
	});
	$("#scale").change(function()
	{
		PopulateListFunction();
	});
	$("#sex").change(function()
	{
		PopulateListFunction();
	});
	$("#client").change(function()
	{
		PopulateListFunction();
	});
	$("#fabric").change(function()
	{
		PopulateListFunction();
	});
	$("#garment").change(function()
	{
		PopulateListFunction();
	});
	function PopulateListFunction()
	{
	  var styleNo = 0;
	  var location = 0;
	  var sex ="";
	  var garment = 0;
	  var fabric = 0;
	  var client = 0;
	  var scale = 0;
	 /* location=document.getElementById('location').value;sex = document.getElementById('sex').value;garment = document.getElementById('garment').value;fabric = document.getElementById('fabric').value;client= document.getElementById('client').value;scale = document.getElementById('scale').value;*/
	      if($("#styleNumber").val()!="")
		  styleNo = $("#styleNumber").val();
		  if($("#location").val()!="")
		  location = $("#location").val();
		  if($("#sex").val()!="")
		  sex = $("#sex").val();
		  if($("#garment").val()!="")
		  garment = $("#garment").val();
		  if($("#fabric").val()!="")
		  fabric = $("#fabric").val();
		  if($("#client").val()!="")
		  client = $("#client").val();
		  if($("#scale").val()!="")
		  scale = $("#scale").val();
		  var dataString = 'styleNo='+styleNo+'&scale='+scale+'&location='+location+'&sex='+ sex+'&garment='+ garment+'&fabric='+ fabric+'&client='+client;
		$.ajax
		({
		type: "POST",
		url: "searchList.php",
		data: dataString,
		cache: false,
		success: function(html)
		{
			$("#styleNumber").html(html);
		}
		});
	}
	function PopulateColor()
	{
		var styleNo = document.getElementById('styleNumber').value;
		var dataString = 'styleNo='+styleNo;
		$.ajax
		({
		type: "POST",
		url: "colorList.php",
		data: dataString,
		cache: false,
		success: function(html)
		{	
			$("#color").html(html);
		}
		});
	}
});
</script>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='inventory.php';" class="button_text" type="submit" name="back" value="Back" /></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
<form action="reports.php" method="post" name="validationForm">
<table width="100%">
<tr>
  <td align="left" valign="top"><center>
    <table width="100%">
        <tr>
          <td align="center"><font size="5">Inventory Reports </font><font size="5"> <br>
              <br>
            </font>
            <table width="95%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td width="150" height="35"><strong>Search Reports </strong></td>
                <td width="200">&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="grid001">Style Number  : </td>
                <td class="grid001"><select name="styleNumber" id="styleNumber">
                  <option value="">---- Select Number ----</option>
                       <?php 
                  for($i=0; $i < count($data_style); $i++){
                      if($data_style[$i]['styleNumber']!="")
                    echo '<option value="'.$data_style[$i]['styleId'].'">'.$data_style[$i]['styleNumber'].'</option>';}
                  ?>                   </select></td>
                <td class="grid001">&nbsp;</td>
                <td class="grid001">Sex: </td>
                <td class="grid001"><select name="sex" id="sex">
                  <option value="">--- Select Gender------</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="unisex">Unisex</option>
                </select>                          &nbsp;</td>
              </tr>
              <tr>
                <td class="grid001">Size Scale:</td>
                <td class="grid001"><select name="scale" id="scale">
                  <option value="">--- Select Size Scale ----</option>
                  <?php 
                  for($i=0; $i < count($data_scale); $i++){
                  if($data_scale[$i]['scaleName']!="")
                    echo '<option value="'.$data_scale[$i]['scaleId'].'">'.$data_scale[$i]['scaleName'].'</option>';}
                  ?>
                </select></td>
                <td class="grid001">&nbsp;</td>
                <td class="grid001">Price:</td>
                <td class="grid001"><input type="text" name="price" id="price" /></td>
              </tr>
              <tr>
                <td class="grid001">Garment:</td>
                <td class="grid001"><select name="garment" id="garment">
                  <option value="">---- Select Garment ----</option>
                  <?php 
                  for($i=0; $i < count($data_garment); $i++){
                  if($data_garment[$i]['garmentName']!="")
                    echo '<option value="'.$data_garment[$i]['garmentID'].'">'.$data_garment[$i]['garmentName'].'</option>';}
                  ?>  </select>                          &nbsp;</td>
                <td class="grid001">&nbsp;</td>
                <td class="grid001">Location:</td>
                <td class="grid001"><select name="location" id="location">
                  <option value="">--- Select Locations-----</option>
                   <?php 
                  for($i=0; $i < count($data_location); $i++){
                    echo '<option value="'.$data_location[$i]['locationId'].'">'.$data_location[$i]['name'].'</option>';}
                  ?>
                </select></td>
              </tr>
              <tr>
                <td class="grid001">Color:</td>
                <td class="grid001"><select name="color" id="color">
                  <option value="">---- Select Color ----</option>
                    </select></td>
                <td class="grid001">&nbsp;</td>
                <td class="grid001">Client:</td>
                <td class="grid001"><select name="client" id="client">
                  <option value="">--- Select Client-----</option>
                   <?php for($i=0; $i < count($data_client); $i++){
                    echo '<option value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';
                    }
                    ?>
                </select>
                </td>
                </tr>
              <tr>
                <td class="grid001">Fabric:</td>
                <td class="grid001"><select name="fabric" id="fabric">
                  <option value="">---- Select Fabric ----</option>
                   <?php 
                  for($i=0; $i < count($data_fab); $i++){
                    echo '<option value="'.$data_fab[$i]['fabricID'].'">'.$data_fab[$i]['fabName'].'</option>';}
                  ?>
                                        </select></td>
                <td class="grid001">&nbsp;</td>
                <td class="grid001">Notes:</td>
                <td class="grid001"><input type="text" name="notes" id="notes" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                <input name="search" type="submit"  onMouseOver="this.style.cursor = 'pointer';" value="Search">
                <input name="cancel" id="cancel" type="button"  onMouseOver="this.style.cursor = 'pointer';" value="Cancel"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            <table width="95%" border="0" cellspacing="1" cellpadding="1" class="no-arrow rowstyle-alt">
            <thead>
            <tr class="sortable"> 
            <th class="sortable">Style Number </th>
            <th class="sortable">Garment</th>
            <th class="sortable">Size Scale</th>
            <th class="sortable">Notes </th>
            <th class="gridHeader">Report View/Edit</th>
            <th class="gridHeader">Style View/Edit </th>
            <th class="gridHeader">Close</th>
            <th class="gridHeader">Remove</th>
      </tr>
      </thead><tbody>
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
?><tr>
<?php 
		echo '<td class="grid001">'.$datalist[$i]['styleNumber'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['garmentName'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['scaleName'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['notes'].'</td>';
		?>
		<td class="grid001"><a href="reportViewEdit.php?styleId=<?php echo $datalist[$i]['styleId'];?>"><img src="<?php echo $mydirectory;?>/images/reportviewEdit.png" border="0"></a></td><?php 
		echo '<td class="grid001"><a href="styleAdd.php?ID='.$datalist[$i]['styleId'].'&type=e"><img src="'.$mydirectory.'/images/styleedit.png" border="0"></a></td>';
?>		<td class="grid001">
		<a <?php if($datalist[$i]['styleId']>0){?>onclick="javascript: if(confirm('Are you sure you want to close the style')) { return true; } else { return false; }" href="reports.php?close=<?php echo $datalist[$i]['styleId'];?>"<?php }else {?>href="#"<?php }?> ><img src="<?php echo $mydirectory;?>/images/close.png" border="0"></a></td> 

		<td class="grid001">
		<a <?php if($datalist[$i]['styleId']>0){?>onclick="javascript: if(confirm('Are you sure you want to delete the style')) { return true; } else { return false; }" href="styleDelete.php?submitType=del&ID=<?php echo $datalist[$i]['styleId'];?>"<?php }else {?>href="#"<?php }?> ><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a></td>
		</tr>
<?php 
	}
	echo 	'</tbody><tr>
			<td width="100%" class="grid001" colspan="10">'.$p->show().'</td>			
		  </tr>';	
} 
else 
{
	echo "</tbody><tr>";
	echo '<td align="left" colspan="10"><font face="arial"><b>No Inventory Found</b></font></td>';
	echo "</tr>";
}?>
                        <!--<tr>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001"><img src="../../../images/addinventory.png" alt="addinventory" width="32" height="32" /></td>
                        <td class="grid001"><a href="#"><img src="../../../images/reportviewEdit.png" alt="reportEdit" width="32" height="32" border="0" /></a></td>
                        <td class="grid001"><a href="#"><img src="../../../images/listviewEdit.png" alt="listView" width="32" height="32" border="0" /></a></td>
                        <td class="grid001"><a href="#"><img src="../../../images/styleEdit.png" alt="styleEdit" width="32" height="32" border="0" /></a></td>
                        <td class="grid001"><img src="../../../images/1277880471_close.png" alt="close" width="32" height="32"></td>
                        <td class="grid001"><img src="../../../images/deact.gif" alt="deactivate" width="24" height="24"></td>
                      </tr>-->
                  </table></td>
                </tr>
              </table>
              <p>
          </center></td>
        </tr>
      </table>
     
</form>
<?php  require('../../trailer.php');
?>