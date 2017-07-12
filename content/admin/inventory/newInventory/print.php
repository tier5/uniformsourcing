<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min-1.8.2.js"></script>
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        /*@page {
            size: A4;
        }
        @media print {
            html, body {
                width: 205mm;
                height: 148mm;
                margin-left: auto;
                margin-right: auto;
            }
            !* ... the rest of the rules ... *!
        }*/
        .inventory-table{
            margin:40px 0;
        }
        .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border: 1px solid #878787;
        }

        .my-table th, .my-table td{
            font-size: 18px;
            color: #333;
            padding: 5px !important;
            width: 5%;
        }

        #print_btn{
            font-size: 17px;
            color: #333;
            padding: 5px 10px;
        }

        body {
            font-size: 200%;
        }

        /* .wrapper {
             text-align: left;
             width: 800px;
             margin: 0 auto;
         }*/
    </style>
    <title>Neck & Sleeve Sizes</title>
</head>
<?php
require('Application.php');
$sql = '';
$sql = 'SELECT style.*,garment.*,client.* FROM "tbl_invStyle" style ' .
    ' LEFT JOIN "tbl_garment" garment on garment."garmentID" = style."garmentId" ' .
    ' LEFT JOIN "clientDB" client on client."ID" = style."clientId" ' .
    ' WHERE style."styleId"=' . $_GET['styleId'];
if (!($resultInfo = pg_query($connection, $sql))) {
    echo '<h1> Failed Info Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
    exit;
}
$dataInfo = pg_fetch_array($resultInfo);
pg_free_result($resultInfo);
//Fetch Size Scale Data
if ($dataInfo['scaleNameId'] != '') {
    //Fetch Main size
    $query2 = 'Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"=' . $dataInfo['scaleNameId'] . ' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
    if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $dataMainSize[] = $row2;
    }
    pg_free_result($result2);
    //Fetch Opt size
    $query2 = 'Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"=' . $dataInfo['scaleNameId'] . ' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
    if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $dataOptSize[] = $row2;
    }
    pg_free_result($result2);
    $dataOptSizeId = array();
    foreach ($dataOptSize as $key => $val) {
        if (isset($val['opt1SizeId'])) {
            $dataOptSizeId[(int)$val['opt1SizeId']] = $val['opt1Size'];
        }
    }
    $dataMainSizeId = array();
    foreach ($dataMainSize as $key => $val) {
        if (isset($val['mainSizeId'])) {
            $dataMainSizeId[(int)$val['mainSizeId']] = $val['scaleSize'];
        }
    }
} else {
    echo '<h1>No size available</h1>';
    die();
}
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$_GET['boxId'].' and "colorId"='.$_GET['colorId'].' and "styleId"='.$_GET['styleId'];
if(!($result=pg_query($connection,$sql))){
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
}
$unit = pg_fetch_array($result);
pg_free_result($result);
if($unit == ''){
    echo "Please select some Box";
    exit;
}
$sql = '';
$sql = 'SELECT unit.*,quantity.* FROM "tbl_invUnit" unit ';
$sql .= ' LEFT JOIN "tbl_invQuantity" quantity on unit.id = quantity."boxId" ';
$sql .= ' WHERE unit."styleId"=' . $_GET['styleId'];
if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
    $sql .= ' and unit.id=' . $_GET['boxId'];
}
if (isset($_GET['colorId']) && $_GET['colorId'] != 0) {
    $sql .= ' and unit."colorId"=' . $_GET['colorId'];
}
if (!($result = pg_query($connection, $sql))) {
    print("Failed location fetch Query: " . pg_last_error($connection));
    exit;
}
while ($row2 = pg_fetch_array($result)) {
    $data[] = $row2;
}
$typeBox = $data[0]['type'];
pg_free_result($result);
$data_set = array();
foreach ($data as $key => $val) {
    if (isset($dataOptSizeId[$val['optSizeId']])
        && isset($dataMainSizeId[$val['mainSizeId']])
    ) {
        $data_set[$val['mainSizeId']][$val['optSizeId']] = $val['qty'];
    }else if (!isset($dataOptSizeId[$val['optSizeId']])
        && isset($dataMainSizeId[$val['mainSizeId']])
    ) {
        $data_set[$val['mainSizeId']][0] = $val['qty'];
    }
}
//Fetch Latest updated records for a Style
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" unit'.
    ' LEFT JOIN "employeeDB" emp ON emp."employeeID"=unit."updatedBy"  '.
    ' WHERE unit.id=' . $_GET['boxId'] .
    ' order by "updatedAt" desc limit 1';
