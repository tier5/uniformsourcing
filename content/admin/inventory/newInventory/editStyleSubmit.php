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
    // echo json_encode([
    //     'status' => false,
    //     'statusCode' => 409,
    //     'message' => 'Style Number you entered already exist in Database.'
    // ]);
    // return;
}
$barcodeExists = 0;
if($_FILES['barCode'] != ''){
    $temp = explode(".", $_FILES["barCode"]['name']);
    if( $temp[1] == 'gif' || $temp[1] == 'png' || $temp[1] == 'jpg' ) {
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["barCode"]["tmp_name"], "../../../uploadFiles/inventory/images/" . $newfilename);
        $barcodeExists = 1;
    }
}

$sql = '';
$sql ="SELECT * FROM \"tbl_invStyle\" WHERE \"styleNumber\" = '".$styleNumber."'";
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
$styleInformation=pg_fetch_array($result_cnt);
pg_free_result($result_cnt);


$styleId = $styleInformation['styleId'];



$query1 = '';
$query1="UPDATE \"tbl_invStyle\" SET";
if($barcodeExists == 1) $query1.=" \"barcode\" = '$newfilename' ";
else $query1.=" \"barcode\" = null ";
$query1.=" , \"scaleNameId\" = '$sizeScale' ";
$query1.=" , \"garmentId\" = '$garment' ";
$query1.=" , \"fabricId\" = '$fabric' ";
$query1.=" , \"sex\" = '$sex' ";
$query1.=" , \"clientId\" = '$client' ";
$query1.=" , \"notes\" = '$notes' ";


$query1.="WHERE \"styleId\" = $styleId";


if(!($result=pg_query($connection,$query1))){
    echo json_encode([
        'status' => false,
        'statusCode' => 500,
        'message' => pg_last_error($connection)
    ]);
    return;
}
//echo 'executed';
//die;
//$styleInformation = pg_fetch_array($result);
//pg_free_result($result);
 $colorArray = explode(',',$colors);
 
 foreach($colorArray as $reValue)
 {
    if($reValue!='')
    {
        $colorArr[] = $reValue;
    }
 }

//  echo count($colorArr).'&&';
// return;

if(count($colorArray) > 0)
{
foreach ($colorArr as $color){
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
        "','".$colorData['name']."','".$colorData['path']."'    )";
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'status' => false,
            'statusCode' => 500,
            'message' => pg_last_error($connection)
        ]);
        return;
    }
}
}
$sql1 = '';
$sql1 = "INSERT INTO \"audit_logs\" (";
$sql1 .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
$sql1 .= " \"log\") VALUES (";
$sql1 .= " '" . $styleInformation['styleId'] . "' ";
$sql1 .= ", '". $_SESSION['employeeID'] ."'";
$sql1 .= ", '". date('U') ."'";
$sql1 .= ", 'Add New Style :  ".$styleNumber ." '";
$sql1 .= ")";

if(!($audit = pg_query($connection,$sql1))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
pg_free_result($audit);

$sql = '';
$sql = 'INSERT INTO "tbl_invUpdateLog" ('.
    '"styleId","createdBy","createdAt",type ) VALUES ('.
    "'".$styleInformation['styleId']."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Add Style' ) RETURNING *";
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$log = pg_fetch_array($result);
pg_free_result($result);
echo json_encode([
    'status' => true,
    'statusCode' => 200,
    'message' => 'Style has been added successfully.',
    'data' => $styleInformation['styleId']
]);
return;
?>
