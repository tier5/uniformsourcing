<?php
require('Application.php');
$display_dir			= "http://".$_SERVER['SERVER_NAME']."/uniform/content/projectimages/";//"http://117.204.122.229/uniform/content/projectimages/";//
$upload_dir			="../../projectimages/";
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
$export_file = "projectreport".date('U').".xls";

ob_end_clean(); 
ini_set('zlib.output_compression','Off');    
header('Pragma: public'); 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past    
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1 
//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header ("Pragma: no-cache"); 
header("Expires: 0"); 
header('Content-Transfer-Encoding: none'); 
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera 
//header("Content-type: application/x-msexcel");                    // This should work for the rest 
header('Content-Disposition: attachment; filename="'.basename($export_file).'"');
/*
echo 	"Client Name \t Project Name \t Project Quote \t Project Current Amount Billed \t Project Total Cost \t Project Work Cost \t Purchase Order \t Quantity of People \t Garment Description \t Size Range needed \t Sample Provided Y/N \t Style Number \t Color \t Type of material \t Embroidery Y/N \t Silk Screening Y/N \t Target Unit Price \t Image 1 \t Image 2 \t Image 3 \t Image 4 \t Image 5 \t Comments \n";
$excelstr="";
	for($i=0; $i < count($datalist); $i++){
		$excelstr.=$datalist[$i]['client']." \t ".$datalist[$i]['pname']." \t ".'$ '.$datalist[$i]['pquote']." \t ".'$ '.$datalist[$i]['pcost']." \t ".'$ '.$datalist[$i]['pestimate']." \t ".'$ '.$datalist[$i]['pcompcost']." \t ".'$ '.$datalist[$i]['purchaseOrder']." \t ".$datalist[$i]['quanPeople']." \t ".$datalist[$i]['pdescription']." \t ".$datalist[$i]['sizeNeeded']." \t ";if($datalist[$i]['samplesProvided']) { $excelstr.= " YES "." \t "; }else  {$excelstr.=  "NO"." \t ";}$excelstr.= $datalist[$i]['styleNumber']." \t ".$datalist[$i]['color']." \t ".$datalist[$i]['typeMaterial']." \t ";if($datalist[$i]['embroidery']) { $excelstr.= " YES "." \t ";} else { $excelstr.=  "NO"." \t ";};if($datalist[$i]['silkScreening']) { $excelstr.= " YES "." \t ";} else { $excelstr.=  "NO"." \t "; }$excelstr.=  '$ '.$datalist[$i]['targetPriceunit']." \t ";if($datalist[$i]['image1'] && file_exists($upload_dir.$datalist[$i]['image1'])) { $excelstr.="<img border=\"0\" alt=\"\" src=\"".$display_dir.$datalist[$i]['image1']."\" />"." \t "; } else {$excelstr.="  "." \t ";  }if($datalist[$i]['image2'] && file_exists($upload_dir.$datalist[$i]['image2'])) { $excelstr.="<img border=\"0\" alt=\"\" src=\"".$display_dir.$datalist[$i]['image2']."\" />"." \t "; } else {$excelstr.="  "." \t ";  }if($datalist[$i]['image3'] && file_exists($upload_dir.$datalist[$i]['image3'])) { $excelstr.="<img border=\"0\" alt=\"\" src=\"".$display_dir.$datalist[$i]['image3']."\" />"." \t "; } else {$excelstr.="   "." \t ";  }if($datalist[$i]['image4'] && file_exists($upload_dir.$datalist[$i]['image4'])) { $excelstr.="<img border=\"0\" alt=\"\" src=\"".$display_dir.$datalist[$i]['image4']."\" />"." \t "; } else {$excelstr.="   "." \t ";  }if($datalist[$i]['image5'] && file_exists($upload_dir.$datalist[$i]['image5'])) { $excelstr.="<img border=\"0\" alt=\"\" src=\"".$display_dir.$datalist[$i]['image5']."\" />"." \t "; } else {$excelstr.="   "." \t ";  }$excelstr.=$datalist[$i]['projectComments']." \n ";	
	}
	echo $excelstr;die();
*/
/*
header("Content-type: application/octet-stream");
//header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=$export_file");
//header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header("Pragma: no-cache");
header("Expires: 0");
*/
echo '<table width="100%" cellspacing="1" cellpadding="1" border="0">';
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
			if($datalist[$i]['image1'] && file_exists($upload_dir.$datalist[$i]['image1'])) echo '<img border="0" alt="" src="'.$display_dir.$datalist[$i]['image1'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image2'] && file_exists($upload_dir.$datalist[$i]['image2'])) echo '<img border="0" alt="" src="'.$display_dir.$datalist[$i]['image2'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image3'] && file_exists($upload_dir.$datalist[$i]['image3'])) echo '<img border="0" alt="" src="'.$display_dir.$datalist[$i]['image3'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image4'] && file_exists($upload_dir.$datalist[$i]['image4'])) echo '<img border="0" alt="" src="'.$display_dir.$datalist[$i]['image4'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">';
			if($datalist[$i]['image5'] && file_exists($upload_dir.$datalist[$i]['image5'])) echo '<img border="0" alt="" src="'.$display_dir.$datalist[$i]['image5'].'" />';
		echo '&nbsp;</td>';
		echo '<td class="grid001">'.$datalist[$i]['projectComments'].'</td>';	
		echo "</tr>";
	}
echo "</table>";
?>