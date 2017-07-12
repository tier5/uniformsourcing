<?php require('Application.php');
include('../../pagination.class.php');
require('../../header.php');
$sql = '';
$sql = 'Select * from "audit_logs" as log';
$sql .= ' LEFT JOIN "tbl_invStyle" as inv ON CAST(log."inventory_id" as VARCHAR(50))=CAST(inv."styleId" as VARCHAR(50))';
$sql .= ' LEFT JOIN "employeeDB" as emp ON CAST(log."employee_id" as VARCHAR(50))=CAST(emp."employeeID" as VARCHAR(50)) order by log."updated_time" desc';
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$items= pg_num_rows($resultProduct);
if($items > 0) {
    $p = new pagination;
    $p->items($items);
    $p->limit(20); // Limit entries per page
    //$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
    //die($_SERVER['REQUEST_URI']);
    $uri= substr($_SERVER['REQUEST_URI'], 0,strpos($_SERVER['REQUEST_URI'], '&paging'));
    if(!$uri) {
        $uri=$_SERVER['REQUEST_URI'].$search_uri;
    }
    $p->target($uri);
    $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
    $p->calculate(); // Calculates what to show
    $p->parameterName('paging');
    $p->adjacents(1); //No. of page away from the current page

    if(!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = $_GET['paging'];
    }
    //Query for limit paging
    $limit = "LIMIT " . $p->limit." OFFSET ".($p->page - 1) * $p->limit;
}
$sql = $sql. " ". $limit;
if(!($result=pg_query($connection,$sql))){
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result)){
    $logs[]=$row;
}
pg_free_result($resultProduct);
//echo "<pre>";print_r($logs);die();
?>
    <table width="100%">
        <tr>
            <td align="center"><font face="arial">
                    <center><font size="5">Audit Log</font><br/><br/><br/>
                        <table width="100%" border="1" cellspacing="1" cellpadding="1" style="font-size: medium">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Time</th>
                                    <th>Logs</th>
                                    <th>Style</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: xx-large">
                            <?php
                                for ($i=0;$i<count($logs);$i++) { ?>
                                    <?php $string=$logs[$i]['log'];?>
                                    <tr>
                                        <td align="center"><strong><?php echo $logs[$i]['firstname'].' '.$logs[$i]['lastname'];?></strong></td>
                                        <td align="center"><strong><?php echo date("d-M-Y h:i:sa", $logs[$i]['updated_time']);?></strong></td>
                                        <td align="center"><strong><?php echo $logs[$i]['log'].'</strong>';?></td>
                                        <td align="center"><strong><?php if($log[$i]['inventory_id'] != 'null') echo $logs[$i]['styleNumber'];?></strong></td>
                                    </tr>
                            <?php    }
                            ?>
                            </tbody>
                            <tr>
                                <td width="100%" class="grid001" colspan="10"><?php echo $p->show();?></td>
                            </tr>
                        </table>
                    </center>
            </td>
        </tr>
    </table>
<?php require('../../trailer.php');
?>