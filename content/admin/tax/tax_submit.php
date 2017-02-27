<?php 
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

$return_arr = array();

extract($_POST);
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['submit']))
{
	if($id == 0)
	{
  		$query="INSERT INTO tbl_tax ( ";
		$query.=" tax_name ";
		$query.=", tax_amount ";
		$query.=", status ";
		$query.=", createddate ";
		$query.=", createdby ";
		$query.=")";
		$query.=" VALUES (";
		$query.="'$tax_name' ";
		$query.=" ,'$amount' ";
		$query.=" ,'$status' ";
		$query.=" ,'".date(U)."' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=")";
	}
	else if($id >0)
	{
		$query="Update tbl_tax SET ";
		$query.="tax_name ='$tax_name'";
		$query.=", tax_amount ='$amount'";
		$query.=", status ='$status'";
		$query.=", updatedby ='{$_SESSION['employeeID']}' ";
		$query.=", updateddate ='".date(U)."' ";
		$query.=" where tax_id=".$id;			
	}
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = "Error while storing tax information to databse!";	
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);	
}
echo json_encode($return_arr);
return;	
?>
