<?php
require('Application.php');
if(isset($_GET['close']))
{
	$id = $_GET['close'];
	$query="Update tbl_sample_database SET status = 0 where sample_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
	$sql = "";
	header("location: database_sample_list.php");
}
if(isset($_GET['del']))
{
	$id = $_GET['del'];
	$query="Update tbl_sample_database SET status = 0 where sample_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
	
	$sql = 'select filename from tbl_sample_database_uploads where sample_id='.$id;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_file[]=$row;
	}
	for($i=0; $i<count($data_file); $i++)
	{
		if(file_exists("$upload_dir"."".$data_file[$i]['filename']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['filename']."");
			}
	}
	$sql = 'delete from tbl_sample_database_uploads where sample_id='.$id ."; ";
	$sql.= 'delete from tbl_sample_database where sample_id='.$id.";";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	$sql = "";
	pg_free_result($result);
	header("location: database_sample_list.php");
}
require('../../header.php');
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

$query1 = ("SELECT \"ID\", \"clientID\", \"client\", \"active\" " .
        "FROM \"clientDB\" " .
        "WHERE \"active\" = 'yes' " .
        "ORDER BY \"client\" ASC");
if (!($result1 = pg_query($connection, $query1))) {
    print("Failed query2: " . pg_last_error($connection));
    exit;
}
while ($row1 = pg_fetch_array($result1)) {
    $data_client[] = $row1;
}
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['brand_manufacture']) && $_REQUEST['brand_manufacture']!="") 
{
	$search_sql=' and s.brand_manufct ILIKE \'%' .$_REQUEST['brand_manufacture'].'%\' ';
	$search_uri="?brand_manufct=".$_REQUEST['brand_manufacture'];
}
if(isset($_REQUEST['sample_id']) && $_REQUEST['sample_id']!="") 
{
	$search_sql=' and s.sample_id_val ILIKE \'%' .$_REQUEST['sample_id'].'%\'';
	$search_uri="?sample_id_val=".$_REQUEST['sample_id'];
}
if(isset($_REQUEST['detailed_description']) && $_REQUEST['detailed_description']!="") 
{
	$search_sql=' and s.detail_description ILIKE \'%' .$_REQUEST['detailed_description'].'%\' ';
	$search_uri="?detail_description=".$_REQUEST['detailed_description'];
}
if(isset($_REQUEST['style_number']) && $_REQUEST['style_number']!="") 
{
	$search_sql=' and s.style_number ILIKE \'%' .$_REQUEST['style_number'].'%\' ';
	$search_uri="?style_number=".$_REQUEST['style_number'];
}
if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="") 
{
	$search_sql=' and s.vid =' .$_REQUEST['vid'];
	$search_uri="?vid=".$_REQUEST['vid'];
}


if(isset($_REQUEST['client']) && $_REQUEST['client']!="") 
{
	$search_sql=' and s.client ='.$_REQUEST['client'].' ';
	$search_uri="?client=".$_REQUEST['client'];
}


$mainQuery='select cl.client,s.brand_manufct,s.style_number,s.detail_description,s.sample_id,s.sample_id_val,s.vid,s.size,s.color,s.samplecost,s.retailprice,vendor."vendorName" from tbl_sample_database as s left join "clientDB" as cl on cl."ID"=s.client left join vendor on vendor."vendorID" = s.vid where s.status=1 '.$search_sql.' order by s.modifieddate  desc';

