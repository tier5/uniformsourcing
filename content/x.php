
<a href="x.php?gallery=Aprons">Aprons</a>
<a href="x.php?gallery=Dresses">Dresses</a>
<a href="x.php?gallery=Pants">Pants</a>
<a href="x.php?gallery=Skirts">Skirts</a>
<a href="x.php?gallery=Tops">Tops</a>
<a href="x.php?gallery=Vests">Vests</a>
<?php
if(isset($_GET[gallery])){
	
//define the path as relative
$path = "images/".$_GET[gallery];

//using the opendir function
$dir_handle = @opendir($path) or die("Unable to open $path");

echo "Directory Listing of $path<br/>";



while ($file = readdir($dir_handle)) {
   if($file!="." && $file!=".."){
      echo "<a href='$path/$file' target='_blank'><img src='$path/$file' border='0' alt='$file' height='100'></a> ";
   }
}

closedir($dir_handle);

}
?>