<?php
require('Application.php');
$upload_dir			= "../../projectimages/";
$search_sql="";
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
	} else if($_REQUEST['dateFrom']=="" && $_REQUEST['dateTo']!="") {
		$dtto= strtotime($_REQUEST['dateTo']);	
		$search_sql.=' and p."createdDate" < '.$dtto;
		//$search_sql.=' and p."closedDate" < '.$dtto.' ';
		//$search_sq.l=' and p."modifiedDate" < '.$dtto.' ';
	} else if($_REQUEST['dateFrom']!="" && $_REQUEST['dateTo']!="") { 
		$dtfr=strtotime($_REQUEST['dateFrom']);
		$dtto= strtotime($_REQUEST['dateTo']);
		//echo date('U');
		$search_sql.=' and p."createdDate" > '.$dtfr.' and p."createdDate" < '.$dtto;
		//$search_sql.=' and p."closedDate" > '.$dtfr.' and p."closedDate" < '.$dtto.' ';
		//$search_sql.=' and p."modifiedDate" > '.$dtfr.' and p."modifiedDate" < '.$dtto.' ';
	}
}
$sql='select p.*,c."client" from "tbl_projects" p inner join "clientDB" c on p."cid"=c."ID" where p."popen" =1 '.$search_sql.' order by p."pid"  desc ';

if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultp)){
	$datalist[]=$rowd;
}

echo '<table width="100%" cellspacing="1" cellpadding="1" border="1">';
if(count($datalist)) {
echo 	'<tr>
			<td class="gridHeader"><strong>Client Name</strong> </td>
			<td class="gridHeader"><strong>Project Name</strong> </td>
			<td class="gridHeader"><strong>Project Quote</strong> </td>
			<td class="gridHeader"><strong>Project Current Amount Billed</strong> </td>
			<td class="gridHeader"><strong>Project Total Cost</strong> </td>
			<td class="gridHeader"><strong>Project Work Cost</strong> </td>
			<td class="gridHeader"><strong>Purchase Order</strong> </td>
			<td class="gridHeader"><strong>Quantity of People</strong> </td>
			<td class="gridHeader"><strong>Garment Description</strong> </td>
			<td class="gridHeader"><strong>Size Range needed</strong> </td>
			<td class="gridHeader"><strong>Sample Provided Y/N</strong>  </td>
			<td class="gridHeader"><strong>Style Number</strong> </td>
			<td class="gridHeader"><strong>Color</strong></td>
			<td class="gridHeader"><strong>Type of material</strong> </td>
			<td class="gridHeader"><strong>Embroidery Y/N</strong> </td>
			<td class="gridHeader"><strong>Silk Screening Y/N</strong> </td>
			<td class="gridHeader"><strong>Target Unit Price</strong> </td>
			<td class="gridHeader"><strong>Image 1</strong> </td>
			<td class="gridHeader"><strong>Image 2</strong> </td>
			<td class="gridHeader"><strong>Image 3</strong> </td>
			<td class="gridHeader"><strong>Image 4</strong> </td>
			<td class="gridHeader"><strong>Image 5</strong> </td>
			<td class="gridHeader"><strong>Comments</strong></td>
		  </tr>';
	for($i=0; $i < count($datalist); $i++){
		echo "<tr>";
		echo '<td class="grid001">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001">'.$datalist[$i]['pname'].'</td>';
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
} else {
	echo "<tr>";
	echo '<td align="left" colspan="8"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
echo '</table></center><script language="javascript">window.print();</script>';
?>