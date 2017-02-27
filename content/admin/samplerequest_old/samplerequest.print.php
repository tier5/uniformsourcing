<?php
require('Application.php');
$upload_dir			= "../../projectimages/";
$is_session = 0;
$emp_join ="";
$emp_id= "";
$emp_sql = "";
$search_sql = "";
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
echo "<title>$compname Internal Intranet</title>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/style.css\" media=\"all\"></link>";
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min-1.4.2.js"></script>';
if(isset($_GET['query']))
	$search_sql = $_GET['query'];
$sql='select s.id,s."srID",s.cid,s.vid,s.size,s.dateneeded,s.sample_name,s.ordered_date,s.brief_desc,s."styleNo" as style,s.detail_description, s.color, s."fabricType", s."cost", s."customerTargetprice",s.customer_po,s.invoicenumber,c."client",sn.notes,vendor."vendorName",upl.uploadtype,upl.uploadid,upl.filename from "tbl_sampleRequest" s inner join "clientDB" c on s."cid"=c."ID" left join "tbl_sampleNotes" sn on sn."notesId" = (select  n1."notesId" from "tbl_sampleNotes" as n1 where n1."sampleId"=s.id order by n1."notesId" desc limit 1) inner join vendor on vendor."vendorID" = s.vid left join tbl_sample_uploads as upl on upl.uploadid = (select n1.uploadid from tbl_sample_uploads as n1  where n1.sampleid=s.id and n1.uploadtype=\'I\' order by n1.uploadid desc limit 1) where s."status"=1'.$search_sql.$emp_sql.' order by s."modifiedDate"  desc';
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultp)){
	$datalist[]=$rowd;
}
?>
<center>
            <table width="100%" id="tblsamplerequest">
                <tr>
                  <td align="center" valign="top"><font face="arial">
                      <font size="5">View S</font><font size="5">ample Request form <br />
                      <br>
                      </font>
                      <table width="95%" border="0">
					     <tr>
                          <td align="left" valign="top"></td>
                          <td align="center">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <td align="right" valign="top"></td>
                        </tr>
                    </table>
					<form name="frmsamplerequestSearch" method="post" action="">
                    <table width="98%" border="0">
                        <tr>
                          <td colspan="3" rowspan="2" align="left" valign="top">
						  <table width="100%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                              <td height="25">Final Customer : </td>
                              <td height="25" colspan="3"><input name="finalCustomer1" id="finalCustomer1" type="text" class="textBox" /></td>
                            </tr>
                            <tr>
                              <td height="25">Sales Executive : </td>
                              <td height="25" colspan="3"><input name="salesExecutive1" id="salesExecutive1" type="text" class="textBox" /></td>
                            </tr>
                            <tr>
                              <td height="25">Quantity of people Required : </td>
                              <td><input name="quanPeople1" id="quanPeople1" type="text" class="textBox" /></td>
                              <td>Costing Required: </td>
                              <td><input name="costing1" id="costing1" type="text" class="textBox" /></td>
                            </tr>
                          </table>                            
                          </td>
                          <td width="150" height="25" align="right" valign="top">Date of request:</td>
                          <td width="100" align="right" valign="top"><input  name="requestDate1" id="requestDate1"type="text" class="textBox" value="" readonly="readonly" /></td>
                        </tr>
                        <tr>
                          <td height="25" align="right" valign="top">Requested Delivery Date: </td>
                          <td align="right" valign="top"><input name="deliveryDate1" id="deliveryDate1" readonly="readonly" type="text" class="textBox" value="" /></td>
                        </tr>
                        <tr>
                          <td width="100" height="25" align="left" class="headerblack">Reference Article </td>
                          <td align="left">&nbsp;</td>
                          <td align="center">&nbsp;</td>
                          <td align="right">&nbsp;</td>
                          <td align="right" valign="top"></td>
                        </tr>
                    </table>
					</form>
					<?php
					if(count($datalist)) {
					?>
					<form name="frmsamplerequestlist" method="post" action="samplerequest.list.php">
                      <table width="95%" border="0" cellspacing="1" cellpadding="1"  id="tblsamplerequest">
                      <tr>
                        <td height="25" align="left" valign="middle" class="headerblack">Sample ID</td>
                        <td align="left" valign="middle" class="headerblack">Style # </td>
                        <td align="left" valign="middle" class="headerblack">Sample Name</td>
                         <td align="left" valign="middle" class="headerblack">Client</td>
                        <td align="left" valign="middle" class="headerblack">Vendor </td>
                        <td align="left" valign="middle" class="headerblack">Sample Description</td>
                        <td align="left" valign="middle" class="headerblack">Date Needed</td>
                        <td align="left" valign="middle" class="headerblack">PO</td>
                        <td align="left" valign="middle" class="headerblack">PT Invoice </td>
                        <td align="left" valign="middle" class="headerblack">Ordered Date </td>
                      </tr>
					  <?php
					  	$count=0;
					  	for($i=0; $i < count($datalist); $i++){
					  ?>
                      <tr>                        
                      <td height="25" align="right" valign="top" class="grey"><?php echo $datalist[$i]['srID'];?></td>
                        <td class="green"><?php echo $datalist[$i]['style'];?></td>
                        <td class="green"><?php echo $datalist[$i]['sample_name'];?></td>					
						<td class="green"><?php echo $datalist[$i]['client'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['vendorName'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['brief_desc'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['dateneeded'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['customer_po'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['invoicenumber'];?></td>
                        <td align="left" valign="top" class="green"><?php if($datalist[$i]['ordered_date']!=""){ echo date('m/d/y',$datalist[$i]['ordered_date']);} else {?>&nbsp;<?php }?></td>
                       </tr> 
					  <?php
					  	 $count++;
					  	}
					  ?>                     				  
                  </table>	
				  <input type="hidden" value="delete" name="act" id="act" />			  
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
				  ?>  </font>                
			  	</td>
                </tr>
              </table>			  
              <p></p>
          </center>
		  <script language="javascript">
			if(window.opener.document.getElementById('finalCustomer')) {
				$('#finalCustomer1').val(window.opener.document.getElementById('finalCustomer').value);
			}
			if(window.parent.document.getElementById('finalCustomer')) {
				$('#finalCustomer1').val(window.parent.document.getElementById('finalCustomer').value);
			}
		   	if(window.opener.document.getElementById('salesExecutive')) {
				$('#salesExecutive1').val(window.opener.document.getElementById('salesExecutive').value);
			}
			if(window.parent.document.getElementById('salesExecutive')) {
				$('#salesExecutive1').val(window.parent.document.getElementById('salesExecutive').value);
			}
			if(window.opener.document.getElementById('quanPeople')) {
				$('#quanPeople1').val(window.opener.document.getElementById('quanPeople').value);
			}
			if(window.parent.document.getElementById('quanPeople')) {
				$('#quanPeople1').val(window.parent.document.getElementById('quanPeople').value);
			}
			if(window.opener.document.getElementById('costing')) {
				$('#costing1').val(window.opener.document.getElementById('costing').value);
			}
			if(window.parent.document.getElementById('costing')) {
				$('#costing1').val(window.parent.document.getElementById('costing').value);
			}
			if(window.opener.document.getElementById('requestDate')) {
				$('#requestDate1').val(window.opener.document.getElementById('requestDate').value);
			}
			if(window.parent.document.getElementById('requestDate')) {
				$('#requestDate1').val(window.parent.document.getElementById('requestDate').value);
			}
			if(window.opener.document.getElementById('deliveryDate')) {
				$('#deliveryDate1').val(window.opener.document.getElementById('deliveryDate').value);
			}
			if(window.parent.document.getElementById('requestDate')) {
				$('#deliveryDate1').val(window.parent.document.getElementById('deliveryDate').value);
			}
			window.print();
		  </script>