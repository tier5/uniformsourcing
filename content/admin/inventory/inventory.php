

<?php
    require('Application.php');
    $query1='SELECT "scaleNameId","styleId","styleNumber","garmentId" from "tbl_invStyle" where "isActive"=1 order by "styleNumber"';
    if(!($result_cnt=pg_query($connection,$query1))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while($row_cnt = pg_fetch_array($result_cnt)) {
        $data_style[]=$row_cnt;
    }
    pg_free_result($result_cnt);


    require('../../header.php');
?>
<style>
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 40%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;

    }

    .location-center{width:55%;
        margin:0 auto;}
    .location-center select{border:1px solid #ccc; padding:10px 15px; margin-bottom: 15px;
    width: 70%;}

    .location-center .submitBtn{background: #5cb85c; border: 1px solid #4cae4c; padding: 10px 15px;
    color: #fff; cursor: pointer;}



</style>
<table width="100%">
    <tr>
      <td align="center"><font face="arial">
        <center><font size="5">INVENTORY</font><br />
        <br />
        <br />
        <table width="50%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <?php if(isset($_SESSION['employeeType']) && $_SESSION['employeeType']<4){ ?>
            <td><a href="database.php"><img src="<?php echo $mydirectory;?>/images/database.jpg" alt="dtab" width="165" height="99" border="0" /></a></td>
            <td><a href="styleAdd.php"><img src="<?php echo $mydirectory;?>/images/newInventory.jpg" alt="invtry" width="165" height="99" border="0" /></a></td>
            <?php }?>
            <td><a href="reports.php"><img src="<?php echo $mydirectory;?>/images/reports.jpg" alt="rprts" width="165" height="99" border="0" /></a></td>
            <?php if(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] != 5){ ?>
            <td><a href="location.php"><img src="<?php echo $mydirectory;?>/images/Locations.jpg" alt="location" width="165" height="99" border="0"></a></td>
            <td><a href="auditLog.php"><img src="<?php echo $mydirectory;?>/images/button.jpg" alt="rprts" width="165" height="99" border="0" /></a> </td>
            <td><a href="setting.php"><img src="<?php echo $mydirectory;?>/images/button_update.jpg" alt="rprts" width="165" height="99" border="0" /></a> </td>
            <td><a href="javascript:void(0);" id="myBtn"><img src="<?php echo $mydirectory;?>/images/button_location_update.jpg" alt="rprts" width="165" height="99" border="0" /></a> </td>
            <?php }?>
          </tr>
      </table></td>
    </tr>
</table>

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="body location-center">
            <h1>
                Please Select A Style
            </h1>
            <select name="styleNumber" id="styleNumber">
                <option value="">---- Select Number ----</option>
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

                <button type="button" class="submitBtn">Submit</button>

        </div>
    </div>
</div>
 <?php  require('../../trailer.php'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
    $(document).ready(function () {
        //Submit Button
        $('.submitBtn').click(function () {
            var styleNumber = $('#styleNumber').val();
            if(styleNumber == ''){
                alert('Please Select A Style');
                return false;
            }
            window.location.replace('newInventory/bulkLocation.php?styleId='+styleNumber);
        });
    });
</script>
