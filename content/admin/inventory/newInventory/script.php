<?php
require('Application.php');
$sql = '';
$sql = 'SELECT * FROM "tbl_invStyle" WHERE "isActive"=1';
if (!($resultStyle = pg_query($connection, $sql))) {
    echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
    exit;
}
while ($row = pg_fetch_array($resultStyle)) {
    $dataStyle[] = $row;
}
pg_free_result($resultStyle);
foreach ($dataStyle as $key => $style) {
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invStorage" WHERE "styleId"=' . $style['styleId'];
    if (!($result = pg_query($connection, $sql))) {
        echo $sql;
        echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $dataStorage[] = $row;
    }
    pg_free_result($result);
    foreach ($dataStorage as $storage) {
        $explodeUnit = explode('_', $storage['unit']);
        $location = '';
        if (count($explodeUnit) > 1) {
            $box = $explodeUnit[2];
            $sql = '';
            $sql = 'SELECT * FROM "locationDetails"' .
                " WHERE \"locationId\"='" . $storage['locationId'] . "'" .
                " and warehouse='" . $explodeUnit[1] . "' or container='" . $explodeUnit[1] . "' or conveyor='" . $explodeUnit[1] . "'";
            if (!($result = pg_query($connection, $sql))) {
                echo $sql;
                echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                exit;
            }
            $location = pg_fetch_array($result);
            pg_free_result($result);
        } else {
            $box = $explodeUnit[0];
        }

        if ($location == '') {
            $sql = '';
            $sql = "SELECT * FROM \"tbl_invLocation\" WHERE identifier='UKN'";
            if (!($result = pg_query($connection, $sql))) {
                echo $sql;
                echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                exit;
            }
            $invLocation = pg_fetch_array($result);
            pg_free_result($result);
            if ($invLocation == '') {
                echo 'Please Enter Unknown location with Identifier IKN and make a warehouse named unknown';
                exit;
            }
            $sql = '';
            $sql = "SELECT * FROM \"locationDetails\" WHERE \"locationId\"='" . $invLocation['locationId'] . "' and warehouse='unknown'";
            if (!($result = pg_query($connection, $sql))) {
                echo $sql;
                echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                exit;
            }
            $location = pg_fetch_array($result);
            pg_free_result($result);
        }
        if ($location != '') {
            $storageId = $location['id'];
            if ($location['conveyor'] != '') {
                $storageType = 'conveyor';
            } elseif ($location['container']) {
                $storageType = 'container';
            } else {
                $storageType = 'warehouse';
            }
        } else {
            echo 'Location Not Found';
            exit;
        }
        $sql = '';
        $sql = "SELECT * FROM \"tbl_invUnit\" WHERE box='" . $box . "'";
        if (!($result = pg_query($connection, $sql))) {
            echo $sql;
            echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
            exit;
        }
        $dataBox = pg_fetch_array($result);
        pg_free_result($result);
        if($storage['opt1ScaleId'] == ''){
            $optSize = 0;
        } else {
            $optSize = $storage['opt1ScaleId'];
        }
        if ($dataBox != '') {
            if ($storage['wareHouseQty'] > 0 && $storage['sizeScaleId'] != '') {
                $sql = '';
                $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$dataBox['id'].' and "mainSizeId"='. $storage['sizeScaleId'] .' and "optSizeId"='.$optSize;
                if (!($result = pg_query($connection, $sql))) {
                    echo $sql;
                    echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                    exit;
                }
                $quantity = pg_fetch_array($result);
                pg_free_result($result);
                if($quantity == ''){
                    $sql = '';
                    $sql = "INSERT INTO \"tbl_invQuantity\" ( ";
                    $sql .= " \"boxId\",\"mainSizeId\",\"optSizeId\",\"qty\" ) VALUES (";
                    $sql .= "'" . $dataBox['id'] . "','" . $storage['sizeScaleId'] . "','" . $optSize . "','" . $storage['wareHouseQty'] . "' )";
                    if (!($result = pg_query($connection, $sql))) {
                        echo $sql;
                        echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                        exit;
                    }
                    pg_free_result($result);
                }
            }
        } else {
            //Insert into unit table
            $sql = "";
            $sql = "INSERT INTO \"tbl_invUnit\" ( ";
            $sql .= " \"styleId\",\"colorId\",type ,";
            $sql .= " row, rack, shelf, ";
            $sql .= " \"createdAt\",\"createdBy\",\"updatedAt\",\"updatedBy\",";
            $sql .= " \"storageId\",box,merged ) VALUES ( ";
            $sql .= "'" . $storage['styleId'] . "','" . $storage['colorId'] . "','" . $storageType . "',";
            $sql .= " '" . $storage['row'] . "','" . $storage['rack'] . "','" . $storage['shelf'] . "',";
            $sql .= "'" . date('Y-m-d G:i:s', $storage['createdDate']) . "','" . $storage['createdBy'] . "','" . date('Y-m-d G:i:s', $storage['updatedDate']) . "','" . $storage['updatedBy'] . "', ";
            $sql .= "'" . $storageId . "','" . $box . "','" . $storage['merged'] . "'";
            $sql .= ") RETURNING *";
            if (!($result = pg_query($connection, $sql))) {
                echo pg_last_error($connection);
            }
            $unit = pg_fetch_array($result);
            pg_free_result($result);
            if($unit != ''){
                if ($storage['wareHouseQty'] > 0  && $storage['sizeScaleId'] != '') {
                    $sql = '';
                    $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$unit['id'].' and "mainSizeId"='. $storage['sizeScaleId'] .' and "optSizeId"='.$optSize;
                    if (!($result = pg_query($connection, $sql))) {
                        echo $sql;
                        echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                        exit;
                    }
                    $quantity = pg_fetch_array($result);
                    pg_free_result($result);
                    if($quantity == '') {
                        $sql = '';
                        $sql = "INSERT INTO \"tbl_invQuantity\" ( ";
                        $sql .= " \"boxId\",\"mainSizeId\",\"optSizeId\",\"qty\" ) VALUES (";
                        $sql .= "'" . $unit['id'] . "','" . $storage['sizeScaleId'] . "','" . $optSize . "','" . $storage['wareHouseQty'] . "' )";
                        if (!($result = pg_query($connection, $sql))) {
                            echo $sql;
                            echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
                            exit;
                        }
                        pg_free_result($result);
                    }
                }
            }
        }
    }
}
echo 'done';
exit;
?>