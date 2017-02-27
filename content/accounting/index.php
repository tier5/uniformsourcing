<?php
require('Application.php');
require('../header.php');
echo "<font face=\"arial\">";
echo "<center><font size=\"5\">ACCOUNTING</font>";
echo "<p>";
echo "<table border=\"0\">";
echo "<tr>";
echo "<td align=\"center\"><font face=\"arial\"><a href=\"billing/index.php\">Billing</a> | ";
echo "<a href=\"billing/archived.index.php\">Archived Invoices</a> | ";
echo "<a href=\"report/report.php\">Project report</a><p></td>";
echo "</tr>";
echo "</table>";
require('../trailer.php');
?>
