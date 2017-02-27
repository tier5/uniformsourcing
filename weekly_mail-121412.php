<?php 
  require('Application.php');
  require('./content/mail.php');					
  require($PHPLIBDIR.'mailfunctions.php');

function rangeWeek($dt) {
    date_default_timezone_set(date_default_timezone_get());   
    $res['start'] = date('N', $dt)==1 ? date('Y-m-d', $dt) : date('M d, Y', strtotime('last monday', $dt));
    $res['end'] = date('N', $dt)==7 ? date('Y-m-d', $dt) : date('M d, Y', strtotime('next sunday', $dt));
    return $res;
    }

  $error = "";
  $return_arr['error'] = "";
	$sql='select Distinct(prj.projectname), prj.is_billed, prj.bill_date, prch.createddate,c.client,prj.pid,prj.order_placeon,prj.status, prj.close_date,emp.firstname,emp.lastname ,prch.purchaseorder,prch.pt_invoice,tbl_carriers.weblink,
 prc.prjquote,prch.purchaseduedate,prc.prjcost,ship.tracking_number ,prc.prj_completioncost ,pro.prdtntrgtdelvry from tbl_newproject as prj inner join tbl_prjpurchase as prch on
 prch.pid = prj.pid left join tbl_prjvendor pv on pv.pid=prj.pid left join tbl_prjorder_shipping as ship on ship.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join
 tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1) left join tbl_carriers on tbl_carriers.carrier_id = ship.carrier_id left join tbl_prjpricing as prc on prc.pid = prj.pid
  left join "clientDB" c on prj.client=c."ID" left join tbl_prmilestone as pro on pro.pid = prj.pid  left join "employeeDB" as emp on emp."employeeID"= prj.project_manager ' .$emp_join.' where prj.status =0 and prch.purchaseorder <> \'\' and prj.close_date > \''.strtotime("-7 days").'\' order by prj.close_date  desc ';

  /* $sql=' select Distinct(prj.projectname),prj.is_billed, prj.bill_date,c.client,prj.pid,prj.order_placeon,prj.status,emp.firstname,emp.lastname ,tbl_prj_style.style,prch.purchaseorder,prc.prjquote,prch.purchaseduedate, prc.prjcost,prc.prj_estimatecost,prc.prj_completioncost from tbl_newproject as prj inner join tbl_prjpurchase as prch on prch.pid = prj.pid left join tbl_prjpricing as prc on prc.pid = prj.pid left join "clientDB" c on prj.client=c."ID" left join "employeeDB" as
  emp on emp."employeeID"= prj.project_manager left join tbl_prj_style on tbl_prj_style.prj_style_id = (select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = prj.pid order by tbl_prj_style.prj_style_id desc limit 1) where prj.status =0 and prch.purchaseorder <> \'\' and close_date > \''.strtotime("-7 days").'\' order by prj."pid"  desc ';
*/  
if(!($resultp=pg_query($connection,$sql)))
  {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
  }
  while($rowd = pg_fetch_array($resultp))
  {
  $datalist[]=$rowd;
  } 
  $email_body = <<<START
      <div>
      <table width="100%" style="border:1px solid #999;" bgcolor="#ebeaea" cellpadding="0" cellspacing="0";">
      <thead style="background-color:#333333;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;color:#FFFFFF;padding-left:10px;height:25px;line-height:25px;" >
       <tr> 
         	<th style="border-left:1px solid white;">Client </th>
            <th style="border-left:1px solid white;">Project Manager</th>
			<th style="border-left:1px solid white;">Project Name</th>
			<th style="border-left:1px solid white;">Purchase Order</th>
            <th style="border-left:1px solid white;">PT Invoice</th>
            <th style="border-left:1px solid white;">P O Due Date</th>
			<th style="border-left:1px solid white;">Project Quote</th>
            <th style="border-left:1px solid white;">Order Placed On</th>
			<th style="border-left:1px solid white;">Tracking Number</th>
			<th style="border-left:1px solid white;">Target Delivery</th>
			<th style="border-left:1px solid white;">Billed</th>
			<th style="border-left:1px solid white;">Closed</th>

    </tr>
    </thead>
