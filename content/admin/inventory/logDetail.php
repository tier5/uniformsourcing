<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
require('../../header.php');?>
<?php 

$sql ='select * from "tbl_log_updates" where "Logid"='.$_GET['LogId'];
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_storage = $row;

}
pg_free_result($resultemp);
			$empsql='select * from "employeeDB" where "employeeID" ='.$data_storage['createdBy'].' LIMIT 1';
			if(!($resultemp=pg_query($connection,$empsql))){
			
			}
			else{
			
			}
			$rowemp = pg_fetch_row($resultemp);
			$oldemp=$rowemp;
			pg_free_result($resultemp);
if($data_storage['present']=="inventory"){
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
<table width="100%">
        <tbody><tr>
          <td align="left" valign="top"><center>
            <table width="100%">
                <tbody><tr>
                  <td align="center" valign="top"><font size="5">Check Log Details</font><font size="5"> 
                  <br>
                      <br>
                      </font>
                       <center>
                        <table><tbody><tr><td>
                        <div align="center" id="message"></div>
                        </td></tr></tbody></table>
                        </center>
                      <form name="validationForm" id="validationForm" method="post" action="">
                        <table width="80%" border="0">
                          <tbody><tr>
                            <td align="center"><p></p></td>
                            <td align="center">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                            <td align="right" valign="top">&nbsp;</td>
                          </tr>
                        </tbody></table>
                        <table width="98%" border="0" cellspacing="1" cellpadding="1">
                          <tbody>
                          	<tr>
                            <td width="355" height="25" align="right" valign="top">Date: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<?php echo date('m/d/Y h:i:s',$data_storage['updatedDate']);?>
                           	</td>
                          	</tr>
                            <tr>
                            
                            <td width="355" height="25" align="right" valign="top">Updated By: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<?php echo $oldemp['1']." ".$oldemp['2'];?>
                           	</td>
                          	</tr>
                            <tr>
                            <td width="355" height="25" align="right" valign="top">Previous: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<table>
                            		<tr>
                            			<td>Scale1</td>
                            			<td>Scale2</td>
                            			<td>Value</td>
                            			<td>Unit</td>
                            		</tr>
                            		<?php $data=json_decode($data_storage['previous']);

                            	foreach ($data as $key => $value) {
                            		
                            		foreach ($value[0] as $key => $previous) {
                            			//print_r($previous);
                            			$sqlinvx='select * from "tbl_invStorage" where "invId" ='.$previous->inventoryId.'  LIMIT 1';
										if(!($resultoldinvx=pg_query($connection,$sqlinvx))){
											//echo "no";
										}
										else{
											//echo "yes";
										}
								        $rowoldinvx = pg_fetch_row($resultoldinvx);
								        $oldinvx=$rowoldinvx;
								        pg_free_result($resultoldinvx);
								        //print_r($oldinvx);
                            			?>
                            			
                            			<tr>
                            			<td><?php echo $previous->mainSize;?></td>
                            			<td><?php echo $previous->rowSize;?></td>
                            			<td><?php echo $previous->quantity;?></td>
                            			<td><?php echo $oldinvx['26']?></td>
                            			</tr>
                            			<?php
                            		}
                            	}
                            	?>
                            		
                            	</table>
                            	
                            </td>
                          	</tr>
                          	<tr>
                            <td width="355" height="25" align="right" valign="top">Present: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<table>
                            		<tr>
                            			<td>Scale1</td>
                            			<td>Scale2</td>
                            			<td>Value</td>
                            			<td>Unit</td>
                            		</tr>
                            	<?php $data=json_decode($data_storage['previous']);

                            	foreach ($data as $key => $valuex) {
                            		
                            		foreach ($valuex[1] as $key => $present) {
                            			$sqlinvx='select * from "tbl_invStorage" where "invId" ='.$present->inventoryId.'  LIMIT 1';
										if(!($resultoldinvx=pg_query($connection,$sqlinvx))){
											//echo "no";
										}
										else{
											//echo "yes";
										}
								        $rowoldinvx = pg_fetch_row($resultoldinvx);
								        $oldinvx=$rowoldinvx;
								        pg_free_result($resultoldinvx);
                            			?>
                            			
                            			<tr>
                            			<td><?php echo $present->mainSize;?></td>
                            			<td><?php echo $present->rowSize;?></td>
                            			<td><?php echo $present->quantity;?></td>
                            			<td><?php echo $oldinvx['26']?></td>
                            			</tr>
                            			<?php
                            		}
                            	}
                            	?>
                            	</table>
                            </td>
                          	</tr>
                          
                        
                          
                        </tbody></table>
                      </form></td>
                </tr>
              </tbody></table>
              <p>
          </p></center></td>
        </tr>
      </tbody></table>
      <?php }if($data_storage['present']=="style"){?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
</table>
<table width="100%">
        <tbody><tr>
          <td align="left" valign="top"><center>
            <table width="100%">
                <tbody><tr>
                  <td align="center" valign="top"><font size="5">Check Log Details</font><font size="5"> 
                  <br>
                      <br>
                      </font>
                       <center>
                        <table><tbody><tr><td>
                        <div align="center" id="message"></div>
                        </td></tr></tbody></table>
                        </center>
                      <form name="validationForm" id="validationForm" method="post" action="">
                        <table width="80%" border="0">
                          <tbody><tr>
                            <td align="center"><p></p></td>
                            <td align="center">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                            <td align="right" valign="top">&nbsp;</td>
                          </tr>
                        </tbody></table>
                        <table width="98%" border="0" cellspacing="1" cellpadding="1">
                          <tbody>
                          	<tr>
                            <td width="355" height="25" align="right" valign="top">Date: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<?php echo date('m/d/Y h:i:s',$data_storage['updatedDate']);?>
                           	</td>
                          	</tr>
                          	<tr>
                            
                            <td width="355" height="25" align="right" valign="top">Updated By: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<?php echo $oldemp['1']." ".$oldemp['2'];?>
                           	</td>
                          	</tr>
                          	<?php $data=json_decode($data_storage['previous']);

                            	foreach ($data as $key => $valuex) {
                            		
                            		
                            		?>
                            <tr>
                            <td width="355" height="25" align="right" valign="top"><?php echo $key;?>: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            	<?php if($key=="style"){?>
                            	<?php foreach ($valuex as $keyeach => $each) { ?>
                            		<TABLE border=".5">
                            			<tr><th><?php echo $keyeach; ?></th></tr>
                            			<?php foreach ($each as $keyno => $eachvalue) { ?>
                            				<tr><td><?php echo $eachvalue; ?></td></tr>
                            			<?php }?>
                            			
                            		</TABLE>
                            	<?php } ?>
                            		
                            	<?php }if($key=="color"){ 
                            		foreach ($valuex as $keycol => $colvalue) {
                            			
                            		
                            		?>
                            		<TABLE border=".5">
                            			<?php foreach ($colvalue as $keyec => $ecvalue) { ?>

                            			<tr><td><?php echo $ecvalue->name;?></td>
                            			<td><img id="bar_img" width="50" height="50" src="../../uploadFiles/inventory/images/thumbs/<?php echo $ecvalue->image; ?>" onclick="PopEx(this, null,  null, 0, 0, 50,'PopBoxImageLarge');" pbsrc="../../uploadFiles/inventory/images/<?php echo $ecvalue->image;?>"><?php echo $ecvalue->image;?></td></tr>
                            			<?php }?>

                            			
                            			
                            		</TABLE>
                            	<?php }  } ?>
                            	
                            </td>
                          	</tr>
                          	<?php } ?>
                          	
                          
                          
                          
                        
                          
                        </tbody></table>
                      </form></td>
                </tr>
              </tbody></table>
              <p>
          </p></center></td>
        </tr>
      </tbody></table>
      <?php }?>
<?php  require('../../trailer.php');
?>