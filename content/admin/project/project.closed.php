<?php
require('Application.php');
require('../../header.php');
$limit="";
include('../../pagination.class.php');
$sql='select p.*,c."client" from "tbl_projects" p inner join "clientDB" c on p."cid"=c."ID" where p."popen" =0 order by p."pid"  desc ';
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
		$uri=$_SERVER['REQUEST_URI'];
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
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\"> Closed Projects</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
echo '<table width="100%" cellspacing="1" cellpadding="1" border="0">';
if(count($datalist)) {
echo 	'<tr>
			<td class="gridHeader">Client Name </td>
			<td class="gridHeader">Project Name</td>
			<td class="gridHeader">Project Order</td>
			<td class="gridHeader">Project Quote</td>
			<td class="gridHeader">Project Current Amount Billed($)</td>
			<td class="gridHeader">Project Total Cost($)</td>
			<td class="gridHeader">Project Total Cost($)</td>
		  </tr>';
	for($i=0; $i < count($datalist); $i++){
		echo "<tr>";
		echo '<td class="grid001">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pname'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['purchaseOrder'].'</td>';
		echo '<td class="grid001">$'.$datalist[$i]['pquote'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pcompcost'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pestimate'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pcost'].'</td>';
		echo "</tr>";
	}
	echo 	'<tr>
				<td class="grid001" colspan="8">'.$p->show().'</td>			
			  </tr>';	
} else {
	echo "<tr>";
	echo '<td align="left" colspan="9"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
echo "</table>";

require('../../trailer.php');
?>