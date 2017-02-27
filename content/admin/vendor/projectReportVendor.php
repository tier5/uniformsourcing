<?php
require('Application.php');
if(isset($_POST['cancel'])=="Reset")
{
header("Location:projectReportVendor.php");
}
//$_SESSION['vendorName']=$_GET['vendor'];
if(isset($_GET['del']))
	{
		$pid = $_GET['del'];
		$sql = "delete from tbl_prjsample where pid=".$pid."; ";
		
		$sql .= "delete from tbl_prjpurchase where pid =".$pid."; ";
		
		$sql .= "delete from tbl_prjpricing where pid =".$pid."; ";
		
		$sql .= "delete from tbl_prjvendor where pid =".$pid."; ";
		
		$sql .= "delete from tbl_mgt_notes where pid =".$pid."; ";
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
		 header("location: projectReportVendor.php");
	}
require('../../header.php');
if($_SESSION['employeeType']==1)
echo '<script type="text/javascript">var vendorVal='.$_SESSION['vendorName'].';</script>';
else
echo '<script type="text/javascript">var vendorVal=0;</script>';

$sql = 'select DIstinct("srID"),id from "tbl_sampleRequest" as sr inner join tbl_prjsample as smp on smp."samplenumberId" = sr.id left join tbl_newproject as p on p.pid = smp.pid inner join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v."vendorID"=pv.vid where sr.status=1';
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_sample[]=$row1;
}
pg_free_result($result1);

