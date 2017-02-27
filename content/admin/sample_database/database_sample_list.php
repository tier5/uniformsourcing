<?php
require('Application.php');


if(isset($_GET['close'])){

	$id = $_GET['close'];
	$query3 = "UPDATE tbl_sample_database ".
		"SET ".
		"status = 0 ".
		"WHERE sample_id = $id ";
	if(!($result3 = pg_query($connection,$query3))){
		print("Failed query3:<br> $query3 <br><br> " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result3);

	$sql = "";
	header("location: database_sample_list.php");
}

if(isset($_GET['del'])){

	$id = $_GET['del'];
	$query4 = "UPDATE tbl_sample_database ".
		"SET ".
		"status = 0 ".
		"WHERE sample_id = $id ";
	if(!($result4 =pg_query($connection,$query4))){
		print("Failed query4:<br> $query4 <br><br> " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result4);

	$query5 = "SELECT filename ".
		"FROM tbl_sample_database_uploads ".
		"WHERE sample_id = '$id' ";
	if(!($result5 = pg_query($connection,$query5))){
		print("Failed query5:<br> $query5 <br><br> " . pg_last_error($connection));
		exit;
	}
	while($row5 = pg_fetch_array($result5)){
		$data5[] = $row5;
	}

	for($i=0; $i<count($data5); $i++){
		if(file_exists("$upload_dir"."".$data5[$i]['filename']."")){
			@ unlink("$upload_dir"."".$data5[$i]['filename']."");
		}
	}

	$query6 = "DELETE FROM tbl_sample_database_uploads WHERE sample_id = '$id' ; ";
	$query6 .= "DELETE FROM tbl_sample_database WHERE sample_id = '$id' ; ";
	if(!($result6 = pg_query($connection,$query6))){
		print("Failed query6:<br> $query6 <br><br> " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);

	header("location: database_sample_list.php");
}

require('../../header.php');

$query1 = ("SELECT \"ID\", \"clientID\", \"client\", \"active\" " .
	"FROM \"clientDB\" " .
	"WHERE \"active\" = 'yes' " .
	"ORDER BY \"client\" ASC");
if (!($result1 = pg_query($connection, $query1))) {
	print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
	exit;
}
while ($row1 = pg_fetch_array($result1)) {
	$data1[] = $row1;
}

$query2 = "SELECT \"garmentID\", \"garmentName\" ".
	"FROM \"tbl_garment\" ".
	"WHERE status = '1' ";
if(!($result2 = pg_query($connection,$query2))){
	print("Failed query2:<br> $query2 <br><br> " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[] = $row2;
}
pg_free_result($result2);

$search_sql="";
$limit="";
$search_uri="";

if(isset($_REQUEST['brand_manufacture']) && $_REQUEST['brand_manufacture']!=""){
	$search_sql = " AND s.brand_manufct ILIKE '%".$_REQUEST['brand_manufacture']."%' ";
	$search_uri = "?brand_manufct=".$_REQUEST['brand_manufacture'];
}

if(isset($_REQUEST['sample_id']) && $_REQUEST['sample_id']!=""){
	$search_sql = " AND s.sample_id_val ILIKE '%".$_REQUEST['sample_id']."%'";
	$search_uri = "?sample_id_val=".$_REQUEST['sample_id'];
}

if(isset($_REQUEST['detailed_description']) && $_REQUEST['detailed_description']!=""){
	$search_sql = " AND s.detail_description ILIKE '%".$_REQUEST['detailed_description']."%' ";
	$search_uri = "?detail_description=".$_REQUEST['detailed_description'];
}

if(isset($_REQUEST['style_number']) && $_REQUEST['style_number']!=""){
	$search_sql = " AND s.style_number ILIKE '%".$_REQUEST['style_number']."%' ";
	$search_uri = "?style_number=".$_REQUEST['style_number'];
}

if(isset($_REQUEST['vid']) && $_REQUEST['vid']!=""){
	$search_sql = " AND s.vid = '".$_REQUEST['vid']."'";
	$search_uri = "?vid=".$_REQUEST['vid'];
}

if(isset($_REQUEST['client']) && $_REQUEST['client']!=""){
	$search_sql = " AND s.client = '".$_REQUEST['client']."' ";
	$search_uri = "?client=".$_REQUEST['client'];
}

if(isset($_REQUEST['department']) && $_REQUEST['department']!=""){
	$search_sql = " AND s.department ILIKE '%".$_REQUEST['department']."%' ";
	$search_uri = "?department=".$_REQUEST['department'];
}

if(isset($_REQUEST['color']) && $_REQUEST['color']!=""){
	$search_sql = " AND s.color ILIKE '%".$_REQUEST['color']."%' ";
	$search_uri = "?color=".$_REQUEST['color'];
}

if(isset($_REQUEST['size_field']) && $_REQUEST['size_field']!=""){
	$search_sql = " AND s.size_field ILIKE '%".$_REQUEST['size_field']."%' ";
	$search_uri = "?size_field=".$_REQUEST['size_field'];
}

if(isset($_REQUEST['garment']) && $_REQUEST['garment']!=""){
	$search_sql = " AND s.garment ILIKE '%".$_REQUEST['garment']."%' ";
	$search_uri = "?garment=".$_REQUEST['garment'];
}

$mainQuery = "SELECT cl.client, ".
	"s.brand_manufct, ".
	"s.style_number, ".
	"s.detail_description, ".
	"s.sample_id, ".
	"s.sample_id_val, ".
	"s.vid,s.size, ".
	"s.color, ".
	"s.samplecost, ".
	"s.retailprice, ".
	"vendor.\"vendorName\" ".
	"FROM tbl_sample_database as s ".
	"LEFT JOIN \"clientDB\" as cl ON cl.\"ID\" = s.client ".
	"LEFT JOIN vendor ON vendor.\"vendorID\" = s.vid ".
	"WHERE s.status = 1 ".$search_sql." ".
	"ORDER BY s.modifieddate desc ";
if(!($mainresult = pg_query($connection,$mainQuery))){
	print("Failed mainQuery:<br> $mainQuery <br><br> " . pg_last_error($connection));
	exit;
}

$items = pg_num_rows($mainresult);

if($items > 0) {

	include('../../pagination.class.php');
	$p = new pagination;
	$p->items($items);
	$p->limit(10); // Limit entries per page
	//$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
	//die($_SERVER['REQUEST_URI']);
	$uri = substr($_SERVER['REQUEST_URI'], 0,strpos($_SERVER['REQUEST_URI'], '&paging'));
	if(!$uri) {
		$uri = $_SERVER['REQUEST_URI'].$search_uri;
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

$query7 = $mainQuery." ".$limit;

if(!($result7 = pg_query($connection,$query7))){
	print("Failed query7:<br> $query7 <br><br> " . pg_last_error($connection));
	exit;
}
while($row7 = pg_fetch_array($result7)){
	$data7[] = $row7;
}

$query8 = ("SELECT DISTINCT color ".
	"FROM tbl_sample_database ".
	"WHERE color IS NOT NULL ".
	"ORDER BY color ");
if(!($result8 = pg_query($connection,$query8))){
	print("Failed query8:<br> $query8 <br><br> " . pg_last_error($connection));
	exit;
}
while($row8 = pg_fetch_array($result8)){
	$data8[] = $row8;
}

?>
<div id="dialog-form" title="Image Gallery" class="popup_block"> 
</div>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  	<tr>
		<td align="left"><input type="button" value="Back" onclick="location.href='../../index.php';" /></td>
		<td align="right">
			<input type="button" value="Closed Samples" onclick="location.href='database_samples_closed.php';" />
			<input type="button" value="Add Sample Database" onclick="location.href='database_sample_add.php';" />
			<input type="button" value="View Gallery" onclick="view_gallery();" />
		</td>
	</tr>
</table>

<form action="database_sample_list.php" method="post" name="frmlist" id="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
	<tbody>
	<tr>
		<td height="35" colspan="5" ><strong>Search Database Samples </strong></td>
	</tr>
	<tr>  
		<td class="grid001">Client</td>
		<td class="grid001">
			<select name="client" id="client" >
				<option value="">---Select Client---</option>
<?php 
for($i=0; $i < count($data1); $i++){
	if($data1[$i]['ID']==$_REQUEST['client']){
		echo "<option value=\"".$data1[$i]['ID']."\" selected>".$data1[$i]['client']."</option>";
	}else{
		echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
	}
} 
?>
 			</select>
		</td>
		<td class="grid001">Department</td>
		<td class="grid001"><input type="text" name="department" value="<?php echo $_REQUEST['department'];?>"/></td> 
	</tr>
	<tr>  
		<td class="grid001">Color</td>
		<td class="grid001">
			<select name="color">
				<option value="">---SELECT---</option>
<?php
for($i=0, $z=count($data8); $i < $z; $i++){
	if($_REQUEST['color'] == $data8[$i]['color']){
		echo "<option value=\"".$data8[$i]['color']."\" selected>".$data8[$i]['color']."</option>";
	}else{
		echo "<option value=\"".$data8[$i]['color']."\">".$data8[$i]['color']."</option>";
	}
}
?>
			</select>
		</td>
		<td class="grid001">Size</td>
		<td class="grid001"><input type="text" name="size_field" value="<?php echo $_REQUEST['size_field'];?>"/></td> 
	</tr>
	<tr>  
		<td class="grid001">Garment</td>
		<td class="grid001">
			<select name="garment" id="garment">
				<option value="">--- Select Garment ------</option>
<?php 
for($i=0; $i < count($data2); $i++){
	if($_REQUEST['garment'] != $data2[$i]['garmentID']){
		echo '<option value="'.$data2[$i]['garmentID'].'">'.$data2[$i]['garmentName'].'</option>';
	}else{ 
		echo '<option value="'.$data2[$i]['garmentID'].'" selected="selected">'.$data2[$i]['garmentName'].'</option>';
	}
}
?>
			</select>
		</td>
		<td class="grid001"></td>
		<td class="grid001"></td> 
	</tr>
	<tr>
		<td colspan="5" align="right">
			<input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"> 
			<input type="reset" value="Cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel" id="cancel">
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
</table>
</form>

<link rel="stylesheet" type="text/css" href="<?php echo $mydirectory;?>/js/jquery-ui-1.8.2.css" media="all">
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/qtip.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox_gal.js"></script>

<table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" class="no-arrow rowstyle-alt">
	<thead style="border:1px white solid;" >
	<tr class="sortable"> 
		<th class="sortable" height="10"><span class="grid001">Style Number</span></th>
		<th class="sortable" height="10"><span class="grid001">Vendor</span></th>
		<th class="sortable">Sample Name</th>
		<th class="sortable">Retail</th>
		<th class="sortable">Client</th>
		<th class="sortable">Description</th>
		<th class="gridHeader">Close</th>
		<th class="gridHeader">Delete</th>
	</tr>
	</thead>

	<tbody id="desc"> 
<?php 
if(count($data7)){
	for($i=0; $i < count($data7); $i++){
		$len = strlen($data7[$i]['detail_description']);
		$readMore = 0;
		if($len > 80){
			$readMore = 1;
			$len = 80;
		}
?>
	<tr>
		<td class="grid001"><?php echo $data7[$i]['style_number'];?></td>
		<td class="grid001"><?php echo $data7[$i]['vendorName'];?></td>
		<td class="grid001"><a href="database_sample_add.php?id=<?php echo $data7[$i]['sample_id'];?>"><?php echo $data7[$i]['sample_id_val'];?></a></td>
<?php 
		if($data7[$i]['retailprice'] == ""){
			echo "<td class=\"grid001\">&nbsp;</td>";
		}else{
			echo "<td class=\"grid001\">$".$data7[$i]['retailprice']."</td>";
		}

		if($data7[$i]['client'] == '0' OR $data7[$i]['client'] == ""){
			echo "<td class=\"grid001\">-- NA --</td>";
		}else{
			echo "<td class=\"grid001\">".$data7[$i]['client']."</td>";
		}
?> 
		<td class="grid001"><?php echo substr($data7[$i]['detail_description'], 0, $len);if($readMore){?>&nbsp;&nbsp;<a style="cursor:hand;cursor:pointer; color:#00F" rel="./readMore.php?Id=<?php echo $data7[$i]['sample_id'];?>">Read more...</a><?php } ?></td>
		<td class="grid001"><a href="database_sample_list.php?close=<?php echo $data7[$i]['sample_id'];?>" onclick="javascript: if(confirm('Are you sure you want to close the sample')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/close.png" border="0"></a></td>
		<td class="grid001">
			<a href="database_sample_list.php?del=<?php echo $data7[$i]['sample_id'];?>" onclick="javascript: if(confirm('Are you sure you want to delete the sample')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/deact.gif" border="0"></a>
		</td>
	</tr>
<?php        
	}
?>	
	</tbody>
	<tr>
<?php 
		echo '<td width="100%" class="grid001" colspan="7">'.$p->show().'</td>';
?>
	</tr>	
<?php
}else{
?>	
	</tbody>
	<tr>
		<td align="left" colspan="7"><font face="arial"><b>No Database Samples Found</b></font></td>
	</tr>
<?php    
}
?>
</table>
</center>

<script type="text/javascript">
$(document).ready(function(){
	$('#desc a[rel]').each(function(){
		$(this).qtip({
			content: {
				text: '<img src="<?php echo $mydirectory;?>/images/loading.gif" alt="Loading..." />',
				url: $(this).attr('rel'),
				title: {
					text: '<?php echo $data7[0]['sample_id_val'];?> ', 
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

	$("#cancel").click(function(){$(location).attr('href',"database_sample_list.php");});

       	$( "#dialog-form" ).dialog({
		autoOpen:false,
		width:800,
		height:600
	});

});

function view_gallery(){
	$('#dialog-form').html('');
	$( "#dialog-form" ).dialog('open'); 
	
	var data=$('#frmlist').serialize();
	$.ajax({
		url:'view_gallery.php',
		type:'post',
		data:data,
		success:function(res){
			$('#dialog-form').html(res);
		},
		error:function(){
			alert('Sorry,some error occurs.Please try again...');    
		}
	});
}
</script>
<?php 
require('../../trailer.php');
?>
