<?php
require('Application.php');
if(isset($_GET['del']))
{
	$id = $_GET['del'];
	$sql .="Delete from tbl_prj_sample_po_items where sample_id = $id;";
	$sql .="Delete from tbl_prj_sample_po where sample_id = $id;";
	$sql .="Delete from tbl_prjsample_uploads where sample_id = $id;";
	$sql .= "DELETE from tbl_prj_sample where id = $id;";
	if(!($result1=pg_query($connection,$sql)))
	{
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	header('location:samplerequest.archive.list.php');
}
if(isset($_GET['del']))
{
	$id = $_GET['del'];
	$sql = "Update \"tbl_sampleRequest\" SET is_archive = 1 , \"modifiedDate\" = ".date('U')." where id = $id";
	if(!($result1=pg_query($connection,$sql)))
	{
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	header('location:samplerequest.list.php');
}
require('../../header.php');
if(isset($_SESSION['errorMsg']) && $_SESSION['errorMsg']!="")
{
	$errorMsg = $_SESSION['errorMsg'];
}

echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.js"></script>';
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

$error="";
$success="";
/*
if(isset($_REQUEST['act']) && $_REQUEST['act']=="delete")	 {
	//print '<pre>';print_r($_POST);print '</pre>';
	if(isset($_REQUEST['chksreq']) && count($_REQUEST['chksreq'])) {
		$strSreq=implode(",",$_REQUEST['chksreq']);
		$upSql='update"tbl_sampleRequest" set "status"=0 , "modifiedDate"='.date('U').' where "id" in ('.$strSreq.')';
		//echo ( $upSql);die();
		if(!($result4=pg_query($connection,$upSql))){
			print("Failed upSql: " . pg_last_error($connection));
			exit;
		}
		$success="Selected  Sample request deleted";
	}  else {
		$error="Please Select atleast one check box to delete Sample request";
	}
}*/
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") 
{
	$search_sql=' and s."cid" ='.$_REQUEST['cid'].' ';
	$search_uri="?cid=".$_REQUEST['cid'];
}

$mainQuery='select s.id,s.pid,s.sample_id,s.vid,s.size_requested,s.dateneeded,s.brief_desc,s.style_number as style,s.detail_description, s.sample_color, s.fabric, s.fabric_cost, s.quote_price,s.customer_po,s.invoicenumber,prj.client,sn.notes,vendor."vendorName",upl.uploadtype,upl.upload_id,upl.filename,cl.client from tbl_prj_sample s  left join tbl_prjsample_notes sn on sn.notes_id = (select n1.notes_id from tbl_prjsample_notes as n1 where n1.sample_id=s.id order by n1.notes_id desc limit 1) left join vendor on vendor."vendorID" = s.vid left join tbl_prjsample_uploads as upl on upl.upload_id =(select n1.upload_id from tbl_prjsample_uploads as n1 where n1.sample_id=s.id and n1.uploadtype=\'I\' order by n1.upload_id desc limit 1) left join tbl_newproject as prj on prj.pid = s.pid  left join "clientDB" as cl on cl."ID"=prj.client where s.is_archive= 1 order by s.modified_date  desc';
$sql = $mainQuery;
//echo $sql;
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($resultp);
if($items > 0) {
	include('../../pagination.class.php');
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
<center>
<?php 
	if($errorMsg)
	{
?>
        <div align="center" style="color:#F00"><h3><?php echo $errorMsg;?></h3></div>

<?php
		unset($_SESSION['errorMsg']);
	}
?>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='samplerequest.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
            <table width="100%" id="tblsamplerequest">
                <tr>
                  <td align="center" valign="top">
                      <font size="5">View Archived Samples<br />
                      <br>
                      </font>
                      <table width="95%" border="0">
					   <?php if($success) {
					   ?>
					   <tr><td colspan="4" align="center" style="color:Red;"><h3><?php echo $success;?></h3></td></tr>
					   <?php
					   }?>
					   <?php if($error) {
					   ?>
					   <tr><td colspan="4" align="center"  style="color:Red;"><h3><?php echo $error;?></h3></td></tr>
					   <?php
					   }?>
					   <?php if(isset($_GET['msg']))
					   {
					  	 $msg="";
					   	if($_GET['msg']=='s') 
						{
							$msg="The updation was successfull";
						} elseif($_GET['msg']=='c') 
						{
							$msg="The edit cannot be done";
						}
					   ?>
					   <tr><td colspan="4" align="center"  style="color:Red;"><h3><?php echo $msg;?></h3></td></tr>
					   <?php
					   }?>
                        <tr>
                        
                          <td align="center">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <td align="right" valign="top">&nbsp;</td>
                        </tr>
                    </table>
					<?php
					if(count($datalist)) {
					?>
					<form name="frmsamplerequestlist" method="post" action="samplerequest.archive.list.php">
                    <table width="100%" cellspacing="1" cellpadding="0" class="no-arrow rowstyle-alt">
                      <thead>
                      <tr class="sortable" >
                      <th align="left" valign="middle" class="sortable">Sample ID</th>
                        <th height="25" align="left" valign="middle"  class="sortable">Style # </th>
                        <th align="left" valign="middle" class="sortable">Client Name </th>
                        <th align="left" valign="middle" class="sortable">Vendor </th>
                        <th align="left" valign="middle" class="sortable">Sample Description</th>
                        <th align="left" valign="middle" class="sortable">Date Needed</th>
                        <th align="left" valign="middle" class="sortable">PO</th>
                        <th align="left" valign="middle" class="sortable">PT Invoice </th>
                         <th align="left" valign="middle" class="headerblack">Delete </th>
                      </tr>
                      </thead>
                      <tbody id="desc">
					  <?php
					  	$count=0;
					  	for($i=0; $i < count($datalist); $i++)
						{
							
					  ?>
                      <tr>
                        <td height="25" align="right" valign="top" class="grey"><a href="../project_mgm/project_mgm.add.php?id=<?php echo $datalist[$i]['id'];?>&page=arc"><?php echo $datalist[$i]['sample_id'];?></a></td>
                        <td class="green"><?php echo $datalist[$i]['style'];?></td>					
						<td class="green"><?php echo $datalist[$i]['client'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['vendorName'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['brief_desc'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['dateneeded'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['customer_po'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['invoicenumber'];?></td>                        
                       <td align="left" valign="top" class="green"><a href="samplerequest.archive.list.php?del=<?php echo $datalist[$i]['id'];?>" onclick="javascript: if(confirm('Are you sure you want to DELETE the sample request')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/close.png" border="0"></a></td>
                      </tr> 
					  <?php
					  	 $count++;
					  	}
					  ?>                     
                      </tbody><tr>
                        <td align="right">&nbsp;</td>
                        <td height="25" align="center" colspan="4"><strong>QUANTITY OF
                        GARMENTS:</strong></td>
                        <td>&nbsp;<?php echo count($datalist);?></td>
                        <td align="left">&nbsp;</td>
                        <td align="left">&nbsp;</td>
                        <td align="left" colspan="7"><?php echo $p->show(); ?></td>                        
                      </tr>					  
                  </table>	
				   
				  </form>
				  <?php
				  	}else {
				  ?>
				  	<table width="95%" border="0">
                        <tr>
                          <td align="center"><p style="color:red; font-weight:bold;">There is no sample request available.</p></td>                         
                        </tr>
                      </table>
				  <?php
					}										
				  ?>                  	
			  	</td>
                </tr>
              </table>
			  
              <p></p>
          </center>    
          <div id="dialog-form" title="Submit By Email">
			<p class="validateTips">All form fields are required.</p>       	
			<form action='sendMail.php?query=<?php echo $search_sql;?>' id="frmmailsendform" method="POST">
			<fieldset>				
				<label for="email">Email</label>
				<input type="text" name="email" id="email"  value="" class="text ui-widget-content ui-corner-all" />
				<label for="subject">Subject:</label>
				<input type="text" name="subject" id="subject" class="text ui-widget-content ui-corner-all" />				
			</fieldset>
			<input type="hidden" name="finalCustomer2" id="finalCustomer2" />
			<input type="hidden" name="salesExecutive2" id="salesExecutive2" />
			<input type="hidden" name="quanPeople2" id="quanPeople2" />
			<input type="hidden" name="costing2" id="costing2" />
			<input type="hidden" name="requestDate2" id="requestDate2" />
			<input type="hidden" name="deliveryDate2" id="deliveryDate2" />
			</form>
		</div>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/projectadd.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/qtip.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	 $('#desc a[rel]').each(function()
   {
      $(this).qtip(
      {
         content: {
            text: '<img src="<?php echo $mydirectory;?>/images/loading.gif" alt="Loading..." />',
            url: $(this).attr('rel'),
            title: {
               text: ':: Sample Request ::', 
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

	$("#cancel").click(function(){$(location).attr('href',"samplerequest.archive.list.php");
					 });
});
</script>   
<!--
<div id="dialog-print" title="">
	<?php /*echo $print; echo $message;*/ ?>		
</div>-->
<?php
require('../../trailer.php');
?>