$query1="SELECT distinct(\"ID\"), \"clientID\", c.\"client\", \"active\" ".
		 "FROM \"clientDB\" as c inner join tbl_newproject as p on p.client = c.\"ID\" where c.active ='yes' "; 
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query3="SELECT \"vendorID\",\"vendorName\" from vendor where \"vendorID\"=".$_SESSION['vendorName'];
if(!($result3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}

$row3 = pg_fetch_array($result3);
$vendorName=$row3['vendorName'];
/* The search query*/
			
include('../../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
	if(isset($_REQUEST['cid']) && $_REQUEST['cid']!=0) {
		$search_sql=' and p.client ='.$_REQUEST['cid'].' ';
		$search_uri="?cid=".$_REQUEST['cid'];
	}
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=0) {
		$search_sql .=' and p.pid ='.$_REQUEST['pid'].' ';
		if($search_uri)  {
			 $search_uri.="&pid=".$_REQUEST['pid'];
		} else {
			$search_uri.="?pid=".$_REQUEST['pid'];
		}
	}
	if(isset($_SESSION['vendorName']) && $_SESSION['vendorName']!=0) {
		$search_sql .=' and pv.vid ='.$_SESSION['vendorName'].' ';
		if($search_uri)  {
			 $search_uri.="&vendorId=".$_SESSION['vendorName'];
		} else {
			$search_uri.="?vendorId=".$_SESSION['vendorName'];
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
		$search_sql .=' and p.style  LIKE \'%' .$_REQUEST['style'].'%\' ';
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
	if(isset($_REQUEST['sampleNmbr']) && $_REQUEST['sampleNmbr']!=0) {
		$search_sql .=' and sm."samplenumberId" =' .$_REQUEST['sampleNmbr'];
		if($search_uri)  {
			 $search_uri.="&sampleNumber=".$_REQUEST['sampleNmbr'];
		} else {
			$search_uri="?sampleNumber=".$_REQUEST['sampleNmbr'];
		}
	}
			/* Query to search all records within the date limit*/
			if(isset($_REQUEST['fromDate']) && isset($_REQUEST['toDate']))
				{
					if($_REQUEST['fromDate']!="")
					{
						$toDate= strtotime($_REQUEST['toDate']);
						if($toDate=="")
							$toDate=date('U');
						$sql='select DISTINCT(p.projectname),p.pid,p.style,pr.purchaseorder,pr.purchaseduedate,sm.etaproduction,c."client",sr."srID" from tbl_newproject as p  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prjsample as sm on sm.pid =p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v."vendorID"=pv.vid left join "tbl_sampleRequest" sr on sr.id = sm."samplenumberId" where p."status" = 1'.$search_sql.' and p.created_date between '.strtotime($_REQUEST['fromDate']).' and '.$toDate;
						
							if($search_uri) 
							{
								 $search_uri.="&fromDate=".$_REQUEST['fromDate'];
								 if($_REQUEST['toDate'] == "")
									$search_uri.="&toDate=".$toDate; 
								 else
									$search_uri.="&toDate=".$_REQUEST['toDate']; 
							} 
							else 
							{
								$search_uri="?fromDate=".$_REQUEST['fromDate'];
								if($_REQUEST['toDate'] == "")
									$search_uri.="&toDate=".$toDate; 
								else
									$search_uri.="&toDate=".$_REQUEST['toDate']; 								
							}
					
					}
					else
					{
						$sql='select DISTINCT(p.projectname),p.pid,p.style,pr.purchaseorder,pr.purchaseduedate,sm.etaproduction,c."client",sr."srID" from tbl_newproject as p  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prjsample as sm on sm.pid =p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v."vendorID"=pv.vid left join "tbl_sampleRequest" sr on sr.id = sm."samplenumberId" where p."status" = 1'.$search_sql;
					}
				}
				else
				{
					$sql='select DISTINCT(p.projectname),p.pid,p.style,pr.purchaseorder,pr.purchaseduedate,sm.etaproduction,c."client",sr."srID" from tbl_newproject as p  inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase as pr on pr.pid= p.pid left join tbl_prjsample as sm on sm.pid =p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v."vendorID"=pv.vid left join "tbl_sampleRequest" sr on sr.id = sm."samplenumberId" where p."status" = 1'.$search_sql;
				}
		if(!($result=pg_query($connection,$sql))){
			print("Failed queryd: " . pg_last_error($connection));
			exit;
		}
	$items= pg_num_rows($result);
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
	if(!($result=pg_query($connection,$sql))){
		print("Failed queryd: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$datalist[]=$row;
	}
	if(isset($_POST['cancel']))
	{
		$sql="";
		$search_sql="";
		$search_uri="";
	}
?>
<center>
          <form name="frmReport" method="post" action="projectReportVendor.php">
            <table width="100%">
                <tr>
                  <td align="center"><font size="5">Report Generation <br/>
                      <br/>
                    </font>
                   <table width="95%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td width="150" height="35"><strong>Search Projects </strong></td>
                        <td width="200">&nbsp;</td>
                        <td width="10">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                            </tr>
                      <tr>
                        <td class="grid001">From Date : </td>
                        <td class="grid001"><input type="text" name="fromDate" id="fromDate" value=""  readonly="readonly"/></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">To Date : </td>
                        <td class="grid001"><input type="text" name="toDate" id="toDate" readonly="readonly"/>&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Client: </td>
                        <td class="grid001"><select  id="cid" class="cid" name="cid" >
                          <option value="0">---- Select Client ----</option>
                          <?php 
						  	for($i=0; $i<count($data1); $i++)
							{ 
						  ?>
                          <option value="<?php echo $data1[$i]['ID'];?>"><?php echo $data1[$i]['client'];?></option>
                          <?php 
							}
						  ?>
                        </select>
                        </td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Vendor:</td>
                        <td class="grid001"><input type="text" name="vendorId" id="vendorId" class="vid" readonly="readonly" value="<?php echo $vendorName;?>" /></td>
                      </tr>
                      <tr>
                        <td class="grid001">Purchase Order:</td>
                        <td class="grid001"><input type="text" name="purchase" /></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Project Name : </td>
                        <td class="grid001"><select id="pid" class="pid" name="pid" style="width:142px">
                          <option value="0">---- Select Project ----</option>
                        </select>&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Style: </td>
                        <td class="grid001"><input type="text" name="style" /></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Color:</td>
                        <td class="grid001"><input type="text" name="color" />
                      <tr>
                        <td class="grid001">Sample Number:</td>
                        <td class="grid001"><select name="sampleNmbr" id="sampleNmbr" style="width:50;" >
                          <option value="0">---Select---</option>
                          <?php
						$sampleIndex = "";
						for($i=0; $i < count($data_sample); $i++)
						{
							echo '<option value="'.$data_sample[$i]['id'].'">'.$data_sample[$i]['srID'].'</option>';
						}
?>
                        </select></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">&nbsp;</td>
                        <td colspan="3" class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                      </tr>
                        <tr>
                        <td class="grid001"><b>Report Options</b></td>
                        <td colspan="3" class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Report Includes:</td>
                        <td class="grid001">All
                          <input type="checkbox" name="all" id="all" />
Hanging
<input type="checkbox" name="hanging" id="hanging" /></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Summary:</td>
                        <td class="grid001">Price Total
                        <input type="checkbox" name="total" id="total" /></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input name="search" type="submit" onMouseOver="this.style.cursor = 'pointer';" value="Search">
                        <input name="cancel" type="submit"  onMouseOver="this.style.cursor = 'pointer';" value="Reset"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                    </td>
                </tr>
              </table>
              </form>
                    
                  <table width="95%" cellspacing="1" cellpadding="1" border="0" class="no-arrow rowstyle-alt">

                    <thead>
                    <tr class="sortable"> 
                            <th class="sortable">Client Name </th>
                            <th class="sortable">Project Name</th>
                            <th class="sortable">Purchase Order</th>
                            <th class="sortable-numeric">PO Due Date</th>
                            <th class="sortable">Style</th>
                            <th class="sortable">Vendor</th>
                            <th class="sortable">Production ETA </th>
                            <th class="gridHeader">Close</th>
                            <th class="gridHeader">Deactivate</th>
                          </tr>
                          </thead>
                          <tbody>
							  <?php 
                    if(count($datalist)) 
                    {
                        for($i=0; $i < count($datalist); $i++)
                        {
                          ?><tr>
                            <td class="grid001"><?php echo $datalist[$i]['client'];?></td>
                            <td class="grid001"><a href="<?php echo $mydirectory;?>/admin/vendor/project_mgm.add.php?id=<?php echo $datalist[$i]['pid'];?>"><?php echo $datalist[$i]['projectname'];?></a></td>
                            <td class="grid001"><?php echo $datalist[$i]['purchaseorder'];?></td>
                            <td class="grid001"><?php echo $datalist[$i]['purchaseduedate'];?></td>
                            <td class="grid001"><?php echo $datalist[$i]['style'];?></td>
                            <td class="grid001"><?php echo $datalist[$i]['vendorName'];?></td>
                            <td class="grid001"><?php echo $datalist[$i]['etaProduction'];?></td>
                          	<td class="grid001"><a href="projectReportVendor.php?close=<?php echo $datalist[$i]['pid'];?>"><img src="<?php echo $mydirectory;?>/images/close.png" alt="close" width="32" height="32"></a></td>
                            <td class="grid001"><a href="projectReportVendor.php?del=<?php echo $datalist[$i]['pid'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" alt="deactivate" width="24" height="24"></a></td>
                           </tr>
                    <?php } ?>
                       </tbody>
                                <tr>
                                <td width="100%" class="grid001" colspan="10"><?php echo $p->show();?></td>			
                              </tr>	
                    <?php } 
                    else 
                    {?>
                        <tr>
                        <td align="left" colspan="10"><font face="arial"><b>No Project Found</b></font></td>
                        </tr>
                    <?php }?> 
                    </table>
                   
          </center>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min-1.4.2.js"></script>       
<script type="text/javascript">
$(document).ready(function()
{
	$(".cid").change(function()
	{
		if(document.getElementById('cid').value)
		{
			var id=$(this).val();
			var dataString = 'clientid='+ id+'&vendorid='; 
			//var vendorVal=document.getElementById('vendorId').value;
			if(vendorVal)
			dataString += vendorVal;
			else
			dataString += "";
			
			$.ajax
			({
				type: "POST",
				url: "projectNameList.php",
				data: dataString,
				cache: false,
				success: function(html)
				{
				$(".pid").html(html);
				} 
			});
		}
	});
	
		if(vendorVal)
		{
			var id=vendorVal;
			var dataString = 'vendorid='+ id +'&client='; 
			var clientVal=document.getElementById('cid').value;
			if(clientVal)
			dataString += clientVal;
			else
			dataString += "";
			if(clientVal=="")
				{
						$.ajax
					({
						type: "POST",
						url: "clientNameList.php",
						data: dataString,
						cache: false,
						success: function(html)
						{
						$(".cid").html(html);
						}
					});
				}
		}
});
</script>
   <?php 
   	require('../../trailer.php');
   ?>