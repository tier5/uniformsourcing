<?php
require('Application.php');
require('../../header.php');
$author=$_POST['author'];
$title=$_POST['title'];
$keyword=$_POST['keyword'];
$subject=$_POST['subject'];
$selectdate=$_POST['selectdate'];
if($debug == "on"){
	echo "author IS $author<br>";
	echo "title IS $title<br>";
	echo "keyword IS $keyword<br>";
	echo "subject IS $subject<br>";
	echo "selectdate IS $selectdate<br>";
}
$today1=date("m/d/Y");
if($author != ""){
	$author1="AND \"author\" LIKE '%$author%'";
}
if($title != ""){
	$title1="AND \"title\" LIKE '%$title%'";
}
if($keyword != ""){
	$keyword1="AND \"keyword\" LIKE '%$keyword%'";
}
if($subject != ""){
	$subject1="AND \"subject\" LIKE '%$subject%'";
}
if($selectdate == "week"){
	$dateselect=mktime(date("H"), date("i"), date("s"), date("m"), date("d")-7, date("Y"));
	$selectdate1="AND \"creationdate\" >= '$dateselect'";
}
if($selectdate == "month"){
	$dateselect=mktime(date("H"), date("i"), date("s"), date("m")-1, date("d"), date("Y"));
	$selectdate1="AND \"creationdate\" >= '$dateselect'";
}
$dateselect=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m")-1, date("d"), date("Y")));
$query1=("SELECT * ".
		 "FROM \"knowledgebase\" ".
		 "WHERE '0'='0' $author1 $title1 $keyword1 $subject1 $selectdate1 ".
		 "ORDER BY \"creationdate\"");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[] = $row1;
}
if($debug == "on"){
	echo "count ";
	echo count($data1);
}

echo "<center>";
echo "<font face=\"arial\" size=\"+1\"><b>Search Results</b></font>";
echo "<p>";
echo "<table cellpadding=\"3\" cellspacing=\"4\">";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Creation Date</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Revised Date</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Title</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Author</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>More</font></td>";
echo "</tr>";
for($i=0; $i < count($data1); $i++){
	$cdate=date("m/d/Y", $data1[$i]['creationdate']);
	$rdate=date("m/d/Y", $data1[$i]['revisedate']);
	echo "<tr bgcolor=cccccc>";
	echo "<td><b><font face=\"arial\" size=\"-1\">$cdate</font></b></td>";
	echo "<td><b><font face=\"arial\" size=\"-1\">$rdate</font></b></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data1[$i]['title']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data1[$i]['author']."</font></td>";
	echo "<td>";
	echo "<font size=\"-1\" face=\"arial\"><a href=\"detail.php?articleID=".$data1[$i]['articleID']."\">More</a>";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
echo "<p>";
echo "<spacer type=\"verticle\" size=\"20%\">";
echo "<hr>";
require('../../trailer.php');
?>
