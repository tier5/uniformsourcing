<?php
require('Application.php');
require('../header.php');

$datehired=mktime(0, 0, 0, $monthhired, $dayhired, $yearhired);
$empid=$_POST['employeeID'];
extract($_POST);
if(isset($debug) AND $debug == "on"){
	echo "firstname IS $firstname<br>";
	echo "lastname IS $lastname<br>";
	echo "empid IS $empid<br>";
	echo "poppassword IS $poppassword<br>";
	echo "wage IS $wage<br>";
	echo "salary IS $salary<br>";
	echo "datehired IS $datehired<br>";
	echo "zip IS $zip<br>";
	echo "state IS $state<br>";
	echo "city IS $city<br>";
	echo "password IS $passwordnew<br>";
	echo "username IS $usernamenew<br>";
	echo "email IS $email<br>";
	echo "cell IS $cell<br>";
	echo "alphapager IS $alphapager<br>";
	echo "pager IS $pager<br>";
	echo "phone IS $phone<br>";
	echo "address IS $address<br>";
	echo "title IS $title<br>";
}
$title=pg_escape_string($title);
$address=pg_escape_string($address);
$query1="UPDATE \"employeeDB\" ".
		 "SET ";
		 if($employeeType!="")$query1.="\"employeeType\" = '$employeeType', ";
		 if($employeeType!="" && $employeeType == 1)$query1.="employee_type_id = '$vendorName', ";
		 else if($employeeType!="" && $employeeType ==2)$query1.="employee_type_id = '$clinetname', ";
		 else $query1.="employee_type_id = 0, ";
		 $query1.="\"firstname\" = '$firstname', ";
		 $query1.="\"lastname\" = '$lastname', ";
		 $query1.="\"title\" = '$title', ";
		 $query1.="\"address\" = '$address', ";
		 $query1.= "\"phone\" = '$phone', ";
		 $query1.="\"pager\" = '$pager', ";
		 $query1.= "\"alphapager\" = '$alphapager', ";
		 $query1.="\"cell\" = '$cell', ";
		 $query1.="\"email\" = '$email', ";
		 $query1.="\"username\" = '$usernamenew', ";
		 $query1.="\"password\" = '$passwordnew', ";
		 $query1.="\"city\" = '$city', ";
		 $query1.="\"state\" = '$state', ";
		 $query1.="\"zip\" = '$zip', ";
		 $query1.="\"datehired\" = '$datehired', ";
		 $query1.="\"salary\" = '$salary', ";
		 $query1.="\"wage\" = '$wage', ";
		 $query1.="\"poppassword\" = '$poppassword', ";
		 $query1.="\"active\" = 'yes' ";
		 $query1.="WHERE \"employeeID\" = '$empid' ";
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$query2=("SELECT * ".
		 "FROM \"permissions\" ".
		 "WHERE \"employee\" = '$empid'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<form action=\"editemp1.php\" method=\"post\">";
echo "<table width=\"80%\">";
echo "<tr>";
echo "<td colspan=\"5\" bgcolor=\"white\"><b>$firstname $lastname 's Permissions</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Accounting</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Administration</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Human Resources</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Internal Directory</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Office Calendar</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
if($data2[0]['accounting'] == "on"){
	echo "<input type=\"checkbox\" name=\"accounting\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"accounting\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['admin'] == "on"){
	echo "<input type=\"checkbox\" name=\"admin\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"admin\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['humanresources'] == "on"){
	echo "<input type=\"checkbox\" name=\"humanresources\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"humanresources\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['directory'] == "on"){
	echo "<input type=\"checkbox\" name=\"directory\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"directory\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['calendar'] == "on"){
	echo "<input type=\"checkbox\" name=\"calendar\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"calendar\">";
}
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Operations</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Sales</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Support</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Production</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Purchasing</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
if($data2[0]['operations'] == "on"){
	echo "<input type=\"checkbox\" name=\"operations\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"operations\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['sales'] == "on"){
	echo "<input type=\"checkbox\" name=\"sales\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"sales\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['support'] == "on"){
	echo "<input type=\"checkbox\" name=\"support\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"support\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['production'] == "on"){
	echo "<input type=\"checkbox\" name=\"production\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"production\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['purchasing'] == "on"){
	echo "<input type=\"checkbox\" name=\"purchasing\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"purchasing\">";
}
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">External User</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Able to Login</font></td>";

echo "</tr>";
echo "<tr>";
echo "<td>";
if($data2[0]['external'] == "on"){
	echo "<input type=\"checkbox\" name=\"external\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"external\">";
}
echo "</td>";
echo "<td>";
if($data2[0]['login'] == "on"){
	echo "<input type=\"checkbox\" name=\"login\" checked>";
}else{
	echo "<input type=\"checkbox\" name=\"login\">";
}
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"hidden\" name=\"ID\" value=\"".$data2[0]['ID']."\"><input type=\"hidden\" name=\"employeeID\" value=\"$empid\"></td>";
echo "<td colspan=\"5\" align=\"center\">";
echo "<br><br>";
echo "<input type=\"submit\" value=\"   Enter Employee Permissions   \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../trailer.php');
?>
