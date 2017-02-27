<?php
	require('Application.php');
	/*if($_SESSION['employeeType']==1)
	{
	header('Location:admin/vendor/projectReportVendor.php');
	}*/
	require('header.php');
	if($debug == "on"){
		echo "count resultapp1 IS ".count($resultapp1)."<br>";
		echo "count dataapp1 IS ".count($dataapp1)."<br>";
		echo "_SESSION firstname IS ".$_SESSION['firstname']."<br>";
		echo "_SESSION lastname IS ".$_SESSION['lastname']."<br>";
		echo "_SESSION lastname IS ".$_SESSION['employeeType']."<br>";
		print_r ($dataapp1);
		print_r (mysql_fetch_array($resultapp1));
	}
	echo "<center>";
	echo "<h3><b>Welcome</b> To the Internal Intranet for $compname<br>";
	echo "Please remember to quit your web browser when finished.</h3>";
	//echo "_SESSION lastname IS ".$_SESSION['employeeType']."<br>";	
?>
<?php
				if(!(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] >0))
				{
				
				?>
                <table width="50%">
                  <tr valign="top">
                    <td align="center"><a href="inner.php"><img src="images/patterns.jpg" alt="projectmnagement" width="165" height="99" border="0" /></a></td>
                   <td align="center"><a href="admin/project_mgm/project_mgm.list.php"><img src="images/projectmanagement.jpg" alt="Quote" width="165" height="99" border="0" /></a></td>
                   <td align="center"><a href="admin/sample_database/database_sample_list.php"><img src="images/sampleDatabase.jpg" alt="Quote" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/work/work.add.php"><img src="images/newworkorder.jpg" alt="neworkorder" width="165" height="99" border="0" /></a></td>
                     <td align="center"><a href="admin/fill_orders/fill_orders_list.php"><img src="images/fill_orders.jpg" alt="Fill Orders" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/inventory/inventory.php"><img src="images/inventory.jpg" alt="inventory" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="barcode/barcode_generator.php"><img src="images/barcode.jpg" alt="barcode" width="165" height="99" border="0" /></a></td>
                  </tr>
                </table>
                <table width="50%">
                  <tr valign="top">
                    <td align="center"><a href="production/clientlookup.php"><img src="images/clientdatabse.jpg" alt="cllientsatabase" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/project_mgm/project_purchase.list.php"><img src="images/projects.jpg" alt="projects" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/samplerequest/samplerequest.list.php"><img src="images/samplerequest.jpg" alt="sampelrequest" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/projectReport/reportGeneration.php"><img src="images/projectreport.jpg" alt="sampelrequest" width="165" height="99" border="0" /></a></td>
                    <td align="center" ><a href="http://internal.uniformsourcing.com/content/admin/editVendr.php"><img src="images/generateQuote.png" alt="Quote" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/generate_request/quoteList.php"><img src="images/generateRequest.jpg" alt="generate_request" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/label/createlabel.php"><img src="images/create_label.jpg" alt="create label" width="165" height="99" border="0" /></a></td>
					
                  </tr>
                </table>
<?php
				}
				else if(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] <=2 )
				{
?>
				 <table width="50%">
                  <tr valign="top">
                <td align="center"><a href="admin/project_mgm/project_mgm.list.php"><img src="images/projectmanagement.jpg" alt="Quote" width="165" height="99" border="0" /></a>										<a href="admin/project_mgm/project_purchase.list.php"><img src="images/projects.jpg" alt="projects" width="165" height="99" border="0" /></a></td></tr>
                </table>
           
<?php
				}
				else if(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] ==3)
				{?>
					 <table width="50%">
                  <tr valign="top">
                    <td align="center"><a href="inner.php"><img src="images/patterns.jpg" alt="projectmnagement" width="165" height="99" border="0" /></a></td>
                   <td align="center"><a href="admin/project_mgm/project_mgm.list.php"><img src="images/projectmanagement.jpg" alt="Quote" width="165" height="99" border="0" /></a></td>
                   <td align="center"><a href="admin/sample_database/database_sample_list.php"><img src="images/sampleDatabase.jpg" alt="Quote" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/work/work.add.php"><img src="images/newworkorder.jpg" alt="neworkorder" width="165" height="99" border="0" /></a></td>
                     <td align="center"><a href="admin/fill_orders/fill_orders_list.php"><img src="images/fill_orders.jpg" alt="Fill Orders" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/inventory/inventory.php"><img src="images/inventory.jpg" alt="inventory" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="barcode/barcode_generator.php"><img src="images/barcode.jpg" alt="barcode" width="165" height="99" border="0" /></a></td>
                  </tr>
                </table>
                <table width="50%">
                  <tr valign="top">
                    <td align="center"><a href="production/clientlookup.php"><img src="images/clientdatabse.jpg" alt="cllientsatabase" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/project_mgm/project_purchase.list.php"><img src="images/projects.jpg" alt="projects" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/samplerequest/samplerequest.list.php"><img src="images/samplerequest.jpg" alt="sampelrequest" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/projectReport/reportGeneration.php"><img src="images/projectreport.jpg" alt="sampelrequest" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/generate_request/quoteList.php"><img src="images/generateRequest.jpg" alt="generate_request" width="165" height="99" border="0" /></a></td>
                    <td align="center"><a href="admin/label/createlabel.php"><img src="images/create_label.jpg" alt="create label" width="165" height="99" border="0" /></a></td>
				<?php 
			} 
             else if(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] ==4 )
        {
        ?>
            <table width="50%">
                  <tr valign="top">
                  <td align="center"><a href="admin/inventory/inventory.php"><img src="images/inventory.jpg" alt="inventory" width="165" height="99" border="0" /></a></td>           
            </table>
           
<?php
        }


	echo "</center>";
	require('trailer.php');
?>
