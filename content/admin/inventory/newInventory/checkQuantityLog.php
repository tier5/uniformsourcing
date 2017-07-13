<?php
require('Application.php');
require('../../../header.php');

$sql = '';
$sql = 'SELECT * FROM "tbl_invUpdateLogQuantity" WHERE "logId" = '.$_GET['logId'];
if(!($result=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($logsQuery = pg_fetch_array($result)){
    $logs[] = $logsQuery;
}
pg_free_result($result);
if(count($logs) == '0'){
    echo '<h1>There is no log for this Style!!!!</h1>';
    exit;
}
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<div class="container">
    <div class="row">
        <h1 class="text-center">Check Log Details</h1>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-inverse">
                    <tr>
                        <th>#</th>
                        <th>Log Type</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>Main Size</th>
                        <th>Optional Size</th>
                    </tr>
                    <?php
                    foreach ($logs as $key=>$log){
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo $key+1;
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['log'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['oldValue'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $log['newValue'];
                                ?>
                            </td>
                            <td>
                                <?php
                                if($log['mainSize'] == 0){
                                    echo 'N/A';
                                } else {
                                    echo getSizeName($log['mainSize'],'mainSize',$connection);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if($log['optSize'] == 0){
                                    echo 'N/A;';
                                } else {
                                    echo getSizeName($log['optSize'],'opt1Size',$connection);;
                                }
                                ?>
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

<?php
require('../../../trailer.php');
?>

