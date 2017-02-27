<?php
require('Application.php');
$target_dir = "../../uploadFiles/client/";

extract($_POST);
$ID=$_POST['ID'];
$client=$_POST['client'];
$contact=$_POST['contact'];
$phone=$_POST['phone'];
$fax=$_POST['fax'];
$address=$_POST['address'];
$address2=$_POST['address2'];
$city=$_POST['city'];
$state-$_POST['state'];
$zip=$_POST['zip'];
$country=$_POST['country'];
$email=$_POST['email'];
$www=$_POST['www'];
$clientID=$_POST['clientID'];
$class=$_POST['class'];
$intranet=$_POST['intranet'];
$accountmanager=$_POST['accountmanager'];
$logo=$_POST['logo'];

$query1=("SELECT \"ID\", \"clientID\" ".
		 "FROM \"clientDB\"");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
for($i=0; $i < count($data1); $i++){
	if($clientID == $data1[$i]['clientID'] AND $ID != $data1[$i]['ID']){
		require('../../header.php');
		echo "The client ID you put in is in use by another client. Please select another client ID.<br>";
		require('../../trailer.php');
		exit;
	}elseif($clientID != ""){
	}else{
		$query4=("SELECT MAX(\"ID\") as maxid ".
				 "FROM \"clientDB\" ");
		if(!($result4=pg_query($connection,$query4))){
			print("Failed query4: " . pg_last_error($connection));
			exit;
		}
		while($row4 = pg_fetch_array($result4)){
			$data4[]=$row4;
		}
		$clientID=($data4[0]['maxid'] + 1);
	}


}
$filename ='';
//echo $_FILES["logo"]["tmp_name"];
if(isset ($_FILES["logo"]) && $_FILES["logo"]["tmp_name"] !='')
{
	//echo "test";
	$filename = basename($_FILES["logo"]["name"]);
	$ext = substr($filename, (strrpos($filename, ".")));
	//echo $ext;
	//echo "test1";
	$filename = $client.$ext;
	$target_file = $target_dir . $filename;
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["update"])) {	
		//echo "test2";
	    $check = getimagesize($_FILES["logo"]["tmp_name"]);
	    if($check !== false) {	    	
	        if (file_exists($target_file)) {
			    unlink($target_file);
			}
			include $appdir.'imageResize.php';
			$image = new Resize_Image;
			 //echo "test3";
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

if(isset($_POST['update'])){
	$query2="UPDATE \"clientDB\" ".
			 "SET ".
			 "\"client\" = '$client', ".
			 "\"contact\" = '$contact', ".
			 "\"phone\" = '$phone', ".
			 "\"fax\" = '$fax', ".
			 "\"address\" = '$address', ".
			 "\"address2\" = '$address2', ".
			 "\"city\" = '$city', ".
			 "\"state\" = '$state', ".
			 "\"zip\" = '$zip', ".
			 "\"country\" = '$country', ".
			 "\"email\" = '$email', ".
			 "\"www\" = '$www', ".
			 "\"clientID\" = '$clientID', ".
			 "\"class\" = '$class', ".
                         "\"intranet\" = '$intranet', ".
			 "\"accountmanager\" = '$accountmanager', ";
			 if($shipperno!="")$query2.="shipperno = '$shipperno',";
			 else $query2.="shipperno = null,";
			if($filename!="")$query2.="logo = '$filename',";
			 if($carrier>0)$query2.="carrier = '$carrier',";
			 $query2.="\"notes\" = '$notes', ".
			 "\"active\" = 'yes' ".
			 "WHERE \"ID\" = '$ID'";
			 if(!($result2=pg_query($connection,$query2))){
				print("Failed query2: " . pg_last_error($connection));
				exit;

			}
			//echo $query2;
			//echo $filename;
	header("location: ../index.php");
}
if(isset($_POST['deactivate'])){
	$query3="UPDATE \"clientDB\" ".
			 "SET ".
			 "\"client\" = '$client', ".
			 "\"contact\" = '$contact', ".
			 "\"phone\" = '$phone', ".
			 "\"fax\" = '$fax', ".
			 "\"address\" = '$address', ".
			 "\"address2\" = '$address2', ".
			 "\"city\" = '$city', ".
			 "\"state\" = '$state', ".
			 "\"zip\" = '$zip', ".
			 "\"country\" = '$country', ".
			 "\"email\" = '$email', ".
			 "\"www\" = '$www', ".
			 "\"clientID\" = '$clientID', ".
			 "\"class\" = '$class', ".
                         "\"intranet\" = '$intranet', ".
			 "\"accountmanager\" = '$accountmanager', ";
			 if($shipperno!="")$query3.="shipperno = '$shipperno',";			 
			 else $query3.="shipperno = null,";
			 if($filename!="")$query3.="logo = '$filename',";
			 if($carrier>0)$query2.="carrier = '$carrier',";
			 $query3.="\"notes\" = '$notes', ".
			 "\"active\" = 'no' ".
			 "WHERE \"ID\" = '$ID'";
	if(!($result3=pg_query($connection,$query3))){
		print("Failed query3: " . pg_last_error($connection));
		exit;
	}
	header("location: ../index.php");
}
?>
