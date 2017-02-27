<?php
//set_time_limit(65);
require('Application.php');
require($JSONLIB . 'jsonwrapper.php');
require($PHPLIBDIR . 'mailfunctions.php');
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['id'] = "";
$sql = '';
$prj_data = '';
$empty_value = "<strong>N/A</strong>";
extract($_POST);
if (isset($_POST['id']) && $_POST['id'] > 0) {
    $return_arr['id'] = $_POST['id'];
    $sql = "select distinct(prj.projectname),prj.project_manager1,prj.project_manager2,emp.firstname,emp.lastname,emp.email as empmail,po.qtypeople,po.purchaseorder,cl.client,cl.email,prjstyle.style,prjstyle.retailprice,prj_shipping.tracking_number,prj_shipping.shipping_notes,tbl_carriers.carrier_name from tbl_newproject as prj inner join \"employeeDB\" as emp on emp.\"employeeID\"=prj.project_manager inner join \"clientDB\" as cl on cl.\"ID\" = prj.client left join tbl_prjpurchase as po on po.pid = prj.pid left join tbl_prj_style as prjstyle on prjstyle.prj_style_id=(select tbl_prj_style.prj_style_id from tbl_prj_style inner join tbl_newproject on tbl_prj_style.pid = prj.pid order by tbl_prj_style.prj_style_id limit 1) left join tbl_prjorder_shipping as  prj_shipping on prj_shipping.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1)  left join tbl_carriers on tbl_carriers.carrier_id = prj_shipping.carrier_id  where prj.pid=" . $_POST['id'];

    if (!($result = pg_query($connection, $sql))) {
        $return_arr['error'] = "Failed db query: " . pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($row = pg_fetch_array($result)) {
        $prj_data = $row;
    }
    pg_free_result($result);

    if ($prj_data['project_manager1'] > 0) {
        $sql = "select   email from \"employeeDB\" where \"employeeID\" =" . $prj_data['project_manager1'];

        if (!($result = pg_query($connection, $sql))) {
            $return_arr['error'] = "Failed db query: " . pg_last_error($connection);
            echo json_encode($return_arr);
            return;
        }
        while ($row = pg_fetch_array($result)) {
            $prj_data['email1'] = $row['email'];
        }

        pg_free_result($result);
    }

    if ($prj_data['project_manager2'] > 0) {
        $sql = "select   email from \"employeeDB\"  where \"employeeID\" =" . $prj_data['project_manager2'];

        if (!($result = pg_query($connection, $sql))) {
            $return_arr['error'] = "Failed db query: " . pg_last_error($connection);
            echo json_encode($return_arr);
            return;
        }
        while ($row = pg_fetch_array($result)) {
            $prj_data['email2'] = $row['email'];
        }

        pg_free_result($result);
    }



    $sql = "select tracking_number,shipping_notes,tbl_carriers.carrier_name from tbl_prjorder_shipping left join tbl_carriers on tbl_carriers.carrier_id = tbl_prjorder_shipping.carrier_id where pid =" . $_POST['id'];
    if (!($result = pg_query($connection, $sql))) {
        $return_arr['error'] = "Failed db query: " . pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($row = pg_fetch_array($result)) {
        $prj_data_shipping[] = $row;
    }
    pg_free_result($result);
} else {
    $return_arr['error'] = "Error : No project / purchase order. closed";
    echo json_encode($return_arr);
    return;
}

$sql = "select  distinct style,retailprice, garments from tbl_prj_style where pid =" . $_POST['id'];
// echo $sql; 
if (!($style_result = pg_query($connection, $sql))) {
    $return_arr['error'] = "Failed db query: " . pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}


$subject = 'Purchase Order Number: ' . $prj_data['purchaseorder'] . ' is now closed';
$email_body = '<p>Our records now show ';
$email_body .=$prj_data['client'] . '  <strong>Purchase Order Number: </strong>' . $prj_data['purchaseorder'] . ' is now closed.</p><p>Please make sure that your receivers, invoices and packing slips match our records:</p>';
if (isset($style_result)) {
    $email_body .= "<table width='90%'>
					<tr>
					<td width='30%'><strong>Style number</strong></td>
					<td width='30%'><strong>Quantity</strong> </td>
					<td width='30%'><strong>Retail Price</strong> </td>
					</tr>";
}
while ($row = pg_fetch_array($style_result)) {
    $email_body .= '<tr>';
    $email_body .='<td>';
    if ($row['style'] != "")
        $email_body .=$row['style'];
    else
        $email_body .=$empty_value;
    $email_body .='</td>';
    $email_body .='<td>';
    if ($row['garments'] != "")
        $email_body .=$row['garments'];
    else
        $email_body .=$empty_value;
    $email_body .='</td>';
    $email_body .='<td>';
    if ($row['retailprice'] != "")
        $email_body .=$row['retailprice'];
    else
        $email_body .=$empty_value;
    $email_body .='</td></tr>';
}
pg_free_result($style_result);
$email_body .= '</table>';
$email_body .= '<p> The following tracking numbers are proof of delivery for this order:</p>';

$email_body .= "<table width='90%'>
					<tr>
					<td width='30%'><strong>Carrier</strong></td>
					<td width='30%'><strong>Tracking Number</strong> </td>
					<td width='30%'><strong>Shipping Notes</strong> </td>
					</tr>";
if (count($prj_data_shipping) > 0) {
    for ($i = 0; $i < count($prj_data_shipping); $i++) {
        $email_body .="<tr><td width='30%'>";
        if ($prj_data_shipping[$i]['carrier_name'] != "")
            $email_body .=$prj_data_shipping[$i]['carrier_name'];
        else
            $email_body .=$empty_value;
        $email_body .="</td><td width='30%'>";
        if ($prj_data_shipping[$i]['tracking_number'] != "")
            $email_body .=$prj_data_shipping[$i]['tracking_number'];
        else
            $email_body .=$empty_value;
        $email_body .="</td><td width='30%'>";
        if ($prj_data_shipping[$i]['shipping_notes'] != "")
            $email_body .=$prj_data_shipping[$i]['shipping_notes'];
        else
            $email_body .=$empty_value;
        $email_body .="</td></tr>";
    }
}
else {
    $email_body .= "<tr>
		<td width='30%'><strong>???</strong></td>
		<td width='30%'><strong>???</strong> </td>
		<td width='30%'><strong>???</strong> </td>
		</tr>";
}
$email_body .="</table>";

$email_body .= '<p> If you have any questions, or you show a different received amount, contact our offices at 714.842.1200 immediately.</p><p> We appreciate your business and look forward to years of continued support.</p>';
$email_body .='<p>For additional tracking numbers and order details, log into your account at internal.uniformsourcing.com and access the complete project.</p><p> If you need support to access online files, email <a href="mailto:support@i2net.com">support@i2net.com</a> for access.</p>';
//echo "mail->".$email_body;
$sent_to = array();
$sent_to[0] = 'systems@uniforms.net';
if ($prj_data['email'] != "")
    $sent_to[count($sent_to)] = trim($prj_data['email']);
if (isset($prj_data['email1']) && $prj_data['email1'] != '')
    $sent_to[count($sent_to)] = trim($prj_data['email1']);
if (isset($prj_data['email2']) && $prj_data['email2'] != '')
    $sent_to[count($sent_to)] = trim($prj_data['email2']);
if ($isMailServer == 'true') {
    $mailServerAddress = '127.0.0.1';
}
for ($i = 0; $i < count($sent_to); $i++) {
    $headers = create_smtp_headers($subject, "admin@uniformsourcing.com", $sent_to[$i],'Administrator' , "", "text/html");
    $data = $headers . "<html><body>" . $email_body . "</body></html>";
    if ((send_smtp($mailServerAddress, "admin@uniformsourcing.com", $sent_to[$i], $data)) == false) {
        $return_arr['error'] = "Unable to send email for '{$sent_to[$i]}'. Please check the email address and try again later";
        echo json_encode($return_arr);
        return;
    }
}
echo json_encode($return_arr);
return;
?>