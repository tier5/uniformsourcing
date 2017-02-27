<?php
require('Application.php');
require('../../header.php');
//print '<pre>';print_r($_SERVER);print '</pre>';
//print $_SERVER['SERVER_NAME'];
$upload_dir			= "../../projectimages/";
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
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
	$search_sql=' and p."cid" ='.$_REQUEST['cid'];
	$search_uri="?cid=".$_REQUEST['cid'];
}
if((isset($_REQUEST['dateFrom']) && $_REQUEST['dateFrom']!="") || (isset($_REQUEST['dateTo']) && $_REQUEST['dateTo']!="")   ) {
	if($_REQUEST['dateFrom']!="" && $_REQUEST['dateTo']=="") {		
		$dtfr= strtotime($_REQUEST['dateFrom']);
		$search_sql.=' and p."createdDate" > '.$dtfr;
		//$search_sql.=' and p."closedDate" > '.$dtfr.' ';
		//$search_sq.l=' and p."modifiedDate" > '.$dtfr.' ';
		if($search_uri)  {
			 $search_uri.="&dateFrom=".$_REQUEST['dateFrom'];
		} else {
			$search_uri.="?dateFrom=".$_REQUEST['dateFrom'];
		}
	} else if($_REQUEST['dateFrom']=="" && $_REQUEST['dateTo']!="") {
		$dtto= strtotime($_REQUEST['dateTo']);	
		$search_sql.=' and p."createdDate" < '.$dtto;
		//$search_sql.=' and p."closedDate" < '.$dtto.' ';
		//$search_sq.l=' and p."modifiedDate" < '.$dtto.' ';
		if($search_uri)  {
			 $search_uri.="&dateTo=".$_REQUEST['dateTo'];
		} else {
			$search_uri.="?dateTo=".$_REQUEST['dateTo'];
		}
	} else if($_REQUEST['dateFrom']!="" && $_REQUEST['dateTo']!="") { 
		$dtfr=strtotime($_REQUEST['dateFrom']);
		$dtto= strtotime($_REQUEST['dateTo']);
		//echo date('U');
		$search_sql.=' and p."createdDate" > '.$dtfr.' and p."createdDate" < '.$dtto;
		//$search_sql.=' and p."closedDate" > '.$dtfr.' and p."closedDate" < '.$dtto.' ';
		//$search_sql.=' and p."modifiedDate" > '.$dtfr.' and p."modifiedDate" < '.$dtto.' ';
		if($search_uri)  {
			 $search_uri.="&dateFrom=".$_REQUEST['dateFrom']."&dateTo=".$_REQUEST['dateTo'];
		} else {
			$search_uri.="?dateFrom=".$_REQUEST['dateFrom']."&dateTo=".$_REQUEST['dateTo'];
		}
	}
}
$sql='select p.*,c."client" from "tbl_projects" p inner join "clientDB" c on p."cid"=c."ID" where p."popen" =1 '.$search_sql.' order by p."pid"  desc ';

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

echo "<center><font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Projects Report</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
echo '<form action="" method="post">';
echo '<table width="100%" cellspacing="1" cellpadding="1" border="0">
                      <tbody><tr>
                        <td height="35" width="150"><strong>Search Projects </strong></td>
                        <td width="200">&nbsp;</td>
                        <td width="10">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>';
echo '                <tr>';
echo '                  <td class="grid001">Client Name: </td>';
echo '                  <td class="grid001"><select name="cid" id="cid">';
echo '<option value="">Select</option>';
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
}
echo '                  </select></td>';
echo '                  <td class="grid001" colspan="3">&nbsp;</td>                        
                      </tr>';
