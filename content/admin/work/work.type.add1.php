<?php
require('Application.php');
require('../../header.php');
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>PRICING ADD</center></b></font>";
echo "<p>";
echo "<form action=\"work.type.add2.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>ITEM ID:</td>";
echo "<td><input type=\"text\" name=\"itemid\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>DESCRIPTION:</td>";
echo "<td><input type=\"text\" name=\"description\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>PRICE per HOUR:</td>";
echo "<td><input type=\"text\" name=\"price\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><div align=\"center\"><input type=\"submit\" name=\"submit\" value=\" Add Pricing Item \"></div></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../../trailer.php');
?>
