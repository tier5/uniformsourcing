<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
require('../../header.php');?>
<?php 
$sql = "SELECT * FROM \"tbl_date_interval_setting\" ";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}


//print_r($data_color_settings);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='inventory.php';" class="button_text" type="submit" name="back" value="Back" /></td>
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
          <td align="center"><font size="5">Inventory setting </font><font size="5"> <br>
              <br>
            </font>
            <table width="95%" border="0" cellspacing="1" cellpadding="1">
				<tbody>

				</tbody>
        </table>
            <table width="95%" border="0" cellspacing="1" cellpadding="1" class="no-arrow rowstyle-alt">
            <thead>
            <tr class="sortable"> 
            <th class="sortable">Color </th>
            <th class="sortable">No Of Days</th>
            <th class="gridHeader">Action</th>
                  </tr>
      </thead>
      <tbody>
      	<?php while ($row = pg_fetch_array($resultProduct)) { ?>
		<tr>
			<td class="grid001"><?php echo $row['color'];?></td>
			<td class="grid001"><?php echo $row['interval'];?></td>
			<td class="grid001">
			<a href="settingViewEdit.php?intervalId=<?php echo $row['Colid'];?>">
			<img src="../../images/reportviewEdit.png" border="0">
			</a>
			</td>
			
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