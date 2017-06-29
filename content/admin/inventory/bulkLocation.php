<?php
    require('Application.php');
    require('../../header.php');
    include('../../pagination.class.php');
$sql = '';
$sql='select DISTINCT storage.unit, st."styleNumber", sn."scaleId",sn."scaleName",st.*,g."garmentID",g."garmentName" from "tbl_invStyle" st left join tbl_garment g on g."garmentID"=st."garmentId" left join "tbl_invScaleName" sn on st."scaleNameId"= sn."scaleId" left join "tbl_invColor" col on col."styleId"=st."styleId" left join "tbl_invStorage" as storage on storage."styleId"=st."styleId" where st."isActive"=1 and storage.merged=\'0\' and storage."styleId"='.$_GET['styleId'].' and storage.unit is not null';
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
$items= pg_num_rows($result_cnt);
$limit = $items;
/*if(isset($_GET['limit']) && $_GET['limit'] != ''){
    $limit = $_GET['limit'];
}*/
if($items > 0) {
    $p = new pagination;
    $p->items($items);
    $p->limit($limit); // Limit entries per page
    //$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
    //die($_SERVER['REQUEST_URI']);
    $uri= substr($_SERVER['REQUEST_URI'], 0,strpos($_SERVER['REQUEST_URI'], '&paging'));
    if(!$uri) {
        $uri=$_SERVER['REQUEST_URI'];
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
    $data[]=$row;
}
pg_free_result($result_cnt);
$sql = '';
$sql = 'SELECT * From "tbl_invLocation"';
if(!($locationQuery=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($loc = pg_fetch_array($locationQuery)) {
    $location[]=$loc;
}
pg_free_result($locationQuery);

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

        table.table{border-collapse: collapse}
        table.table tr th{text-align: center; }
        table.table table{margin-bottom: 10px;}
        table.table tr td{text-align: center; }
        table.table tr th{padding: 5px; background: #ccc;}
        table.table tr td{padding: 5px; border: 1px solid #ccc;}

        #updateLocation{margin: 15px 0; background: #5cb85c; border: 1px solid #4cae4c; padding: 7px 15px;
            color: #fff; cursor: pointer;}


        .location-center{width:55%;
            margin:0 auto;}
        .location-center select{border:1px solid #ccc; padding:10px 15px; margin-bottom: 15px;
            width: 100%;}

        .location-center input[type="text"]{border:1px solid #ccc; padding:10px 15px; margin-bottom: 15px;
            width: 100% !important;}

        .location-center .submitBtn{background: #5cb85c; border: 1px solid #4cae4c; padding: 10px 15px;
            color: #fff; cursor: pointer;}
        .location-center span{font-size: 14px; padding-bottom: 6px; display: block}
        #loader{padding-bottom: 10px;padding-top: 10px; }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.min.css"
          rel="stylesheet">
    <table width="100%" class="table">
        <tr>
            <td align="center"><font face="arial">
                    <br/><br/>
                <center><font size="5">Bulk Update Location</font><br />
                <br/><br/>
                <table width="50%" border="0" cellspacing="1" cellpadding="1">
                    <tr>
                        <th><input id="select_all" type="checkbox"></th>
                        <th>Box</th>
                        <th>Style Number</th>
                        <th>Size Scale</th>
                        <th>Gender</th>
                        <th>Garment</th>
                    </tr>
                    <?php
                        if(count($data) > 0) {
                            foreach ($data as $key => $value) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" class="checkbox" value="<?php echo $value['unit'] ?>" name="checkboxlist"></td>
                                    <td><?php echo $value['unit'] ?></td>
                                    <td><?php echo $value['styleNumber'] ?></td>
                                    <td><?php echo $value['scaleName'] ?></td>
                                    <td><?php echo $value['sex'] ?></td>
                                    <td><?php echo $value['garmentName'] ?></td>
                                </tr>
                                <?php
                            } ?>
                            <tr>
                                <td colspan="6">
                                    <?php echo $p->show(); ?>
                                </td>
                                <!--<td colspan="2">
                                   <strong>Limit Per Page</strong>
                                    <select id="limit">
                                        <option value="10">10</option>
                                        <option value="20" <?php /*echo (isset($_GET['limit']) && $_GET['limit'] == 20)?'selected':''; */?>>20</option>
                                        <option value="50" <?php /*echo (isset($_GET['limit']) && $_GET['limit'] == 50)?'selected':''; */?>>50</option>
                                        <option value="100" <?php /*echo (isset($_GET['limit']) && $_GET['limit'] == 100)?'selected':''; */?>>100</option>
                                        <option value="<?php /*echo $items; */?>" <?php /*echo (isset($_GET['limit']) && $_GET['limit'] == $items)?'selected':''; */?>>All</option>
                                    </select>
                                </td>-->
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td><h2>No Box available for this style</h2></td>
                            </tr>
                            <?php
                        }
                     ?>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr >
           <td  style="text-align: center;">
               <button type="button" id="updateLocation"> Update Location</button>
           </td>
        </tr>
    </table>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="body location-center">
                <h1>
                    Please Select A Location
                </h1>
                <div>
                    <span>Select Location</span>
                    <select name="location" id="location">
                        <option value="">----Select A Location----</option>
                        <?php
                            foreach ($location as $locationValue) {
                                ?>
                                <option
                                    value="<?php echo $locationValue['locationId']; ?>"><?php echo $locationValue['name']; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
                <div id="loader" style="line-height: 115px; text-align: center; display: none;">
                    <img alt="activity indicator" src="../../images/ajax-loader.gif">
                </div>
                <div id="secondSelect" style="display: none;">
                    <span>Select Storage</span>
                    <select name="storage" id="storage">
                        <option value="">----Select A Storage----</option>
                    </select>
                </div>
                <span id="error" style="display: none;color: red;">This Location is Empty Please Add a Storage</span>
                <div>
                    <button type="button" class="submitBtn" disabled="disabled">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        // Get the modal
        var modal = document.getElementById('myModal');

        // Get the button that opens the modal
        var btn = document.getElementById("updateLocation");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on the button, open the modal
        btn.onclick = function() {
            var checkValues = $('input[name=checkboxlist]:checked').map(function()
            {
                return $(this).val();
            }).get();
            if(checkValues == ''){
                alert('Please Check Some Box First');
                return false;
            }
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
        //select all checkboxes
        $("#select_all").change(function(){  //"select all" change
            $(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
        });

        //".checkbox" change
        $('.checkbox').change(function(){
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if(false == $(this).prop("checked")){ //if this item is unchecked
                $("#select_all").prop('checked', false); //change "select all" checked status to false
            }
            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length ){
                $("#select_all").prop('checked', true);
            }
        });
    </script>
    <script type="text/javascript">

        $(document).ready(function(){
            $('.submitBtn').click(function(){
                var checkValues = $('input[name=checkboxlist]:checked').map(function()
                {
                    return $(this).val();
                }).get();
                var styleId = <?php echo $_GET['styleId']; ?>;
                var location = $('#storage').val();
                $.ajax({
                    url: 'bulkLocationSubmit.php',
                    type: 'post',
                    data: {
                        units: checkValues,
                        styleId : styleId,
                        location : location
                    },
                    success:function(data){
                        if(data == 1){
                            alert('Updated Successfully');
                            window.location.reload(true);
                        } else {
                            alert('Location Not Updated Please Try Again After Some Time');
                        }
                    }
                });
            });
            $('#location').change(function () {
                var location = $(this).val();
                jQuery.ajax({
                    type: "POST",
                    url: "bulkLocationIdentifierList.php",
                    data: {
                        id: location,
                    },
                    beforeSend: function(){ jQuery("#loader").show(); },
                    complete: function(){ jQuery("#loader").hide(); },
                    success: function(response){
                        if (response == 0){
                            jQuery("#storage").html('');
                            $('.submitBtn').attr('disabled','disabled');
                            $('#error').show();
                            jQuery("#secondSelect").hide();
                        } else {
                            $('#error').hide();
                            $('.submitBtn').removeAttr('disabled');
                            jQuery("#storage").html(response);
                            jQuery("#secondSelect").show();
                        }
                    }
                });
            });
            $('#limit').change(function () {
                var limit = $(this).val();
                var url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
                //var newUrl = replaceUrlParam(url,'paging',1);
                var finalUrl = replaceUrlParam(url,'limit',limit);
                window.location.replace(finalUrl);
            });
        });
        /*function replaceUrlParam(url, paramName, paramValue){
            if(paramValue == null)
                paramValue = '';
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)')
            if(url.search(pattern)>=0){
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        };*/
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>
    <script>
        $('#location').selectize({
            maxOptions: <?php echo count($location); ?>
        });
        //$('#storage').selectize();
    </script>
<?php
    require('../../trailer.php');
?>