<?php
require('Application.php');
$author=$_POST['author'];
$title=$_POST['title'];
$keyword=$_POST['keyword'];
$subject=$_POST['subject'];
$article=$_POST['article'];
if($debug == "on"){
	require('../../header.php');
	echo "author IS $author<br>";
	echo "title IS $title<br>";
	echo "keyword IS $keyword<br>";
	echo "subject IS $subject<br>";
	echo "article IS $article<br>";
	require('../../trailer.php');
}
$creationdate=mktime();
$revisedate=mktime();
$query1=("INSERT INTO \"knowledgebase\" ".
		 "(\"author\", \"title\", \"creationdate\", \"revisedate\", \"keyword\", \"subject\", \"article\") ".
		 "VALUES ('$author', '$title', '$creationdate', '$revisedate', '$keyword', '$subject', '$article')");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: index.php");
?>
