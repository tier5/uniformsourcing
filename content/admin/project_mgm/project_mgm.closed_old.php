<?php
require('Application.php');
$current_page ="project_mgm.closed.php";
$type = "project_purchase";
$paging = 'paging=';
if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging .= $_GET['paging'];
}
else
{
	$paging .= 1;
}
if(isset($_SESSION['search_uri']) && $_SESSION['search_uri']!="")
{
	if($type !=$_SESSION['page_type'])
	{
		$_SESSION['search_uri'] = "";
	}
}

$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
	$search_sql=' and prj.client ='.$_REQUEST['cid'].' ';
	$search_uri="?cid=".$_REQUEST['cid'];
	$_SESSION['search_uri'] = $search_uri;
	}
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="") {
	$search_sql .=' and prj.pid ='.$_REQUEST['pid'].' ';
	if($search_uri)  {
		 $search_uri.="&pid=".$_REQUEST['pid'];
	} else {
		$search_uri.="?pid=".$_REQUEST['pid'];
	}
	if($_SESSION['search_uri'])
			$_SESSION['search_uri'].= $search_uri;
		else	
			$_SESSION['search_uri']= $search_uri;
}
if(isset($_GET['reopen']))
{
	$ID=$_GET['reopen'];
	$query1=("UPDATE tbl_newproject ".
		"SET ".
		"status = '1',  updateddate = '".date('U')."' ".
		"WHERE pid = '$ID'");
	if(!($result1=pg_query($connection,$query1))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result1);
	header("location: project_mgm.closed.php?$paging");
}	
if(isset($_GET['del']))
{
	$pid = $_GET['del'];
	$sql = "delete from tbl_prjsample where pid=".$pid."; ";
	
	$sql .= "delete from tbl_prjpurchase where pid =".$pid."; ";
	
	$sql .= "delete from tbl_prjpricing where pid =".$pid."; ";
	
	$sql .= "delete from tbl_prjvendor where pid =".$pid."; ";
	
	$sql .= "delete from tbl_mgt_notes where pid =".$pid."; ";
	
	$sql .= "delete from tbl_prj_style where pid =".$pid."; ";
	
	$sql .= "delete from tbl_prjorder_shipping where pid =".$pid."; ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
	$sql = 'select elementfile,image from tbl_prj_elements where pid='.$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_file[]=$row;
	}
	pg_free_result($result);
	for($i=0; $i<count($data_file); $i++)
	{
		if(file_exists("$upload_dir"."".$data_file[$i]['elementfile']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['elementfile']."");
			}
			if(file_exists("$upload_dir"."".$data_file[$i]['image']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['image']."");
			}
	}
	$data_file[] = "";
	$sql = "delete from tbl_prj_elements where pid =".$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}

	$sql = 'select file_name from tbl_prjimage_file where pid='.$pid;
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
		if(file_exists("$upload_dir"."".$data_file[$i]['file_name']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['file_name']."");
			}
	}
	$sql = 'delete from tbl_prjimage_file where pid='.$pid ."; ";
	
	$sql.= 'delete from tbl_newproject where pid='.$pid.";";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
	header("location: project_mgm.closed.php?$paging");
}
require('../../header.php');

$_SESSION['page'] = $current_page;

?>

<script type="text/javascript">
var cIndex=0;
</script>
<?php
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
include('../../pagination.class.php');
$sql=' select Distinct(prj.projectname),c.client,prj.pid,prj.order_placeon,prj.status, tbl_prj_style.style,emp.firstname,emp.lastname,prch.purchaseorder,prc.prjquote,prc.prjcost,prc.prj_completioncost,prc.prj_estimatecost,prc.prj_completioncost from tbl_newproject as prj left join tbl_prjpurchase as prch on prch.pid = prj.pid left join tbl_prjpricing as prc on prc.pid = prj.pid left join "employeeDB" as emp on emp."employeeID"= prj.project_manager left join "clientDB" c on prj.client=c."ID" left join tbl_prj_style on tbl_prj_style.prj_style_id = (select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = prj.pid order by tbl_prj_style.prj_style_id desc limit 1) where prch.purchaseorder IS NULL and prj.status =0 '.$search_sql.' order by prj."pid"  desc ';
//echo $sql;
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($resultp);
if($items > 0) {
	$p = new pagination;
	$p->items($items);
	$p->limit(10); // Limit entries per page
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
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultp)){
	$datalist[]=$rowd;
}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.js"></script>';
?>
	
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onClick="location.href='project_mgm.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center>
<?php 
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Closed Projects</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
<form action="project_mgm.closed.php" method="post" name="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td height="35" colspan="5" ><strong>Search Projects </strong></td>
 </tr>
<tr>
<td width="100px" class="grid001">Client Name: </td>
 	<td width="400px" class="grid001">
      <select name="cid" id="cid" class="cid">
		<option value="">Select</option>
<?php for($i=0; $i < count($data1); $i++)
{
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
} 
?>
 </select></td>
  <td width="20px" class="grid001">&nbsp;</td>
  <td width="110px" class="grid001">Project Name: </td>
  <td class="grid001">
  <select  name="pid" id="pid" class="pid">
	<option value="">Select</option>
    </select>
</td>
 </tr>
 <tr>                 <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"> <input type="submit" value="Cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel"></td>
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

<table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">

<thead style="border:1px white solid;" >
    <tr class="sortable"> 
			<th class="sortableB" height="10">Client </th>
            <th class="sortableB" height="10">Project Manager</th>
			<th class="sortableB">Project Name</th>
			<th class="sortable-numericB">Project Quote</th>
			<th class="sortable-numericB">Style</th>
            <th class="sortable-numericB">Order Placed On</th>
			<th class="sortable-numericB">Project Estimatd Unit Cost</th>			
			<th class="gridHeaderBClose">Edit</th>
            <th class="sortable-numericB">ReOpen</th>
		  </tr>
		  </thead><tbody> 
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
		echo "<tr>";
		echo '<td class="grid001B">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['firstname'].$datalist[$i]['lastname'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['projectname'].'</td>';
		echo '<td class="grid001B">$'.$datalist[$i]['prjquote'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['style'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['order_placeon'].'</td>';
		echo '<td class="grid001B">$'.$datalist[$i]['prj_estimatecost'].'</td>';		
		echo '<td class="grid001B"><a href="project_mgm.add.php?id='.$datalist[$i]['pid'].'&'.$paging.'"><img src="'.$mydirectory.'/images/edit.png" alt="edit" /></a></td>';
		echo '<td class="grid001B"><a href="project_mgm.closed.php?reopen='.$datalist[$i]['pid'].'" onclick="javascript: if(confirm(\'Are you sure you want to reopen the project\')) { return true; } else { return false; }"><img src="'.$mydirectory.'/images/bullet_add.png" border="0"></a></td>';
		
		echo "</tr>";
	}
	echo 	'</tbody><tr>
			<td width="100%" class="grid001B" colspan="10">'.$p->show().'</td>			
		  </tr>';	
} 
else 
{
	echo "</tbody><tr>";
	echo '<td align="left" colspan="11"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
?>
</table></center>
<script type= "text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
 $(".cid").change(function()
	{
		var id=$(this).val();
		var dataString = 'clientid='+ id+"&list_type=0&status=0";
		$.ajax
		({
			type: "POST",
			url: "projectname.list.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$(".pid").html(html);
			} 
		});
	});
});
</script>

<?php
require('../../trailer.php');
?>