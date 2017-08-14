<?php
require('Application.php');
require('../../../header.php');
$query1='SELECT "scaleNameId","styleId","styleNumber","garmentId" from "tbl_invStyle" where "isActive"=1 order by "styleNumber"';
if(!($result_cnt=pg_query($connection,$query1))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt)) {
    $data_style[]=$row_cnt;
}
pg_free_result($result_cnt);
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <table width="100%">
        <tr>
            <td align="center"><font face="arial">
                <center><font size="5">INVENTORY MANAGEMENT</font><br />
                <br />
                <br />
                <table width="50%" border="0" cellspacing="1" cellpadding="1">
                    <tr>
                        <?php if(isset($_SESSION['employeeType']) && $_SESSION['employeeType']<4){ ?>
                            <td><a href="#" data-toggle="modal" data-target="#myModal"><img src="<?php echo $mydirectory;?>/images/color-mgt.jpg" alt="dtab" width="165" height="99" border="0" /></a></td>
                            <td><a href="../database.php"><img src="<?php echo $mydirectory;?>/images/database.jpg" alt="dtab" width="165" height="99" border="0" /></a></td>
                        <?php }?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Select a Style</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                               <label class="control-label">Select a Style</label>
                            </div>
                            <div class="col-md-8">
                                <select name="style" id="style" class="form-control">
                                    <option value="">-------Select a Style---------</option>
                                    <?php
                                    for($i=0; $i < count($data_style); $i++){
                                        if($data_style[$i]['styleNumber']!=""){
                                            echo '<option value="'.$data_style[$i]['styleId'].'"';
                                            if(isset($_REQUEST['styleNumber']) && $_REQUEST['styleNumber']==$data_style[$i]['styleId'])
                                                echo ' selected="selected" ';
                                            echo '>'.$data_style[$i]['styleNumber'].'</option>';}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<?php require('../../../trailer.php'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script>
    $('#style').on('change',function () {
        var styleNumber = $(this).val();
        if(styleNumber == ''){
            alert('Please Select A Style');
            return false;
        }
        window.location.replace('colorManagement.php?styleId='+styleNumber);
    });
</script>
