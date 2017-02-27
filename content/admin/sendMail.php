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

	$datalist;unset($datalist);
	$search_sql = $_GET['query'];
	$sql='select s.id,s.pid,s.sample_id,s.vid,s.size_requested,s.order_on,s.dateneeded,s.brief_desc,s.style_number as style,s.detail_description, s.sample_color, s.fabric, s.fabric_cost, s.quote_price,s.customer_po,s.invoicenumber,prj.client as prj_client,sn.notes,vendor."vendorName",upl.uploadtype,upl.upload_id,upl.filename,cl.client from tbl_prj_sample as s  left join tbl_prjsample_notes as sn on sn.notes_id = (select n1.notes_id from tbl_prjsample_notes as n1 where n1.sample_id=s.id order by n1.notes_id desc limit 1) left join vendor on vendor."vendorID" = s.vid left join tbl_prjsample_uploads as upl on upl.upload_id =(select n1.upload_id from tbl_prjsample_uploads as n1 where n1.sample_id=s.id and n1.uploadtype=\'I\' order by n1.upload_id desc limit 1) left join tbl_newproject as prj on prj.pid = s.pid  left join "clientDB" as cl on cl."ID"=prj.client where s.status=1 and  prj.status =1 '.$search_sql.$emp_sql.' order by s.id desc';
	//echo $sql;
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
                        <td height="25" align="right" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['sample_id'].'</td>
                        <td style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['style'].'</td>
						<td style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['client'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['vendorName'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['brief_desc'].'</td>
                        <td align="left" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['dateneeded'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['customer_po'].'</td>
                        <td align="left" valign="top" style="background-color:#DCE0AC;text-align:left;padding-left:5px;">'.$datalist[$i]['invoicenumber'].'</td>
                        <td align="left" valign="top" style="background-color:#DDE4FF;text-align:left; padding-left:5px;">'.$datalist[$i]['order_on'].'</td>
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