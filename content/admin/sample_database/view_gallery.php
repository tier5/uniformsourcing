<?php
require('Application.php');
if(isset($_GET['close']))
{
	$id = $_GET['close'];
	$query="Update tbl_sample_database SET status = 0 where sample_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
	$sql = "";
	header("location: database_sample_list.php");
}






$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['brand_manufacture']) && $_REQUEST['brand_manufacture']!="") 
{
	$search_sql=' and s.brand_manufct ILIKE \'%' .$_REQUEST['brand_manufacture'].'%\' ';
	$search_uri="?brand_manufct=".$_REQUEST['brand_manufacture'];
}
if(isset($_REQUEST['sample_id']) && $_REQUEST['sample_id']!="") 
{
	$search_sql=' and s.sample_id_val ILIKE \'%' .$_REQUEST['sample_id'].'%\'';
	$search_uri="?sample_id_val=".$_REQUEST['sample_id'];
}
if(isset($_REQUEST['detailed_description']) && $_REQUEST['detailed_description']!="") 
{
	$search_sql=' and s.detail_description ILIKE \'%' .$_REQUEST['detailed_description'].'%\' ';
	$search_uri="?detail_description=".$_REQUEST['detailed_description'];
}
if(isset($_REQUEST['style_number']) && $_REQUEST['style_number']!="") 
{
	$search_sql=' and s.style_number ILIKE \'%' .$_REQUEST['style_number'].'%\' ';
	$search_uri="?style_number=".$_REQUEST['style_number'];
}
if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="") 
{
	$search_sql=' and s.vid =' .$_REQUEST['vid'];
	$search_uri="?vid=".$_REQUEST['vid'];
}


if(isset($_REQUEST['client']) && $_REQUEST['client']!="") 
{
	$search_sql=' and s.client ='.$_REQUEST['client'].' ';
	$search_uri="?client=".$_REQUEST['client'];
}

if(isset($_REQUEST['department']) && $_REQUEST['department']!="") 
{
	$search_sql=' and s.department ilike \'%'.$_REQUEST['department'].'%\' ';
	$search_uri="?department=".$_REQUEST['department'];
}
if(isset($_REQUEST['color']) && $_REQUEST['color']!="") 
{
	$search_sql=' and s.color ilike \'%'.$_REQUEST['color'].'%\' ';
	$search_uri="?color=".$_REQUEST['color'];
}
if(isset($_REQUEST['size_field']) && $_REQUEST['size_field']!="") 
{
	$search_sql=' and s.size_field ilike \'%'.$_REQUEST['size_field'].'%\' ';
	$search_uri="?size_field=".$_REQUEST['size_field'];
}
if(isset($_REQUEST['garment']) && $_REQUEST['garment']!="") 
{
	$search_sql=' and s.garment ilike \'%'.$_REQUEST['garment'].'%\' ';
	$search_uri="?garment=".$_REQUEST['garment'];
}

$mainQuery='select s.style_number,up.* from tbl_sample_database as s left join "clientDB" as cl on cl."ID"=s.client '
 .' left join tbl_sample_database_uploads as up on up.sample_id=s.sample_id '       
 .' where s.status=1 '.$search_sql.' order by s.modifieddate  desc';

$sql = $mainQuery;
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed sample_database_query: " . pg_last_error($connection));
	exit;
}
$imageArr=array();
while($row=pg_fetch_array($result))
{
$imageArr[]=$row ;
} //print_r($imageArr);?>

<?php if(count($imageArr))
	{
		for($i=0; $i<count($imageArr); $i++)
		{
	?>

	<?php
if(trim($imageArr[$i]['uploadtype'])=="I")
			{
	?> 
<div style="float:left;padding:5px;">
<img src="<?php echo ($upload_dir.$imageArr[$i]['filename']);?>" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');$('#dialog-form').dialog('close');">
<br/>Style number:<br/><strong><?php echo $imageArr[$i]['style_number'];?></strong>
</div>				

	<?php
			}
	?>

<?php
		}
	}



?>
<style type="text/css">
    .PopBoxImageLarge{
     z-index:100000;   
    }    
</style>  