$sql = $mainQuery;
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed sample_database_query: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($result);
if($items > 0) {
	include('../../pagination.class.php');
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
while($rowd = pg_fetch_array($result)){
	$datalist[]=$rowd;
}


?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='../../index.php';" /></td>
    <td align="right"><input type="button" value="Closed Samples" onclick="location.href='database_samples_closed.php';" /><input type="button" value="Add Sample Database" onclick="location.href='database_sample_add.php';" /></td>
  </tr>
</table>
<form action="database_sample_list.php" method="post" name="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td height="35" colspan="5" ><strong>Search Database Samples </strong></td>
 </tr>
<tr>
<td width="100" class="grid001">Style Number: </td>
 	<td width="400" class="grid001"><input type="text" name="style_number" id="style_number" value="<?php echo $_REQUEST['style_number'] ?>" /></td>
  <td width="20" class="grid001">&nbsp;</td>
  <td width="110" class="grid001">Sample Name: </td>
  <td class="grid001"><input type="text" name="sample_id" id="sample_id" value="<?php echo $_REQUEST['sample_id'] ?>" /></td>
 </tr>
 <tr>
   <td class="grid001">Description</td>
   <td class="grid001"><input type="text" name="detailed_description" id="detailed_description" value="<?php echo $_REQUEST['detailed_description'] ?>" /></td>
   <td class="grid001">&nbsp;</td>
   <td class="grid001">Client</td>
   <td class="grid001"><select name="client" id="client" >
		<option value="">---Select Client---</option>
<?php for($i=0; $i < count($data_client); $i++){
	echo "<option value=\"".$data_client[$i]['ID']."\">".$data_client[$i]['client']."</option>";
} ?>
 </select></td>
 </tr>
 <tr>
   <td class="grid001">Vendor</td>
   <td class="grid001"><select name="vid" id="vid" >
		<option value="">---Select Vendor---</option>
<?php for($i=0; $i < count($data_Vendr); $i++){
	echo "<option value=\"".$data_Vendr[$i]['vendorID']."\">".$data_Vendr[$i]['vendorName']."</option>";
} ?>
 </select></td>
   <td class="grid001">&nbsp;</td>
   <td class="grid001">&nbsp;</td>
   <td class="grid001">&nbsp;</td>
 </tr>
                      <tr>
                        <td colspan="5" align="right"><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"> <input type="reset" value="Cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel" id="cancel"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody></table>
</form>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/qtip.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" class="no-arrow rowstyle-alt">

	<thead style="border:1px white solid;" >
    <tr class="sortable"> 
			<th class="sortable" height="10"><span class="grid001">Style Number</span></th>
            <th class="sortable" height="10"><span class="grid001">Vendor</span></th>
			<th class="sortable">Sample Name</th>
			<th class="sortable">Client</th>
            <th class="sortable">Description</th>
			<th class="gridHeader">Close</th>
			<th class="gridHeader">Delete</th>
	  </tr>
</thead><tbody id="desc"> 
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
		$len = strlen($datalist[$i]['detail_description']);
		$readMore = 0;
		if($len > 80)
		{
			$readMore = 1;
			$len = 80;
		}
?>		
		<tr><td class="grid001"><?php echo $datalist[$i]['style_number'];?></td>
        <td class="grid001"><?php echo $datalist[$i]['vendorName'];?></td>
		<td class="grid001"><a href="database_sample_add.php?id=<?php echo $datalist[$i]['sample_id'];?>"><?php echo $datalist[$i]['sample_id_val'];?></a></td>
		<td class="grid001"><?php echo $datalist[$i]['client'];?></td>        
		<td class="grid001"><?php echo substr($datalist[$i]['detail_description'], 0, $len);if($readMore){?>&nbsp;&nbsp;<a style="cursor:hand;cursor:pointer; color:#00F" rel="./readMore.php?Id=<?php echo $datalist[$i]['sample_id'];?>">Read more...</a><?php } ?></td>
		<td class="grid001"><a href="database_sample_list.php?close=<?php echo $datalist[$i]['sample_id'];?>" onclick="javascript: if(confirm('Are you sure you want to close the sample')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/close.png" border="0"></a></td>
		<td class="grid001">
			<a href="database_sample_list.php?del=<?php echo $datalist[$i]['sample_id'];?>" onclick="javascript: if(confirm('Are you sure you want to delete the sample')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a></td>
	</tr>
<?php        
	}
?>	
	</tbody><tr>
			<?php echo '<td width="100%" class="grid001" colspan="7">'.$p->show().'</td>';?>			
		  </tr>	
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
<script type="text/javascript">
$(document).ready(function()
{
	 $('#desc a[rel]').each(function()
   {
      $(this).qtip(
      {
         content: {
            text: '<img src="<?php echo $mydirectory;?>/images/loading.gif" alt="Loading..." />',
            url: $(this).attr('rel'),
            title: {
               text: '<?php echo $datalist[0]['sample_id_val'];?> ', 
               button: 'Close'
            }
         },
         position: {
            corner: {
               target: 'bottomMiddle', 
               tooltip: 'topMiddle'
            },
            adjust: {
               screen: true
            }
         },
         show: { 
            when: 'click', 
            solo: true
         },
         hide: 'unfocus',
         style: {
            tip: true,
            border: {
               width: 0,
               radius: 4
            },
            name: 'light',
            width: 400
         }
      })
   });				   

	$("#cancel").click(function(){$(location).attr('href',"database_sample_list.php");
					 });
});
</script>
<?php 
require('../../trailer.php');
?>