START;
$require_email = 1;
$newline = "\n\r";
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{		
		if(trim($datalist[$i]['purchaseduedate'])!= '')
		{
			$today = strtotime(date("Y-m-d"));								
			$timestamp = strtotime($datalist[$i]['purchaseduedate']);
			if($timestamp < $today)
				$po_style='style="color:red;"';
		}
		$email_body .= '<tr>'.$newline;
		if ($datalist[$i]['client']!="") 
		$email_body .= '<td style="border:1px #FFF solid">'.$datalist[$i]['client'].'</td>'.$newline;
		else 
		   $email_body .= '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
		
		$email_body .= '<td style="border:1px #FFF solid;">'.$datalist[$i]['firstname'].$datalist[$i]['lastname'].'</td>'.$newline;
		$email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['projectname'].'</td>'.$newline;
		$email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['purchaseorder'].'</td>'.$newline;
		
		if ($datalist[$i]['pt_invoice']!="")
			$email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['pt_invoice'].'</td>'.$newline;
	       else 
            $email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
        if ($datalist[$i]['purchaseduedate']!="")
            $email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['purchaseduedate'].'</td>'.$newline;
          else 
             $email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
        if ($datalist[$i]['prjquote']!="") 
             $email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['prjquote'].'</td>'.$newline;
          else   
             $email_body .= '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;  
		if ($datalist[$i]['order_placeon']!="")
	     	 $email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['order_placeon'].'</td>'.$newline;
          else 
              $email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;  
         if ($datalist[$i]['tracking_number']!="")
            $email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['tracking_number'].'</td>'.$newline; 
         else 
             $email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
		if ($datalist[$i]['tracking_number']!="")
		$email_body .=  '<td style="border:1px #FFF solid;">'.$datalist[$i]['prdtntrgtdelvry'].'</td>'.$newline;
		else 
		    $email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
		if($datalist[$i]['is_billed'] != '' && $datalist[$i]['is_billed'] > 0)
			$email_body .=  '<td style="border:1px #FFF solid;">&nbsp;Yes&nbsp;:&nbsp;'.date('m/d/Y',$datalist[$i]['bill_date']).'</td>'.$newline;
		else 	
			$email_body .=  '<td style="border:1px #FFF solid;">&nbsp;No&nbsp;</td>'.$newline;    
				
		if ($datalist[$i]['close_date']!="")
            		$email_body .=  '<td style="border:1px #FFF solid;">'.date('m/d/Y',$datalist[$i]['close_date']).'</td>'.$newline; 
         	else 
             		$email_body .=  '<td style="border:1px #FFF solid;">'.'&nbsp;'.'</td>'.$newline;
		$email_body .=  "</tr>".$newline;

	}
	$email_body .= '<tr><td width="100%"  colspan="11"></td></tr>'.$newline;	
} 
else 
{
	$email_body .=  '<tr>';
	$email_body .=  '<td align="left" colspan="14"><font face="arial"><b>No Project Found</b></font></td>';
	$email_body .=  '</tr>'.$newline;
	$require_email = 0;
}
$email_body .= '</table></div>'.$newline;

  
   $email_body .="<p>Please login to internal.uniformsourcing.com to review all project updates.  If you have forgotten your user name or password, email support@i2net.com to request support.</p>".$newline;

//echo $email_body;
$sent_to = array();
$sent_to[0] = 'systems@uniforms.net';
$week = rangeWeek(strtotime("last Saturday"));
$subject = 'Weekly Billing Summary - '.$week['start'].' to '.$week['end'];

if($require_email)
{
	if($isMailServer == 'true')
	{	
		$mail       = new PHPMailer();	
						
		$mail->AddReplyTo('noreply@uniformsourcing.com', 'No Reply');
	
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
			//echo json_encode($return_arr);
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
				//echo json_encode($return_arr);
				return;
			
			}
		}
	}
}
?>