if (!($result = pg_query($connection, $sql))) {
    print("Failed location fetch Query: " . pg_last_error($connection));
    exit;
}
$emp = pg_fetch_array($result);
pg_free_result($result);
//Get Location for a Unit
if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invUnit" unit' .
        ' LEFT JOIN "locationDetails" details on details.id = unit."storageId"' .
        ' LEFT JOIN "tbl_invLocation" location on location."locationId" = CAST(details."locationId" as bigint)' .
        ' WHERE unit.id=' . $_GET['boxId'];
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    $location = pg_fetch_array($result);
    pg_free_result($result);
    $locationName = $location['name'];
    $storageType = $location['type'];
    $boxName = $location['identifier'].'_'.$location[$storageType].'_'.$location['box'];
    $rowName = $location['row'];
    $rackName = $location['rack'];
    $shelfName = $location['shelf'];
} else {
    $locationName = 'All Location';
    $boxName = 'All Box';
    $storageType = 'allBox';
    $rowName = 'All Row';
    $rackName = 'All Rack';
    $shelfName = 'All Shelf';
}
$sql = '';
$sql = 'SELECT * FROM "tbl_invColor" WHERE "colorId"='.$_GET['colorId'];
if (!($result = pg_query($connection, $sql))) {
    print("Failed location fetch Query: " . pg_last_error($connection));
    exit;
}
$colorName = pg_fetch_array($result);
pg_free_result($result);
?>
<body>
<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="top-table">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 30%">Style #: <?php echo $dataInfo['styleNumber']; ?></td>
                            <td style="width: 35%">Employee:<?php echo $emp['firstname'] . ' ' . $emp['lastname']; ?></td>
                            <td style="width: 35%">Date Entered:
                                <?php
                                if ($emp != null) {
                                    echo date("F j, Y, g:i a", strtotime($emp['updatedAt']));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Garment Type: <?php echo $dataInfo["garmentName"]; ?></td>
                            <td style="width: 35%">Color:
                                <?php
                                echo $colorName['name']
                                ?>
                            </td>
                            <td style="width: 35%">Gender :<?php echo $dataInfo['sex'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Client: <?php echo $dataInfo    ['client']; ?></td>
                            <td colspan="2">Location: <?php echo $locationName; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Box#: <?php echo $boxName; ?></td>
                            <td colspan="2" style="width: 70%">
                                <table width="100%">
                                    <tr>
                                        <?php
                                        if($typeBox == 'warehouse') {
                                            ?>
                                            <td>
                                                Row: <?php echo $data[0]['row'] == '' ? 'nil' : $data[0]['row'] ?></td>
                                            <td>
                                                Rack: <?php echo $data[0]['rack'] == '' ? 'nil' : $data[0]['rack'] ?></td>
                                            <td>
                                                Shelf: <?php echo $data[0]['shelf'] == '' ? 'nil' : $data[0]['shelf'] ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <form id="inventoryFormNew">
                <div class="col-md-11 right-sidebar">
                    <div class="inventory-table">
                        <div class="table-responsive">
                            <table class="table my-table table-bordered text-center">
                                <?php
                                $element = '';
                                $element .= '<tr><th>Sizes</th>';
                                foreach ($dataMainSizeId as $key => $value){;
                                    $element .= '<th class="text-center">'.$value.'</th>';
                                }
                                $element .= '</tr>';
                                if(count($dataOptSizeId) > 0){
                                    foreach ($dataOptSizeId as $key1=>$value1){
                                        $element .= '<tr>';
                                        $element .= '<td class="text-left">'.$value1.'</td>';
                                        foreach ($dataMainSizeId as $key2 => $value2){;
                                            if (isset($data_set[$key2][$key1]) && $data_set[$key2][$key1] >0) {
                                                $element .= '<td class="text-center">' . $data_set[$key2][$key1] . '</td>';
                                            } else {
                                                $element .= '<td>&nbsp;</td>';
                                            }
                                        }
                                        $element .= '</tr>';
                                    }
                                } else {
                                    $element .= '<tr>';
                                    $element .= '<td class="text-left">Qty</td>';
                                    foreach ($dataMainSizeId as $key2 => $value2){
                                        if (isset($data_set[$key2][0]) && $data_set[$key2][0] > 0) {
                                            $element .= '<td class="text-center">' . $data_set[$key2][0] . '</td>';
                                        } else {
                                            $element .= '<td>&nbsp;</td>';
                                        }
                                    }
                                    $element .= '</tr>';
                                }
                                echo $element;
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-md-12 align-right">
                <button id="print_btn" onclick="print_me()" >Print</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function print_me()
        {
            //Get the print button and put it into a variable
            var printButton = document.getElementById("print_btn");
            //Set the print button visibility to 'hidden'
            printButton.style.visibility = 'hidden';
            //Print the page content
            window.print()
            //Set the print button to 'visible' again
            //[Delete this line if you want it to stay hidden after printing]
            printButton.style.visibility = 'visible';
        }
    </script>
</body>
</html>