<?php require('Application.php');
	
	if(isset($_GET['type']))
	{		
		if($_GET['type']=="d")
		{
			$sql = 'update "tbl_invScaleName" SET "isActive"=0 where "scaleId"='.$_GET['id'];
			if(!($result=pg_query($connection,$sql))){
				print("Failed scaleList1: " . pg_last_error($connection));
				exit;
			}
			pg_free_result($result);
			header("Location: sizeScaleList.php");
		}
	}
	require('../../jsonwrapper/jsonwrapper.php');
	require('../../header.php');	
	
	$sql = 'select * from "tbl_invScaleName" where "isActive"=1 order by "scaleId" desc';
	if(!($result=pg_query($connection,$sql))){
		print("Failed scaleList1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$nameData[]=$row;
	}
	pg_free_result($result);
	?>    
    <table width="100%">
                <tr>
                  <td align="center" valign="top"><font size="5">List Size Scale </font><font size="5">   <br>
                      <br>                      
                    </font>
                     <table width="95%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td align="left"><input type="button" value="Back" onclick="location.href='../index.php'" /></td>
                          <td>&nbsp;</td>
                          <td align="right"><input type="button" value="Add Size Scale" onclick="javascript:window.location='sizeScaleAdd.php'" /></td>
                        </tr>
                        <tr>
                          <td align="left">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td align="right">&nbsp;</td>
                        </tr>
                      </table>                  
                    <table width="95%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td class="gridHeader">Name </td>
                        <td class="gridHeader">Row Name </td>
                        <td class="gridHeader">Column Name  </td>
                        <td class="gridHeader">Edit</td>
                        <td class="gridHeader">Delete</td>
                      </tr>
                      <?php
					  for($i=0; $i<count($nameData);$i++)
					  { ?>
                      
                      <tr>
                        <td class="grid001"><?php echo $nameData[$i]['scaleName'];?></td>
                        <td class="grid001"><?php echo $nameData[$i]['opt1Name'];?></td>
                        <td class="grid001"><?php echo $nameData[$i]['opt2Name'];?></td>
                        <td class="grid001"><a href="./sizeScaleAdd.php?type=e&id=<?php echo $nameData[$i]['scaleId'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" alt="edit" width="24" height="24" /></a></td>
                        <td class="grid001"><a href="sizeScaleDelete.php?type=all&id=<?php echo $nameData[$i]['scaleId'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" alt="deactivate" width="24" height="24"/></a></td>
                      </tr>
 <?php
					 }
 ?>
                  </table></td>
                </tr>
              </table>
 <?php  require('../../trailer.php');
?>