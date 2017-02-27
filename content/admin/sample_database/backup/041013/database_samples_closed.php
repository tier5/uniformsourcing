<?php
require('Application.php');

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
	header("location: database_samples_closed.php");
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
$mainQuery='select s.brand_manufct,s.style_number,s.detail_description,s.sample_id,s.sample_id_val,s.vid,s.size,s.color,s.samplecost,s.retailprice,vendor."vendorName" from tbl_sample_database as s left join vendor on vendor."vendorID" = s.vid where s.status=0 order by s.modifieddate  desc';
$sql = $mainQuery;

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
    <td align="left"><input type="button" value="Back" onclick="location.href='database_sample_list.php'" /></td>
  </tr>
</table>

<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/qtip.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" class="no-arrow rowstyle-alt">

	<thead style="border:1px white solid;" >
    <tr class="sortable"> 
			<th class="sortable" height="10"><span class="grid001">Sample ID</span></th>
            <th class="sortable" height="10"><span class="grid001">Brand/Manufacture</span></th>
			<th class="sortable">StyleNumber</th>
			<th class="sortable">Vendor</th>
            <th class="sortable">Detailed Description</th>
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
		<tr>
		<td class="grid001"><?php echo $datalist[$i]['sample_id_val'];?></td>
		<td class="grid001"><?php echo $datalist[$i]['brand_manufct'];?></td>
		<td class="grid001"><?php echo $datalist[$i]['style_number'];?></td>
        <td class="grid001"><?php echo $datalist[$i]['vendorName'];?></td>
		<td class="grid001"><?php echo substr($datalist[$i]['detail_description'], 0, $len);if($readMore){?>&nbsp;&nbsp;<a style="cursor:hand;cursor:pointer; color:#00F" rel="./readMore.php?Id=<?php echo $datalist[$i]['sample_id'];?>">Read more...</a><?php } ?></td>
		
		<td class="grid001">
			<a href="database_samples_closed.php?del=<?php echo $datalist[$i]['sample_id'];?>" onclick="javascript: if(confirm('Are you sure you want to delete the sample')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a></td>
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