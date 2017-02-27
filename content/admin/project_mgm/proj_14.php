<?php

require('Application.php');
$is_session = 0;
$emp_type = "";
$emp_id = "";
$pid = $_POST['pid'];
$return_arr = array();
$return_arr['name'] = '';
$return_arr['html'] = '';
$return_arr['error'] = '';

if (!$pid) {
    $bid .='Choose BID</em>: <select name="bid" id="bid">' .
            '<option value="">------------SELECT-------------</option>';
    $bid .='</select>';
    $return_arr['html'] = $bid;
    $return_arr['error'] = '';
    echo json_encode($return_arr);
    return;
}
$query2 = "SELECT p.client as cid, po.bid, po.purchaseorder FROM tbl_newproject as p left join tbl_prjpurchase as po on po.pid=p.pid where p.pid=$pid";
if (!($result1 = pg_query($connection, $query2))) {
    $return_arr['error'] = "Failed project query: " . pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while ($row1 = pg_fetch_array($result1)) {
    $project = $row1;
}
if($project['purchaseorder'] == '')
{
    $return_arr['error'] = "ERROR: Please enter Purchase order first..!";
    echo json_encode($return_arr);
    return;
}
if (isset($project) && $project['cid'] != '' && $project['cid'] > 0) {
    $query1 = "SELECT qid,project_name,po_number FROM tbl_quote where client_id = {$project['cid']}";
    if (!($result1 = pg_query($connection, $query1))) {
        $return_arr['error'] = "Failed quote query: " . pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($row1 = pg_fetch_array($result1)) {
        $quote[] = $row1;
    }
    $bid = 'Choose BID</em>: <select name="bid" id="bid">' .
            '<option value="">------------SELECT-------------</option>';
    if (isset($quote)) {
        for ($i = 0; $i < count($quote); $i++) {
            $bid .="<option value=\"" . $quote[$i]['qid'] . "\"   ";
            if ($project["bid"] == $quote[$i]['qid'])
                $bid.=" selected='selected' ";
            $bid.=">" . $quote[$i]['po_number'] . " - " . $quote[$i]['project_name'] . "</option>";
        }
    }
    $bid .='</select>';
} else {
    $bid .='Choose BID</em>: <select name="bid" id="bid">' .
            '<option value="">------------SELECT-------------</option>';
    $bid .='</select>';
    $return_arr['html'] = $bid;
    echo json_encode($return_arr);
    return;
}

//echo $notification;
$return_arr['html'] = $bid;
echo json_encode($return_arr);
return;
?>
