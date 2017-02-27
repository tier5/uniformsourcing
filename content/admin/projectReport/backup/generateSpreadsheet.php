<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$path = $mydirectory."/uploadFiles/reports/";
$filename = "projectReport_csvfile.csv";
$fullPath = $path.$filename;
$return_arr = array();

$return_arr['fileName'] = "";
if(file_exists($fullPath))
{
	@ chmod($fullPath,0777);
	@ unlink($fullPath);
}
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
$search_sql = "";
$toDate= "";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
		$search_sql=' and p.client ='.$_REQUEST['cid'].' ';
		$search_uri="?cid=".$_REQUEST['cid'];
	}
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="") {
		$search_sql .=' and p.pid ='.$_REQUEST['pid'].' ';
		if($search_uri)  {
			 $search_uri.="&pid=".$_REQUEST['pid'];
		} else {
			$search_uri="?pid=".$_REQUEST['pid'];
		}
	}
	if(isset($_REQUEST['manager_id']) && $_REQUEST['manager_id']!="") {
		$search_sql .=' and p.project_manager ='.$_REQUEST['manager_id'].' ';
		if($search_uri)  {
			 $search_uri.="&manager_id=".$_REQUEST['manager_id'];
		} else {
			$search_uri="?manager_id=".$_REQUEST['manager_id'];
		}
		$_SESSION['search_uri']= $search_uri;
	}	
	if(isset($_REQUEST['project_manager']) && $_REQUEST['project_manager']!="") {
		$search_sql .=' and p.project_manager ='.$_REQUEST['project_manager'].' ';
		if($search_uri)  {
			 $search_uri.="&project_manager=".$_REQUEST['project_manager'];
		} else {
			$search_uri="?project_manager=".$_REQUEST['project_manager'];
		}
		$_SESSION['search_uri']= $search_uri;
	}	
	if(isset($_REQUEST['vendorId']) && $_REQUEST['vendorId']!="") {
		$search_sql .=' and pv.vid ='.$_REQUEST['vendorId'].' ';
		if($search_uri)  {
			 $search_uri.="&vendorId=".$_REQUEST['vendorId'];
		} else {
			$search_uri="?vendorId=".$_REQUEST['vendorId'];
		}
	}
	if(isset($_REQUEST['purchase']) && $_REQUEST['purchase']!="") {
		$search_sql .=' and pr.purchaseorder  LIKE \'%' .$_REQUEST['purchase'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&purchase=".$_REQUEST['purchase'];
		} else {
			$search_uri="?purchase=".$_REQUEST['purchase'];
		}
	}
	if(isset($_REQUEST['style']) && $_REQUEST['style']!="") {
		$search_sql .=' and tbl_prj_style.style  LIKE \'%' .$_REQUEST['style'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&style=".$_REQUEST['style'];
		} else {
			$search_uri="?style=".$_REQUEST['style'];
		}
	}
	if(isset($_REQUEST['color']) && $_REQUEST['color']!="") {
		$search_sql .=' and p.color  LIKE \'%' .$_REQUEST['color'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&color=".$_REQUEST['color'];
		} else {
			$search_uri="?color=".$_REQUEST['color'];
		}
	}
	if(isset($_REQUEST['sampleNumber']) && $_REQUEST['sampleNumber']!="") {
		$search_sql .=' and sm.id =' .$_REQUEST['sampleNumber'];
		if($search_uri)  {
			 $search_uri.="&sampleNumber=".$_REQUEST['sampleNumber'];
		} else {
			$search_uri="?sampleNumber=".$_REQUEST['sampleNumber'];
		}
	}

/* Query to search all records within the date limit*/
if(isset($_REQUEST['fromDate']) && isset($_REQUEST['toDate']))
{
	if($_REQUEST['fromDate']!="")
	{
		$toDate= $_REQUEST['toDate'];
		if($toDate=="")
			$toDate=date('m/d/Y',date('U'));
			
	$sql='select DISTINCT(p.projectname),p.pid,c."client",pr.purchaseorder,pr.pt_invoice,pr.purchaseduedate,tbl_prj_style.style,v."vendorName" from tbl_newproject as p
  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prj_style on tbl_prj_style.prj_style_id = (select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = p.pid order by tbl_prj_style.prj_style_id desc limit 1) left join tbl_prjvendor pv on pv.pid=p.pid left join vendor as v on v."vendorID"=pv.vid left join tbl_prj_sample as sm on sm.pid =p.pid where p.status=1 '.$search_sql.' and p.created_date between '.strtotime($_REQUEST['fromDate']).' and '.$toDate;
	}
	else
	{
		$sql=' select DISTINCT(p.projectname),p.pid,c."client",pr.purchaseorder,pr.pt_invoice,pr.purchaseduedate,tbl_prj_style.style,v."vendorName" from tbl_newproject as p
  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prj_style on tbl_prj_style.prj_style_id = (select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = p.pid order by tbl_prj_style.prj_style_id desc limit 1) left join tbl_prjvendor pv on pv.pid=p.pid left join vendor as v on v."vendorID"=pv.vid left join tbl_prj_sample as sm on sm.pid =p.pid where p.status=1 '.$search_sql;
	}
}
else
{
	$sql=' select DISTINCT(p.projectname),p.pid,c."client",pr.purchaseorder,pr.pt_invoice,pr.purchaseduedate,tbl_prj_style.style,v."vendorName" from tbl_newproject as p
  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prj_style on tbl_prj_style.prj_style_id = (select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = p.pid order by tbl_prj_style.prj_style_id desc limit 1) left join tbl_prjvendor pv on pv.pid=p.pid left join vendor as v on v."vendorID"=pv.vid left join tbl_prj_sample as sm on sm.pid =p.pid where p.status=1 '.$search_sql;

}
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result))
{
	$datalist[]=$row;
}
$content = ',,,Project Report      '."\n\n";
if(isset($_REQUEST['fromDate']))
{
$content.= 'From Date:,'.$_REQUEST['fromDate'] .",,";
}
if(isset($_REQUEST['toDate']))
{
$content.= 'To Date:,'.$toDate ."\n";
}

$file=fopen($fullPath,'w');
$content .= 'Client Name,Project Name,Purchase Order,PO Due Date,Style,Report,Vendor,Status Updates'."\n";
fwrite($file, $content);
$content ="";
for($i=0; $i < count($datalist); $i++)
{
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['client'])).'",'; 
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['projectname'])).'",'; 
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['purchaseorder'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['purchaseduedate'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['style'])).'",';
        $content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['pt_invoice'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['vendorName'])).'"'."\n";
	fwrite($file, $content);
	$content ="";
}
fclose($file);
$return_arr['fileName'] = $fullPath;
echo json_encode($return_arr);
exit; 	
?>