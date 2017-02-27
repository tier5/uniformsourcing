<?php
require('Application.php');
$is_session = 0;
$emp_join ="";
$emp_id= "";
$emp_sql = "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_id =  $_SESSION['employee_type_id'];
	$emp_sql = ' and vendor."vendorID" ='.$emp_id;
	$is_session = 1;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_id =$_SESSION['employee_type_id'];
	$emp_sql = ' and c."ID" ='.$emp_id;
	$is_session = 1;
}
if(isset($_GET['del']))
{
	$id = $_GET['del'];
	$sql = "Update tbl_prj_sample SET status = 0 ,is_archive =1, modified_date = ".date('U')." where id = $id";
	if(!($result1=pg_query($connection,$sql)))
	{
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	header('location:oldsamplerequest.list.php');
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

$mainQuery='select s.id,s."srID",s.cid,s.vid,s.size,s.dateneeded,s.sample_name,s.ordered_date,s.brief_desc,s."styleNo" as style,s.detail_description, s.color, s."fabricType", s."cost", s."customerTargetprice",s.customer_po,s.invoicenumber,c."client",sn.notes,vendor."vendorName",upl.uploadtype,upl.uploadid,upl.filename from "tbl_sampleRequest" s left join "clientDB" c on s."cid"=c."ID" left join "tbl_sampleNotes" sn on sn."notesId" = (select  n1."notesId" from "tbl_sampleNotes" as n1 where n1."sampleId"=s.id order by n1."notesId" desc limit 1) left join vendor on vendor."vendorID" = s.vid left join tbl_sample_uploads as upl on upl.uploadid = (select n1.uploadid from tbl_sample_uploads as n1  where n1.sampleid=s.id and n1.uploadtype=\'I\' order by n1.uploadid desc limit 1) where s."status"=1 '.$search_sql.$emp_sql.' order by s.id desc';
$sql = $mainQuery;
//echo $mainQuery;
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
            <table width="100%" id="tblsamplerequest">
                <tr>
                  <td align="center" valign="top">
                      <font size="5">View S</font><font size="5">ample Request form <br />
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
                          <td align="right" valign="top"><input type="button" name="archieve_btn" id="archieve_btn" value="View Archived Requests" onclick="javascript:location.href='samplerequest.archive.list.php'" /></td>
                        </tr>
                    </table>
					<form name="frmsamplerequestSearch" method="post" action="">
					<table width="100%" cellspacing="1" cellpadding="1" border="0">
                    <tr>
                      <td height="35" colspan="5" ><strong>Search Projects </strong></td>
                    </tr>
                    <tr>
                      <td width="11%" class="grid001">Client Name: </td>
                      <td width="62%" class="grid001"><select name="cid" id="cid" style="width:250px">
                          <option value="">Select</option>
                          <?php for($i=0; $i < count($data1); $i++){?>
                          <option value="<?php echo $data1[$i]['ID'];?>"><?php echo $data1[$i]['client'];?></option>
                          <?php }?>
                      </select></td>
                      <td width="8%" class="grid001"><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="Search"  /></td>
                      <td width="8%" class="grid001"><input type="reset" value="Cancel" onmouseover=
						"this.style.cursor = 'pointer';" name="button2" id="cancel_btn" onclick="javascript:location.href='samplerequest.list.php'" /></td>
                      <td width="11%" class="grid001">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"></td>
                    </tr>
                  </table>
				  </form>
					<form name="frmsamplerequest" method="post" action="">
                    <table width="98%" border="0">
                        <tr>
                          <td colspan="3" rowspan="2" align="left" valign="top">
						  <table width="100%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                              <td height="25">Final Customer : </td>
                              <td height="25" colspan="3"><input name="finalCustomer" id="finalCustomer" type="text" class="textBox" /></td>
                            </tr>
                            <tr>
                              <td height="25">Sales Executive : </td>
                              <td height="25" colspan="3"><input name="salesExecutive" id="salesExecutive" type="text" class="textBox" /></td>
                            </tr>
                            <tr>
                              <td height="25">Quantity of people Required : </td>
                              <td><input name="quanPeople" id="quanPeople" type="text" class="textBox" /></td>
                              <td>Costing Required: </td>
                              <td><input name="costing" id="costing" type="text" class="textBox" /></td>
                            </tr>
                          </table>                            
                          </td>
                          <td width="150" height="25" align="right" valign="top">Date of request:</td>
                          <td width="100" align="right" valign="top"><input  name="requestDate" id="requestDate"type="text" class="textBox" value="" readonly="readonly" /></td>
                        </tr>
                        <tr>
                          <td height="25" align="right" valign="top">Requested Delivery Date: </td>
                          <td align="right" valign="top"><input name="deliveryDate" id="deliveryDate" readonly="readonly" type="text" class="textBox" value="" /></td>
                        </tr>
                        <tr>
                          <td width="100" height="25" align="left" class="headerblack">Reference Article </td>
                          <td align="left">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <td align="right">&nbsp;</td>
<!--                          <td align="right" valign="top"><input name="btnDelete" type="button" onmouseover="this.style.cursor = 'pointer';" value="Delete Record" onclick="javascript: if(confirm('Do you want to delete selected records')) { document.frmsamplerequestlist.submit(); } else { return false; }" /></td>-->
                        </tr>
                    </table>
					</form>
					<?php
					if(count($datalist)) {
					?>
					<form name="frmsamplerequestlist" method="post" action="samplerequest.list.php">
                    <table width="100%" cellspacing="1" cellpadding="0" class="no-arrow rowstyle-alt">
                      <thead>
                      <tr class="sortable" >
                      <th align="left" valign="middle" class="sortable">Sample ID</th>
                        <th height="25" align="left" valign="middle"  class="sortable">Style # </th>
                        <th align="left" valign="middle" class="sortable">Sample Name</th>
                        <th align="left" valign="middle" class="sortable">Client Name </th>
                        <th align="left" valign="middle" class="sortable">Vendor </th>
                        <th align="left" valign="middle" class="sortable">Sample Description</th>
                        <th align="left" valign="middle" class="sortable">Date Needed</th>
                        <th align="left" valign="middle" class="sortable">PO</th>
                        <th align="left" valign="middle" class="sortable">PT Invoice </th>
                        <th align="left" valign="middle" class="sortable">Ordered Date </th>
                         <th align="left" valign="middle" class="headerblack">Archive</th>
                      </tr>
                      </thead>
                      <tbody id="desc">
					  <?php
					  	$count=0;
					  	for($i=0; $i < count($datalist); $i++)
						{
							
					  ?>
                      <tr>
                        <td height="25" align="right" valign="top" class="grey"><a href="samplerequest_new.add.php?id=<?php echo $datalist[$i]['id'];?>"><?php echo $datalist[$i]['srID'];?></a></td>
                        <td class="green"><?php echo $datalist[$i]['style'];?></td>
                        <td class="green"><?php echo $datalist[$i]['sample_name'];?></td>					
						<td class="green"><?php echo $datalist[$i]['client'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['vendorName'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['brief_desc'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['dateneeded'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['customer_po'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['invoicenumber'];?></td>
                        <td align="left" valign="top" class="green"><?php if($datalist[$i]['ordered_date']!=""){ echo date('m/d/y',$datalist[$i]['ordered_date']);} else {?>&nbsp;<?php }?></td>
                       <td align="left" valign="top" class="green"><a href="samplerequest.list.php?del=<?php echo $datalist[$i]['id'];?>" onclick="javascript: if(confirm('Are you sure you want to archeive the sample request')) { return true; } else { return false; }"><img src="<?php echo $mydirectory;?>/images/archive.png" border="0"></a></td>
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
					if($count) {					
				  ?>
                  	
                      <table width="95%" border="0">
                        <tr>
                          <td align="center"><p></p></td>
                          <td align="center">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <script>
						  <?php if($search_sql!="")
						  {
?>
						  var printReq = 'samplerequest.print.php?query=<?php echo $search_sql;?>';
                          <?php
						  }
						  else
						  {
						  ?>
						   var printReq = 'samplerequest.print.php';
						    <?php
						  }
						  ?>
                          </script>
                          <td align="right" valign="top"><input name="btnSubmitEmail" type="button" id="send-email" onmouseover="this.style.cursor = 'pointer';fnsetval();" value="Submit by Email"  />
                              <input name="btnPrint" id="btnPrint" type="button" value="Print Preview" onclick="javascript:alert(printReq);return window.open(printReq);" /></td>
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

	$("#cancel").click(function(){$(location).attr('href',"samplerequest.list.php");
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