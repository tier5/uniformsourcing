<?php
require('Application.php');
require('../header.php');
?>
<script type="text/javascript">
function RestartInvoice()
{
	if(confirm('Are you sure you want to restart invoice')) 
	{
		window.location="samplerequest/restartinvoice.php"; 
		return true;
	} 
	else 
	{ 
		return false; 
	}
}
</script>
<?php

echo "<font face=\"arial\"";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"<b><center>Administration</center></b></font>";
echo "<p>";
echo "<br><br>";
echo "<center>";
echo "<a href=\"addemp.php\">Add Employee</a> | ";
echo "<a href=\"editemp0.php\">Edit Employee</a> | ";
echo "<a href=\"addvendr.php\">Add Vendor</a> | ";
echo "<a href=\"editVendr.php\">Edit Vendor</a> | ";
echo "<a href=\"Timeclock\">Time Clock</a> | ";
echo "<a href=\"knowledgebase/index.php\">Knowledgebase</a> | ";
echo "<a href=\"production/addclient.php\">Add Client</a> | ";
echo "<a href=\"production/editclient1.php\">Update/Delete Client</a> | ";
echo "<a href=\"officecal/admin_menu.php\">Calendar</a>";
echo "</center>";
echo "<br><br>";
echo "<center>";
echo "<a href=\"project/project.add.php\">Add Projects</a> | ";
echo "<a href=\"project/project.list.php\">List Projects</a> | ";
echo "<a href=\"projectReport/reportGeneration.php\">Project Reports</a>";
echo "</center>";
echo "<br><br>";
echo "<center>";
echo "<a href=\"tax/tax_list.php\">Tax</a>  | ";
echo "<a href=\"measurments.php\">Measurements</a>  | ";
if($_SESSION['employeeID']== 34 || $_SESSION['employeeID']==2)echo "<a href=\"track_log/track_log.php\">Tracking Log </a>  | ";
echo "<a href=\"carrier/carrier_list.php\">Carrier</a>  ";
echo "</center>";
echo "<br><br>";
echo "<center>";?>

<a href="" onclick="javascript:RestartInvoice();">Restart Invoice</a> | 
<?php 
echo "<a href=\"inventory/sizeScaleList.php\">Size Scale</a> | ";
echo "<a href=\"inventory/inventory.php\">Inventory</a>";
echo "</center>";
echo "<br><br>";
echo "<center>";
echo "<a href=\"work/work.add.php\">New Workorder</a> | <a href=\"work/work.view.php\">View Workorders</a> | <a href=\"work/work.type.add1.php\">Add Type and Global Pricing</a> | <a href=\"work/work.type.view.php\">Edit Type and Global Pricing</a>";
echo "</center>";
echo "</p>";
require('../trailer.php');
?>
