<?php
require('Application.php');
$image_path = $server_URL."/content/projectimages/";
$upload_dir			= "../../projectimages/";
$search_sql = "";
$is_session = 0;
$emp_join ="";
$emp_id= "";
$emp_sql = "";
$cust="";
$sale="";
$quan="";
$cost="";
$reDate='';
$delDate='';
if(isset($_REQUEST['finalCustomer2']) && $_REQUEST['finalCustomer2'] !="")  $cust=$_REQUEST['finalCustomer2'];
if(isset($_REQUEST['salesExecutive2']) && $_REQUEST['salesExecutive2'] !="")  $sale=$_REQUEST['salesExecutive2'];
if(isset($_REQUEST['quanPeople2']) && $_REQUEST['quanPeople2'] !="")  $quan=$_REQUEST['quanPeople2'];
if(isset($_REQUEST['finalCustomer2']) && $_REQUEST['costing2'] !="")  $cost=$_REQUEST['costing2'];
if(isset($_REQUEST['finalCustomer2']) && $_REQUEST['requestDate2'] !="")  $reDate=$_REQUEST['requestDate2'];
if(isset($_REQUEST['finalCustomer2']) && $_REQUEST['deliveryDate2'] !="")  $delDate=$_REQUEST['deliveryDate2'];
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

