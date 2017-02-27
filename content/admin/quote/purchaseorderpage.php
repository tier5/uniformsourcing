<?php 
require('Application.php');
$image = "";
$height = "";
if(isset($_GET['print']) && $_GET['print']=='true')
{
	$isPrint =$_GET['print'];
}
if(isset($_GET['qid']))
{
	$qid = $_GET['qid'];
	$sql = "Select q.*,cl.client,v.\"vendorName\",pay.payment,emp.firstname,emp.lastname,sh.shipvia,car.carrier from tbl_request as q left join \"clientDB\" as cl on cl.\"ID\" = q.client_id left join vendor as v on v.\"vendorID\"=q.vendor_id left join tbl_quote_payment as pay on pay.payment_id = q.payment_id left join \"employeeDB\" as emp on emp.\"employeeID\" = q.sales_rep left join tbl_ship_via as sh on sh.ship_via_id = q.ship_via left join tbl_quote_carrier as car on car.carrier_id=q.carrier_id where q.status = 1 and q.qid =$qid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed quote_query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_quote=$row;
	}
	pg_free_result($result);
	
	$query="Select * from tbl_request_items where status = 1 and qid = $qid";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_items[]=$row;
	}
	pg_free_result($result);
}
switch($data_quote['company_id'])
{
	case 1:
	{
		$image = "PDFSmall.jpg";
		break;
	}
	case 2:
	{
	 	$image = "Premier-logoSmall.jpg";
		break;
	}
	case 3:
	{
		$image = "login_logo.gif";
		break;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Global Uniform Sourcing Internal Intranet</title>
<style <?php if($isPrint == 'true'){ echo 'media="all"' ;}?>>

.grid001{
background-color:#CCCCCC;
height:25px;
text-align:left;
font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;
color:#000000;
padding-left:10px;
line-height:25px;}


.gridGrey{
background-color:#EAEAEA;
height:25px;
text-align:left;
font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;
color:#000000;
padding-left:10px;
line-height:25px;}

.blkHeader{
font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:18px;
font-weight:bold;
}

.blkHeader2{
font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:16px;
font-weight:bold;}

#fade {
	display: none;
	background: #000; 
	position: fixed; left: 0; top: 0; 
	z-index: 10;
	width: 100%; height: 100%;
	opacity: .80;
	z-index: 9999;
}
.popup_block{
	display: none;
	background: #fff;
	padding: 20px; 	
	border: 20px solid #ddd;
	float: left;
	font-size: 1.2em;
	position: fixed;
	top: 50%; left: 50%;
	z-index: 99999;
	-webkit-box-shadow: 0px 0px 20px #000;
	-moz-box-shadow: 0px 0px 20px #000;
	box-shadow: 0px 0px 20px #000;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	height:500px;
}
img.btn_close {
	float: right; 
	margin: -55px -55px 0 0;
}
.popup p {
	padding: 5px 10px;
	margin: 5px 0;
}

*html #fade {
	position: absolute;
}
*html .popup_block {
	position: absolute;
}
.emailBG{
	background-color:#CCCCCC;
	font-family:Tahoma, Geneva, sans-serif;
	font-size:11px;
	color:#000;
	padding-left:10px;
	text-align:left;
	}
.emailTxtBox{
	border:#A0A0A0 1px solid;
	font-family:Tahoma, Geneva, sans-serif;
	font-size:11px;
	padding-left:10px;
	line-height:18px;
	width:99%;
	height:18px;}
