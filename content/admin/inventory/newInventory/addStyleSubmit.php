<?php
require('Application.php');
extract($_POST);
$sql = '';
$sql = "SELECT count(*) FROM \"tbl_invStyle\" WHERE \"styleNumber\" = '".$styleNumber."'";
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'status' => false,
        'statusCode' => 500,
        'message' => pg_last_error($connection)
    ]);
    return;
}
$style = pg_fetch_row($result);
pg_free_result($result);
if($style[0] > 0)
{
    echo json_encode([
        'status' => false,
        'statusCode' => 409,
        'message' => 'Style Number you entered already exist in Database.'
    ]);
    return;
}
$barcodeExists = 0;
if($_FILES['barCode'] != ''){
    $temp = explode(".", $_FILES["file"]["barCode"]);
    if( $temp[1] == 'gif' || $temp[1] == 'png' || $temp[1] == 'jpg' ) {
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["file"]["tmp_name"], "../../../uploadFiles/inventory/images/thumbs/" . $newfilename);
        $barcodeExists = 1;
    }
}
$query1 = '';
$query1="INSERT INTO \"tbl_invStyle\" (";
$query1.=" \"styleNumber\" ";
$query1.=" ,\"barcode\" ";
$query1.=" ,\"scaleNameId\" ";
$query1.=", \"garmentId\" ";
$query1.=", \"fabricId\" ";
$query1.=", \"sex\" ";
$query1.=", \"clientId\" ";
$query1.=", \"notes\" ";
$query1.=")";
$query1.=" VALUES (";
$query1.=" '$styleNumber' ";
if($barcodeExists == 1) $query1.=", '".$newfilename."' ";
else $query1.=", null ";
$query1.=", '$sizeScale' ";
$query1.=", '$garment' ";
$query1.=" ,'$fabric' ";
$query1.=" ,'$sex' ";
$query1.=" ,'$client' ";
$query1.=" ,'".$notes."' ";
$query1.=" ) RETURNING * ";
if(!($result=pg_query($connection,$query1))){
    echo json_encode([
        'status' => false,
        'statusCode' => 500,
        'message' => pg_last_error($connection)
    ]);
    return;
}
$styleInformation = pg_fetch_array($result);
pg_free_result($result);
$colorArray = explode(',',$colors);
foreach ($colorArray as $color){
    $sql = '';
    $sql = "SELECT * FROM \"tbl_colorTemp\" WHERE id='".$color."'";
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'status' => false,
            'statusCode' => 500,
            'message' => pg_last_error($connection)
        ]);
        return;
    }
    $colorData = pg_fetch_array($result);
    pg_free_result($result);

    $sql = '';
    $sql = "INSERT INTO \"tbl_invColor\" (\"styleId\",name,image) VALUES ('".$styleInformation['styleId'].
        "','".$colorData['name']."','".$colorData['path']."')";
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'status' => false,
            'statusCode' => 500,
            'message' => pg_last_error($connection)
        ]);
        return;
    }
}
echo json_encode([
    'status' => true,
    'statusCode' => 200,
    'message' => 'Style has been added successfully.',
    'data' => $styleInformation['styleId']
]);
return;
?>