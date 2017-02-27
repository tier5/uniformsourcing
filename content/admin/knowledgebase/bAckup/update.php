<?php
require('Application.php');
$author=$_POST['author'];
$title=$_POST['title'];
$keyword=$_POST['keyword'];
$subject=$_POST['subject'];
$article=$_POST['article'];
$articleID=$_POST['articleID'];
if($debug == "on"){
	require('../../header.php');
	echo "author IS $author<br>";
	echo "title IS $title<br>";
	echo "keyword IS $keyword<br>";
	echo "subject IS $subject<br>";
	echo "article IS $article<br>";
	echo "articleID IS $articleID<br>";
	require('../../trailer.php');
	exit;
}
$today=mktime();
$query1=("UPDATE \"knowledgebase\" ".
		 "SET ".
		 "\"title\" = '$title', ".
		 "\"author\" = '$author', ".
		 "\"keyword\" = '$keyword', ".
		 "\"subject\" = '$subject', ".
		 "\"article\" = '$article', ".
		 "\"revisedate\" = '$today' ".
		 "WHERE \"articleID\" = '$articleID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: index.php");
?>
