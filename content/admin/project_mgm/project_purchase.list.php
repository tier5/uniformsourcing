<?php
require ('Application.php');
//abc
$current_page = "project_purchase.list.php";
$type = "project_purchase";
$paging = 'paging=';
if (isset($_GET['paging']) && $_GET['paging'] != "") {
    $paging.= $_GET['paging'];
}
else {
    $paging.= 1;
}
if (isset($_SESSION['search_uri']) && $_SESSION['search_uri'] != "") {
    if ($type != $_SESSION['page_type']) {
        $_SESSION['search_uri'] = "";
    }
}
if (isset($_POST['cancel'])) {
    $sql = "";
    $search_sql = "";
    $search_uri = "";
    $_SESSION['search_uri'] = "";
}
$is_session = 0;
$emp_join = "";
$emp_id = "";
$emp_sql = "";
$_SESSION['page'] = $current_page;
// echo "emp_type_id".$_SESSION['employee_type_id'];
// echo "empType".$_SESSION['employeeType'];
if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) {
    $emp_id = $_SESSION['employee_type_id'];
    $emp_join = ' left join tbl_prjvendor pv on pv.pid=prj.pid left join vendor v on v."vendorID"=pv.vid';
    $emp_sql = ' and v."vendorID" =' . $emp_id;
    $is_session = 1;
}
else if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 2)) {
    $emp_id = $_SESSION['employee_type_id'];
    $emp_sql = ' and c."ID" =' . $emp_id;
    $is_session = 1;
}
else if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] == 5)) {
    $emp_id = $_SESSION['employeeID'];
    $emp_sql = ' and (prj."project_manager" = ' . $emp_id . ' or prj."project_manager1" = ' . $emp_id . ' or prj."project_manager2" = ' . $emp_id . ' )';
    //    $is_session = 1;
}
if (isset($_GET['close'])) {
    $qName = '';
    $tbl_list = array(
        array(
            'tbl_newproject',
            'rr'
        ) ,
        array(
            'tbl_prjpurchase',
            'purchaseId'
        ) ,
        array(
            'tbl_prjimage_file',
            'tbl_prjimage_file'
        ) ,
        array(
            'tbl_prj_style_custom',
            'tt'
        ) ,
        array(
            'tbl_prj_style',
            'prj_style_id'
        ) ,
        array(
            'tbl_prjvendor',
            'tbl_vendorid'
        ) ,
        array(
            'tbl_prjsample_uploads',
            'upload_id'
        ) ,
        array(
            'tbl_prjsample_notes',
            'notes_id'
        ) ,
        array(
            'tbl_prj_sample_po_items',
            'id'
        ) ,
        array(
            'tbl_prj_sample_po',
            'id'
        ) ,
        array(
            'tbl_prj_sample',
            'sample_id'
        ) ,
        array(
            'tbl_prjpricing',
            'pricingId'
        ) ,
        array(
            'tbl_prmilestone',
            'id'
        ) ,
        array(
            'tbl_prj_elements',
            'prj_element_id'
        ) ,
        array(
            'tbl_mgt_notes',
            'notesid'
        ) ,
        array(
            'tbl_upload_pack',
            'pid'
        ) ,
        array(
            'tbl_prjorder_shipping',
            'shipping_id'
        ) ,
        array(
            'tbl_prjorder_track_no',
            'track_id'
        ) ,
        array(
            'tbl_qty_shipped',
            'shipping_id'
        )
    );
    $pid = $_GET['close'];
    $qName = '';
    $columns = '';
    $values = '';
    for ($i = 0; $i < count($tbl_list); $i++) {
        if ($qName != '') $qName.= ';';
        $sql = 'SELECT * from "' . $tbl_list[$i][0] . '" where pid=' . $pid;
        // echo $sql;
        unset($prj_data);
        unset($row2);
        if (($r = pg_query($connection, $sql))) {
            /*print("Failed query1: " .$i. pg_last_error($connection));
            exit;     */
            while ($row2 = pg_fetch_array($r)) {
                unset($prj_data);
                $prj_data = $row2;
                $columns = '';
                $values = '';
                /* if($tbl_list[$i][0]=='tbl_prjorder_track_no')
                print_r($prj_data);*/
                $j = 0;
                if ($prj_data[0] != '') {
                    foreach($prj_data as $key => $value) {
                        if ($j % 2 != 0 && $value != "") {
                            if ($columns == '') {
                                $columns.= '"' . $key . '"';
                                $values.= "'" . pg_escape_string($value) . "'";
                            }
                            else {
                                $columns.= ',"' . $key . '"';
                                $values.= ",'" . pg_escape_string($value) . "'";
                            }
                        }
                        $j+= 1;
                    }
                    if ($columns != "" && $values != "") {
                        $qName.= ';insert into "' . $tbl_list[$i][0] . '_closed" (' . $columns . ') values(' . $values . ')';
                    }
                }
            }
            pg_free_result($r);
            if ($tbl_list[$i][0] != 'tbl_newproject') $qName.= ';delete from ' . $tbl_list[$i][0] . ' where pid=' . $pid;
            $sql = '';
        }
        else echo pg_last_error($connection);
    }
    $qName.= ';delete from tbl_newproject where pid=' . $pid;
    // echo $qName;
    if (!($result = pg_query($connection, $qName))) {
        echo pg_last_error($connection);
        exit();
    }
    header("location: project_purchase.list.php?$paging");
}
else if (isset($_GET['del'])) {
    $pid = $_GET['del'];
    $sql = "delete from tbl_prjsample where pid=" . $pid . "; ";
    $sql.= "delete from tbl_prjpurchase where pid =" . $pid . "; ";
    $sql.= "delete from tbl_prjpricing where pid =" . $pid . "; ";
    $sql.= "delete from tbl_prjvendor where pid =" . $pid . "; ";
    $sql.= "delete from tbl_mgt_notes where pid =" . $pid . "; ";
    $sql.= "delete from tbl_prjorder_shipping where pid =" . $pid . "; ";
    if (!($result = pg_query($connection, $sql))) {
        print ("Failed query: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result);
    $sql = 'select elementfile,image from tbl_prj_elements where pid=' . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print ("Failed query: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_file[] = $row;
    }
    pg_free_result($result);
    for ($i = 0; $i < count($data_file); $i++) {
        if (file_exists("$upload_dir" . "" . $data_file[$i]['elementfile'] . "")) {
            @unlink("$upload_dir" . "" . $data_file[$i]['elementfile'] . "");
        }
        if (file_exists("$upload_dir" . "" . $data_file[$i]['image'] . "")) {
            @unlink("$upload_dir" . "" . $data_file[$i]['image'] . "");
        }
    }
    $data_file[] = "";
    $sql = "delete from tbl_prj_elements where pid =" . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print ("Failed query: " . pg_last_error($connection));
        exit;
    }
    $sql = 'select file_name from tbl_prjimage_file where pid=' . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print ("Failed query: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_file[] = $row;
    }
    for ($i = 0; $i < count($data_file); $i++) {
        if (file_exists("$upload_dir" . "" . $data_file[$i]['file_name'] . "")) {
            @unlink("$upload_dir" . "" . $data_file[$i]['file_name'] . "");
        }
    }
    $sql = 'delete from tbl_prjimage_file where pid=' . $pid . "; ";
    $sql.= 'delete from tbl_newproject where pid=' . $pid . ";";
    if (!($result = pg_query($connection, $sql))) {
        print ("Failed query: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result);
    header("location: project_purchase.list.php?$paging");
}
require ('../../header.php');

?>
<script type="text/javascript" src="<?php
echo $mydirectory; ?>/js/jquery.min-1.4.2.js"></script>
<script type="text/javascript">
var cIndex=0;
</script>
<?php
$query1 = 'SELECT distinct(c."client"),"ID", "clientID"  FROM "clientDB" as c inner join tbl_newproject as prj on prj.client=c."ID" left join tbl_prjpurchase as prch on
 prch.pid = prj.pid';
if ($_SESSION['employeeType'] == 1) $query1.= $emp_join;
$query1.= ' WHERE c."active" = \'yes\' and prch.purchaseorder IS NOT NULL and prj.status=1 and c."client" IS NOT NULL';
if ($_SESSION['employeeType'] == 1 || $_SESSION['employeeType'] == 2) $query1.= $emp_sql;
$query1.= ' ORDER BY c."client" ASC';
// echo $query1;
if (!($result1 = pg_query($connection, $query1))) {
    print ("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row1 = pg_fetch_array($result1)) {
    $data1[] = $row1;
}
$query2 = 'SELECT distinct(lastname),"employeeID",firstname FROM tbl_newproject inner join "employeeDB" on tbl_newproject.project_manager="employeeDB"."employeeID" left join tbl_prjpurchase as prch on prch.pid = tbl_newproject.pid  WHERE prch.purchaseorder IS NOT NULL and tbl_newproject.status=1';
// echo $query2;
if (!($result1 = pg_query($connection, $query2))) {
    print ("Failed query1: " . pg_last_error($connection));
    exit;
}
unset($data2);
while ($row2 = pg_fetch_array($result1)) {
    $data2[] = $row2;
}
$query3 = "SELECT \"vendorID\",\"vendorName\" from vendor where active='yes' ORDER BY \"vendorName\" asc";
if (!($result3 = pg_query($connection, $query3))) {
    print ("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row3 = pg_fetch_array($result3)) {
    $vendorData[] = $row3;
}
pg_free_result($result3);
$prjname = array();
$sql = "select Distinct(p.projectname),prch.purchaseorder as po,p.pid from tbl_newproject as p  inner join \"clientDB\" as c on 
        c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid where  prch.purchaseorder!='' and p.status =1  order by p.projectname";
if (!($result = pg_query($connection, $sql))) {
    print ("Failed query: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $prjname[] = $row;
}
pg_free_result($result);
include ('../../pagination.class.php');

$search_sql = "";
$limit = "";
$search_uri = "";
if (isset($_REQUEST['cid']) && $_REQUEST['cid'] != "") {
    $search_sql = ' and prj.client =' . $_REQUEST['cid'] . ' ';
    $search_uri = "?cid=" . $_REQUEST['cid'];
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != "") {
    $search_sql.= ' and prj.pid =' . $_REQUEST['pid'] . ' ';
    if ($search_uri) {
        $search_uri.= "&pid=" . $_REQUEST['pid'];
    }
    else {
        $search_uri.= "?pid=" . $_REQUEST['pid'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['manager_id']) && $_REQUEST['manager_id'] != "") {
    $search_sql.= ' and prj.project_manager =' . $_REQUEST['manager_id'] . ' ';
    if ($search_uri) {
        $search_uri.= "&manager_id=" . $_REQUEST['manager_id'];
    }
    else {
        $search_uri.= "?manager_id=" . $_REQUEST['manager_id'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['manager_id1']) && $_REQUEST['manager_id1'] != "") {
    $search_sql.= ' and prj.project_manager1 =' . $_REQUEST['manager_id1'] . ' ';
    if ($search_uri) {
        $search_uri.= '&manager_id1=' . $_REQUEST['manager_id1'];
    }
    else {
        $search_uri.= "?manager_id1=" . $_REQUEST['manager_id1'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['manager_id2']) && $_REQUEST['manager_id2'] != "") {
    $search_sql.= ' and prj.project_manager2 =' . $_REQUEST['manager_id2'] . ' ';
    if ($search_uri) {
        $search_uri.= '&manager_id2=' . $_REQUEST['manager_id2'];
    }
    else {
        $search_uri.= "?manager_id2=" . $_REQUEST['manager_id2'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
/*if (isset($_REQUEST['purchaseorder']) && $_REQUEST['purchaseorder'] != "") {
$search_sql .=' and prch.purchaseorder =' . $_REQUEST['purchaseorder'] . ' ';

$_SESSION['search_uri'] = $search_uri;
}
*/
/*----worked on purchase order drop down on 17052018---*/
if (isset($_REQUEST['purchaseorder']) && $_REQUEST['purchaseorder'] != "") {
    $search_sql.= " and prch.purchaseorder ='" . $_REQUEST['purchaseorder'] . "'";
    if ($search_uri) {
        $search_uri.= "&purchaseorder='" . $_REQUEST['purchaseorder'] . "'";
    }
    else {
        $search_uri.= "?purchaseorder='" . $_REQUEST['purchaseorder'] . "'";
    }
    $_SESSION['search_uri'] = $search_uri;
}
/*----end for worked on purchase order drop down on 17052018---*/
if (isset($_REQUEST['vendorId']) && $_REQUEST['vendorId'] != "") {
    $search_sql.= ' and pv.vid =' . $_REQUEST['vendorId'] . ' ';
    if ($search_uri) {
        $search_uri.= "&vendorId=" . $_REQUEST['vendorId'];
    }
    else {
        $search_uri.= "?vendorId=" . $_REQUEST['vendorId'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
if ($_SESSION['search_uri'] != "") {
    $_SESSION['page_type'] = $type;
}
$sql = 'select Distinct(prj.projectname),ship.carrier_id,track_nos.tracking_no, prj.is_billed, prj.bill_date, prj.updateddate,prj.order_eta_on, prch.createddate,note.*,prch.createdDate as date,c.client,prj.pid,prj.order_placeon,prj.status,emp.firstname,emp.lastname,prch.purchaseorder,prch.pt_invoice,tbl_carriers.weblink,
prc.prjquote,prch.purchaseduedate,prc.prjcost,ship.tracking_number ,prc.prj_completioncost ,pro.prdtntrgtdelvry from tbl_newproject as prj inner join tbl_prjpurchase as prch on
 prch.pid = prj.pid ';
if ($emp_join == "") $sql.= 'left join tbl_prjvendor pv on pv.pid=prj.pid ';
$sql.= 'left join tbl_prjorder_shipping as ship on ship.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join
  tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1) left join tbl_carriers on tbl_carriers.carrier_id = ship.carrier_id left join tbl_prjpricing as prc on prc.pid = prj.pid' . ' left join tbl_prjorder_track_no as track_nos on track_nos.track_id= (select track.track_id from tbl_prjorder_track_no as track where track.shipping_id=ship.shipping_id order by track.track_id desc limit 1) '
/*.' left join tbl_prjorder_track_no as track_nos on track_nos.shipping_id=(select ship.shipping_id from tbl_prjorder_shipping '
.' where shipping_id=ship.shipping_id and track_nos.track_id=(select max(track_id) from tbl_prjorder_track_no where shipping_id=ship.shipping_id)) '*/ . ' left join "clientDB" c on prj.client=c."ID" left join tbl_mgt_notes note on note.notesid=( select notesid from tbl_mgt_notes where  pid=prj.pid order by notesid desc limit 1) left join tbl_prmilestone as pro on pro.pid = prj.pid  left join "employeeDB" as emp on emp."employeeID"= prj.project_manager ' . $emp_join . ' where prj.status =1' . $search_sql . $emp_sql . ' and prch.purchaseorder <> \'\' ' . $search_sql . ' order by prch.createddate desc ';
if (!($resultp = pg_query($connection, $sql))) {
    print ("Failed queryde: " . pg_last_error($connection));
    // exit;
}
$items = pg_num_rows($resultp);
if ($items > 0) {
    $p = new pagination;
    $p->items($items);
    $p->limit(10); // Limit entries per page
    $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&paging'));
    if (!$uri) {
        $uri = $_SERVER['REQUEST_URI'] . $search_uri;
    }
    $p->target($uri);
    $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
    $p->calculate(); // Calculates what to show
    $p->parameterName('paging');
    $p->adjacents(1); //No. of page away from the current page
    if (!isset($_GET['paging'])) {
        $p->page = 1;
    }
    else {
        $p->page = $_GET['paging'];
    }
    // Query for limit paging
    $limit = "LIMIT " . $p->limit . " OFFSET " . ($p->page - 1) * $p->limit;
}
$sql = $sql . " " . $limit;
// echo $sql;
if (!($resultp = pg_query($connection, $sql))) {
    print ("Failed queryd: " . pg_last_error($connection));
    exit;
}
while ($rowd = pg_fetch_array($resultp)) {
    $datalist[] = $rowd;
}
echo '<script type="text/javascript" src="' . $mydirectory . '/js/tablesort.js"></script>';
?>  
    <center><blockquote><font face="arial">
    <font size="5">
    Purchase Order</font><br/><br/>
    
    </font>
</blockquote><center>
<div style="width:50%" id="message"></div></center>
<table border="0" width="40%">
<?php
if ($is_session != 1) {
?>
<tr><td align="center"><input type="button" value="Add New Projects" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='project_mgm.add.php?pge=pm'" style="cursor: pointer;"></td>
<td align="center">&nbsp;</td>
<td valign="top" align="left"><input type="button" value="View Closed Projects" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='project_purchase.closed.php'" style="cursor: pointer;"></td>
 <td align="left"><input type="button" value="Genereate Spreadsheet" onclick="javascript:GenerateSpreadSheet();" /></td>
 <td align="center"><input type="button" value="Send Email" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:SendEmail();" style="cursor: pointer;"></td>
</tr>
<?php
}
?>
</table>

<!-- ROW 1: Client Name - Purchase Order - Project Name
ROW 2: Project Manager - Secondary Project Manager 1 - Secondary Project Manager 2
ROW 3: Vendor -->
<form action="project_purchase.list.php" method="post" name="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td height="35" colspan="5" ><strong>Search Projects </strong></td>
 </tr>
<tr>
    <td width="100px" class="grid001"> Client Name: </td>
    <td width="300px" class="grid001"><select name="cid" id="cid" class="cid" style="width:200px;">
        <option value="">-----Select------</option>
        <?php
for ($i = 0; $i < count($data1); $i++) {
    echo "<option value=\"" . $data1[$i]['ID'] . "\">" . $data1[$i]['client'] . "</option>";
} ?>

    </select> </td>
    <!--worked on purchase order drop down on 17052018-->
     <td width="100px" class="grid001">Purchase Order: </td>
     <td width="300px" class="grid001">
        <select name="purchaseorder" id="purchaseorder" class="purchaseorder" style="width:200px;">
            <option value="">-----Select------</option>
           <?php
            foreach($prjname as $prj) {
            echo '<option value="' . $prj['po'] . '">' . $prj['po'] . '</option>';
            } ?>
        </select>
     </td>
    <!--worked on purchase order drop down on 17052018-->
     <td width="110px" class="grid001">Project Name: </td>
     <td class="grid001">
     <select name="pid" id="pid" class="pid" style="width:200px;">
        <option value="">-----Select------</option>
        <?php
        foreach($prjname as $prj) {
            echo '<option value="' . $prj['pid'] . '">' . $prj['projectname'] . '</option>';
        }
        ?>
     </select>
     </td>

     </tr><tr>
    
    
    
    <td width="100px" class="grid001"> Project Manager: </td> 
    <td width="300px" class="grid001"><select name="manager_id" id="manager_id" style="width:200px;" >
        <option value="">-----Select------</option>
        <?php
for ($i = 0; $i < count($data2); $i++) {
    echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
} ?>
    </select> </td> 
    <td width="100px" class="grid001"> Secondary Project Manager 1: </td> 
    <td width="300px" class="grid001">
            <select name="manager_id1" id="manager_id1" style="width:200px;">
                <option value="">-----Select------</option>
                <?php
                for ($i = 0; $i < count($data2); $i++) {
                    echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
                }
                ?>
            </select> 
    </td> 
                        
    <td width="100px" class="grid001"> Secondary Project Manager 2: </td> 
    <td width="300px" class="grid001">
        <select name="manager_id2" id="manager_id2" style="width:200px;">
            <option value="">-----Select------</option>
            <?php
            for ($i = 0; $i < count($data2); $i++) {
                echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
            }
            ?>
        </select> 
    </td>

</tr>
        
        <tr>
            <?php
            if ($is_session != 1) {
            ?>
                <td width="110px" class="grid001">Vendor: </td>
                <td class="grid001" colspan="6">
                <select id="vendorId" class="vid" name="vendorId" style="width:200px;">
                                      <option value="">---- Select Vendor ----</option>
                                       <?php
                for ($i = 0; $i < count($vendorData); $i++) {
            ?>
                                      <option value="<?php
                    echo $vendorData[$i]['vendorID']; ?>"><?php
                    echo $vendorData[$i]['vendorName']; ?></option>
                                      <?php
                }
            ?>
                </select>
                </td>
             <?php
            }
            ?>

            
        </tr>
        

        <tr>            <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"> <input type="submit" value="Cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody></table>
</form>
<div style="width:100%;overflow-x:scroll;">
<form action="" name="frm_send_email" id="frm_send_email">
<table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">

    <thead style="border:1px white solid;" >
    <tr class="sortable" > 
     <th class="gridHeaderBClose" height="10" style="width:100px;"><center>Select All</center>CSR<input type="checkbox" name="csrAll" id="csrAll" /> VSR<input type="checkbox" name="vsrAll" id="vsrAll" /></th>
            <th class="sortableB" height="10" style="width:300px;">Client </th>
            <th class="sortableB" height="10" style="width:200px;">Project Manager</th>
            <th class="sortableB" style="width:200px;">Project Name</th>
            <th class="sortable-numericB" style="width:150px;">Purchase Order</th>
            <th class="sortable-numericB" style="width:100px;">PT Invoice</th>
            <th class="sortable-numericB" style="width:100px;">PO Due Date</th>
            <th class="sortable-numericB" style="width:100px;">PO Total</th>
            <th class="sortable-numericB" style="width:100px;">Order Placed On</th>
            <th class="sortable-numericB" style="width:200px;">Tracking Number</th>
            <!--<th class="sortable-numericB" style="width:100px;">Target Delivery</th>-->
            <th class="sortable-numericB" style="width:100px;">Order ETA</th>
<?php
/*if($is_session !=1)
echo '<th class="sortable-numericB" style="width:200px;">Billed</th>';*/
?>
            
            <th class="sortable-numericB" style="width:50px;">Edit</th>
<?php
if ($is_session != 1) {
?>
            <th class="sortable-numericB" style="width:50px;">Mail</th>
             <th class="sortable-numericB">Push PO</th>
            <th class="sortable-numericB" style="width:50px;">Close</th>
<?php
}
?>
          </tr>
    
          </thead><tbody>
          <?php
if (count($datalist)) {
    for ($i = 0; $i < count($datalist); $i++) {
        $po_style = 'style="color:#000;"';
        if (trim($datalist[$i]['purchaseduedate']) != '') {
            $today = strtotime(date("Y-m-d"));
            $timestamp = strtotime($datalist[$i]['purchaseduedate']);
            if ($timestamp < $today) $po_style = 'style="color:red;"';
        }
        $orderedData = array();
        $shippedData = array();
        $query_ordered = "SELECT \"garments\", \"prj_style_id\" from tbl_prj_style where \"pid\" =" . $datalist[$i]['pid'];
        if (!($result_ordered = pg_query($connection, $query_ordered))) {
            print ("Failed result_ordered: " . pg_last_error($connection));
            exit;
        }
        while ($row_ordered = pg_fetch_array($result_ordered, NULL, PGSQL_NUM)) {
            $orderedData[] = $row_ordered;
        }
        pg_free_result($result_ordered);
        if (!empty($orderedData) && is_array($orderedData)) {
            $query_shipped = "SELECT qty_ship from tbl_qty_shipped where pid=" . $datalist[$i]['pid'];
            if (!($result_shipped = pg_query($connection, $query_shipped))) {
                print ("Failed result_shipped: " . pg_last_error($connection));
                exit;
            }
            while ($row_shipped = pg_fetch_array($result_shipped, NULL, PGSQL_NUM)) {
                $shippedData[] = $row_shipped;
            }
            pg_free_result($result_shipped);
        }
        $sqlts0 = "select garments from tbl_prj_style where pid =" . $datalist[$i]['pid'];
        if (!($resultts0 = pg_query($connection, $sqlts0))) {
            print ("Failed query1: " . pg_last_error($connection));
            exit;
        }
        $data_order0 = array();
        while ($rowts0 = pg_fetch_array($resultts0)) {
            $data_order0[] = $rowts0;
        }
        $sum_all = 0;
        foreach($data_order0 as $obj) {
            $sum_all+= $obj[0];
        }
        $sqlts1 = "select sum(qty_ship) totalShippedItems from  tbl_prjorder_shipping inner join tbl_qty_shipped on tbl_prjorder_shipping.shipping_id = tbl_qty_shipped.shipping_id where tbl_prjorder_shipping.status=1 and tbl_qty_shipped.pid = " . $datalist[$i]['pid'];
        if (!($resultts1 = pg_query($connection, $sqlts1))) {
            print ("Failed query1: " . pg_last_error($connection));
            exit;
        }
        $shipped = pg_fetch_array($resultts1);
        $cntShip = $shipped[0];
        $color = "grid001B";
        if ((!empty($shippedData)) && (!empty($orderedData))) {
            $left_to_shipped = array();
            foreach($orderedData as $key => $value) {
                $left_to_shipped[] = ($orderedData[$key][0] - $shippedData[$key][0]);
            }
            $checkZero = array_filter($left_to_shipped);
            if ($cntShip >= $sum_all) {
                $color = "gridgreen";
            }
            else {
                $color = "grid001B";
            }
        }
        echo "<tr>";
        echo '<td class="' . $color . '">CSR<input type="checkbox" name="csr[]" value="' . $datalist[$i]['pid'] . '"><br />VSR<input type="checkbox" name="vsr[]" value="' . $datalist[$i]['pid'] . '"></td>';
        echo '<td class="' . $color . '">' . $datalist[$i]['client'] . '</td>';
        echo '<td class="' . $color . '">' . $datalist[$i]['firstname'] . $datalist[$i]['lastname'] . '</td>';
        echo '<td class="' . $color . '">' . str_replace("''", "'", $datalist[$i]['projectname']) . '</td>';
        echo '<td class="' . $color . '">' . $datalist[$i]['purchaseorder'] . '</td>';
        echo '<td class="' . $color . '">' . $datalist[$i]['pt_invoice'] . '</td>';
        echo '<td class="' . $color . '" ' . $po_style . '>' . $datalist[$i]['purchaseduedate'] . '</td>';
        echo '<td class="' . $color . '">$' . $datalist[$i]['prjquote'] . '</td>';
        echo '<td class="' . $color . '">' . $datalist[$i]['order_placeon'] . '</td>';
        if ($datalist[$i]['tracking_number'] != '' && $datalist[$i]['tracking_number'] != 'Array') {
            echo '<td class="' . $color . '">';
            if (isset($datalist[$i]['carrier_id']) && $datalist[$i]['carrier_id'] == 49) {
                echo 'Hand Delivered';
            }
            else echo '<a href="javascript:void(0);" onclick="javascript:popupWindow(\'' . $datalist[$i]['weblink'] . $datalist[$i]['tracking_number'] . '\');">' . $datalist[$i]['tracking_number'] . '</a>';
            echo '</td>';
        }
        else {
            echo '<td class="' . $color . '">';
            if (isset($datalist[$i]['carrier_id']) && $datalist[$i]['carrier_id'] == 49) {
                echo 'Hand Delivered';
            }
            else echo '<a href="javascript:void(0);" onclick="javascript:popupWindow(\'' . $datalist[$i]['weblink'] . $datalist[$i]['tracking_no'] . '\');">' . $datalist[$i]['tracking_no'] . '</a>';
            echo '</td>';
        }
        /*echo '<td class="'.$color.'">'.$datalist[$i]['prdtntrgtdelvry'].'</td>';*/
        echo '<td class="' . $color . '">';
        if ($datalist[$i]['order_eta_on'] != "") echo $datalist[$i]['order_eta_on'];
        echo '</td>';
        if ($is_session != 1) {
            /*echo '<td class="'.$color.'" id="bill_'.$i.'" > <div style="cursor:pointer;cursor:hand;" onclick="javascript:editBilledinfo('.$datalist[$i]['pid'].', \'load\', '.$i.');" >';
            if($datalist[$i]['is_billed'] != '' && $datalist[$i]['is_billed'] > 0)
            echo '&nbsp;Yes&nbsp;:&nbsp;'.date('m/d/Y',$datalist[$i]['bill_date']);
            else
            echo '&nbsp;No&nbsp;';
            echo '</div></td>';*/
        }
        echo '<td class="' . $color . '"><a href="project_mgm.add.php?id=' . $datalist[$i]['pid'] . '&' . $paging . '"><img src="' . $mydirectory . '/images/edit.png" alt="edit" /></a></td>';
        if ($is_session != 1) {
            echo '<td class="' . $color . '"><a style="cursor:hand;cursor:pointer;"  onclick="javascript:popOpen(' . $datalist[$i]['pid'] . ');"><img src="' . $mydirectory . '/images/email.png" alt="send" /></a></td>';
            echo '<td class="' . $color . '"><a style="cursor:hand;cursor:pointer;"  onclick="javascript:pushPO(' . $datalist[$i]['pid'] . ');"><img src="' . $mydirectory . '/images/push.png" alt="Push" /></a></td>';
            echo '<td class="' . $color . '"><img style="cursor:hand;cursor:pointer;" onclick="javascript:closedNotificationMail(' . $datalist[$i]['pid'] . ');" src="' . $mydirectory . '/images/close.png" border="0"></td>';
            // echo '<td class="'.$color.'"><a href="project_purchase.list.php?close='.$datalist[$i]['pid'].'" onclick="javascript: if(confirm(\'Are you sure you want to close the project\')) { return true; } else { return false; }"><img src="'.$mydirectory.'/images/close.png" border="0"></a></td>';
        }
        echo "</tr>";
    }
    echo '</tbody><tr>
            <td width="100%" class="' . $color . '" colspan="16">' . $p->show() . '</td>            
          </tr>';
}
else {
    echo "</tbody><tr>";
    echo '<td align="left" colspan="14"><font face="arial"><b>No Project Found</b></font></td>';
    echo "</tr>";
}
?>
</table>
</form>
</div>
</center>
<div id="dialog-form" title="Submit By Email" class="popup_block">
      <div align="center" id="msg_email"></div>
              <p>All form fields are required.</p>  
              <fieldset>
              <form id="pop" name="pop" method="post" >
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="10">&nbsp;</td>
                  <td colspan="3">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="10" height="30">&nbsp;</td>
                    <td colspan="3" class="emailBG"><a style="cursor:hand;cursor:pointer;" onclick="javascript:SendMail();"><img src="<?php
echo $mydirectory; ?>/images/sendButon.jpg" width="68" height="24" alt="send" /></a><a  style="cursor:hand;cursor:pointer;" onclick="javascript:Fade();"><img src="<?php
echo $mydirectory; ?>/images/discardButton.jpg" width="68" height="24" alt="discard" /></a></td>
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
                    <td class="emailBG"> <label for="subject">Subject :</label></td>
                    <td class="emailBG"><input  name="subject" type="text" class="emailTxtBox" id="subject" value="" size="35px" /><input type="hidden" id="email_pid" name="email_pid" value="0" /></td>
                    <td class="emailBG">&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                

               </form>
                <p>
                  
                </p>
              </fieldset>
          </div>
<!--<div id="project_pop" class="popup_block"></div>-->
<script type="text/javascript" src="<?php
echo $mydirectory; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php
echo $mydirectory; ?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="./project_popup.js"></script>
<script type="text/javascript" src="<?php
echo $mydirectory; ?>/js/PopupBox.js"></script>
<script type="text/javascript"><!--
$(document).ready(function()
{
$("#manager_id").change(function()
{
    if(document.getElementById('manager_id').value)
    {
        var id=$(this).val();
        var dataString = 'number=0&prj_manager='+ id+'&client='; 
        var clientVal=document.getElementById('cid').value;
        if(clientVal)
        dataString += clientVal;
        else
        dataString += "";
        $.ajax
        ({
            type: "POST",
            url: "prch_projectname.list.php",
            data: dataString,
            cache: false,
            success: function(html)
            {
            $("#pid").html(html);
            if(clientVal=="")
            {
                $.ajax
                ({
                    type: "POST",
                    url: "prch_clientname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                    $("#cid").html(html);
                    
                    } 
                });
            }
            } 
        });
    }
});
        $("#manager_id1").change(function()
        {
            if(document.getElementById('manager_id1').value)
            {
                var id=$(this).val();
                var dataString = 'number=1&prj_manager1='+ id+'&client='; 
                var clientVal=document.getElementById('cid').value;
                if(clientVal)
                    dataString += clientVal;
                else
                    dataString += "";
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
                        if(clientVal=="")
                        {
                            $.ajax
                            ({
                                type: "POST",
                                url: "prj_clientname.list.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#cid").html(html);
                    
                                } 
                            });
                        }
                    } 
                });
            }
        });



        $("#manager_id2").change(function()
        {
            if(document.getElementById('manager_id2').value)
            {
                var id=$(this).val();
                var dataString = 'number=2&prj_manager2='+ id+'&client='; 
                var clientVal=document.getElementById('cid').value;
                if(clientVal)
                    dataString += clientVal;
                else
                    dataString += "";
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
                        if(clientVal=="")
                        {
                            $.ajax
                            ({
                                type: "POST",
                                url: "prj_clientname.list.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#cid").html(html);
                    
                                } 
                            });
                        }
                    } 
                });
            }
        });
$("#cid").change(function()
{
    if(document.getElementById('cid').value)
    {
        var id=$(this).val();
        var dataString = 'client='+ id; 
        
        $.ajax
        ({
            type: "POST",
            url: "prch_projectname.list.php",
            data: dataString,
            cache: false,
            success: function(html)
            {
            $("#pid").html(html);
            
            } 
        });
    }
});
$("#vendorId").change(function()
{
    if(document.getElementById('vendorId').value)
    {
        var id=$(this).val();
        var dataString = 'vendor='+ id;     
        $.ajax
        ({
            type: "POST",
            url: "prch_projectname.list.php",
            data: dataString,
            cache: false,
            success: function(html)
            {
            $("#pid").html(html);
            
            } 
        });
    }
});
});
function GenerateSpreadSheet()
{
    dataString ='';
    <?php
if ($search_uri != "") {
    echo "dataString=\"" . substr($search_uri, 1) . "\";";
}
?>  
    $.ajax({
           type: "POST",
           url: "spreadsheet_purchase.php",
           data: dataString,
           dataType: "json",
           success:function(data)
            {
                if(data!=null)
                {
                    if(data.name || data.error)
                    {
                        $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
                    } 
                    else
                    {   
                        $("#message").html("<div class='successMessage'><strong>Spread sheet generated successfully...</strong></div>");
                        location.href='download.php?file='+data.fileName;
                    }
                }
                else
                {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                }
                
            }
        });
}
function popupWindow(type) 
{
    var url = "";
    url = type;
 params  = 'width='+screen.width;
 params += ', height='+screen.height;
 params += ', top=0, left=0'
 params += ', fullscreen=yes';
 params += ', scrollbars=yes';

 newwin=window.open(url,'windowname4', params);
 if (window.focus) {newwin.focus()}
 return false;
}
function closedNotificationMail(pid)
{
    if(!confirm('Are you sure you want to close the project ?')){ return false; }
    dataString ='id='+pid;
    // alert(dataString);
    $.ajax({
       type: "POST",
       url: "closed_notification_mail.php",
       data: dataString,
       dataType: "json",
           timeout:60000,
       success:function(data)
        {
            if(data!=null)
            {
                if(data.name || data.error)
                {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
                                   if(confirm("Failed to send a notification mail to the user.Do yo want to close this purchase order without notification ? "))
                                       {
                                         location.href='project_purchase.list.php?close='+data.id;  
                                       }
                } 
                else
                {
                    location.href='project_purchase.list.php?close='+data.id;
                }
            }
            else
            {
                $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
            }
            
        },
          error: function (request, status, error) {
             if(confirm("Failed to send a notification mail to the user.Do yo want to close this purchase order without notification ? "))
                                       {
                                         location.href='project_purchase.list.php?close='+pid;  
                                       }
            }
    });
}
$('#csrAll').change(function() {
     $("INPUT[name='csr\\[\\]']").attr('checked', $('#csrAll').is(':checked'));   
   }
)
$('#vsrAll').change(function() {
     $("INPUT[name='vsr\\[\\]']").attr('checked', $('#vsrAll').is(':checked'));   
   }
)
function SendEmail()
{
    dataString ='';
    dataString = $("#frm_send_email").serialize();
    $.ajax({
           type: "POST",
           url: "send_email.php",
           data: dataString,
           dataType: "json",
           success:function(data)
            {
                if(data!=null)
                {
                    if(data.name || data.error)
                    {
                        $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
                    } 
                    else
                    {   
                        $("#message").html("<div class='successMessage'><strong>Mail Send successfully...</strong></div>");
                    }
                }
                else
                {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                }
                
            }
        });
}
function showDate(obj)
{
    $(obj).datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(obj).datepicker('show'); });
    $(obj).datepicker('show');
}
function editBilledinfo(pid,type,td_id)
{
    dataString ='pid='+pid+'&type='+type+'&td='+td_id;
    if(type == 'save')
    {
        if($('#is_billed_'+td_id).is(":checked"))
            dataString += '&is_billed=1';
        else
            dataString += '&is_billed=0';
        if($('#bill_date_'+td_id).val() != '') dataString += '&bill_date='+$('#bill_date_'+td_id).val(); else dataString += '&bill_date=0';
    }   
    $.ajax({
           type: "POST",
           url: "edit_bill.php",
           data: dataString,
           dataType: "json",
           success:function(data)
            {
                if(data!=null)
                {
                    $('#bill_'+data.td).html(data.msg);
                }
                else
                {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                }
                
            }
        });
}

function pushPO(pid)
{
var data='pid='+pid;    
  $.ajax({
data:data,
type:'post',
url:'push_po.php',
success:function(res){$("#message").html(res);},
error:function(){}
  });  
    
}
</script>
<?php
require ('../../trailer.php');

?>