.errorMessage{color:#000;background-color:#fff7c0;padding: 5px;margin: 5px; font : normal 11px Verdana, Arial, Helvetica, sans-serif;}
.successMessage{color: #fff;background-color: green;padding: 5px;margin: 5px; font : normal 11px Verdana, Arial, Helvetica, sans-serif;}

</style>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<?php
$html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
'<html xmlns="http://www.w3.org/1999/xhtml">'.
'<title>Global Uniform Sourcing Internal Intranet</title>'.
'<div align="left" style="width:600px;"><img '; 
$html .='src="'.$_SESSION['HOME_URL'].'/images/'.$image.'" /></div><table style="font-family: Verdana,sans-serif; font-size: 11px; color: #374953; width: 600px;">'.
  '<tr>'.
    '<td align="left" valign="top"><table style="width: 100%;">'.
      '<tr>'.
        '<td align="left" valign="top"><p><strong>Premier Uniform Supply</strong></p>'.
              '<p>7275 Murdy Circle<br />Huntington Beach,CA 92647<br />USA</p>'.
          '<p>Voice: 714.842.1200<br />Fax: 714.375.4743</p></td>'.
        '<td align="left" valign="top">&nbsp;</td>'.
        '<td align="left" valign="top"><p><strong>QUOTE REQUEST</strong></p>'.
              '<p>Quote Number:'.$data_quote['po_number'].'</p>'.
          '<p>Internal Quote #.:'.$data_quote['internal_po'].'<br />Quote Date:  '.date('m/d/Y',$data_quote['po_date']).'</p></td>'.
      '</tr>'.
    '</table>'.
        '<table style="width: 100%;">'.
          '<tr>'.
            '<td align="left" valign="top"><p>&nbsp;</p></td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td  align="left" valign="top"><p>&nbsp;</p></td>'.
          '</tr>'.
          '<tr>'.
            '<td align="left" valign="top"><table style="width: 100%;">'.
                '<tr>'.
                  '<td align="left" valign="top"><table style="width: 100%;">'.
                      '<tr>'.
                        '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;color:#000000;line-height:25px; padding: 0.5em 1em;"><strong> Quoted To: </strong></td>'.
                      '</tr>'.
                    '</table>'.
                      '<table style="width: 100%;">'.
                        '<tr>'.
                          '<td align="left" valign="top" style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;">';
						  if($data_quote['ship_to'] ==1)
						  {
							  $html .=nl2br(htmlentities($data_quote['ship_to_clientfield']));
						  }
						  else if($data_quote['ship_to'] ==2)
						  {
						  	$html .=nl2br(htmlentities($data_quote['ship_to_vendorfield']));
						  }
						  else if($data_quote['ship_to'] ==3)
						  {
							$html .=$data_quote['other_name']."<br />".$data_quote['other_state']."<br />".$data_quote['other_city']."<br />".$data_quote['other_zip']."<br />".$data_quote['other_street'];
						  }
						   $html .='</td>'.
                        '</tr>'.
                        '<tr>'.
                          '<td align="left" valign="top">&nbsp;</td>'.
                        '</tr>'.
                    '</table></td>'.
                '</tr>'.
            '</table></td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top">'.
            '<table style="width: 100%;">'.
                '<tr>'.
                  '<td align="left" valign="top"><table style="width: 100%;">'.
                      '<tr>'.
                        '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>';
			if($data_quote['ship_to'] == 1)
			{
					 $html .="Client";
			}
			else if($data_quote['ship_to'] == 2)
			{
					$html .="Vendor";
			}
			else if($data_quote['ship_to'] == 3)
			{
					$html .="Other";
			}
              $html .='</strong></td>'.
                      '</tr>'.
                    '</table>'.
                      '<table style="width: 100%;">'.
                        '<tr>'.
                          '<td align="left" valign="top" style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>';
							if($data_quote['ship_to'] == 1)

							{
								$html .=$data_quote['client'];
							}
							else if($data_quote['ship_to'] == 2)
							{
								$html .=$data_quote['client'];
							}
							else if($data_quote['ship_to'] == 3)
							{
								$html .='Other';
							}
	
						$html .='</p></td>'.
                        '</tr>'.
                        '<tr>'.
                          '<td align="left" valign="top">&nbsp;</td>'.
                        '</tr>'.
                   '</table></td>'.
                '</tr>'.
              '</table>'.
              '</td>'.
          '</tr>'.
        '</table>'.
      '<table style="width: 100%;">'.
          '<tr>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>';
if($data_quote['ship_to'] == 1)
{
	$html .='Client ID' ;
}
else if($data_quote['ship_to'] == 2)
{
	$html .='Vendor ID';
}
$html .='</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Good Thru </strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Payment Terms </strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Sales Rep </strong></td>'.
          '</tr>'.
          '<tr>'.
            '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;">';
if($data_quote['ship_to'] == 1)
{
	$html .=$data_quote['ship_to_customer_id'];
}
else if($data_quote['ship_to'] == 2)
{
	$html .=$data_quote['shipto_vendor_id'];
}
$html .='</td>'.
            '<td align="left" valign="top" style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;">'.date('m/d/Y',$data_quote['good_thru']).'</td>'.
            '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;">'.$data_quote['payment'].'</td>'.
            '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;">'.$data_quote['firstname'].$data_quote['lastname'].'</td>'.
          '</tr>'.
          '<tr>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
          '</tr>'.
        '</table>'.
      '<table style="width: 100%;">'.
          '<tr>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Quantity</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Item</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Description</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Unit Price</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Tax Amount</strong></td>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Subtotal</strong></td>'.
          '</tr>';

if(count($data_items))
{
 			for($i=0; $i<count($data_items); $i++)
			{
              $html .='<tr>'.
                 '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>'.$data_items[$i]['quantity'].'</p></td>'.
                '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>'.$data_items[$i]['itemno'].'</p></td>'.
                '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>'.nl2br(htmlentities($data_items[$i]['description'])).'</p></td>'.
                '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>$'.$data_items[$i]['unit_price'].'</p></td>'.
                '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>$'.$data_items[$i]['tax_amount'].'</p></td>'.
                '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;"><p>$'.$data_items[$i]['amount'].'</p></td>'.
              '</tr>';
			}
}
else
{
 			$html .='<tr><td colspan="6"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;padding: 0.5em 1em;">No Items found</td><tr>';
}

         
        $html .='</table>'.
      '<table style="width: 100%;">'.
          '<tr>'.
            '<td align="left" valign="top"><p>&nbsp;</p></td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top"><p>&nbsp;</p></td>'.
          '</tr>'.
          '<tr>'.
            '<td align="left" valign="top">'.
               '<table style="width: 100%;">'.
                  '<tr>'.
                    '<td width="150" align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;"><strong>Quote Via: </strong></td>'.
                    '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">'.$data_quote['shipvia'].'</td>'.
                  '</tr>'.
                  '<tr>'.
                    '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;"><strong>Shipper #: </strong></td>'.
                    '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">'.$data_quote['shipperno'].'</td>'.
                  '</tr>'.
                  '<tr>'.
                    '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;"><strong>Carrier:</strong></td>'.
                    '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">'.$data_quote['carrier'].'</td>'.
                  '</tr>'.
              '</table></td>'.
            '<td align="left" valign="top">&nbsp;</td>'.
            '<td align="left" valign="top"><table style="width: 100%;">'.
                '<tr>'.
                  '<td align="left" valign="top"><table style="width: 100%;">'.
                      '<tr>'.
                        '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">Sub Total  : </td>'.
                        '<td width="150px" align="left" valign="top" style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;color:#000000;line-height:25px;">$'.$data_quote['amount_sub_total'].'</td>'.
                      '</tr>'.
                      '<tr>'.
                        '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">Sales Tax : </td>'.
                        '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;">$'.$data_quote['tax_sub_total'].'</td>'.
                      '</tr>'.
                      '<tr>'.
                        '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>TOTAL:</strong></td>'.
                        '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;">$'.$data_quote['total'].'</td>'.
                      '</tr>'.
                  '</table></td>'.
                '</tr>'.
            '</table></td>'.
          '</tr>'.
        '</table>'.
      '<table style="width: 100%;">'.
          '<tr>'.
            '<td>&nbsp;</td>'.
          '</tr>'.
        '</table>'.
      '<table style="width: 100%;">'.
          '<tr>'.
            '<td align="left" valign="top" style="background-color:#CCCCCC;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;
font-size:12px;color:#000000;line-height:25px;padding: 0.5em 1em;"><strong>Instructions/Notes:</strong></td>'.
          '</tr>'.
          '<tr>'.
            '<td align="left" valign="top"  style="background-color:#EAEAEA;height:25px;text-align:left;font-family:Tahoma, Verdana, Arial, Helvetica;font-size:12px;
color:#000000;line-height:25px;"><p>'.nl2br(htmlentities($data_quote['instruction_notes'])).'</p></td>'.
          '</tr>'.
      '</table></td>'.
  '</tr>'.
'</table></html>';
$_SESSION['emailBody'] = $html;
if($isPrint != 'true')
{
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='quoteList.php';" /></td>
    <td align="right"><a style="cursor:hand;cursor:pointer;" onclick="javascript:popupWindow();"><img src="<?php echo $mydirectory;?>/images/print.jpg" width="130" height="49" alt="print" /></a><a style="cursor:hand;cursor:pointer;" onclick="javascript:popOpen();"><img src="<?php echo $mydirectory;?>/images/sendemailButon.jpg" width="130" height="49" alt="send" /></a></td>
  </tr>
</table>
<?php
}
?>
<table width="60%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
    <img src="<?php echo $mydirectory;?>/images/<?php echo $image;?>" /></td>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="left" valign="top"><p class="blkHeader2">Premier Uniform Supply</p>
              <p>7275 Murdy Circle<br />
                Huntington Beach, CA 92647<br />
                USA</p>
          <p>Voice: 714.842.1200<br />
            Fax: 714.375.4743</p></td>
        <td width="1%" align="left" valign="top">&nbsp;</td>
        <td width="49%" align="left" valign="top"><p class="blkHeader"><strong>QUOTE REQUEST</strong></p>
              <p>Quote  Number: <?php echo $data_quote['po_number'];?></p>
          <p>Internal Quote #: <?php echo $data_quote['internal_po'];?><br />
            Quote Date: <?php echo date('m/d/Y',$data_quote['po_date']);?></p></td>
      </tr>
    </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="50%" align="left" valign="top"><p>&nbsp;</p></td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top"><p>&nbsp;</p></td>
          </tr>
          <tr>
            <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top" class="grid001"><strong>Quoted To: </strong></td>
                      </tr>
                    </table>
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td align="left" valign="top" class="gridGrey">
 <?php
						  if($data_quote['ship_to'] ==1)
						  {
							 echo nl2br(htmlentities($data_quote['ship_to_clientfield']));
						  }
						  else if($data_quote['ship_to'] ==2)
						  {
						  	 echo nl2br(htmlentities($data_quote['ship_to_vendorfield']));
						  }
						  else if($data_quote['ship_to'] ==3)
						  {
							 echo $data_quote['other_name']."<br />".$data_quote['other_state']."<br />".$data_quote['other_city']."<br />".$data_quote['other_zip']."<br />".$data_quote['other_street'];
						  }
?>						</td>
                        </tr>
                        <tr>
                          <td align="left" valign="top">&nbsp;</td>
                        </tr>
                    </table></td>
                </tr>
            </table></td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top" class="grid001"><strong>
<?php 			
			if($data_quote['ship_to'] == 1)
			{
?>
					Client
<?php
			}
			else if($data_quote['ship_to'] == 2)
			{
?>
					Vendor
<?php 
			}
			else if($data_quote['ship_to'] == 3)
			{
?>
					Other
<?php 
			}
?>
              </strong></td>
                      </tr>
                    </table>
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td align="left" valign="top" class="gridGrey"><p>
<?php
							if($data_quote['ship_to'] == 1)
							{
								echo $data_quote['client'];
							}
							else if($data_quote['ship_to'] == 2)
							{
								echo $data_quote['client'];
							}
							else if($data_quote['ship_to'] == 3)
							{
								echo "Other";
							}
?>						
	
						</p></td>
                        </tr>
                        <tr>
                          <td align="left" valign="top">&nbsp;</td>
                        </tr>
                    </table></td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="left" valign="top" class="grid001"><strong><?php if($data_quote['ship_to'] == 1){?>Client ID <?php } else if($data_quote['ship_to'] == 2){?>Vendor ID<?php }?></strong></td>
            <td align="left" valign="top" class="grid001"><strong>Good Thru </strong></td>
            <td align="left" valign="top" class="grid001"><strong>Payment Terms </strong></td>
            <td align="left" valign="top" class="grid001"><strong>Sales Rep </strong></td>
          </tr>
          <tr>
            <td align="left" valign="top" class="gridGrey"><?php if($data_quote['ship_to'] == 1){echo $data_quote['ship_to_customer_id']; } else if($data_quote['ship_to'] == 2){echo $data_quote['shipto_vendor_id']; }?></td>
            <td align="left" valign="top" class="gridGrey"><?php echo date('m/d/Y',$data_quote['good_thru']);?></td>
            <td align="left" valign="top" class="gridGrey"><?php echo $data_quote['payment']; ?></td>
            <td align="left" valign="top" class="gridGrey"><?php echo $data_quote['firstname'].$data_quote['lastname']; ?></td>
          </tr>
          <tr>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top">&nbsp;</td>
          </tr>
        </table>
      <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="left" valign="top" class="grid001"><strong>Quantity</strong></td>
            <td align="left" valign="top" class="grid001"><strong>Item</strong></td>
            <td align="left" valign="top" class="grid001"><strong>Description</strong></td>
            <td align="left" valign="top" class="grid001"><strong>Unit Price</strong></td>
           <td align="left" valign="top" class="grid001"><strong>Tax Amount</strong></td>
           <!--changed label  "Amount" to "Subtotal"-->
           <td align="left" valign="top" class="grid001"><strong>Subtotal</strong></td>
          </tr>
<?php 
if(count($data_items))
{
 			for($i=0; $i<count($data_items); $i++)
			{
?>
              <tr>
                 <td align="left" valign="top" class="gridGrey"><p><?php echo $data_items[$i]['quantity'];?></p></td>
                <td align="left" valign="top" class="gridGrey"><p><?php echo $data_items[$i]['itemno'];?></p></td>
                <td align="left" valign="top" class="gridGrey"><p><?php echo nl2br(htmlentities($data_items[$i]['description']));?></p></td>
                <td align="left" valign="top" class="gridGrey"><p>$<?php echo $data_items[$i]['unit_price'];?></p></td>
                <td align="left" valign="top" class="gridGrey"><p>$<?php echo $data_items[$i]['tax_amount'];?></p></td>
                <!--database name ['amount'] have no changes  -->
                <td align="left" valign="top" class="gridGrey"><p>$<?php echo $data_items[$i]['amount'];?></p></td>
              </tr>
 <?php
			}
}
else
{
 ?>
 			<tr><td colspan="6" class="gridGrey">No Items found</td></tr>
<?php 
}
 ?>
         
        </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="50%" align="left" valign="top"><p>&nbsp;</p></td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top"><p>&nbsp;</p></td>
          </tr>
          <tr>
            <td align="left" valign="top">
                <table width="100%" border="0" cellspacing="1" cellpadding="1">
                  <tr>
                    <td width="150" align="left" valign="top" class="grid001"><strong>Quote Via: </strong></td>
                    <td align="left" valign="top" class="gridGrey"><?php echo $data_quote['shipvia'];?></td>
                  </tr>
                  <tr>
                    <td align="left" valign="top" class="grid001"><strong>Shipper #: </strong></td>
                    <td align="left" valign="top" class="gridGrey"><?php echo $data_quote['shipperno'];?></td>
                  </tr>
                  <tr>
                    <td align="left" valign="top" class="grid001"><strong>Carrier:</strong></td>
                    <td align="left" valign="top" class="gridGrey"><?php echo $data_quote['carrier'];?></td>
                  </tr>
              </table></td>
            <td align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td align="left" valign="top" class="gridGrey">Sub Total  : </td>
                        <td width="150" align="left" valign="top" class="gridGrey">$<?php echo $data_quote['amount_sub_total'];?></td>
                      </tr>
                      <tr>
                        <td align="left" valign="top" class="gridGrey">Sales Tax : </td>
                        <td align="left" valign="top" class="gridGrey">$<?php echo $data_quote['tax_sub_total'];?></td>
                      </tr>
                      <tr>
                        <td align="left" valign="top" class="grid001"><strong>TOTAL:</strong></td>
                        <td align="left" valign="top" class="grid001">$<?php echo $data_quote['total']; ?></td>
                      </tr>
                  </table></td>
                </tr>
            </table></td>
          </tr>
        </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="left" valign="top" class="grid001"><strong>Instructions/Notes:</strong></td>
          </tr>
          <tr>
            <td align="left" valign="top" class="gridGrey"><p><?php echo nl2br(htmlentities($data_quote['instruction_notes'])); ?></p></td>
          </tr>
      </table></td>
  </tr>
</table>
<div id="dialog-form" title="Submit By Email" class="popup_block">
    <div align="center" id="message"></div>
			<p>All form fields are required.</p>  
			<fieldset>
           	
           	  <table class="emailBG" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
           	      <td width="10" height="30">&nbsp;</td>
           	      <td colspan="3" class="emailBG"><a style="cursor:hand;cursor:pointer;" onclick="javascript:SendMail();"><img src="<?php echo $mydirectory;?>/images/sendButon.gif" width="68" height="24" alt="send" /></a><a  style="cursor:hand;cursor:pointer;" onclick="javascript:Fade();"><img src="<?php echo $mydirectory;?>/images/discardButton.gif" width="68" height="24" alt="discard" /></a></td>
           	      <td width="10">&nbsp;</td>
       	        </tr>
           	    <tr>
           	      <td width="10" height="30">&nbsp;</td>
           	      <td width="75" class="emailBG"><label for="email">Email :</label></td>
           	      <td class="emailBG"><input name="email" type="text" class="emailTxtBox" id="email" value="" size="35px"  /></td>
           	      <td width="10" class="emailBG">&nbsp;</td>
           	      <td width="10">&nbsp;</td>
       	        </tr>
           	    <tr>
           	      <td height="40">&nbsp;</td>
           	      <td class="emailBG"><label for="subject">Subject :</label></td>
           	      <td class="emailBG"><input  name="subject" type="text" class="emailTxtBox" id="subject" value="Quote Request" size="33px" /></td>
           	      <td class="emailBG">&nbsp;</td>
           	      <td>&nbsp;</td>
       	        </tr>
           	    
       	      </table>
           	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
           	    <tr>
           	      <td width="10" align="left" valign="top">&nbsp;</td>
           	      <td width="10" align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td align="left" valign="top"><div id="divBody" style="width:750px;height:250px; overflow:scroll;"><?php echo $html;?></div></td>
           	      <td width="10" align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td width="10" align="left" valign="top">&nbsp;</td>
       	        </tr>
           	    <tr>
           	      <td align="left" valign="top">&nbsp;</td>
           	      <td align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td align="left" valign="top">&nbsp;</td>
       	        </tr>
       	      </table>
			</fieldset>
		</div>
		<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script type="text/javascript">

function SendMail()
{
  var email = '';
  var subject = '';
  var mailBody = '';
  email = document.getElementById('email').value;
  subject = document.getElementById('subject').value;
  mailBody = document.getElementById('divBody').innerHTML;
  dataString = "email="+email+"&subject="+subject;
  $.ajax({
		 type: "POST",
		 url: "mail_purchaseorder.php",
		 data: dataString,
		 dataType: "json",
		 success: function(data) 
		 {	
			 if(data!=null)
			 {	
				 if(data.name || data.error)
				 {
					 $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				 } 
				 else if(data.email != "")
				 {
					 $("#message").html("<div class='errorMessage'><strong>Email were not send to following email Id's "+ data.email +" </strong></div>"); 
				 }
				 else 
				 {
					 $("#message").html("<div class='successMessage'><strong>Email Send Successfully.</strong></div>");
				 }	
			 }
			 else
			 {
				 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>"); 
			 }
		 }
		 });
  return false;
}

function popOpen()
{
	var popID = 'dialog-form'; //Get Popup Name
	
	popWidth = '800'; $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
	   
	//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;
	//Apply Margin to Popup
	$('#' + popID).css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});	
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 		
	}
	//Close Popups and Fade Layer
	$('span.close, #fade, #cancel').live('click', function() { //When clicking on the close or fade layer...
	Fade();
	return false;
});
function Fade()
{
	$("#message").html('');
	document.getElementById('email').value="";
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
}
function popupWindow() 
{
	var url = "purchaseorderpage.php?print=true&qid=<?php echo $qid;?>";
  //var url = "print_purchase_order.php";
 params  = 'width='+screen.width;
 params += ', height='+screen.height;
 params += ', top=0, left=0'
 params += ', fullscreen=yes';
 params += ', scrollbars=yes';

 newwin=window.open(url,'windowname4', params);
 if (window.focus) {newwin.focus()}
 return false;
}
<?php 
if($isPrint)
{
	?>
	$(document).ready(function()
	{
		window.print();
	});
	<?php
}

?>

</script>
</body>
</html>
