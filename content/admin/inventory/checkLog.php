<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
require('../../header.php');?>




<?php 
$sql = 'select * from "tbl_log_updates" where "styleId" ='.$_GET['ID'].'order by "updatedDate" desc';
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}


//print_r($data_color_settings);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='reports.php';" class="button_text" type="submit" name="back" value="Back" /></td>
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
          <td align="center"><font size="5">Change Log </font><font size="5"> <br>
              <br>
            </font>
            <table width="95%" border="0" cellspacing="1" cellpadding="1">
				<tbody>

				</tbody>
        </table>
            <table width="95%" border="0" cellspacing="1" cellpadding="1" class="no-arrow rowstyle-alt">
            <thead>
            <tr class="sortable"> 
            <th class="sortable">Date Time </th>
            <th class="sortable">Changed By</th>
            <th class="gridHeader">Type</th>
            <th class="gridHeader">Action</th>
                  </tr>
      </thead>
      <tbody>
      	<?php while ($row = pg_fetch_array($resultProduct)) { 
      		pg_free_result($resultemp);
			$empsql='select * from "employeeDB" where "employeeID" ='.$row['createdBy'].' LIMIT 1';
			if(!($resultemp=pg_query($connection,$empsql))){
			
			}
			else{
			
			}
			$rowemp = pg_fetch_row($resultemp);
			$oldemp=$rowemp;
			pg_free_result($resultemp);

      		?>
		<tr>
			<td class="grid001"><?php echo date('m/d/Y H:i:s',$row['updatedDate']);?></td>
			<td class="grid001"><?php echo $oldemp['1']." ".$oldemp['2'];?></td>
			<td class="grid001"><?php echo $row['present'];?>
			</td>
			<td><a href="logDetail.php?LogId=<?php echo $row['Logid'];?>">
			<img src="<?php echo $mydirectory;?>/images/reportviewEdit.png" border="0">
			</a></td>
			
		</tr>
		<?php } ?>
		</tbody>
		<tbody>                     
        </tbody>
    	</table>
    	</td>
        </tr>
        </tbody></table>
              <p>
          </p></center></td>
        </tr>
      </tbody></table>
     

<?php  require('../../trailer.php');
?>