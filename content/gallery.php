<?php
	require('Application.php');
	require('header.php');
	if($debug == "on"){
		echo "count resultapp1 IS ".count($resultapp1)."<br>";
		echo "count dataapp1 IS ".count($dataapp1)."<br>";
		echo "_SESSION firstname IS ".$_SESSION['firstname']."<br>";
		echo "_SESSION lastname IS ".$_SESSION['lastname']."<br>";
		print_r ($dataapp1);
		print_r (mysql_fetch_array($resultapp1));
	}
	echo "<center>";
	echo "<h3>".$_GET[gallery]."</h3>";
	
	
	
	if(isset($_GET[gallery])){
	
//define the path as relative
$path = "images/".$_GET[gallery];

//using the opendir function
$dir_handle = @opendir($path) or die("Unable to open $path");

echo "Directory Listing of $path<br/>";



while ($file = readdir($dir_handle)) {
   if($file!="." && $file!=".."){
      echo "<a href='$path/$file' target='_blank'><img src='$path/$file' border='0' alt='$file' height='100' style='margin-right:20px;margin-bottom:20px;'></a> ";
   }
}

closedir($dir_handle);

}
	
	
	
	
	echo "</center>";
	require('trailer.php');
?>
