<?php
require('Application.php');
$target_dir = "../../uploadFiles/client/";

extract($_POST);
$client=$_POST['client'];
$contact=$_POST['contact'];
$phone=$_POST['phone'];
$fax=$_POST['fax'];
$address=$_POST['address'];
$address2=$_POST['address2'];
$city=$_POST['city'];
$state=$_POST['state'];
$zip=$_POST['zip'];
$country=$_POST['country'];
$email=$_POST['email'];
$www=$_POST['www'];
$clientID=$_POST['clientID'];
$class=$_POST['class'];
$intranet=$_POST['intranet'];
$accountmanager=$_POST['accountmanager'];
$notes=$_POST['notes'];
$logo=$_POST['logo'];
if($debug == "on"){
	require('../../header.php');
	echo "client IS $client<br>";
	echo "contact IS $contact<br>";
	echo "phone IS $phone<br>";
	echo "fax IS $fax<br>";
	echo "address IS $address<br>";
	echo "address2 IS $address2<br>";
	echo "city IS $city<br>";
	echo "state IS $state<br>";
	echo "zip IS $zip<br>";
	echo "country IS $country<br>";
	echo "email IS $email<br>";
	echo "www IS $www<br>";
	echo "clientID IS $clientID<br>";
	echo "class IS $class<br>";
	echo "accountmanager IS $accountmanager<br>";
	echo "notes IS $notes<br>";
	require('../../trailer.php');
	exit;
}
if(!isset($_POST['client']) OR $_POST['client'] == ""){
	$error.="You forgot to enter the client name. Please go back and enter one.<br>";
}
if(!isset($_POST['contact']) OR $_POST['contact'] == ""){
	$error.="You forgot to enter the contact person of the new client. Please go back and enter one.<br>";
}
if(!isset($_POST['phone']) OR $_POST['phone'] == ""){
	$error.="You forgot to enter the phone number for the client. Please go back and enter one.<br>";
}
if(!isset($_POST['address']) OR $_POST['address'] == ""){
	$error.="You forgot to enter the address of the client. Please go back and enter one.<br>";
}
if(!isset($_POST['city']) OR $_POST['city'] == ""){
	$error.="You forgot to enter the city of the client. Please go back and enter one.<br>";
}
if(!isset($_POST['state']) OR $_POST['state'] == ""){
	$error.="You forgot to enter the state of the client. Please go back and enter one.<br>";
}
if(!isset($_POST['zip']) OR $_POST['zip'] == ""){
	$error.="You forgot to enter the zip code of the client. Please go back and enter one.<br>";
}
if(!isset($_POST['country']) OR $_POST['country'] == ""){
	$error.="You forgot to enter the country of the client. Please go back and enter one.<br>";
}
if(!isset($_POST['email']) OR $_POST['email'] == ""){
	$error.="You forgot to enter the email address of the client. Please go back and enter one.<br>";
}
if(isset($error)){
	require('error.php');
	exit;
}
if(isset($clientID) AND $clientID != ""){
	$query1=("SELECT \"clientID\" ".
			 "FROM \"clientDB\" ".
			 "WHERE \"clientID\" = '$clientID'");
	if(!($result1=pg_query($connection,$query1))){
		print("Failed query1 " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data1[]=$row1;
	}
	for($i=0; $i < count($data1); $i++){
		if($clientID == $data1[$i]['clientID']){
			require('../../header.php');
			echo "The client ID you selected is already in use. Please use the <b>BACK</b> button on your browser and correct this.";
			require('../../trailer.php');
			exit;
		}
	}
}else{
	$query3=("SELECT MAX(\"ID\") as \"maxid\" ".
			 "FROM \"clientDB\" ");
	if(!($result3=pg_query($connection,$query3))){
		print("Failed query3: " . pg_last_error($connection));
		exit;
	}
	while($row3 = pg_fetch_array($result3)){
		$data3[]=$row3;
	}
	$clientID=($data3[0]['maxid'] + 1);
}
//logoUpload
$filename ='';
if(isset ($_FILES["logo"]))
{
	$filename = basename($_FILES["logo"]["name"]);
	$ext = substr($filename, (strrpos($filename, ".")));
	//echo $ext;
	$filename = $client.$ext;
	$target_file = $target_dir . $filename;
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["Submit"])) {	
	    $check = getimagesize($_FILES["logo"]["tmp_name"]);
	    if($check !== false) {	    	
	        if (file_exists($target_file)) {
			    unlink($target_file);
			}
			include $appdir.'imageResize.php';
 
			$image = new Resize_Image;
			 
			$image->new_width = 450;
			$image->new_height = 450;
			 
			$image->image_to_resize = $_FILES["logo"]["tmp_name"]; // Full Path to the file
			 
			$image->ratio = true; // Keep Aspect Ratio?
			 
			// Name of the new image (optional) - If it's not set a new will be added automatically
			
			$image->new_image_name =  $client;
			$image->save_folder = $target_dir;
			$image->resize();
	        $uploadOk = 1;
	    } else {	       
	        $uploadOk = 0;
	    }
	}
}
$query2="INSERT INTO \"clientDB\" ".
		 "(\"client\", \"contact\", \"phone\", \"fax\", \"address\", \"email\", \"www\", \"clientID\", \"class\", \"accountmanager\", \"notes\", \"city\", \"state\", \"zip\", \"country\", \"address2\",";
		  if($shipperno!="")$query2.="shipperno,";
		  if($filename!="")$query2.="logo,";
		   if($carrier!="")$query2.="carrier,";
		   $query2.="\"intranet\",\"active\")";
		 $query2.="VALUES ('$client', '$contact', '$phone', '$fax', '$address', '$email', '$www', '$clientID', '$class', '$accountmanager', '$notes', '$city', '$state', '$zip', '$country', '$address2',";
		if($shipperno!="")$query2.="'$shipperno',";
		if($filename!="")$query2.="'$filename',";
		if($carrier!="")$query2.="'$carrier',";
		$query2.="'".$intranet."','yes')";

if(!($result2=pg_query($connection,$query2))){
	print("Failed query " . pg_last_error($connection));
	exit;
}
//header("location: ../index.php");
?>
