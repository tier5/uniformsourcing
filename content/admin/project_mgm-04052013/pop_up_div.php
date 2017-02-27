<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();
  $return_arr['div'] = '	
  <div id="dialog-form" title="Submit By Email" class="popup_block">
	  <div align="center" id="message"></div>
			  <p>All form fields are required.</p>  
			  <fieldset>
			  
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				  <td height="10">&nbsp;</td>
				  <td colspan="3">&nbsp;</td>
				  <td>&nbsp;</td>
				</tr>
				<tr>
					<td width="10" height="30">&nbsp;</td>
					<td colspan="3" class="emailBG"><a style="cursor:hand;cursor:pointer;" onclick="javascript:SendMail();"><img src="'.$mydirectory.'/images/sendButon.jpg" width="68" height="24" alt="send" /></a><a  style="cursor:hand;cursor:pointer;" onclick="javascript:Fade();"><img src="'.$mydirectory.'/images/discardButton.jpg" width="68" height="24" alt="discard" /></a></td>
					<td width="10">&nbsp;</td>
				  </tr>
				  <tr>
					<td width="10" height="30">&nbsp;</td>
					<td width="75" class="emailBG"><label for="email">Email :</label></td>
					<td class="emailBG"><input name="email" type="text" class="emailTxtBox" id="email" value="" size="35px"  /></td>
					<td width="10" class="emailBG">&nbsp;</td>
					<td width="10">&nbsp;</td>
				  </tr>
				  <tr>
					<td height="40">&nbsp;</td>
					<td class="emailBG"> <label for="subject">Subject :</label></td>
					<td class="emailBG"><input  name="subject" type="text" class="emailTxtBox" id="subject" value="Rapid Tract Project Report" size="33px" /></td>
					<td class="emailBG">&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				  
				</table>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td width="10" align="left" valign="top">&nbsp;</td>
					<td width="10" align="left" valign="top" class="emailBG">&nbsp;</td>
					<td align="left" valign="top"><div id="divBody" style="width:750px;height:300px; overflow:scroll;"></div></td>
					<td width="10" align="left" valign="top" class="emailBG">&nbsp;</td>
					<td width="10" align="left" valign="top">&nbsp;</td>
				  </tr>
				  <tr>
					<td align="left" valign="top">&nbsp;</td>
					<td align="left" valign="top" class="emailBG">&nbsp;</td>
					<td align="left" valign="top" class="emailBG">&nbsp;</td>
					<td align="left" valign="top" class="emailBG">&nbsp;</td>
					<td align="left" valign="top">&nbsp;</td>
				  </tr>
				</table>
				<p>
				  
				</p>
			  </fieldset>
		  </div>';
			
  echo json_encode($return_arr);
  return;
?>