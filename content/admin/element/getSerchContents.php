<?php
require('Application.php');
extract($_POST);
switch($_GET['opt'])
{
    case "client":
        $packlist=array();
$query= 'Select distinct main.*,(select client.client from tbl_element_package as pack left join "clientDB" as client '.
 ' on client."ID"=pack.client'.
 ' where pack_id= main.pack_id limit 1) as client_name from "tbl_element_pack_main" as main,tbl_element_package as package where package.client= '.$client.
        ' order by main.pack_id';

//echo $query;
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$packlist[]=$row;
}
pg_free_result($result);
//print_r($datalist);

echo '<option value="0">--Select--</option>';
for($i=0;$i<count($packlist);$i++)
{
echo '<option value="'.$packlist[$i]['pack_id'].'"';
       if(isset($_GET['pack'])&& $_GET['pack']==$packlist[$i]['pack_id']) 
         echo ' selected="selected" ';   
     echo '>';

     echo $packlist[$i]['pack_name'].'</option>';   
 }

break;

 






case "style":
        $packlist=array();
$query= 'Select distinct main.*,(select client.client from tbl_element_package as pack left join "clientDB" as client '.
 ' on client."ID"=pack.client'.
 ' where pack_id= main.pack_id limit 1) as client_name from "tbl_element_pack_main" as main,tbl_element_package as package where package.style like \'%'.
        $style.'%\' order by main.pack_id';

echo $query;
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$packlist[]=$row;
}
pg_free_result($result);
//print_r($datalist);

echo '<option value="0">--Select--</option>';
for($i=0;$i<count($packlist);$i++)
{
echo '<option value="'.$packlist[$i]['pack_id'].'"';
       if(isset($_GET['pack'])&& $_GET['pack']==$packlist[$i]['pack_id']) 
         echo ' selected="selected" ';   
     echo '>';

     echo $packlist[$i]['pack_name'].'</option>';   
 }

break;





}

?>