<?php
require('Application.php');
if(isset($_GET['element_id']))
{
	$sql="Delete from tbl_element_package where element_id = ".$_GET['element_id'];
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed delete_quote: " . pg_last_error($connection));
		exit;
	}
	header('location:element_list.php');
}
require('../../header.php');

$query= 'SELECT pack.*,v."vendorName" FROM tbl_element_package as pack left join "vendor" as v on pack."vendor_id"=v."vendorID"';
include('../../pagination.class.php');
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$datalist[]=$row;
}
pg_free_result($result);

?>
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Element Package</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
<table width="100%"> 
    <tr>
        <td align="left" valign="top"><center>
        <table width="100%">
            <tr>
                <td align="center" valign="top"><font size="5"><br>
                    <table width="80%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>&nbsp;</td>
                            <td align="center"><input type="button" value="Add New Element" onclick="location.href='element_add.php';" /></td>    
                        </tr>
                    </table>
                    <br>
                    </font>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td width="10">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                        	<td class="gridHeader">Package Name</td> 
                            <td class="gridHeader">Element</td> 
                          <td class="gridHeader">Vendor</td>
                            <td class="gridHeader">Style#</td>
                            <td class="gridHeader">Cost</td>
                            <td class="gridHeader">Edit</td>
                            <td class="gridHeader">Delete</td>
                            </tr>

        <?php
			  if(count($datalist) > 0)
			  {
				  for($i = 0; $i < count($datalist); $i++)
				  {
			  ?>
                                <tr>
                                    <td class="grid001"><?php echo $datalist[$i]['package'];?></td>
                                    <td class="grid001"><?php echo $datalist[$i]['element_type'];?></td>
                                    <td class="grid001"><?php echo $datalist[$i]['vendorName'];?></td>
                                    <td class="grid001"><?php echo $datalist[$i]['style'];?></td>
                                    <td class="grid001"><?php echo $datalist[$i]['cost'];?></td>
                                    <td class="grid001"><a href="element_add.php?element_id=<?php echo $datalist[$i]['element_id'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                                     <td class="grid001"><a href="element_list.php?element_id=<?php echo $datalist[$i]['element_id'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
                                </tr>              
        <?php
				  }
				  echo 	'<tr>
			<td width="100%" class="grid001" colspan="7"></td>			
		  </tr>';
			  }
			  else
			  {
				  echo '<tr><td colspan="7" class="grid001">No Quotes found</td><tr>';
			  }
			 ?>       
                        <tr>
                          <td colspan="5">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p>
    </center></td>
</tr>
</table>
<?php
require('../../trailer.php');
?>