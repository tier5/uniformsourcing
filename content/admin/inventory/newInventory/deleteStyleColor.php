<?php
require('Application.php');
extract($_POST);

///// For Deleting Color /////

$sql = 'DELETE FROM "tbl_invColor" WHERE "colorId"='.$colorId.' and "styleId"='.$styleId;
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }

///// For Deleting Color /////

// Get All Color Data for a style
$sql = '';
$sql = 'SELECT * FROM "tbl_invColor" WHERE "styleId"='.$styleId;
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
    $data_color[]=$row_cnt;
}
pg_free_result($result_cnt);

$htmlDom = '';
$htmlDom .='<label class="col-md-12 control-label center-block">Available Colors:</label>';
foreach ($data_color as $key => $color) 
{

         $colorId = $color['colorId'];  

         $htmlDom .='<div class="col-md-6"><span>'. $color['name'] .'</span>';
         $htmlDom .='<img src="../../../uploadFiles/inventory/images/'. $color['image'] .'" height="50" width="50"><span>';
         $htmlDom .= '<a href="javascript:void(0)" onclick="deleteStyleColor('. $colorId .')">delete</a></div>';
         
                        

  }

  echo json_encode([
        'message' => pg_last_error($connection),
        'htmlStruct' => $htmlDom,
        'success' => true,
        'code' => 200,
        'styleId' => $styleId
    ]);


?>