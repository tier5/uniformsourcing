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
        font-size: 15px;
        color: #333;
        padding: 5px !important;
        width: 5%;
    }

    #print_btn{
        font-size: 17px;
        color: #333;
        padding: 5px 10px;
    }
   /* body {
        text-align: center;
    }

    .wrapper {
        text-align: left;
        width: 800px;
        margin: 0 auto;
    }*/
</style>
    <title>Neck & Sleeve Sizes</title>
</head>
<?php 
require('Application.php');
	$search = "";
	if(isset($_GET['styleId']))
	{
		$styleId 	= $_GET['styleId'];
		$unit = $_GET['unit'];
        $search = "";
		if(isset($_GET['colorId']))
		{ 
			$clrId 		= $_GET['colorId'];
			$opt1Id 	= $_GET['opt1Id'];
			$opt2Id 	= $_GET['opt2Id'];
		}
		else
		{
			$clrId 		= 0;
			$opt1Id 	= 0;
			$opt2Id 	= 0;
		}
		if($clrId > 0)
		{
			$search = " and inv.\"colorId\"=$clrId ";
			if($opt1Id > 0)
			 	$search .= "and \"opt1ScaleId\"=$opt1Id ";
			if($opt2Id > 0)
			 	$search .= "and \"opt2ScaleId\"=$opt2Id ";
		}
	    if(isset($_GET['unit'])&&$_GET['unit']!='0')
	    {
	  		$search .= " and st.\"unit\"='".$_GET['unit']."'";
	    }
	}
    $sql = 'select "styleId","sex","garmentId","barcode", "styleNumber", "scaleNameId", price, "locationIds", "clientId" from "tbl_invStyle" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    $data_style = $row;
    pg_free_result($result);
    $query2 = 'Select * from "tbl_invColor" where "styleId"=' . $data_style['styleId'];
    if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data_color[] = $row2;
    }
    pg_free_result($result2);
    if ($data_style['scaleNameId'] != "") {
        $query2 = 'Select * from "tbl_invScaleName" where "scaleId"=' . $data_style['scaleNameId'];
        if (!($result = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($result);
        $data_optionName = $row;
        pg_free_result($result);
        $query2 = 'Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_mainSize[] = $row2;
        }
        pg_free_result($result2);
        $query2 = 'Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_opt1Size[] = $row2;
        }
        pg_free_result($result2);
        $query2 = 'Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
        while ($row2 = pg_fetch_array($result2)) {
            $data_opt2Size[] = $row2;
        }
        pg_free_result($result2);
        $sql = 'select distinct unit from "tbl_invStorage" where "styleId"=' . $styleId;
        if ($colorId > 0) {
            $sql .= ' and "colorId"=' . $colorId;
        } else if (count($data_color) > 0) {
            $sql .= ' and "colorId"=' . $data_color[0]['colorId'];
        }
        $sql .= ' order by unit asc';
        if (!($result_cnt9 = pg_query($connection, $sql))) {
            print("Failed InvData: " . pg_last_error($connection));
            exit;
        }
        while ($row_cnt9 = pg_fetch_array($result_cnt9)) {
            $data_storage[] = $row_cnt9;
        }
        pg_free_result($result_cnt9);
    }
    $totalScale = count($data_mainSize);
    $tableWidth = 0;
    $tableWidth = $totalScale * 100;
    $sql = 'select "inventoryId",quantity,"newQty","isStorage","warehouse_id" from "tbl_inventory" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_inv[] = $row;
    }
    if (count($data_color) > 0) {
        if ($search != "") {
            $query = 'select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
            if (isset($unit) && $unit != '0') {
                $query .= ',st."wareHouseQty" as st_quantity, st."storageId" ';
            }
            $query .= ', inv."updatedBy",inv."updatedDate",inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
            if (isset($unit) && $unit != '0') {
                $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
            }
            $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1 ' . $search .' '. $searchUnit .' order by "inventoryId"';
        } else {
            $clrId = $data_color[0]['colorId'];
            $query = 'select "updatedBy","updatedDate","inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"=' . $data_style['styleId'] . ' and "colorId"=' . $data_color[0]['colorId'] . '  and "isActive"=1 order by "inventoryId"';
        }
        if (!($result = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $data_inv[] = $row;
        }
    }
    pg_free_result($result);
    if (count($data_inv) > 0) {
        for ($l = 0; $l < count($data_inv); $l++) {
            if (isset($data_inv[$l]['st_quantity']) && $data_inv[$l]['st_quantity'] != '') {
                $data_inv[$l]['quantity'] = $data_inv[$l]['st_quantity'];
            }
        }
    }
    /*echo '<pre>';
    print_r($data_inv);die();*/
	$sql = 'select * from "tbl_garment" where "garmentID"='.$data_style["garmentId"];
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed StyleQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_garment=$row;
	pg_free_result($result);
	$query='select * from "tbl_invLocation" order by "locationId"';
	if(!($result=pg_query($connection,$query)))
	{
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_loc[]=$row;
	}
	pg_free_result($result);
	$locArr = array();
	if($data_style['locationIds'] != "")
	{
		$locArr = explode(",",$data_style['locationIds']);
	}
    $sql = '';
    $sql = 'SELECT * FROM "clientDB" where "ID"=' . $data_style['clientId'];
    if (!($resultClient = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($resultClient);
    $data_client = $row;
    $exp = explode('_',$_GET['unit']);
    $sql = '';
    $sql = "SELECT \"locationId\" FROM \"tbl_invLocation\" WHERE identifier='".$exp[0]."'";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $locId = pg_fetch_row($result);
    $sql = '';
    $sql = "SELECT * FROM \"locationDetails\" WHERE \"locationId\"='".$locId[0]."'";
    $sql .= " and ( warehouse='".$exp[1]."' OR container='".$exp[1]."' OR conveyor ='".$exp[1]."' )";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $warehouses_all = pg_fetch_row($result);
    if($warehouses_all[4] != ''){
        $typeBox = 'conveyor';
    } elseif ($warehouses_all[3] != ''){
        $typeBox = 'container';
    } else {
        $typeBox = 'warehouse';
    }
    $query = 'select * from "tbl_invStorage" WHERE unit=' . "'" . $_GET['unit'] . "'";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($rowProduct = pg_fetch_array($resultProduct)) {
        $data_product[] = $rowProduct;
    }
    pg_free_result($rowProduct);
    if(count($data_product) > 0) {
        $key_product = 0;
        foreach ($data_product as $dk => $dv) {
            if ($dv['updatedDate'] > $latest) {
                $latest = $dv['updatedDate'];
                $key_product = $dk;
            }
        }
        $sql = '';
        $sql = 'SELECT * FROM "employeeDB" where "employeeID"=' . $data_product[$key_product]['updatedBy'];
        if (!($resultClient = pg_query($connection, $sql))) {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($resultClient);
        $data_employee = $row;
        } else {
        $data_employee = '';
        $latest = 0;
    }
    $opt1SizeIdHash = array();
    foreach ($data_opt1Size as $key => $val) {
        if (isset($val['opt1SizeId'])) {
            $opt1SizeIdHash[(int)$val['opt1SizeId']] = $val['opt1Size'];
        }
    }
    $data_mainSizeIdHash = array();
    foreach ($data_mainSize as $key => $val){
        if (isset($val['mainSizeId'])){
            $data_mainSizeIdHash[(int)$val['mainSizeId']] = $val['scaleSize'];
        }
    }
    $data_set = array();
    foreach ($data_inv as $key => $val) {
        if (isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['quantity'];
        }else if (!isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][0] = $val['quantity'];
        }
    }
?>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="top-table">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width: 30%">Style #: <?php echo $data_style['styleNumber']; ?></td>
                                <td style="width: 35%">Employee:<?php echo $data_employee['firstname'] . ' ' . $data_employee['lastname']; ?></td>
                                <td style="width: 35%">Date Entered: <?php echo ($latest != '0')?date("F j, Y, g:i a", $latest):''; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%">Garment Type: <?php echo $data_garment["garmentName"]; ?></td>
                                <td style="width: 35%">Color:
                                    <?php
                                    for ($i = 0; $i < count($data_color); $i++) {
                                        if ($data_color[$i]['name'] != "") {
                                            if ($data_color[$i]['colorId'] == $clrId) {
                                                echo $data_color[$i]['name'];
                                                break;
                                            }
                                        } else {
                                            echo 'Unknown';
                                        }
                                    }
                                    ?>
                                </td>
                                <td style="width: 35%">Gender :<?php echo (" ".$data_style['sex']); ?></td>
                            </tr>
                            <tr>
                                <td>Client: <?php echo $data_client['client']; ?></td>
                                <td colspan="2">Location: <?php echo $_GET['location']; ?></td>
                            </tr>
                            <tr>
                                <?php
                                    if ($typeBox == 'warehouse' || $typeBox == 'container'){
                                        ?>
                                        <td style="width: 30%">Box#: <?php echo $_GET['unit']; ?></td>
                                <?php
                                    } else {
                                ?>
                                        <td style="width: 30%">Box#:  <?php echo $_GET['unit']; ?></td>
                                <?php
                                    }
                                ?>
                                <td colspan="2" style="width: 70%">
                                    <table width="100%">
                                        <tr>
                                            <?php
                                                if($typeBox == 'warehouse') {
                                                    ?>
                                                    <td>
                                                        Row: <?php echo $data_storage[0]['row'] == '' ? 'nil' : $data_storage[0]['row'] ?></td>
                                                    <td>
                                                        Rack: <?php echo $data_storage[0]['rack'] == '' ? 'nil' : $data_storage[0]['rack'] ?></td>
                                                    <td>
                                                        Shelf: <?php echo $data_storage[0]['shelf'] == '' ? 'nil' : $data_storage[0]['shelf'] ?></td>
                                                    <?php
                                                }
                                            ?>
                                        </tr>
                                    </table>

                                </td>

                            </tr>
                            <tr>
                                <td style="width: 30%"></td>
                                <td style="width: 35%"></td>
                                <td style="width: 35%"></td>
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
                                        foreach ($data_mainSizeIdHash as $key => $value){;
                                            $element .= '<th class="text-center">'.$value.'</th>';
                                        }
                                        $element .= '</tr>';
                                        if(count($opt1SizeIdHash) > 0){
                                            foreach ($opt1SizeIdHash as $key1=>$value1){
                                                $element .= '<tr>';
                                                $element .= '<td class="text-left">'.$value1.'</td>';
                                                foreach ($data_mainSizeIdHash as $key2 => $value2){;
                                                    if (isset($data_set[$key2][$key1])) {
                                                        $element .= '<td>' . $data_set[$key2][$key1] . '</td>';
                                                    } else {
                                                        $element .= '<td>&nbsp;</td>';
                                                    }
                                                }
                                                $element .= '</tr>';
                                            }
                                        } else {
                                            $element .= '<tr>';
                                            $element .= '<td class="text-left">Qty</td>';
                                            foreach ($data_mainSizeIdHash as $key2 => $value2){;
                                                if (isset($data_set[0][$key2])) {
                                                    $element .= '<td>' . $data_set[0][$key2] . '</td>';
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