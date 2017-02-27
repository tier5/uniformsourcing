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
  		$query="INSERT INTO tbl_item ( ";
		$query.="\"quoteId\" ";
		$query.=", \"name\" ";
		$query.=", \"itemNum\" ";		
		$query.=", \"vendor\" ";
		$query.=", \"description\" ";
		$query.=", \"price\" ";
		$query.=", \"adjPrice\" ";
		$query.=", \"createdBy\" ";
		$query.=", \"createdDate\" ";
		$query.=", \"updatedBy\" ";
		$query.=", \"updatedDate\" ";
		$query.=")";
		$query.=" VALUES (";
		$query.="'$qid' ";
		$query.=", '$itemName' ";
		$query.=" ,'$itemNum' ";
		$query.=" ,'$vendor' ";
		$query.=" ,'$description' ";
		$query.=" ,'$price' ";
		$query.=" ,'$priceDisc' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=" ,'".date(U)."' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=" ,'".date(U)."' ";
		$query.=")";
	}
	else if($_POST['submit'] == 'Save')
	{
		$query="Update tbl_item SET ";
		$query.="\"name\" ='$itemName'";
		$query.=", \"vendor\" ='$vendor'";
		$query.=", \"itemNum\" ='$itemNum'";
		$query.=", \"description\" ='$description' ";
		$query.=", \"price\" ='$price' ";
		$query.=", \"adjPrice\" ='$priceDisc' ";
		$query.=", \"updatedBy\" ='{$_SESSION['employeeID']}' ";
		$query.=", \"updatedDate\" ='".date(U)."' ";
		$query.=" where \"itemId\"=".$iid;			
	}
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = "Error while storing Item information to databse!";	
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);	
}
echo json_encode($return_arr);
return;	
?>
