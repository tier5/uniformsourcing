<?php 
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

$return_arr = array();

extract($_POST);
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['submit']))
{
	if($_POST['submit'] == 'Add')
	{
  		$query="INSERT INTO tbl_quote ( ";
		$query.="\"name\" ";
		$query.=", \"client\" ";
		$query.=", \"date\" ";
		$query.=", \"priceAdj\" ";
		$query.=", \"priceType\" ";
		$query.=", \"createdBy\" ";
		$query.=", \"createdDate\" ";
		$query.=", \"updatedBy\" ";
		$query.=", \"updatedDate\" ";
		$query.=")";
		$query.=" VALUES (";
		$query.="'$itemName' ";
		$query.=" ,'$client' ";
		$query.=" ,'$date' ";
		$query.=" ,'$priceAdj' ";
		$query.=" ,'$adjType' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=" ,'".date(U)."' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=" ,'".date(U)."' ";
		$query.=")";
	}
	else if($_POST['submit'] == 'Save')
	{
		$query="Update tbl_quote SET ";
		$query.="\"name\" ='$itemName'";
		$query.=", \"client\" ='$client'";
		$query.=", \"date\" ='$date'";
		$query.=", \"priceAdj\" ='$priceAdj' ";
		$query.=", \"priceType\" ='$adjType' ";
		$query.=", \"updatedBy\" ='{$_SESSION['employeeID']}' ";
		$query.=", \"updatedDate\" ='".date(U)."' ";
		$query.=" where \"quoteId\"=".$qid;			
	}
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = "Error while storing Quote information to databse!";	
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);	
}
echo json_encode($return_arr);
return;	
?>
