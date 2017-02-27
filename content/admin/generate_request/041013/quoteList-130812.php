<?php
require('Application.php');
if (isset($_GET['qid']) && isset($_GET['opt']) && $_GET['opt'] == 1) {
    $id = $_GET['qid'];
    $sql = "Delete from tbl_quote_items where qid = " . $id . ";";
    $sql.="Delete from tbl_quote where qid = $id";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed delete_quote: " . pg_last_error($connection));
        exit;
    }
    header('location:quoteList.php');
}

if (isset($_GET['qid']) && isset($_GET['opt']) && $_GET['opt'] == 2) {
    $id = $_GET['qid'];

    $query = "select * from tbl_quote where qid=" . $id;
    $result = pg_query($connection, $query);
    $row = pg_fetch_array($result);


    $query = "INSERT INTO tbl_quote ( status";
    if ($row['company_id'] > 0)
        $query.=",company_id ";
    if ($row['vendor_id'] > 0)
        $query.=", vendor_id ";
    if ($row['client_id'] > 0)
        $query.=", client_id ";
    if ($row['ship_to'] > 0)
        $query.=", ship_to ";
    if ($row['other_name'] != "")
        $query.=", other_name ";
    if ($row['other_street'] != "")
        $query.=", other_street ";
    if ($row['other_city'] != "")
        $query.=", other_city ";
    if ($row['other_state'] != "")
        $query.=", other_state ";
    if ($row['other_zip'] != "")
        $query.=", other_zip ";
    if ($row['ship_to_clientfield'] != "")
        $query.=", ship_to_clientfield ";
    if ($row['ship_to_customer_id'] != "")
        $query.=", ship_to_customer_id ";
    if ($row['ship_to_vendorfield'] != "")
        $query.=", ship_to_vendorfield ";
    if ($row['po_number'] != "")
        $query.=", po_number ";
    if ($row['po_date'] != "")
        $query.=", po_date ";
    if ($row['internal_po'] != "")
        $query.=", internal_po ";
    if ($row['shipto_vendor_id'] != "")
        $query.=", shipto_vendor_id ";
    if ($row['good_thru'] != "")
        $query.=", good_thru ";
    if ($row['payment_id'] > 0)
        $query.=", payment_id ";
    if ($row['sales_rep'] > 0)
        $query.=", sales_rep ";
    if ($row['amount_sub_total'] != "")
        $query.=", amount_sub_total ";
    if ($row['tax_sub_total'] != "")
        $query.=", tax_sub_total ";
    if ($row['total'] != "")
        $query.=", total ";
    if ($row['ship_via'] > 0)
        $query.=", ship_via ";
    if ($row['shipperno'] != "")
        $query.=", shipperno ";

    if ($row['carrier_id'] > 0)
        $query.=", carrier_id ";
    if ($row['instruction_notes'] != "")
        $query.=", instruction_notes ";
    if ($row['project_name'] != "")
        $query.=", project_name ";
    $query.=", createdby ";
    $query.=", createddate ";
    $query.=", updateddate ";
    $query.=")";
    $query.=" VALUES ( 1";
    if ($row['company_id'] > 0)
        $query.="," . $row['company_id'];
    if ($row['vendor_id'] > 0)
        $query.="," . $row['vendor_id'];
    if ($row['client_id'] > 0)
        $query.=" ," . $row['client_id'];
    if ($row['ship_to'] > 0)
        $query.=" ," . $row['ship_to'];
    if ($row['other_name'] != "")
        $query.=" ,'" . $row['other_name'] . "'";
    if ($row['other_street'] != "")
        $query.=" ,'" . $row['other_street'] . "'";
    if ($row['other_city'] != "")
        $query.=" ,'" . $row['other_city'] . "'";
    if ($row['other_state'] != "")
        $query.=" ,'" . $row['other_state'] . "'";
    if ($row['other_zip'] != "")
        $query.=" ,'" . $row['other_zip'] . "'";
    if ($row['ship_to_clientfield'] != "")
        $query.=",'" . $row['ship_to_clientfield'] . "'";
    if ($row['ship_to_customer_id'] != "")
        $query.=" ,'" . $row['ship_to_customer_id'] . "'";
    if ($row['ship_to_vendorfield'] != "")
        $query.=" ,'" . $row['ship_to_vendorfield'] . "'";
    if ($row['po_number'] != "")
        $query.=" ,''";
    if ($row['internal_po'] != "")
        $query.=" ,'" . $row['internal_po'] . "'";
    if ($row['po_date'] != "")
        $query.=" ,'" . $row['po_date'] . "'";

    if ($row['shipto_vendor_id'] != "")
        $query.=" ,'" . $row['shipto_vendor_id'] . "'";
    if ($row['good_thru'] != "")
        $query.=" ,'" . $row['good_thru'] . "'";
    if ($row['payment_id'] > 0)
        $query.=" ,'" . $row['payment_id'] . "'";
    if ($row['sales_rep'] > 0)
        $query.=" ,'" . $row['sales_rep'] . "'";
    if ($row['amount_sub_total'] != "")
        $query.=" ,'" . $row['amount_sub_total'] . "'";
    if ($row['tax_sub_total'] != "")
        $query.=" ,'" . $row['tax_sub_total'] . "'";
    if ($row['total'] != "")
        $query.=" ,'" . $row['total'] . "'";
    if ($row['ship_via'] > 0)
        $query.=" ,'" . $row['ship_via'] . "'";
    if ($row['shipperno'] != "")
        $query.=" ,'" . $row['shipperno'] . "'";

    if ($row['carrier_id'] > 0)
        $query.=" ,'" . $row['carrier_id'] . "'";
    if ($row['instruction_notes'] != "")
        $query.=",'" . $row['instruction_notes'] . "'";
    if ($row['project_name'] != "")
        $query.=",'" . $row['project_name'] . "'";
    $query.=" ,{$_SESSION['employeeID']} ";
    $query.=" ,'" . date(U) . "' ";
    $query.=" ,'" . date(U) . "' ";
    $query.=")";

    // echo $query;
    $result = pg_query($connection, $query);
    pg_free_result($result);
    header('location:quoteList.php');
}
require('../../header.php');
$query = "SELECT q.*,cmp.company,c.client FROM tbl_quote q left join \"clientDB\" c on q.client_id=c.\"ID\" left join tbl_quot_company as cmp on q.company_id = cmp.company_id where q.status = 1 ORDER BY q.qid DESC ";
include('../../pagination.class.php');
$search_sql = "";
$limit = "";
$search_uri = "";
if (!($result = pg_query($connection, $query))) {
    print("Failed quote: " . pg_last_error($connection));
    exit;
}
$items = pg_num_rows($result);
pg_free_result($result);
if ($items > 0) {
    $p = new pagination;
    $p->items($items);
    $p->limit(10); // Limit entries per page
    //$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
    //die($_SERVER['REQUEST_URI']);
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
    } else {
        $p->page = $_GET['paging'];
    }
    //Query for limit paging
    $limit = "LIMIT " . $p->limit . " OFFSET " . ($p->page - 1) * $p->limit;
}
$query = $query . " " . $limit;
if (!($result = pg_query($connection, $query))) {
    print("Failed quote: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_quote[] = $row;
}
pg_free_result($result);
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="left"><input type="button" value="Back" onclick="location.href='../../index.php';" /></td>
        <td>&nbsp;</td>
    </tr>
</table>
<table width="100%">
    <tr>
        <td align="left" valign="top"><center>
        <table width="100%">
            <tr>
                <td align="center" valign="top"><font size="5">Quote Request List<br>
                    <table width="80%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>&nbsp;</td>
                            <td align="right"><input type="button" value="Add Quote Request" onclick="location.href='add_quote.php';" /></td>    
                        </tr>
                    </table>
                    <br>
                    </font>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td width="10">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <thead >
                            <tr class="sortable">
                            <th class="gridHeader sortableB">Client</th> 
                            <th class="gridHeader">Company Name</th>
                            <th class="gridHeader">Project Name</th>
                            <th class="gridHeader">Quote Number</th>
                            <th class="gridHeader">Quote Date</th>
                            <th class="gridHeader">Edit</th>
                            <th class="gridHeader">Delete</th>
                            <th class="gridHeader">Copy</th>
                            </tr>
                        </thead><tbody>
<?php
if (count($data_quote) > 0) {
    for ($i = 0; $i < count($data_quote); $i++) {
        ?>
                                <tr>
                                    <td class="grid001"><?php echo $data_quote[$i]['client']; ?></td>
                                    <td class="grid001"><a href="purchaseorderpage.php?qid=<?php echo $data_quote[$i]['qid']; ?>"><?php echo $data_quote[$i]['company']; ?></a></td>
                                    <td class="grid001"><?php echo $data_quote[$i]['project_name']; ?></td>
                                    <td class="grid001"><?php echo $data_quote[$i]['po_number']; ?></td>
                                    <td class="grid001"><?php if ($data_quote[$i]['po_date'] != "") {
            echo date('m/d/Y', $data_quote[$i]['po_date']);
        } else { ?>&nbsp;<?php } ?></td>
                                    <td class="grid001"><a href="add_quote.php?qid=<?php echo $data_quote[$i]['qid']; ?>"><img src="<?php echo $mydirectory; ?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                                    <td class="grid001"><a href="quoteList.php?qid=<?php echo $data_quote[$i]['qid']; ?>&opt=1"><img src="<?php echo $mydirectory; ?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
                                    <td class="grid001"><a href="quoteList.php?qid=<?php echo $data_quote[$i]['qid']; ?>&opt=2"><img src="<?php echo $mydirectory; ?>/images/copy.jpg" width="24" height="24" alt="copy" /></a></td>
                                </tr>              
        <?php
    }
    echo '</tbody><tr>
			<td width="100%" class="grid001" colspan="8">' . $p->show() . '</td>			
		  </tr>';
} else {
    echo '</tbody><tr><td colspan="8">No Quotes found</td><tr>';
}
?>          
                        <tr >
                            <td colspan="5">&nbsp;</td>
                        </tr></tbody>
                    </table>
                </td>
            </tr>
        </table>
        <p>
    </center></td>
</tr>
</table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<?php
require('../../trailer.php');
?>