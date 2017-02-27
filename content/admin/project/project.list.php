<?php
require('Application.php');
require('../../header.php');

?>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min-1.4.2.js"></script>
<script type="text/javascript">
	$(document).ready(function()
{
$(".cid").change(function()
{
var id=$(this).val();
var dataString = 'clientid='+ id;
$.ajax
({
type: "POST",
url: "projctNameList.php",
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
$queryd=("SELECT * ".
		 "FROM \"tbl_projects\" ".
		 "WHERE popen = 1 ");
if(!($resultd=pg_query($connection,$queryd))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultd)){
	$datapro[]=$rowd;
}
include('../../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
	echo "cid==>".$_REQUEST['cid'];
	$search_sql=' and p."cid" ='.$_REQUEST['cid'].' ';
	$search_uri="?cid=".$_REQUEST['cid'];
}
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="") {
	$search_sql .=' and p."pid" ='.$_REQUEST['pid'].' ';
	if($search_uri)  {
		 $search_uri.="&pid=".$_REQUEST['pid'];
	} else {
		$search_uri.="?pid=".$_REQUEST['pid'];
	}
}
$sql='select p.*,c."client" from "tbl_projects" p inner join "clientDB" c on p."cid"=c."ID" where p."popen" =1 and p."status" = 1'.$search_sql.' order by p."pid"  desc ';
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($resultp);
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
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultp)){
	$datalist[]=$rowd;
}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.js"></script>';
?>
	<script type="text/javascript">
		/*function prjctName()
		{
			var val=frmlist.cid.options[frmlist.cid.options.selectedIndex].value; 
		self.location='project.list.php?slctIndex=' + val+'&cIndex='+frmlist.cid.options.selectedIndex;
		}*/
		
	</script>
	
<?php 
echo "<center><font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Projects</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
echo "<table border=\"0\" width=\"40%\">";
echo '<tr><td align="center"><input type="button" value="Add New Projects" onmouseover="this.style.cursor = \'pointer\';" onclick="javascript:location.href=\'project.add.php\'" style="cursor: pointer;"></td>';
echo '<td align="center">&nbsp;</td>';
echo '<td valign="top" align="left"><input type="button" value="View Closed Projects" onmouseover="this.style.cursor = \'pointer\';" onclick="javascript:location.href=\'project.closed.php\'" style="cursor: pointer;"></td></tr>';
echo "</table>";?>
<form action="project.list.php" method="post" name="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td height="35" colspan="5" ><strong>Search Projects </strong></td>
 </tr>
<tr>
<td width="100px" class="grid001">Client Name: </td>
 	<td width="400px" class="grid001"><select name="cid" id="cid" class="cid">
		<option value="">Select</option>
<?php for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
} ?>
 </select></td>
  <td width="20px" class="grid001">&nbsp;</td>
  <td width="110px" class="grid001">Project Name: </td>
  <td class="grid001">
  <select name="pid" id="pid" class="pid">
	<option value="">Select</option>
 </select></td>
 </tr>
 <tr><?php 
echo '                  <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><input type="submit" value="Search" onmouseover="this.style.cursor = \'pointer\';" name="button"> <input type="reset" value="Cancel" onmouseover="this.style.cursor = \'pointer\';" name="button2"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody></table>';
echo "</form>";
?>
<table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">

	<thead style="border:1px white solid;" >
    <tr class="sortable" > 
			<th class="sortableB" height="10">Client </th>
			<th class="sortableB">Project Name</th>
			<th class="sortable-numericB">Purchase Order</th>
			<th class="sortable-numericB">Project Quote</th>
			<th class="sortable-numericB">Style</th>
			
			<th colspan="3" class="numericBA">Project Pricing</th>
			<th class="gridHeaderBClose">Close</th>
			<th class="gridHeaderBClose">Deactivate</th>
		  </tr>
 	<tr class="sortable" > 
			<th class="gridHeaderB">&nbsp;</th>
			<th class="gridHeaderB">&nbsp;</th>
			<th class="gridHeaderB">&nbsp;</th>
			<th class="gridHeaderB">&nbsp;</th>
			<th class="gridHeaderB">&nbsp;</th>
			<th class="sortable-numericB">Est. Unit Cost</th>
			<th class="sortable-numericB">Total Cost</th>
			<th class="sortable-numericB">Work Cost</th>
			<th class="gridHeaderB">&nbsp;</th>
			<th class="gridHeaderB">&nbsp;</th>
		  </tr>
		  </thead><tbody>
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
		echo "<tr>";
		echo '<td class="grid001B">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001B"><a href="project.edit.php?ID='.$datalist[$i]['pid'].'">'.$datalist[$i]['pname'].'</a></td>';
		echo '<td class="grid001B">'.$datalist[$i]['purchaseOrder'].'</td>';
		echo '<td class="grid001B">$'.$datalist[$i]['pquote'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['styleNumber'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['pcost'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['pestimate'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['pcompcost'].'</td>';
		echo '<td class="grid001B"><a href="project.close.php?ID='.$datalist[$i]['pid'].'" onclick="javascript: if(confirm(\'Are you sure you want to close the project\')) { return true; } else { return false; }"><img src="'.$mydirectory.'/images/close.png" border="0"></a></td>';
		echo '<td class="grid001B">';
		if($datalist[$i]['status'])
			echo '<a href="project.delete.php?ID='.$datalist[$i]['pid'].'" onclick="javascript: if(confirm(\'Are you sure you want to delete the project\')) { return true; } else { return false; }"><img src="'.$mydirectory.'/images/deact.gif" border="0"></a>';
		echo '&nbsp;</td>';
		echo "</tr>";
	}
	echo 	'</tbody><tr>
			<td width="100%" class="grid001B" colspan="10">'.$p->show().'</td>			
		  </tr>';	
} 
else 
{
	echo "</tbody><tr>";
	echo '<td align="left" colspan="10"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
echo "</table></center>";

require('../../trailer.php');
?>