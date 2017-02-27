<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
require('../../mail.php');					


require($PHPLIBDIR.'mailfunctions.php');
					
$error = "";
$msg = "";
$mail=0;
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['email'] = "";
if(isset($_POST['vsr']) && $_POST['vsr']!="")
	$vsr = $_POST['vsr'];
if($_POST['csr'] && $_POST['csr']!="")
	$csr = $_POST['csr'];
if($_POST['all_projects_check'])
{
	$selected_projects = $_POST['all_projects_check']; 
}
if(isset($vsr) || isset($csr))
{
	if(isset($vsr) && count($vsr)>0)
	{
		for($vendor_index=0; $vendor_index<count($vsr); $vendor_index++)
		{
				$sql ="select emp.firstname,emp.lastname,emp.email as empmail,prj.projectname,prj.pid,cl.client,cl.address,cl.city,cl.state,cl.zip,cl.shipperno,cl.carrier,cl.email,style.style,prj.projectname,prch.purchaseorder,prch.purchaseduedate,mile.lapdip,mile.lapdipapproval,mile.estdelivery,mile.prdtnsample,mile.prdtnsampleapprval,mile.szngline,mile.prdtntrgtdelvry,mile.desbordcmplt,mile.desbordappval,mile.design_board_calender,image.file_name from tbl_newproject as prj inner join \"employeeDB\" as emp on emp.\"employeeID\"=prj.project_manager inner join \"clientDB\" as cl on cl.\"ID\" = prj.client left join tbl_prjpurchase as prch on prch.pid=prj.pid left join tbl_prjimage_file as image on image.\"prjimageId\"=(select tbl_prjimage_file.\"prjimageId\" from tbl_prjimage_file inner join tbl_newproject on 
  tbl_prjimage_file.pid=prj.pid order by tbl_prjimage_file.\"prjimageId\" limit 1)  left join tbl_prmilestone as mile on mile.pid=prj.pid left join tbl_prj_style as  style on style.prj_style_id=(select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = prj.pid order by tbl_prj_style.prj_style_id limit 1)where prj.pid=".$vsr[$vendor_index];
		//	echo $sql;
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Failed query : " . pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$data_project=$row;
			}
			pg_free_result($result);
			
			$sql ="select style.style,style.garments,style.retailprice,style.priceunit from tbl_newproject as prj left join tbl_prj_style as style on style.pid = prj.pid where prj.pid =".$vsr[$vendor_index];		
			
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Failed query : " . pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$data_style[]=$row;
			}
			pg_free_result($result);
			
			$sql="select vendor.email from vendor inner join tbl_prjvendor on tbl_prjvendor.vid = vendor.\"vendorID\" where tbl_prjvendor.pid=".$vsr[$vendor_index];
			//echo $sql;
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Failed query : " . pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$data_vendor[]=$row;
			}
			pg_free_result($result);
			$empty_subject_value = '???';
			$empty_value ="<strong>???</strong>";
			
			for($v_present_index=0; $v_present_index<count($data_vendor); $v_present_index++)
			{
				if(count($data_vendor)>0 && $data_vendor[$v_present_index]['email']!="")
				{
					$subject = "(VSR) Status Update ";
					if($data_project['client']!="")$subject .= $data_project['client']." / ";
					else $subject .='???';
					if($data_project['purchaseorder']!="")$subject .=$data_project['purchaseorder']." /  ";
					else $subject .='???';
					if($data_project['purchaseduedate']!="")$subject .=$data_project['purchaseduedate']."";
					else $subject .='???';
					$email_body ="<p>Please review this project and update us with the information requested below.  This project has due dates and shipping instructions that are important to the order process.</p>
		
				<br/><p>If you have any questions, please contact ";
					if($data_project['firstname']!="")
						$email_body .=$data_project['firstname'];
					else 
						$email_body .=$empty_value;
					$email_body .=" our offices at 714.842.1200 or email ";
					if($data_project['empmail']!="")
						$email_body .=$data_project['empmail'];
					else 
						$email_body .=$empty_value;
					$email_body .=" with updates.</p>			
					<br/><p>Thank you for the attention to this order!</p>		
					<p><strong>Client:</strong> ";
					if($data_project['client']!="")
						$email_body.=$data_project['client'];
					else
						$email_body .=$empty_value;
					$email_body.="</p><p><strong>Project Name: </strong>  ";
					if($data_project['projectname']!="")
						$email_body.=$data_project['projectname'];
					else 
						$email_body .=$empty_value;
					$email_body.="</p>";
					$email_body.="<p><strong>Purchase Order: </strong> ";
					if($data_project['purchaseorder']!="")
						$email_body.=$data_project['purchaseorder'];
					else 
						$email_body .=$empty_value;
					
					
					$email_body .="</p>
					<p><strong>Due Date PO: </strong> ";
					if($data_project['purchaseduedate']!="")
						$email_body.=$data_project['purchaseduedate'];
					else 
						$email_body .=$empty_value;
					$email_body .="</p><br/>
					<table width='90%'>
					<tr>
					<td width='30%'><strong>Style number:&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
					<td width='30%'><strong>Total #of garments:&nbsp;&nbsp;&nbsp;&nbsp;</strong> </td>
					<td width='30%'><strong>Target Price:&nbsp;&nbsp;&nbsp;&nbsp;</strong> </td>
					</tr>";
					for($style_index=0; $style_index<count($data_style); $style_index++)
					{
						$email_body .="<tr><td width='30%'>";
						if($data_style[$style_index]['style']!="")
							$email_body .=$data_style[$style_index]['style'];
						else 
						 	$email_body .=$empty_value;
						$email_body .="</td><td width='30%'>";
						if($data_style[$style_index]['garments']!="")
							$email_body .=$data_style[$style_index]['garments'];
						else 
						 	$email_body .=$empty_value;
						$email_body .="</td><td width='30%'>";
						if($data_style[$style_index]['priceunit']!="")
							$email_body .=$data_style[$style_index]['priceunit'];
						else 
						 	$email_body .=$empty_value;
						$email_body .="</td></tr>";
					}
				 $email_body.="</table><p>
				
				<strong>Shipping Instructions</strong></p>
				
				<p><strong>Client: </strong> ";
				 if($data_project['client']!="")
					$email_body .=$data_project['client'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Purchase Order: </strong>";
				if($data_project['purchaseorder']!="")
					$email_body .=$data_project['purchaseorder'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Address: </strong> ";
				if($data_project['address']!="")
					$email_body .=$data_project['address'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>City: </strong> ";
				if($data_project['city']!="")
					$email_body .=$data_project['city'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>State: </strong> ";
				if($data_project['state']!="")
					$email_body .=$data_project['state'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Zip: </strong>";
				if($data_project['zip']!="")
					$email_body .=$data_project['zip'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Carrier: </strong> ";
				if($data_project['carrier']!="")
					$email_body .=$data_project['carrier'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Shipping Account Number: </strong> ";
				if($data_project['shipperno']!="")
					$email_body .=$data_project['shipperno'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p></table>
			
				<p>The following information needs to be confirmed by your staff, so we can inform our client that we are on track for order delivery.
				
				</p><p>PLEASE CONFIRM:	
				
				</p><p><strong>Project Milestones</strong>
				</p><p><strong>Lap Dip: </strong> ";
				if($data_project['lapdip']!="")
					$email_body .=$data_project['lapdip'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Lap Dip Approval: </strong>";
				if($data_project['lapdipapproval']!="")
					$email_body .=$data_project['lapdipapproval'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Estimated Fabric Delivery Date: </strong> ";
				if($data_project['estdelivery']!="")
					$email_body .=$data_project['estdelivery'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production Sample: </strong> ";
				if($data_project['prdtnsample']!="")
					$email_body .=$data_project['prdtnsample'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production Sample Approval: </strong> ";
				if($data_project['prdtnsampleapprval']!="")
					$email_body .=$data_project['prdtnsampleapprval'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Sizing Line: </strong> ";
				if($data_project['szngline']!="")
					$email_body .=$data_project['szngline'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production target Delivery: </strong> ";
				if($data_project['prdtntrgtdelvry']!="")
					$email_body .=$data_project['prdtntrgtdelvry'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p>PLEASE CONFIRM TRACKING NUMBERS:
				
				</p><p>All boxes must have the \"purchase order\" on the outside of EVERY box.  Please include a packing slip for accurate inventory.</p>";
				if($data_project['file_name']!="")
				{
					$email_body .="<p><img src=\"".$_SESSION['HOME_URL']."/uploadFiles/project_mgm/".$data_project['file_name']."\" /></p>";
				}
				$email_body .="<p>Please login to internal.uniformsourcing.com to review all project updates.  If you have forgotten your user name or password, email support@i2net.com to request support.</p>";
				//$sent_to = $data_vendor[$v_present_index]['email'];
				$manager_email = $data_project['empmail'];
				
				
				$sent_to = array();
				if($data_vendor[$v_present_index]['email']!="")
					$sent_to[0] =$data_vendor[$v_present_index]['email'];
				if($manager_email!="")
					$sent_to[1] = $manager_email;
				//echo $email_body;
				if($isMailServer == 'true')
				{	
					$mail       = new PHPMailer();	
									
					$mail->AddReplyTo($data_project['empmail'], $name = $data_project['firstname'].' '.$data_project['lastname']);
			
					$mail->From       = "admin@uniformsourcing.com";
					
					$mail->FromName   = "";
					
					$mail->Subject    = $subject;
					
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					
				
					$mail->MsgHTML($email_body);
					
					for($i = 0; $i < count($sent_to); $i++)
						$mail->AddAddress($sent_to[$i], $name="");
					
					if(!$mail->Send())
					{	
						$return_arr['error'] = "Unable to send email. Please try again later";
						echo json_encode($return_arr);
						return;
					}
					
				}
				else
				{
					for($i = 0; $i < count($sent_to); $i++)
					{
						$headers=create_smtp_headers($subject, "admin@uniformsourcing.com", $sent_to[$i], 'Uniform Sourcing',"","text/html");
						$data=$headers. "<html><BODY>".$email_body."</body></html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$sent_to[$i], $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						
						}
					}
				}	
			}
			else
			{
				$return_arr['error'] = "Vendor Mail id is empty. Please try again later";
				echo json_encode($return_arr);
				return;
			}
		}
		unset($data_project);
		unset($data_style);
		unset($data_vendor);	
		}

	}
	if(isset($csr) && count($csr)>0)
	{
		for($client_index=0; $client_index<count($csr); $client_index++)
		{
			$sql ="select emp.firstname,emp.lastname,emp.email as empmail,prj.projectname,prj.pid,cl.client,cl.address,cl.city,cl.state,cl.zip,cl.shipperno,cl.carrier,cl.email,style.style,prj.projectname,prch.purchaseorder,prch.purchaseduedate,mile.lapdip,mile.lapdipapproval,mile.estdelivery,mile.prdtnsample,mile.prdtnsampleapprval,mile.szngline,mile.prdtntrgtdelvry,mile.desbordcmplt,mile.desbordappval,mile.design_board_calender from tbl_newproject as prj inner join \"employeeDB\" as emp on emp.\"employeeID\"=prj.project_manager inner join \"clientDB\" as cl on cl.\"ID\" = prj.client left join tbl_prjpurchase as prch on prch.pid=prj.pid left join tbl_prmilestone as mile on mile.pid=prj.pid left join tbl_prj_style as  style on style.prj_style_id=(select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = prj.pid order by tbl_prj_style.prj_style_id limit 1) left join tbl_prjimage_file as image on image.\"prjimageId\"=(select tbl_prjimage_file.\"prjimageId\" from tbl_prjimage_file inner join tbl_newproject on tbl_prjimage_file.pid=prj.pid order by tbl_prjimage_file.\"prjimageId\" limit 1) where prj.pid=".$csr[$client_index];
			//echo $sql;
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Failed query : " . pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$data_project=$row;
			}
			pg_free_result($result);
			
			$sql ="select style.style,style.garments,style.retailprice,style.priceunit from tbl_newproject as prj left join tbl_prj_style as style on style.pid = prj.pid where prj.pid =".$csr[$client_index];
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Failed query : " . pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$data_style[]=$row;
			}
			pg_free_result($result);
			
			$empty_value ="<strong>???</strong>";
			if($data_project['email']!="")
			{
				$subject = "(CSR) ";
				if($data_project['purchaseorder']!="")$subject .="PO ".$data_project['purchaseorder']." /  ";
				else $subject .='???';
				if($data_project['projectname']!="")$subject .="Project Name ".$data_project['projectname']."";
				else $subject .='???';
				$email_body ="<p>Please review this project and update us with the information requested below.  This purchase order has updates that you should review.</p>
		
				<br/><p>If you have any questions, please contact ";
				if($data_project['firstname']!="")
					$email_body .=$data_project['firstname'];
				else 
					$email_body .=$empty_value;
				$email_body .=" our offices at 714.842.1200 or email ";
				if($data_project['empmail']!="")
					$email_body .=$data_project['empmail'];
				else 
					$email_body .=$empty_value;
				$email_body .=" with updates.</p>			
				<br/><p>Thank you for the attention!</p>";	
				$email_body.="<p><strong>Project Name: </strong> ";
				if($data_project['projectname']!="")
					$email_body.=$data_project['projectname'];
				else 
					$email_body .=$empty_value;
				$email_body.="</p>";
				$email_body.="<p><strong>Purchase Order: </strong> ";
				if($data_project['purchaseorder']!="")
					$email_body.=$data_project['purchaseorder'];
				else 
					$email_body .=$empty_value;
					
					
				$email_body .="</p>
				<p><strong>Due Date PO: </strong> ";
				if($data_project['purchaseduedate']!="")
					$email_body.=$data_project['purchaseduedate'];
				else 
					$email_body .=$empty_value;
				$email_body .="</p><br/>
				<table  width='80%'>
				<tr>
				<td width='30%'><strong>Style number:&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				<td width='30%'><strong>Total #of garments:&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				<td width='30%'><strong>Target Price:&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				</tr>";
				for($style_index=0; $style_index<count($data_style); $style_index++)
				{
					$email_body .="<tr><td width='30%'>";
					if($data_style[$style_index]['style']!="")
						$email_body .="&nbsp;".$data_style[$style_index]['style'];
					else 
					 	$email_body .=$empty_value;
					$email_body .="</td><td width='30%'>";
					if($data_style[$style_index]['garments']!="")
						$email_body .="&nbsp;".$data_style[$style_index]['garments'];
					else 
					 	$email_body .=$empty_value;
					$email_body .="</td><td width='30%'>";
					if($data_style[$style_index]['retailprice']!="")
						$email_body .="&nbsp;".$data_style[$style_index]['retailprice'];
					else 
					 	$email_body .=$empty_value;
					$email_body .="</td></tr>";
				}
				 $email_body.="</table><p>
				
				<strong>Shipping Instructions</strong></p>
				
				</p><p><strong>Client: </strong> ";
				 if($data_project['client']!="")
					$email_body .=$data_project['client'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Purchase Order: </strong>";
				if($data_project['purchaseorder']!="")
					$email_body .=$data_project['purchaseorder'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Address: </strong> ";
				if($data_project['address']!="")
					$email_body .=$data_project['address'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>City: </strong> ";
				if($data_project['city']!="")
					$email_body .=$data_project['city'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>State: </strong> ";
				if($data_project['state']!="")
					$email_body .=$data_project['state'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Zip: </strong>";
				if($data_project['zip']!="")
					$email_body .=$data_project['zip'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Carrier: </strong> ";
				if($data_project['carrier']!="")
					$email_body .=$data_project['carrier'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Shipping Account Number: </strong> ";
				if($data_project['shipperno']!="")
					$email_body .=$data_project['shipperno'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p></table>
					<p>The following information is updated regularly so you can inform your purchasing and departments on any changes in production.
		
		 PLEASE REVIEW:</p>
				
				<p><strong>Project Milestones</strong></p>
				<p><strong>Lap Dip: </strong> ";
				if($data_project['lapdip']!="")
					$email_body .=$data_project['lapdip'];
				else 
				 	$email_body .=$empty_value;
				
				$email_body .="</p><p><strong>Lap Dip Approval: </strong>";
				if($data_project['lapdipapproval']!="")
					$email_body .=$data_project['lapdipapproval'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Estimated Fabric Delivery Date: </strong> ";
				if($data_project['estdelivery']!="")
					$email_body .=$data_project['estdelivery'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production Sample: </strong> ";
				if($data_project['prdtnsample']!="")
					$email_body .=$data_project['prdtnsample'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production Sample Approval: </strong> ";
				if($data_project['prdtnsampleapprval']!="")
					$email_body .=$data_project['prdtnsampleapprval'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Sizing Line: </strong> ";
				if($data_project['szngline']!="")
					$email_body .=$data_project['szngline'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p><p><strong>Production target Delivery: </strong> ";
				if($data_project['prdtntrgtdelvry']!="")
					$email_body .=$data_project['prdtntrgtdelvry'];
				else 
				 	$email_body .=$empty_value;
				$email_body .="</p>";
				if($data_project['file_name']!="")
				{
					$email_body .="<p><img src=\"".$_SESSION['HOME_URL']."/uploadFiles/project_mgm/".$data_project['file_name']."\" /></p>";
				}		
				$email_body .="<p>Please login to internal.uniformsourcing.com to review all project updates.  If you have forgotten your user name or password, email support@i2net.com to request support.";
				//$sent_to = $data_project['email'];
				$manager_email = $data_project['empmail'];
				
				
				if($data_project['email']!="")
					$sent_to[0] =$data_project['email'];
				if($manager_email!="")
					$sent_to[1] =  $manager_email;
				//echo $email_body;
				if($isMailServer == 'true')
				{	
					$mail       = new PHPMailer();	
									
					$mail->AddReplyTo($data_project['empmail'], $name = $data_project['firstname'].' '.$data_project['lastname']);
			
					$mail->From       = "admin@uniformsourcing.com";
					
					$mail->FromName   = "";
					
					$mail->Subject    = $subject;
					
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					
				
					$mail->MsgHTML($email_body);
					
					for($i = 0; $i < count($sent_to); $i++)
						$mail->AddAddress($sent_to[$i], $name="");
					
					if(!$mail->Send())
					{	
						$return_arr['error'] = "Unable to send email. Please try again later";
						echo json_encode($return_arr);
						return;
					}
					
				}
				else
				{
					for($i = 0; $i < count($sent_to); $i++)
					{
						$headers=create_smtp_headers($subject, "admin@uniformsourcing.com", $sent_to[$i], $subject,"","text/html");
						$data=$headers. "<html><BODY>".$email_body."</body></html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$sent_to[$i], $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						}
					}
				}	
			}
			else
			{
				$return_arr['error'] = "Client Mail id is empty. Please try again later";
				echo json_encode($return_arr);
				return;
			}
			//end of client if statement
			unset($data_project);
			unset($data_style);
		}//end of client for loop
	}//end of client present if condition
}
else
{
	$return_arr['error'] = "Select either CSR or VSR before sending email. Please try again later";
}
echo json_encode($return_arr);
return;
?>