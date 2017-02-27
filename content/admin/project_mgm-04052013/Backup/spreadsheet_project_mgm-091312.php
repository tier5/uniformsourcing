<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$path = $mydirectory."/uploadFiles/reports/";
$filename = "project_mgm_csvfile.csv";
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
}
if(isset($_REQUEST['manager_id']) && $_REQUEST['manager_id']!="")
{
	$search_sql .=' and prj.project_manager ='.$_REQUEST['manager_id'].' ';
	if($search_uri) 
	{
		 $search_uri.="&manager_id=".$_REQUEST['manager_id'];
	} 
	else
	{
		$search_uri.="?manager_id=".$_REQUEST['manager_id'];
	}
	$_SESSION['search_uri']= $search_uri;
}	

/* Query to search all records within the date limit*/


/*$sql = 'select Distinct(prj.projectname), prj.bid_number,prj.project_budget,c.client,prj.pid,prj.order_placeon,prj.status,ship.tracking_number ,'
.'emp.firstname,emp.lastname,prch.purchaseorder,prc.prjquote,prc.prjcost,prc.prj_completioncost,prc.prj_estimatecost,'
        .' prc.prj_completioncost,pro.prdtntrgtdelvry from tbl_newproject as prj left join tbl_prjpurchase as prch on prch.pid = prj.pid'
        
        .' left join tbl_prjorder_shipping as ship on ship.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join'
  .' tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1)'
       
        .' left join tbl_prjpricing as prc on prc.pid = prj.pid left join "employeeDB" as emp on emp."employeeID"= prj.project_manager'
        .' left join tbl_prmilestone as pro on pro.pid = prj.pid  left join "clientDB" c on prj.client=c."ID" ' 
        .'  where prch.purchaseorder IS NULL and prj.status =1   ' 
        . $search_sql .  ' order by prj."pid" desc ';*/


//$sql='
 //select Distinct(prj.projectname),prj_shipping.tracking_number,c.client,prj.pid,prj.order_placeon,prj.status,emp.firstname,prch.pt_invoice,mile.prdtntrgtdelvry,prc.prjquote,prj.project_budget,prj.bid_number,vendor."vendorName" from tbl_newproject as  prj left join tbl_prmilestone as mile on mile.pid = prj.pid left join tbl_prjorder_shipping as  prj_shipping on prj_shipping.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc) left join tbl_prjpurchase as prch on prch.pid = prj.pid left join tbl_prjpricing as prc on prc.pid = prj.pid  left join "employeeDB" as emp on emp."employeeID"= prj.project_manager inner join "clientDB" c on prj.client=c."ID"  left join tbl_prjvendor as prj_vendor on prj_vendor.pid=prj.pid left join vendor on vendor."vendorID"=prj_vendor.vid where prch.purchaseorder IS NULL and prj.status =1 '.$search_sql.' order by prj."pid" desc ';


$sql='select Distinct(prj.projectname),pro.prdtntrgtdelvry,prc.targetpriceunit,prc.targetretail,c.client,prj.pid,prj.order_placeon,emp.firstname,emp.lastname,mile.prdtntrgtdelvry,prc.prjquote,prj.project_budget,tbl_carriers.weblink,prch.purchaseorder,prch.pt_invoice,prch.purchaseduedate,prj.bid_number,prj_shipping.tracking_number from tbl_newproject as  prj left join tbl_prmilestone as mile on mile.pid = prj.pid  left join tbl_prmilestone as pro on pro.pid = prj.pid  left join tbl_prjpurchase as prch on prch.pid = prj.pid left join tbl_prjpricing as prc on prc.pid = prj.pid  left join "employeeDB" as emp on emp."employeeID"= prj.project_manager inner join "clientDB" c on prj.client=c."ID" left join tbl_prjvendor as prj_vendor on prj_vendor.pid=prj.pid left join tbl_prjorder_shipping as  prj_shipping on prj_shipping.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1)left join tbl_carriers on tbl_carriers.carrier_id = prj_shipping.carrier_id where prj.status =1  and prch.purchaseorder IS NULL order by prj."pid" desc';

	//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result))
{
	$datalist[]=$row;
}
//print_r($datalist);
$content = ',,,Projects       '."\n\n";
$file=fopen($fullPath,'w');
$content .= 'Project Name,Client Name,Project Manager,Target Price Per Unit,Target Retail Price,Bid Number,Tracking Number,Target Delivery'."\n";
fwrite($file, $content);
$content ="";
for($i=0; $i < count($datalist); $i++)
{
   
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['projectname'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['client'])).'",';  
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['firstname'].$datalist[$i]['lastname'])).'",'; 
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['targetpriceunit'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['targetretail'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['bid_number'])).'",';
        $content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['tracking_number'])).'",';
	$content .= '"'.rtrim(str_replace('"','""',$datalist[$i]['prdtntrgtdelvry'])).'"'."\n";
	
	fwrite($file, $content);
	$content ="";
}
fclose($file);
$return_arr['fileName'] = $fullPath;
echo json_encode($return_arr);
exit; 	
?>