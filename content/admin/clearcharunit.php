<?php
require('Application.php');

$sql = '';
$sql = "SELECT * FROM \"tbl_invUnit\"";
if (!($resultStyle = pg_query($connection, $sql))) {
        echo 'Failed style Query: ' . pg_last_error($connection) . '';
        exit;
    }
    while($dataStyle = pg_fetch_array($resultStyle)){

    	echo $dataStyle['styleId'].'  '.$dataStyle['colorId'].'<br>';
    	$sql2 = 'SELECT * FROM "tbl_invColor" where "colorId"='.$dataStyle['colorId'].' and "styleId"='.$dataStyle['styleId'].'';
    	//echo $sql2.'<br>';
    	$resultStyle2 = pg_query($connection, $sql2);
    	$dataStyle2 = pg_fetch_array($resultStyle2);
    	//print_r($dataStyle2);
    	if(!empty($dataStyle2))
         echo " Found<br> ";
        else{
          echo " Not Found<br> ";
          $sql3 = 'INSERT INTO "tbl_invColor" ("colorId","styleId","name","image")VALUES ('.$dataStyle['colorId'].', '.$dataStyle['styleId'].', \'Unknown\',\'unknown.png\')';
          if (!($resultStyle3 = pg_query($connection, $sql3))) {
	        echo 'Failed style Query: ' . pg_last_error($connection) . '';
	        exit;
    		} 
         }	
    }
?>