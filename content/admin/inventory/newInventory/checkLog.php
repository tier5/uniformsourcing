<?php
require('Application.php');
require('../../../header.php');
$sql = '';
$sql = 'SELECT * FROM "tbl_invUpdateLog" log'.
    ' LEFT JOIN "employeeDB" emp ON emp."employeeID"=log."createdBy"'.
    ' WHERE log."styleId"='.$_GET['styleId'].' ORDER BY log."createdAt" DESC';
if(!($result=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($logsQuery = pg_fetch_array($result)){
    $logs[] = $logsQuery;
}
pg_free_result($result);
if(count($logs) == '0'){
    echo 'There is no log for this Style!!!!';
    exit;
}
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<div class="container">
    <div class="row">
        <h1 class="text-center">Change Log</h1>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-inverse">
                    <tr>
                        <th>#</th>
                        <th>Date Time</th>
                        <th>Change By</th>
                        <th>Log</th>
                        <th>Box</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach ($logs as $key=>$log) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo $key+1;
                                ?>
                            </td>
                            <td>
                                <?php
                                echo date("F j, Y, g:i a", strtotime($log['createdAt']));
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['firstname'].' '.$log['lastname'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['type'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['boxId'];
                                ?>
                            </td>
                            <td>
                                <?php
                                if($log['type'] == 'Add Box' || $log['type'] == 'Update Box') {
                                    ?>
                                    <a href="checkQuantityLog.php?logId=<?php echo $log['id'] ?>">
                                        <img src="<?php echo $mydirectory; ?>/images/reportviewEdit.png" border="0">
                                    </a>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

<?php require('../../../trailer.php'); ?>