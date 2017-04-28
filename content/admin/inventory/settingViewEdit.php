<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
require('../../header.php');?>
<?php 
if($_POST){
	
	$query = "UPDATE \"tbl_date_interval_setting\" SET ";
	$query .="\"color\" = '".$_POST['color']."' ";
	$query .=",\"interval\" = '".$_POST['interval']."' ";
	
	$query .="  where \"Colid\"='".$_POST['Colid']."' ";
	if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
}
$sql ='select * from "tbl_date_interval_setting" where "Colid"='.$_GET['intervalId'];
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_storage = $row;
}


?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10" align="left">&nbsp;</td>
          <td align="left"><input id="back" onclick="javascript:location.href='setting.php';" class="button_text" type="button" name="back" value="Back" /></td>
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
                  <td align="center" valign="top"><font size="5">EDIT Interval Setting</font><font size="5"> 
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
                            <td width="355" height="25" align="right" valign="top">Color Name: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            <input name="color" id="color" readonly="readonly" type="text" class="textBox" value="<?php echo $data_storage['color'];?>"></td>
                          	</tr>
                            <tr>
                            <td width="355" height="25" align="right" valign="top">No Interval Days: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                            <input name="interval" id="interval" r type="text" class="textBox" value="<?php echo $data_storage['interval'];?>"></td>
                          	</tr>
                          
                          <input name="Colid" id="Colid" r type="hidden" class="textBox" value="<?php echo $data_storage['Colid'];?>">
                          
                          
                        
                          <tr>
                            <td height="25" align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="left">
                            
                            <input id="isEdit" type="hidden" value="1">
                            <input name="submit" type="submit" onmouseover="this.style.cursor = 'pointer';" value="Edit Style"> 
                            <input name="cancel" onclick="javascript:location.href='setting.php';" type="button" onmouseover="this.style.cursor = 'pointer';" value="Cancel">
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
<?php  require('../../trailer.php');
?>