$print='<table width="100%" border="0">
			<tr>
			  <td align="center"><p></p></td>
			  <td align="center">&nbsp;</td>
			  <td align="center">&nbsp;</td>
			  <td valign="top" align="right"><input type="button" value="Print " onmouseover="this.style.cursor = \'pointer\';" onclick="javascript:window.print();" id="btnpopprint" style="cursor: pointer;"></td>
			</tr>
			<tr>
			  <td colspan="3" rowspan="2" align="left" valign="top">
			  <table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
				  <td height="25">Final Customer : </td>
				  <td height="25" colspan="3"><input name="finalCustomer1" id="finalCustomer1" type="text" class="textBox" readonly="readonly" value="'.$cust.'" /></td>
				</tr>
				<tr>
				  <td height="25">Sales Executive : </td>
				  <td height="25" colspan="3"><input name="salesExecutiv1e" id="salesExecutive1" type="text" class="textBox" readonly="readonly" value="'.$sale.'"  /></td>
				</tr>
				<tr>
				  <td height="25">Quantity of people Required : </td>
				  <td><input name="quanPeople1" id="quanPeople1" type="text" class="textBox" readonly="readonly" value="'.$quan.'"  /></td>
				  <td>Costing Required: </td>
				  <td><input name="costing1" id="costing1" type="text" class="textBox" readonly="readonly" value="'.$cost.'"  /></td>
				</tr>
			  </table>                            
			  </td>
			  <td width="150" height="25" align="right" valign="top">Date of request:</td>
			  <td width="100" align="right" valign="top"><input  name="requestDate1" id="requestDate1" type="text" class="textBox"  readonly="readonly" value="'.$reDate.'"  /></td>
			</tr>
			<tr>
			  <td height="25" align="right" valign="top">Requested Delivery Date: </td>
			  <td align="right" valign="top"><input name="deliveryDate1" id="deliveryDate1" readonly="readonly" type="text" class="textBox"  value="'.$delDate.'"  /></td>
			</tr>
			<tr>
			  <td width="100" height="25" align="left" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Reference Article </td>
			  <td align="left">&nbsp;</td>
			  <td align="center">&nbsp;</td>
			  <td align="right">&nbsp;</td>
			  <td align="right" valign="top"></td>
			</tr>
		</table>';
	$datalist;unset($datalist);
	$search_sql = $_GET['query'];
	$sql='select s.id,s."srID",s.cid,s.vid,s.size,s.dateneeded,s.sample_name,s.ordered_date,s.brief_desc,s."styleNo" as style,s.detail_description, s.color, s."fabricType", s."cost", s."customerTargetprice",s.customer_po,s.invoicenumber,c."client",sn.notes,vendor."vendorName",upl.uploadtype,upl.uploadid,upl.filename from "tbl_sampleRequest" s inner join "clientDB" c on s."cid"=c."ID" left join "tbl_sampleNotes" sn on sn."notesId" = (select  n1."notesId" from "tbl_sampleNotes" as n1 where n1."sampleId"=s.id order by n1."notesId" desc limit 1) inner join vendor on vendor."vendorID" = s.vid left join tbl_sample_uploads as upl on upl.uploadid = (select n1.uploadid from tbl_sample_uploads as n1  where n1.sampleid=s.id and n1.uploadtype=\'I\' order by n1.uploadid desc limit 1) where s."status"=1'.$search_sql.$emp_sql.' order by s."modifiedDate"  desc';
	if(!($resultp=pg_query($connection,$sql))){
		print("Failed queryd: " . pg_last_error($connection));
		exit;
	}
	while($rowd = pg_fetch_array($resultp)){
		$datalist[]=$rowd;
	}
	$message="";
	if(count($datalist)) {
		$message='<table width="100%" border="0" cellspacing="1" cellpadding="1">
                      <tr>                       
                        <td height="25" align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Sample ID</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Style #</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Sample Name</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Client Name</td>
<td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Vendor</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Sample Description</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Date Needed</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">PO</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">PT Invoice</td>
                        <td align="left" valign="middle" style="background-color:#000000;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:11px; line-height:20px; color:#FFFFFF; padding-left:5px;">Ordered Date</td>
                      </tr>';

		$cnt=0;
		for($i=0; $i < count($datalist); $i++){
			$message .='<tr>                        
                        <td height="25" align="right" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['srID'].'</td>
                        <td style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['style'].'</td>
                        <td align="left" valign="top" style="background-color:#FCF305;text-align:left;padding-left:5px;">'.$datalist[$i]['sample_name'].'</td>
						<td style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['client'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['vendorName'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['brief_desc'].'</td>
                        <td align="left" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['dateneeded'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['customer_po'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['invoicenumber'].'</td>
                        <td align="left" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['ordered_date'].'</td>
                      </tr> ';  	 
		}                    
		$message .='<tr>                        
                        <td height="25" align="center" colspan="4"><strong>QUANTITY OF
                        GARMENTS:</strong></td>
                        <td>&nbsp;'.count($datalist).'</td>
                        <td align="left">&nbsp;</td>
                        <td align="left">&nbsp;</td>
                        <td align="left" colspan="7"></td>                        
                      </tr>					  
                  </table>';
	} else {
		$message = '<table width="95%" border="0">
                        <tr>
                          <td align="center"><p style="color:red; font-weight:bold;">There is no sample request available.</p></td>                         
                        </tr>
                      </table>';
	}
if(isset($_REQUEST['email']) && isset($_REQUEST['subject']))
{
	
	extract($_POST);	
	
	$body='There are no sample request made yet..';
	if($message) 
	{
		$body=$print.$message; 
	}
	if($isMailServer == 'true')
	{
		require('../../mail.php');
		$mail             = new PHPMailer();
					
		$mail->AddReplyTo("Do Not Reply", $name = "");

		$mail->From       = "samplerequest@i2net.com";
		
		$mail->FromName   = "";
		
		$mail->Subject    = $subject;
		
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		
		$mail->MsgHTML($body);
		
		$mail->AddAddress($email, $name="");
		
		if($mail->Send())
		{
			header('Location: samplerequest.list.php');
		
		}
		else
		{
			$msg = '<p>Sorry, Unable to send the email.<br/> Please try again later or contact uniformsourcing@i2net.com</p>';
			$_SESSION['errorMsg'] = $msg;
		}
	}
	else
	{
		require($PHPLIBDIR.'mailfunctions.php');
		$headers=create_smtp_headers($subject, "samplerequest@i2net.com", $email, $subject,"","text/html");
		$data=$headers. "<html>".$body."</html>";
		if((send_smtp("mail.i2net.com","samplerequest@i2net.com",$email, $data)) == false){
			global $last_output;
			echo "ERROR sending message d00d. $last_output<br>";
			exit;
		}	
		foreach ($_REQUEST as $i => $value) 
		{
			unset($_REQUEST[$i]);
		}
	}
}
header("location: samplerequest.list.php");
?>