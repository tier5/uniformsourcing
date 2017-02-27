<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$mail=0;
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['email'] = "";
$pid=$email_pid; 


if(isset($_POST['email']) && $_POST['email']!="")
{
	$sent_to=$email;
	$sql = "Select * from tbl_newproject where status =1 and pid = $pid";
	  if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
	  while($row = pg_fetch_array($result)){
		  $data_prj=$row;
	  }
	  pg_free_result($result);
	  
	  $query="SELECT client FROM \"clientDB\" where \"ID\"=".$data_prj['client'];
	  if(!($result=pg_query($connection,$query))){
		  print("Failed custom-query1 on mailing: " . pg_last_error($connection));
		  exit;	
	  }
	  $client = pg_fetch_result($result,0,'client');
	  pg_free_result($result);
	
	$sql = "Select * from tbl_prjpurchase where status =1 and pid = $pid";
	  if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
	  while($row = pg_fetch_array($result)){
		  $data_prjPurchase =$row;
	  }
	  if( $data_prjPurchase['purchaseId'] !="")
	  $purchaseId = $data_prjPurchase['purchaseId'];
	  pg_free_result($result);
		
	
			
	$query="SELECT firstname,lastname FROM \"employeeDB\" where \"employeeID\"='".$data_prj['project_manager']."'";	
	  if(!($result=pg_query($connection,$query))){
		  print("Failed custom-query2 on mailing: " . pg_last_error($connection));
		  exit;	
	  }
	  $manager=pg_fetch_array($result);
	  pg_free_result($result);
	  
	$query="SELECT firstname,lastname FROM \"employeeDB\" where \"employeeID\"='".$data_prj['createdby']."'";
	  if(!($result=pg_query($connection,$query))){
		  print("Failed custom-query3 on mailing: " . pg_last_error($connection));
		  exit;	
	  }
	  $updated_by=pg_fetch_array($result);
	  pg_free_result($result);
	  
	$sql = "Select notes from tbl_mgt_notes where \"isActive\" =1 and pid = '$pid' order by notesid desc";
	  if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
	  $data_prjNotes = pg_fetch_array($result);
	  pg_free_result($result);
		
	$sql = "select tracking_number from tbl_prjorder_shipping left join tbl_carriers on tbl_carriers.carrier_id = tbl_prjorder_shipping.carrier_id   where tbl_prjorder_shipping.status=1 and pid = $pid order by shipping_id desc limit 1";
	  //echo $sql;
	  if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
	  $tracking_number = pg_fetch_array($result);
	  pg_free_result($result);
		
	$sql = "Select * from tbl_prmilestone where pid = $pid and status = 1";	
	  if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	  }
	  while($row = pg_fetch_array($result)){
		  $data_prj_milestone = $row;
	  }
	  if($data_prj_milestone['id']!="")
	  $milestone_id = $data_prj_milestone['id'];
	  pg_free_result($result);
	  
	
			$headers = "From: PDF Imagewear" . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//$subject = "An update for {$client} {$data_prj['projectname']} has been made.";
			$email_body = "<p>You have recieved a new message from the enquiries form on your website.</p>
						  <p><strong>Client Name: </strong> {$client} </p>
						  <p><strong>Project/PO: </strong>";
						  if($data_prjPurchase['purchaseorder']!="")
						  {
							  $email_body .=$data_prjPurchase['purchaseorder'];
						  }
						  else
						  {
							  $email_body .=$data_prj['projectname'];
						  }
						  $email_body .= "</p> 
						  <p><strong>Due Date: </strong> {$data_prjPurchase['purchaseduedate']} </p>
						  <p><strong>Project Manager:</strong> {$manager['firstname']} {$manager['lastname']}</p>
						  <p><strong>Updated By: </strong> {$updated_by['firstname']} {$updated_by['lastname']} </p>
						  <p><strong>Notes:</strong> {$data_prjNotes['notes']} </p>
						  <p><strong>Tracking Number:</strong> {$tracking_number['tracking_number']} </p>
						  <p><strong>Milestone Information </strong> </p>
						  <p><strong>Lap Dip: </strong> {$data_prj_milestone['lapdip']} </p>
						  <p><strong>Lap Dip Approval: </strong> {$data_prj_milestone['lapdipapproval']} </p>
						  <p><strong>Estimated Fabric Delivery Date: </strong> {$data_prj_milestone['estdelivery']} </p>
						  <p><strong>Production Sample: </strong> {$data_prj_milestone['prdtnsample']} </p>
						  <p><strong>Production Sample Approval: </strong> {$data_prj_milestone['prdtnsampleapprval']} </p>
						  <p><strong>Sizing Line: </strong> {$data_prj_milestone['szngline']} </p>
						  <p><strong>Production target Delivery: </strong> {$data_prj_milestone['prdtntrgtdelvry']} </p>
						  
						  <p><strong>Please login to the system to view complete project or purchase order
							details and full updates, images and files.</strong></p>";
							//echo $email_body.$email.$subject;
		/*	if($isMailServer == 'true')
			{
				require('../../mail.php');
				
				$mail       = new PHPMailer();	
				
				$mail->AddReplyTo("Do Not Reply", $name = "DO NOT REPLY");
		
				$mail->From       = "admin@uniformsourcing.com";
				
				$mail->FromName   = "";
				
				$mail->Subject    = $subject;
				
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					
			
				$mail->MsgHTML($email_body);
				
				$mail->to[0][0]=$sent_to;
				$mail->to[0][1]='';
				
				if(!$mail->Send())
				{	
					$return_arr['error'] = "Unable to send email. Please try again later";
					echo json_encode($return_arr);
					return;
				}
				
			}
			else*/
			{
                          
				require($PHPLIBDIR.'mailfunctions.php');
				
                                     // echo 'sent-'.$sent_to;
					$headers=create_smtp_headers($subject, "admin@uniformsourcing.com", $sent_to, $subject,"","text/html");
					$data=$headers. "<html><BODY>".$email_body."</body></html>";
					if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$sent_to, $data)) == false)
					{
						$return_arr['error'] = "Unable to send email. Please try again later";
						echo json_encode($return_arr);
						return;
					
					}
				
			}	
}
else
{
	$return_arr['error'] = "Email field appears to be empty";
	echo json_encode($return_arr);
	return;
}


echo json_encode($return_arr);
return;
?>