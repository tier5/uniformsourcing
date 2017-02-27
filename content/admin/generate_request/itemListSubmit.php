<?php 
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

$return_arr = array();

extract($_POST);
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['quoteSubmit']))
{
	if($_POST['quoteSubmit'] == 'Save')
	{
		$query="Update tbl_quote SET ";
		$query.=" \"priceAdj\" ='$priceAdj' ";
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
	$query = "";
	for($i=0, $queryCount = 0;$i < count($price);$i++, $queryCount++)
	{
		$modifiedPrice = $price[$i];
		if(!isset($_POST["disable_{$itemId[$i]}"]))
		{
			if($adjType == '%')
			{			
				$modifiedPrice = $acutalPrice[$i] + ($acutalPrice[$i]*$priceAdj/100);
			}
			else
			{				
				$modifiedPrice = $acutalPrice[$i] + $priceAdj;				
			}
		}
		$query .="Update tbl_item SET ";		
		$query.=" \"adjPrice\" ='$modifiedPrice' ";
		if(isset($_POST["disable_{$itemId[$i]}"]))
			$query.=", \"disableAutoUpdate\" =1 ";
		else
			$query.=", \"disableAutoUpdate\" =0 ";
		$query.=", \"updatedBy\" ='{$_SESSION['employeeID']}' ";
		$query.=", \"updatedDate\" ='".date(U)."' ";
		$query.=" where \"itemId\"=".$itemId[$i].";";
		if($queryCount >9)
		{
			$queryCount = 0;
			if(!($result=pg_query($connection,$query)))
			{
				$return_arr['error'] = "Error while storing Quote information to database!";	
				echo json_encode($return_arr);
				return;
			}	
			pg_free_result($result);
			$query = "";
		}
	}
	if($query != "")
	{
		if(!($result=pg_query($connection,$query)))
		{
			$return_arr['error'] = "Error while storing Quote information to database!";	
			echo json_encode($return_arr);
			return;
		}	
		pg_free_result($result);
	}
}
if(isset($_POST['itemSubmit']))
{
	if($_POST['itemSubmit'] == 'Save')
	{
		$query = "";
		for($i=0, $queryCount = 0;$i < count($price);$i++, $queryCount++)
		{			
			$query .="Update tbl_item SET ";		
			$query.=" \"adjPrice\" ='$price[$i]' ";
			if(isset($_POST["disable_{$itemId[$i]}"]))
				$query.=", \"disableAutoUpdate\" =1 ";
			else
				$query.=", \"disableAutoUpdate\" =0 ";
			$query.=", \"updatedBy\" ='{$_SESSION['employeeID']}' ";
			$query.=", \"updatedDate\" ='".date(U)."' ";
			$query.=" where \"itemId\"=".$itemId[$i].";";
			if($queryCount >9)
			{
				$queryCount = 0;
				if(!($result=pg_query($connection,$query)))
				{
					$return_arr['error'] = "Error while storing Quote information to database!";	
					echo json_encode($return_arr);
					return;
				}	
				pg_free_result($result);
				$query = "";
			}
		}
	}
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = "Error while storing Item information to database!";	
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);
}
echo json_encode($return_arr);
return;	
?>
