<?php
	require('Application.php');
	if(isset($_GET['del']))
	{		
		$sql='Update tbl_inventory SET "isActive"=0 where "inventoryId"='.$_GET['del'];
		if(!($result=pg_query($connection,$sql))){
					print("Failed DeleteQuery: " . pg_last_error($connection));
					exit;
				}
				pg_free_result($result);
				header('Location: inventoryReports.php?ID='.$_GET['ID']);
	}
	if(isset($_GET['close']))
	{
		$sql='Update tbl_inventory SET "isActive"=0 where "inventoryId"='.$_GET['close'];
		if(!($result=pg_query($connection,$sql))){
					print("Failed Closequery: " . pg_last_error($connection));
					exit;
				}
				pg_free_result($result);
				header('Location: inventoryReports.php?ID='.$_GET['ID']);
	}
	require('../header.php');
if(isset($_GET['ID']))
{
$Id=$_GET['ID'];
}
else if(isset($_POST['invId']))
{
	$Id=$_POST['invId'];
}

if($Id != "")
{
	$query1='SELECT Distinct inv.quantity,st."scaleNameId",inv."scaleId",inv."styleId",inv."styleNumber",inv."garmentId",inv."sizeScaleId",
             inv."fabricId", inv."colorId" from "tbl_inventory" inv inner join "tbl_invStyle" st on st."styleId"=inv."styleId" where inv."styleId"='.$Id;
	if(!($result_cnt=pg_query($connection,$query1))){
					print("Failed query1: " . pg_last_error($connection));
					exit;
				}
				while($row_cnt = pg_fetch_array($result_cnt))
				{$data_inventory[]=$row_cnt;}
				pg_free_result($result_cnt);
}
	if($data_inventory[0]['garmentId']!="")
	{
	$query2='Select "garmentID","garmentName" from tbl_garment where status=1 and "garmentID"='.$data_inventory[0]['garmentId'];
	if(!($result2=pg_query($connection,$query2))){
					print("Failed query2: " . pg_last_error($connection));
					exit;
				}
				$row2 = pg_fetch_array($result2);
				$data_garment=$row2;
				pg_free_result($result2);
	}
				if($data_inventory[0]['scaleNameId'])
				{
	$query3='Select * from "tbl_invScaleName" where "isActive"=1 and "scaleId"='.$data_inventory[0]['scaleNameId'];
	if(!($result_cnt3=pg_query($connection,$query3))){
					print("Failed query3: " . pg_last_error($connection));
					exit;
				}
				while($row_cnt3 = pg_fetch_array($result_cnt3)){
					$data_scale=$row_cnt3;}
				
				pg_free_result($result_cnt3);		
	$query4='Select * from "tbl_invScaleSize" where "scaleId"='.$data_inventory[0]['scaleNameId'];
	if(!($result_cnt4=pg_query($connection,$query4))){
					print("Failed query4: " . pg_last_error($connection));
					exit;
				}
				while($row_cnt4 = pg_fetch_array($result_cnt4)){
					$data_size[]=$row_cnt4;}
				
				pg_free_result($result_cnt4);
				}
	$query5='Select "fabricID","fabName" from "tbl_fabrics" where status=1';
	if(!($result_cnt5=pg_query($connection,$query5))){
					print("Failed query5: " . pg_last_error($connection));
					exit;
				}
				while($row_cnt5 = pg_fetch_array($result_cnt5)){
					$data_fab[]=$row_cnt5;
				}
				pg_free_result($result_cnt5);
	$query6='Select * from "tbl_invColor" where "styleId"='.$Id;
	if(!($result_cnt6=pg_query($connection,$query6))){
		  print("Failed query6: " . pg_last_error($connection));
		  exit;
	  }
				  while($row_cnt6 = pg_fetch_array($result_cnt6)){
					  $data_color[]=$row_cnt6;
				  }
				  pg_free_result($result_cnt6);
	$query7=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
		if(!($result=pg_query($connection,$query7))){
			print("Failed query7: " . pg_last_error($connection));
			exit;
		}
		while($row1 = pg_fetch_array($result)){
			$data_client[]=$row1;
		}
		pg_free_result($result);
