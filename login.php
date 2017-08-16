<?php
	require('Application.php');
	session_start();
	if(isset($_SESSION['username'])){
		header ("Location: content/index.php");
	} else {
		echo "<html>";
		echo "<head>";
		echo "<title>$compname Admin Section</title>";
		echo "<SCRIPT>";
	  //Clear inherited frames
		echo "if (top.frames.length!=0)";
		echo "top.location=self.document.location;";
		echo "</SCRIPT>";
		echo "<STYLE>";
		echo "TD {";
		echo "COLOR: #303030; FONT-FAMILY: verdana, Helvetica, sans-serif; FONT-SIZE: 11px; FONT-WEIGHT: normal";
		echo "}";
		echo "A:link {";
		echo "COLOR: #737373; TEXT-DECORATION: none";
		echo "}";
		echo "A:visited {";
		echo "COLOR: #737373; TEXT-DECORATION: none";
		echo "}";
		echo "A:active {";
		echo "COLOR: #0074E0; TEXT-DECORATION: none";
		echo "}";
		echo "A:hover {";
		echo "COLOR: #0074E0; TEXT-DECORATION: underline";
		echo "}";
		echo "input.btn {";
		echo "color:#333333;";
		echo "font-family:'trebuchet ms',helvetica,sans-serif;";
		echo "font-size:12px;";
		echo "font-weight:bold;";
		echo "background-color:#fed;";
		echo "border:1px solid;";
		echo "border-top-color:#545454;";
		echo "border-left-color:#545454;";
		echo "border-right-color:#3E3E3E;";
		echo "border-bottom-color:#3E3E3E;";
		echo "filter:progid:DXImageTransform.Microsoft.Gradient";
		echo "(GradientType=0,StartColorStr='#ffffffff',EndColorStr='#DADADA');}";
		echo "input.btnhov{";
		echo "border-top-color:#169D13;";
		echo "border-left-color:#169D13;";
		echo "border-right-color:#169D13;";
		echo "border-bottom-color:#169D13;}";
		echo "</STYLE>";
		echo "</head>";
		echo "<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>";
		if(isset($_GET['error'])) {
	   $myerror=$_GET['error'];
	  }else{
	   $myerror="";
	  }
		echo "<table height=\"100%\" width=\"100%\" border=0 cellpadding=0 cellspacing=0>";
		echo "<tr>";
		echo "<td align=\"center\">";
		echo "<font color=\"#FF0000\"><h4>";
		if($myerror == "both") {
			echo "Please enter in a Username and Password below.";
		}
		if($myerror == "nu") {
			echo "Please enter in a USERNAME below.";
		}
		if($myerror == "np") {
			echo "Please enter in a PASSWORD below.";
		}
		if($myerror == "nomatch") {
			echo "I'm sorry, your USERNAME and PASSWORD did not match.";
		}
		echo "</h4></font>";
		echo "<table width=\"393\" border=0 cellpadding=0 cellspacing=0>";
		echo "<form action=\"content/index.php\" method=\"POST\">";
		echo "<tr>";
		echo "<td colspan=\"4\"><img src=\"content/images/login_01.gif\" width=\"393\" height=\"55\" border=\"0\"></td>";
		echo "</tr>";
		echo "</table>";
		echo "<table width=\"393\" border=0 cellpadding=0 cellspacing=0>";
		echo "<tr>";
		echo "<td width=\"8\"><img src=\"content/images/login_03.gif\" width=\"8\" height=\"59\" border=\"0\"></td>";
		echo "<td width=\"372\" colspan=\"2\" background=\"content/images/login_bg.gif\" align=\"center\">";
		echo "<table>";
		echo "<tr>";
		echo "<td>Username:</td><td><input type=\"text\" name=\"username\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>Password:</td><td><input type=\"password\" name=\"password\"></td>";
		echo "</tr>";
		echo "</table>";
		echo "</td>";
		echo "<td width=\"13\" height=\"59\"><img src=\"content/images/login_04.gif\" width=\"13\" height=\"59\" border=\"0\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td width=\"8\"><img src=\"content/images/login_06.gif\" width=\"8\" height=\"61\" border=\"0\"></td>";
		echo "<td width=\"372\" colspan=\"2\" align=\"center\" valign=\"top\"><img src=\"content/images/login_05.gif\" width=\"372\" height=\"13\" border=\"0\"><br>";
		echo "<CENTER><B>WELCOME</B> to The Internal Intranet.<br>Please enter your Username and Password<br>then hit the \"Submit\" button below.</CENTER>";
		echo "</td>";
		echo "<td width=\"13\"><img src=\"content/images/login_07.gif\" width=\"13\" height=\"61\" border=\"0\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td width=\"8\"><img src=\"content/images/login_08.gif\" width=\"8\" height=\"105\" border=\"0\"></td>";
		echo "<td width=\"176\"><img src=\"content/images/login_logo.gif\" width=\"176\" height=\"105\" border=\"0\"></td>";
		echo "<td width=\"196\" align=\"center\">";
		echo "<input type=\"submit\" value=\"Submit\" class=\"btn\"";
		echo "onmouseover=\"this.className='btn btnhov'\" onmouseout=\"this.className='btn'\"/> &nbsp; ";
		echo "<input type=\"reset\" value=\"Reset\" class=\"btn\"";
		echo "onmouseover=\"this.className='btn btnhov'\" onmouseout=\"this.className='btn'\"/><br>";
		echo "<img src=\"content/images/blank.gif\" width=\"196\" height=\"10\" border=\"0\"></td>";
		echo "<td width=\"13\"><img src=\"content/images/login_09.gif\" width=\"13\" height=\"105\" border=\"0\"></td>";
		echo "</tr>";
		echo "</table>";
		echo "<table width=\"393\" border=0 cellpadding=0 cellspacing=0>";
		echo "<tr>";
		echo "<td colspan=\"4\"><a href=\"http://www.i2net.com\"><img src=\"content/images/login_02.gif\" width=\"393\" height=\"67\" border=\"0\"></a></td>";
		echo "</tr>";
		echo "</form>";
		echo "</table>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</body>";
		echo "</html>";
	}
?>
