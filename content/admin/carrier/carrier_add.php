<?php
require('Application.php');
require('../../header.php');
if(isset($_GET['id']))
{
    $carrier_id = $_GET['id'];
	$sql ="select * from tbl_carriers where status =1 and carrier_id = '$carrier_id'";
	if(!($result=pg_query($connection,$sql)))
	{
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result)){
		$data_product=$row;
	}
	pg_free_result($result);
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
    <input type="button" value="Back" onclick="location.href ='carrier_list.php'" /></td>
    <td align="right">&nbsp;</td>
  </tr>
</table>
<br />
<form  id="validation" name="validation" action="" method="post">
<center>
<table width="60%"><tr><td> 
<div id="message" align="center"></div>
</td>
</tr>
</table>
</center>
  <table width="598" border="0" align="center">
    <tr align="right">
    

      <td height="46" colspan="2" align="center"><font face="arial">
<blockquote>
<font face="arial" size="+2"><b><center>Carriers</center></b></font>
</blockquote>
</font></td>
    </tr>
    <tr>
      <td width="272" align="right">Name:</td>
      <td width="316" align="left">
        <input type="text" name="cname" id="cname" value="<?php echo stripslashes($data_product['carrier_name']);?>"><input type="hidden" id="carrier_id0" name="carrier_id" value="<?php echo $carrier_id; ?>"/>
      </td>
    </tr>
    <tr>
      <td align="right">Website link:</td>
      <td align="left"><label>
        <input type="text" name="cweblink" id="cweblink" value="<?php echo stripslashes($data_product['weblink']);?>">
      </label></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="left"><input name="add" type="submit" id="button" value="Add">
          <input name="reset" type="reset" id="button2" value="Cancel"></td>
    </tr>
  </table>
</form>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script src="project.js" type="text/javascript"></script>
<script type="text/javascript">
$().ready(function() {
	// validate form on keyup and submit
	$("#validation").validate({
	rules: {
			cname: "required"
		},
		messages: {
			cname: "Please enter carrier name"
			}
	});
});
</script>
<script type="text/javascript">
$(function(){$("#validation").submit(function(){
if($("#validation").valid()){
	//var cname = document.getElementById('cname');
	datastring = $("#validation").serialize();
	$.ajax({
		   type: "POST",
		   url: "carrier_submit.php",
		   data: datastring,
		   dataType: "json",
		   success: function(data){
			   if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Carrier Updated...</strong></div>");
						/* document.getElementById('cname').value = "";
						 document.getElementById('cweblink').value = "";*/
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
	}
			return false;
	});
});


</script>
<?php
require('../../trailer.php');
?>