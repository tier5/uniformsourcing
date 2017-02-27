<?php
require('Application.php');
if(isset($_GET['del']))
{
	$id = $_GET['del'];
	$query="Update tbl_carriers SET status = 0 where carrier_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
}
require('../../header.php');
$queryVendor="SELECT  \"carrier_id\", \"carrier_name\", \"weblink\" ".
		 "FROM \"tbl_carriers\" ".
		 "WHERE \"status\" = '1' ".
		 "ORDER BY \"carrier_name\" ASC ";
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$datalist[]=$row;
} 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
    <input type="button" value="Back" onclick="location.href ='../index.php'" />
    
  </td>  <td align="right"><input type="button" value="Add Carrier" onClick="window.location = 'carrier_add.php'"></td>
  </tr>
</table>
<br />
<table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" class="no-arrow rowstyle-alt">

	<thead style="border:1px white solid;" >
    <tr class="sortable"> 
			<th class="sortable" height="10">Carrier Name</th>
            <th class="sortable" height="10">Weblink</th>
			<th class="gridHeader">Edit</th>
			<th class="gridHeader">Delete</th>
	  </tr>
</thead><tbody id="desc"> 
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
?>		
		<tr>
		<td class="grid001"><?php echo $datalist[$i]['carrier_name'];?></td>
		<td class="grid001"><?php echo $datalist[$i]['weblink'];?></td>	
		<td class="grid001"><a href="carrier_add.php?id=<?php echo $datalist[$i]['carrier_id'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" border="0"></a></td>
		<td class="grid001">
			<a href="carrier_list.php?del=<?php echo $datalist[$i]['carrier_id'];?>" onclick="javascript: if(confirm('Are you sure you want to delete the Carrier')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a></td>
	</tr>
<?php        
	}
?>	
	</tbody>
<?php        
	}
else 
{
?>	
	</tbody><tr>
	<td align="left" colspan="7"><font face="arial"><b>No Database Samples Found</b></font></td>
	</tr>
<?php    
}
?>
</table></center>
<?php 
require('../../trailer.php');
?>