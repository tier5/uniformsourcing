<?php
require('Application.php');
require('../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

extract($_POST);
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['submitType']))
{
	if($_POST['submitType'] == 'colorAdd')
	{
		if($cNameA == "")
		{		
			$return_arr['name'] = "Please Enter Color Name before submiting.";
		}
		else 
		{
			$query1 = "select count(1) from tbl_color where \"colorName\" = '".$cNameA."'";
			if(!($result1=pg_query($connection,$query1))){
				$return_arr['error'] = "Error while processing Color information!";
				echo json_encode($return_arr);			
				return;
			}
			$row = pg_fetch_row($result1);
			pg_free_result($result1);
			if($row[0] == 0)
			{
				$query1="INSERT INTO tbl_color ( ";
				$query1.="\"colorName\" ";
				if($cUnicodeA) $query1.=", \"cUnicode\" ";
				if($cHexA) $query1.=", \"hex\" ";
				if($cpmsA) $query1.=", \"pms\" ";
				$query1.=", \"status\" ";
				$query1.=")";
				$query1.=" VALUES (";
				$query1.="'$cNameA' ";
				if($cUnicodeA) $query1.=" ,'$cUnicodeA' ";
				if($cHexA) $query1.=" ,'$cHexA' ";
				if($cpmsA) $query1.=" ,'$cpmsA' ";
				$query1.=" ,1 ";
				$query1.=")";				
				if(!($result1=pg_query($connection,$query1))){
					$return_arr['error'] = "Error while storing Color information to databse!";	
				}
				pg_free_result($result1);
			}
			else
			{
				$return_arr['error'] = "Color information you entered already exist in Database.";				
			}
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'colorEdit')
	{
		
		if($cNameE == "")
		{		
			$return_arr['name'] = "Please Enter Color Name before submiting.";
		}
		else 
		{
			$query1="Update tbl_color SET ";
			$query1.="\"colorName\" ='$cNameE'";
			 $query1.=", \"cUnicode\" ='$cUnicodeE'";
			$query1.=", \"hex\" ='$chexE'";
			 $query1.=", \"pms\" ='$cpmsE' ";
			$query1.=" where \"colorID\"=".$colorEditId;
			if(!($result1=pg_query($connection,$query1))){
					$return_arr['error'] = "Error while storing Color information to databse!";	
					echo json_encode($retun_arr);
					return;
				}
			pg_free_result($result1);
			
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'garmentAdd')
	{
		if($gNameA == "")
		{		
			$return_arr['name'] = "Please Enter Garment Name before submiting.";
		}
		else
		{
			$query2 = "select count(1) from \"tbl_garment\" where \"garmentName\" = '".$gNameA."'";
			if(!($result2=pg_query($connection,$query2))){
				$return_arr['error'] = "Error while processing garment information!";
				echo json_encode($return_arr);			
				return;
			}
			$row = pg_fetch_row($result2);
			pg_free_result($result2);
			if($row[0] == 0)
			{
				$query2="INSERT INTO \"tbl_garment\" ( ";
				$query2.="\"garmentName\" ";
				if($gDescA) $query2.=", \"gdescription\" ";
				if($gUnicodeA) $query2.=", \"gUnicode\" ";
				if($gImg_urlA) $query2.=", \"imgURL\" ";
				 $query2.=", \"status\" ";
				$query2.=")";
				$query2.=" VALUES (";
				$query2.="'$gNameA' ";
				if($gDescA) $query2.=" ,'$gDescA' ";
				if($gUnicodeA) $query2.=" ,'$gUnicodeA' ";
				if($gImg_urlA) $query2.=" ,'$gImg_urlA' ";
				$query2.=" ,1 ";
				$query2.=")";
				
				if(!($result2=pg_query($connection,$query2))){
					$return_arr['error'] = "Error while storing Garment information to databse!";					
				}
				pg_free_result($result2);
			}
			else
			{
				$return_arr['error'] = "Garment information you entered already exist in Database.";				
			}
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'garmentEdit')
	{
		if($gNameE == "")
		{		
			$return_arr['name'] = "Please Enter Garment Name before submiting.";
		}
		else
		{
				$query2="Update tbl_garment SET ";
				$query2.="\"garmentName\" ='$gNameE'";
				$query2.=", \"gdescription\" ='$gDescE'";
				$query2.=", \"gUnicode\" ='$gUnicodeE'";
				$query2.=", \"imgURL\" ='$gImg_urlE' ";
				$query2.=" where \"garmentID\"=".$garmentEditId;
				if(!($result2=pg_query($connection,$query2))){
					$return_arr['error'] = "Error while storing Garment information to databse!";	
					echo json_encode($return_arr);			
					return;
				}
				pg_free_result($result2);
			
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'fabricAdd')
	{
		if($fNameA == "")
		{		
			$return_arr['name'] = "Please Enter Fabric Name before submiting.";
		}
		else
		{
			$query3 = "select count(1) from \"tbl_fabrics\" where \"fabName\" = '".$fNameA."'";
			if(!($result3=pg_query($connection,$query3))){
				$return_arr['error'] = "Error while processing garment information!";
				echo json_encode($return_arr);			
				return;
			}
			$row = pg_fetch_row($result3);
			pg_free_result($result3);
			if($row[0] == 0)
			{
				$query3="INSERT INTO \"tbl_fabrics\" ( ";
				$query3.="\"fabName\" ";
				if($fDescA) $query3.=", \"fabDescription\" ";
				if($fUnicodeA) $query3.=", \"fUnicode\" ";
				if($fImg_urlA) $query3.=", \"imgURL\" ";
				$query3.=", \"status\" ";
				$query3.=")";
				$query3.=" VALUES (";
				$query3.="'$fNameA' ";
				if($fDescA) $query3.=" ,'$fDescA' ";
				if($fUnicodeA) $query3.=" ,'$fUnicodeA' ";
				if($fImg_urlA) $query3.=" ,'$fImg_urlA' ";
				$query3.=" ,1 ";
				$query3.=")";
				
				if(!($result3=pg_query($connection,$query3))){
					$return_arr['error'] = "Error while storing Fabric information to databse!";				
				}
				pg_free_result($result3);
			}
			else
			{
				$return_arr['error'] = "Fabric information you entered already exist in Database.";				
			}
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'fabricEdit')
	{
		if($fNameE == "")
		{		
			$return_arr['name'] = "Please Enter Fabric Name before submiting.";
		}
		else
		{
			
				$query3="Update tbl_fabrics SET ";
				$query3.="\"fabName\" ='$fNameE'";
				$query3.=", \"fabDescription\" ='$fDescE'";
				$query3.=", \"fUnicode\" ='$fUnicodeE'";
				$query3.=", \"imgURL\" ='$fImg_urlE' ";
				$query3.=" where \"fabricID\"=".$fabricEditId;
				if(!($result3=pg_query($connection,$query3))){
					$return_arr['error'] = "Error while storing Fabric information to databse!";
					echo json_encode($return_arr);			
					return;
				}
				pg_free_result($result3);
		
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'sizeAdd')
	{
		if($sNameA == "")
		{		
			$return_arr['name'] = "Please Enter Size Name before submiting.";
		}
		else
		{
			$query4 = "select count(1) from \"tbl_size\" where \"sizeName\" = '".$sNameA."'";
			if(!($result4=pg_query($connection,$query4))){
				$return_arr['error'] = "Error while processing garment information!";
				echo json_encode($return_arr);			
				return;
			}
			$row = pg_fetch_row($result4);
			pg_free_result($result4);
			if($row[0] == 0)
			{
				$query4="INSERT INTO \"tbl_size\" ( ";
				$query4.="\"sizeName\" ";
				if($sDescA) $query4.=", \"sizeDescription\" ";
				if($sUnicodeA) $query4.=", \"sUnicode\" ";
				 $query4.=", \"status\" ";
				$query4.=")";
				$query4.=" VALUES (";
				$query4.="'$sNameA' ";
				if($sDescA) $query4.=" ,'$sDescA' ";
				if($sUnicodeA) $query4.=" ,'$sUnicodeA' ";
				$query4.=" ,1 ";
				$query4.=")";
				
				if(!($result4=pg_query($connection,$query4))){
					$return_arr['error'] = "Error while storing Size information to databse!";		
				}
				pg_free_result($result4);
			}
			else
			{
				$return_arr['error'] = "Size information you entered already exist in Database.";				
			}
		}
		echo json_encode($return_arr);
		return;
	}
	if($_POST['submitType'] == 'sizeEdit')
	{
		if($sNameE == "")
		{		
			$return_arr['name'] = "Please Enter Size Name before submiting.";
		}
		else
		{
			
				$query4="Update tbl_size SET ";
				$query4.="\"sizeName\" ='$sNameE'";
				$query4.=", \"sizeDescription\" ='$sDescE'";
				$query4.=", \"sUnicode\" ='$sUnicodeE'";
				$query4.=" where \"sizeID\"=".$sizeEditId;
				if(!($result4=pg_query($connection,$query4))){
					$return_arr['error'] = "Error while storing Size information to databse!";	
					echo json_encode($return_arr);			
					return;
				}
				pg_free_result($result4);
			
		}
		echo json_encode($return_arr);
		return;
	}
}
$return_arr['error'] = "Internal Error. Please consult your system administrator.";
echo json_encode($return_arr);
?>