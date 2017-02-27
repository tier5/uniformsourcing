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
$sql='select s.id,s.pid,s.sample_id,s.vid,s.size_requested,s.dateneeded,s.brief_desc,s.style_number as style,s.detail_description, s.sample_color, s.fabric, s.fabric_cost, s.quote_price,s.customer_po,s.invoicenumber,prj.client,sn.notes,vendor."vendorName",upl.uploadtype,upl.upload_id,upl.filename,cl.client from tbl_prj_sample s  left join tbl_prjsample_notes sn on sn.notes_id = (select n1.notes_id from tbl_prjsample_notes as n1 where n1.sample_id=s.id order by n1.notes_id desc limit 1) left join vendor on vendor."vendorID" = s.vid left join tbl_prjsample_uploads as upl on upl.upload_id =(select n1.upload_id from tbl_prjsample_uploads as n1 where n1.sample_id=s.id and n1.uploadtype=\'I\' order by n1.upload_id desc limit 1) left join tbl_newproject as prj on prj.pid = s.pid  left join "clientDB" as cl on cl."ID"=prj.client where s.status=1'.$search_sql.$emp_sql.' order by s.modified_date  desc';
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
					<form name="frmsamplerequestlist" method="post" action="samplerequest.list.php">
					<?php
					if(count($datalist)) {
					?>
                      <table width="95%" border="0" cellspacing="1" cellpadding="1"  id="tblsamplerequest">
                      <tr>
                        <td height="25" align="left" valign="middle" class="headerblack">Sample ID</td>
                        <td align="left" valign="middle" class="headerblack">Style # </td>
                         <td align="left" valign="middle" class="headerblack">Client</td>
                        <td align="left" valign="middle" class="headerblack">Vendor </td>
                        <td align="left" valign="middle" class="headerblack">Sample Description</td>
                        <td align="left" valign="middle" class="headerblack">Date Needed</td>
                        <td align="left" valign="middle" class="headerblack">PO</td>
                        <td align="left" valign="middle" class="headerblack">PT Invoice </td>
                      </tr>
					  <?php
					  	$count=0;
					  	for($i=0; $i < count($datalist); $i++){
					  ?>
                      <tr>                        
                      <td height="25" align="right" valign="top" class="grey"><?php echo $datalist[$i]['sample_id'];?></td>
                        <td class="green"><?php echo $datalist[$i]['style'];?></td>					
						<td class="green"><?php echo $datalist[$i]['client'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['vendorName'];?></td>
                        <td align="left" valign="top" class="yellow"><?php echo $datalist[$i]['brief_desc'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['dateneeded'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['customer_po'];?></td>
                        <td align="left" valign="top" class="green"><?php echo $datalist[$i]['invoicenumber'];?></td>
                       </tr> 
					  <?php
					  	 $count++;
					  	}
					  ?>                     				  
                  </table>	
				  <input type="hidden" value="delete" name="act" id="act" />	
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
				  </form> </font>                
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