<?php
require('Application.php');
require('../../header.php');
$today1=date("l, F d, Y");
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>$compname Administration</center></b></font>";
echo "<p>";
echo "<center>";
echo "<font face=\"arial\" size=\"+1\" color=000000>";
echo "<i><b>$today1</b></i>";
echo "</font>";
echo "</center>";
echo "<p>";
echo "<center>";
echo "<a href=\"add_article.php\">Add an article</a> | ";
echo "<a href=\"search.php\">Edit an article</a>";
require('../../trailer.php');
?>