include('../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['garment']) && $_REQUEST['garment']!="") {
	$search_sql=' and inv."garmentId" ='.$_REQUEST['garment'].' ';
	$search_uri="?garment=".$_REQUEST['garment'];
}
if(isset($_REQUEST['field1']) && $_REQUEST['field1']!="") {
	$search_sql .=' and field1 ILIKE \'%' .$_REQUEST['field1'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&field1=".$_REQUEST['field1'];
	} else {
		$search_uri.="?field1=".$_REQUEST['field1'];
	}
}
if(isset($_REQUEST['field2']) && $_REQUEST['field2']!="") {
	$search_sql .=' and field2 ILIKE \'%' .$_REQUEST['field2'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&field2=".$_REQUEST['field2'];
	} else {
		$search_uri.="?field2=".$_REQUEST['field2'];
	}
}
if(isset($_REQUEST['field3']) && $_REQUEST['field3']!="") {
	$search_sql .=' and field3 ILIKE \'%' .$_REQUEST['field3'].'%\' ';
	if($search_uri)  {
		 $search_uri.="&field3=".$_REQUEST['field3'];
	} else {
		$search_uri.="?field3=".$_REQUEST['field3'];
	}
}
if(isset($_REQUEST['scale']) && $_REQUEST['scale']!="") {
	$search_sql=' and inv."scaleId" =\''.$_REQUEST['scale'].'\' ';
	if($search_uri)  {
		 $search_uri.="&scaleId=".$_REQUEST['scale'];
	} else {
		$search_uri.="?scaleId=".$_REQUEST['scale'];
	}
}
if(isset($_REQUEST['option2']) && $_REQUEST['option2']!="") {
	$search_sql=' and "opt2Name" =\''.$_REQUEST['option2'].'\' ';
	if($search_uri)  {
		 $search_uri.="&option2=".$_REQUEST['option2'];
	} else {
		$search_uri.="?option2=".$_REQUEST['option2'];
	}
}
if(isset($_REQUEST['option1']) && $_REQUEST['option1']!="") {
	$search_sql=' and "opt1Name" =\''.$_REQUEST['option1'].'\' ';
	if($search_uri)  {
		 $search_uri.="&option1=".$_REQUEST['option1'];
	} else {
		$search_uri.="?option1=".$_REQUEST['option1'];
	}
}

