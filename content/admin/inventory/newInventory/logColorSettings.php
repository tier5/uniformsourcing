<?php
    // Load Main Application with Database Connection
    require('Application.php');
    // Load Header File
    require('../../../header.php');
    //Fetch All log Color Information
    $sql = "SELECT * FROM \"tbl_date_interval_setting\" ORDER BY interval ASC";
    if (!($row = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($colorResult = pg_fetch_array($row)) {
        $colorSettings[] = $colorResult;
    }
    pg_free_result($row);
?>
<!--load Bootstrap -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Main Page Container -->
<div class="container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-1 pull-left">
                    <button class="btn btn-success btn-xs" onclick="javascript:location.href='../inventory.php';" type="button">
                        <i class="glyphicon glyphicon-arrow-left"></i> Back
                    </button>
                </div>
                <div class="col-md-10">
                    <h3 class="text-center">Log Color Settings</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered dt-responsive nowrap">
                    <thead>
                      <th style="text-align: center;">
                        No. of Days
                      </th>
                      <th  style="text-align: center;">
                        Color
                      </th>
                      <th  style="text-align: center;">
                        Action
                      </th>
                    </thead>
                    <tbody>
                      <?php
                          foreach ($colorSettings as $color) {
                            ?>
                              <tr style="text-align: center;">
                                <td>
                                  <?php echo $color['interval']; ?>
                                </td>
                                <td>
                                  <?php echo $color['color']; ?>
                                </td>
                                <td>
                                  <button type="button" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-pencil"></i></button>
                                  <button type="button" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
                                </td>
                              </tr>
                            <?php
                          }
                       ?>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Color modal -->
<div id="addColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Color</h4>
            </div>
            <div class="modal-body">
              <h1>Body</h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--load Footer Section -->
<?php require('../../../trailer.php'); ?>
<!-- load Jquery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Load Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<!--All Js Scripts -->
