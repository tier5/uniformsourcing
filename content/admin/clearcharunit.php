<?php
require('Application.php');

$sql = '';
$sql = "SELECT * FROM \"tbl_invUnit\"";
if (!($resultStyle = pg_query($connection, $sql))) {
        echo 'Failed style Query: ' . pg_last_error($connection) . '';
        exit;
    }
    while($dataStyle = pg_fetch_array($resultStyle)){

    	if(is_numeric($dataStyle['box']))
          echo $dataStyle['box'].'<br>';
        else{

        	if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $dataStyle['box']))
			{
			    echo 'Contains at least one letter and one number  '.$dataStyle['box'].' '.$dataStyle['id'].'<br>';
			    $box = preg_replace('/\D/', '', $dataStyle['box']);
			    $sql = "UPDATE \"tbl_invUnit\" SET \"box\" = '" .$box."' WHERE \"id\" = '".$dataStyle['id']."'";
			    echo $sql.'<br>';
	    		//pg_query($connection, $sql);
			}
			else{
				echo 'Contains only letter  '.$dataStyle['box'].' '.$dataStyle['id'].'<br>';
				$sql = "DELETE FROM \"tbl_invUnit\" WHERE \"id\" = '".$dataStyle['id']."'";
				echo $sql.'<br>';
	    		//pg_query($connection, $sql);
			}

        }
    
    }
?>