if(isset($_REQUEST['color']) && $_REQUEST['color']!="") {
	$search_sql=' and inv."colorId" ='.$_REQUEST['color'].' ';
	if($search_uri)  {
		 $search_uri.="&color=".$_REQUEST['color'];
	} else {
		$search_uri.="?color=".$_REQUEST['color'];
	}
}
if(isset($_REQUEST['quantity']) && $_REQUEST['quantity']!="") {
	$search_sql=' and inv.quantity ='.$_REQUEST['quantity'];
	if($search_uri)  {
		 $search_uri.="&quantity=".$_REQUEST['quantity'];
	} else {
		$search_uri.="?quantity=".$_REQUEST['quantity'];
	}
}
if(isset($_REQUEST['fabric']) && $_REQUEST['fabric']!="") {
	$search_sql=' and inv."fabricId" ='.$_REQUEST['fabric'];
	if($search_uri)  {
		 $search_uri.="&fabric=".$_REQUEST['fabric'];
	} else {
		$search_uri.="?fabric=".$_REQUEST['fabric'];
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
/*'select sn."scaleId",sn."scaleName",st.*,g."garmentID",g."garmentName" from "tbl_invStyle" st inner join tbl_garment g on g."garmentID"=st."garmentId" left join "tbl_invScaleName" sn on st."scaleNameId"= sn."scaleId" where st."isActive"=1'.$search_sql.' order by "styleNumber"  desc ';
*/

$sql='select sc."scaleSize",sc."opt1Size",sc."opt2Size",gar."garmentName",inv.*,scn."scaleName",scn."scaleId",scn."opt1Name",scn."opt2Name",col."name",fab."fabName" from "tbl_inventory" as inv inner join "tbl_invScaleName" as scn on inv."scaleId"=scn."scaleId" left join tbl_garment as gar on inv."garmentId"=gar."garmentID" left join tbl_fabrics as fab on inv."fabricId"=fab."fabricID" left join "tbl_invColor" as col on inv."colorId"=col."colorId"left join "tbl_invScaleSize" sc on sc."sizeScaleId"=inv."sizeScaleId" where ';
	
//TO be changed tommorow
/*$sql='select DISTINCT inv."inventoryId",gar."garmentName",inv."styleId",inv."scaleId",inv."field1", inv."field2",inv."field3",
inv."sizeScaleId",inv."opt1ScaleId",inv."opt2ScaleId",
inv.quantity,st."styleNumber" ,scn."scaleName",scn."opt1Name" as opt1Name, scn."opt2Name" as opt2Name,
col."name",fab."fabName" from "tbl_inventory" as inv inner join "tbl_invStyle" st on st."styleId"=inv."styleId" inner join
 "tbl_invScaleName" as scn on st."scaleNameId"=scn."scaleId" left join tbl_garment as gar on st."garmentId"=gar."garmentID"  
 left join tbl_fabrics as fab on inv."fabricId"=fab."fabricID" left join "tbl_invColor" as col on inv."colorId"=col."colorId" where ';*/
	if(isset($_GET['ID']))$sql.= 'inv."styleId"='.$Id;
	else if(isset($_POST['invId']))
	$sql.= $_POST['invId'];
	$sql.=$search_sql.' and inv."isActive"= 1 order by inv."inventoryId" ';
if(!($result=pg_query($connection,$sql))){
	print("Failed SearchQuery: " . pg_last_error($connection));
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
	?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='reports.php';" class="button_text" type="submit" name="back" value="Back" /></td>
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
          <td align="left" valign="top"><center>
            <table width="100%">
                <tr>
                  <td align="center"><font size="5">Inventory List </font><font size="5"> <br>
                      <br>
                    </font>
                    <form action="" method="post" name="validationForm">
                    <table width="95%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td width="150" height="35"><strong>Search Inventory </strong></td>
                        <td width="200">&nbsp;</td>
                        <td width="10">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Garment: </td>
                        <td class="grid001"><input type="text" name="garment" id="garment" value="<?php echo $data_garment['garmentName'];?>"  readonly="readonly"/>
                        <?php /*?><select name="garment" id="garment">
                          <option value="">---- Select Garment ----</option>
                          <?php 
						  for($i=0; $i < count($data_garment); $i++){
						  if($data_garment[$i]['garmentName']!="")
							echo '<option value="'.$data_garment[$i]['garmentID'].'">'.$data_garment[$i]['garmentName'].'</option>';}
						  ?>  </select><?php */?></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001"><?php if($data_scale['opt1Name']!=""){ echo $data_scale['opt1Name'].':';}else{ ?>Option 1:<?php }?></td>
                        <td class="grid001"><select name="option1" id="option1"> 
                            <option value="">--- Select Option-----</option>
                            <?php 
						  for($i=0; $i < count($data_size); $i++){
							  if($data_size[$i]['opt1Size']!="")
							echo '<option value="'.$data_size[$i]['sizeScaleId'].'">'.$data_size[$i]['opt1Size'].'</option>';}
						  ?>
                          </select>                          &nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Field 1:</td>
                        <td class="grid001"><input type="text" name="field1" id="field1" /></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001"><?php if($data_scale['opt2Name']!=""){ echo $data_scale['opt2Name'].':';}else{ ?>Option 2:<?php }?></td>
                        <td class="grid001"><select name="option2" id="option2"> 
                            <option value="">--- Select Option-----</option>
                            <?php 
						  for($i=0; $i < count($data_size); $i++){
							  if($data_size[$i]['opt2Size']!="")
							echo '<option value="'.$data_size[$i]['sizeScaleId'].'">'.$data_size[$i]['opt2Size'].'</option>';}
						  ?>
                          </select></td>
                      </tr>
                      <tr>
                        <td class="grid001">Field 2:</td>
                        <td class="grid001">
                          <input type="text" name="field2" id="field2" />
                        </td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Color:</td>
                        <td class="grid001"><select name="color" id="color">
                          <option value="">---- Select Color ----</option>
                           <?php 
						  for($i=0; $i < count($data_color); $i++){
							echo '<option value="'.$data_color[$i]['colorId'].'">'.$data_color[$i]['name'].'</option>';}
						  ?>
                        </select></td>
                      </tr>
                      <tr>
                        <td class="grid001">Field 3:</td>
                        <td class="grid001"><input type="text" name="field3" id="field3" /></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Quantity:</td>
                        <td class="grid001"><select name="quantity" id="quantity">
                          <option value="">--- Select Quantity-----</option>
                           <?php for($i=0; $i < count($data_inventory); $i++){
							   if($data_inventory[$i]['quantity']!="")
                            echo '<option value="'.$data_inventory[$i]['inventoryId'].'">'.$data_inventory[$i]['quantity'].'</option>';
                            }
                            ?>
                        </select>
                      <tr>
                        <td class="grid001"><?php if($data_scale['scaleName']!=""){ echo $data_scale['scaleName'].':';}else{ ?>Size:<?php }?></td>
                        <td class="grid001"><select name="scale" id="scale">
                          <option value="">--- Select Size Scale ----</option>
                          <?php 
						  for($i=0; $i < count($data_size); $i++){
						  if($data_size[$i]['scaleSize']!="")
							echo '<option value="'.$data_size[$i]['sizeScaleId'].'">'.$data_size[$i]['scaleSize'].'</option>';}
						  ?>
                        </select>
                		</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Fabric:</td>
                        <td class="grid001"><select name="fabric" id="fabric">
                          <option value="">--- Select Fabric-----</option>
                           <?php for($i=0; $i < count($data_fab); $i++){
                            echo '<option value="'.$data_fab[$i]['fabricID'].'">'.$data_fab[$i]['fabName'].'</option>';
                            }
                            ?>
                        </select></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>
                        <input type="hidden" name="invId" id="invId" value="<?php echo $Id;?>" />
                        <input name="search" type="submit"  onMouseOver="this.style.cursor = 'pointer';" value="Search">
                        <input name="cancel" type="submit"  onMouseOver="this.style.cursor = 'pointer';" value="Cancel"></td>
                      </tr>
                      
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                    </form>
                    <table width="95%" border="0" cellspacing="1" cellpadding="1" class="no-arrow rowstyle-alt">
                    <thead>
                    <tr class="sortable"> 
                    <th class="sortable">Style Number </th>
                    <th class="sortable">Garment</th>
                    <th class="sortable">Field 1</th>
                    <th class="sortable">Field 2 </th>
                    <th class="sortable">Field 3</th>
                    <th class="gridHeader"><?php echo $datalist[0]['scaleName']; ?></th>
                   <?php if($datalist[0]['opt1Name']!=""){?> <th class="gridHeader"><?php echo $datalist[0]['opt1Name'] ;?></th><?php }?>
                    <?php if($datalist[0]['opt2Name']!=""){?><th class="gridHeader"><?php echo $datalist[0]['opt2Name'] ;?> </th><?php }?>
                    <th class="gridHeader">Color </th>
                    <th class="gridHeader">Quantity </th>
                    <th class="gridHeader">Fabric </th>
                    <th class="gridHeader">Edit </th>
                    <th class="gridHeader">Close</th>
                    <th class="gridHeader">Remove</th>
                  </tr>
                  </thead><tbody>
		  <?php
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
		echo "<tr>";
		echo '<td class="grid001">'.$datalist[$i]['styleNumber'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['garmentName'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['field1'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['field2'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['field3'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['scaleSize'].'</td>';
		if($datalist[0]['opt1Name']!=""){echo '<td class="grid001">'.$datalist[$i]['opt1Size'].'</td>';}
		if($datalist[0]['opt2Name']!=""){echo '<td class="grid001">'.$datalist[$i]['opt2Size'].'</td>';}
		echo '<td class="grid001">'.$datalist[$i]['name'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['quantity'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['fabName'].'</td>';
		echo '<td class="grid001"><a href="inventoryAdd.php?invId='.$datalist[$i]['inventoryId'].'&ID='.$datalist[$i]['styleId'].'&type=e"><img src="'.$mydirectory.'/images/edit.png" border="0"></a></td>';
		echo '<td class="grid001">';?>
		<a <?php if($datalist[$i]['styleId']>0){?>onclick="javascript: if(confirm('Are you sure you want to close the inventory')) { return true; } else { return false; }" href="inventoryReports.php?close=<?php echo $datalist[$i]['inventoryId'];?>&ID=<?php echo $datalist[$i]['styleId'];?>"<?php }else {?>href="#"<?php }?> ><img src="<?php echo $mydirectory;?>/images/close.png" border="0"></a> <?php echo '</td>';
		echo '<td class="grid001">';
		?>
		<a <?php if($datalist[$i]['styleId']>0){?>onclick="javascript: if(confirm('Are you sure you want to delete the inventory')) { return true; } else { return false; }" href="inventoryReports.php?del=<?php echo $datalist[$i]['inventoryId'];?>&ID=<?php echo $datalist[$i]['styleId'];?>"<?php }else {?>href="#"<?php }?> ><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a><?php 
		echo '&nbsp;</td>';
		echo "</tr>";
	}
	echo 	'</tbody><tr>
			<td width="100%" class="grid001" colspan="14">'.$p->show().'</td>			
		  </tr>';	
} 
else 
{
	echo "</tbody><tr>";
	echo '<td align="left" colspan="14"><font face="arial"><b>No Inventory Found</b></font></td>';
	echo "</tr>";
}?>
            	</table></td>
                </tr>
              </table>
              <p>
          </center></td>
        </tr>
      </table>

<?php  require('../trailer.php');
?>