echo '                <tr>
                        <td class="grid001">Start Date: </td>
                        <td class="grid001"><input type="text" name="dateFrom" id="dateFrom" readonly="readonly" /></td>
                        <td class="grid001" colspan="2">End Date : </td>
                        <td class="grid001"><input type="text" name="dateTo" id="dateTo" readonly="readonly" /></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input type="submit" value="Search" onmouseover="this.style.cursor = \'pointer\';" name="button" style="cursor: pointer;">
                        <input type="reset" value="Cancel" onmouseover="this.style.cursor = \'pointer\';" name="button2"></td>
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
echo '<div class="reports"><font face="arial">';
echo '<table width="100%" cellspacing="1" cellpadding="1" border="0">';
if(count($datalist)) {
echo 	'<tr>
			<td class="gridHeader">Client Name </td>
			<td class="gridHeader">Project Name </td>
			<td class="gridHeader">Project Quote </td>
			<td class="gridHeader">Project Current Amount Billed </td>
			<td class="gridHeader">Project Total Cost </td>
			<td class="gridHeader">Project Work Cost </td>
			<td class="gridHeader">Purchase Order </td>
			<td class="gridHeader">Quantity of People </td>
			<td class="gridHeader">Garment Description </td>
			<td class="gridHeader">Size Range needed </td>
			<td class="gridHeader">Sample Provided Y/N  </td>
			<td class="gridHeader">Style Number </td>
			<td class="gridHeader">Color</td>
			<td class="gridHeader">Type of material </td>
			<td class="gridHeader">Embroidery Y/N </td>
			<td class="gridHeader">Silk Screening Y/N </td>
			<td class="gridHeader">Target Unit Price </td>
			<td class="gridHeader">Image 1 </td>
			<td class="gridHeader">Image 2 </td>
			<td class="gridHeader">Image 3 </td>
			<td class="gridHeader">Image 4 </td>
			<td class="gridHeader">Image 5 </td>
			<td class="gridHeader">Comments</td>
		  </tr>';
	for($i=0; $i < count($datalist); $i++){
		echo "<tr>";
		echo '<td class="grid001">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001"><a href="project.edit.php?ID='.$datalist[$i]['pid'].'">'.$datalist[$i]['pname'].'</a></td>';
		echo '<td class="grid001">';
			if($datalist[$i]['pquote']) echo '$ '.$datalist[$i]['pquote'];
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['pcost']) echo '$ '.$datalist[$i]['pcost'];
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['pestimate']) echo '$ '.$datalist[$i]['pestimate'];
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['pcompcost']) echo '$ '.$datalist[$i]['pcompcost'];
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['purchaseOrder']) echo ' '.$datalist[$i]['purchaseOrder'];
		echo '&nbsp;</td>';			
		echo '<td class="grid001">'.$datalist[$i]['quanPeople'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pdescription'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['sizeNeeded'].'</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['samplesProvided']) echo 'YES '; else echo 'NO';
		echo '&nbsp;</td>';
		echo '<td class="grid001">'.$datalist[$i]['styleNumber'].'</td>';		
		echo '<td class="grid001">'.$datalist[$i]['color'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['typeMaterial'].'</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['embroidery']) echo 'YES '; else echo 'NO';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['silkScreening']) echo 'YES '; else echo 'NO';
		echo '&nbsp;</td>';	
		echo '<td class="grid001">';
			if($datalist[$i]['targetPriceunit']) echo '$ '.$datalist[$i]['targetPriceunit'];
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image1'] && file_exists($upload_dir.$datalist[$i]['image1'])) echo '<img border="0" alt="" src="'.$upload_dir.$datalist[$i]['image1'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image2'] && file_exists($upload_dir.$datalist[$i]['image2'])) echo '<img border="0" alt="" src="'.$upload_dir.$datalist[$i]['image2'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image3'] && file_exists($upload_dir.$datalist[$i]['image3'])) echo '<img border="0" alt="" src="'.$upload_dir.$datalist[$i]['image3'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image4'] && file_exists($upload_dir.$datalist[$i]['image4'])) echo '<img border="0" alt="" src="'.$upload_dir.$datalist[$i]['image4'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image5'] && file_exists($upload_dir.$datalist[$i]['image5'])) echo '<img border="0" alt="" src="'.$upload_dir.$datalist[$i]['image5'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">'.$datalist[$i]['projectComments'].'</td>';	
		echo "</tr>";
	}
	echo 	'<tr>
			<td class="grid001" colspan="23">'.$p->show().'</td>			
		  </tr>';	
} else {
	echo "<tr>";
	echo '<td align="left" colspan="8"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
echo "</table>";
echo "</font></div>";

echo '<font face="arial">					
                    <table width="990%" cellspacing="0" cellpadding="0" border="0">
                      <tbody><tr>
                        <td><input type="button" value="Print" onmouseover="this.style.cursor = \'pointer\';" name="button3" style="cursor: pointer;" onclick="javascript:return window.open(\'report.print.php'.$search_uri.' \');">
                          <input type="button" value="Export as Excel sheet" onmouseover="this.style.cursor = \'pointer\';" name="button32" style="cursor: pointer;" onclick="javascript:return window.open(\'report.excel.php'.$search_uri.' \');"></td>
                      </tr>
                    </tbody></table></font>';

require('../../trailer.php');
?>
