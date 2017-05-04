<?php

echo $_GET['table'];

$server_URL = "http://127.0.0.1:4569";  //Server address needed for sending sam$
$db_server = "localhost";
$db_name = "php_intranet_uniformsourcing";                          // database$
$db_uname= "globaluniformuser";                              // username to con$
$db_pass= "globaluniformpassword";  
try{
	$connection = pg_connect("host = $db_server ".
						 "dbname = $db_name ".
						 "user = $db_uname ".
						 "password = $db_pass");

}
catch(\Exception $e)
{
	var_dump($e->getMessage());
}
$sqlone="select column_name, data_type, character_maximum_length
				from INFORMATION_SCHEMA.COLUMNS where table_name = '".$_GET['table']."'";
				$tbl_struct;
if(!($result=pg_query($connection,$sqlone)))
			{
				print("Failed StyleQuery: " . pg_last_error($connection));
				exit;
			}
			while($row = pg_fetch_array($result))
			{

				$tbl_struct_exist[]=$row;
			}
				pg_free_result($row);
?>

<table style="width:100%" border="1">
  <tr>
    <th>New Structure</th>
    <th>Old Structure</th> 
    
  </tr>
  
  
  <tr>
    <td>
		<table style="width:100%" border="1">
		  <tr>
		    <th>id</th>
		    <th>Details</th> 
		    
		  </tr>
		  
		  <?php foreach ($tbl_struct_exist as $exickey => $exisvalue) {
		  ?>
		  
		  <tr>
		    <td><?php echo $exickey; ?></td> 
		    <td><?php  print_r($exisvalue);?></td>
		  </tr>
		  <?php } ?>
		</table>
    </td> 
    <td></td>
  </tr>
  
</table>