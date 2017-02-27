<?php 
require('Application.php');
require('../../header.php');
$isEdit = 0;
$id = 0;
if(isset($_GET['id']))
{
	$isEdit = 1;
	$id = $_GET['id'];
}
if($isEdit)
{
	$query="Select * from tbl_tax where tax_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_tax=$row;
	}
	pg_free_result($result);
}

?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='tax_list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center><table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table></center>

<table width="100%">    
        <tr>
          <td align="center" valign="top"><font size="5"><?php if($isEdit) echo 'EDIT'; else echo 'ADD';?> TAX</font><font size="5"><br>
              <br>
            </font>
            <form id="validationForm" action="" method="post">          
            <table align="center">
             <tr>
                <td align="right"><font face="arial"><b>Name </b></font></td>
                <td align="left"><input type="text" name="tax_name" id="tax_name" size="20" value="<?php echo $data_tax['tax_name'];?>" /></td>                
              </tr>
              <tr>
                <td align="right"><font face="arial"><b>Amount </b></font></td>
                <td align="left"><input type="text" name="amount" value="<?php echo $data_tax['tax_amount'];?>"/></td>                
              </tr>
              <tr>
                <td align="right"><font face="arial"><b>Status </b></font></td>
                <td align="left"><select name="status">
               <?php if($data_tax['status'] == 0 && $data_tax['status']!="") { ?> 
               		<option value="1" >Enable</option>
                  <option value="0" selected="selected">Disable</option>
                 <?php } else { ?>
                 
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                 <?php } ?>
                </select></td>                
              </tr>
    
              <tr>
                <td>&nbsp;</td>
                <td>
                <input type="hidden" id="id" name="id" value="<?php echo $id;?>" />
                <input name="submit" type="submit" onmouseover="this.style.cursor = 'pointer';" <?php if($isEdit) echo ' value="Save"'; else echo ' value="Add"';?> />
                <input name="cancel" id="cancel" type="reset"  onmouseover="this.style.cursor = 'pointer';" value="Cancel" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            </form>
            </td>
        </tr>
      </table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript"> 
$(function(){$("#validationForm").submit(function(){
	if($("#validationForm").valid())
	{
		dataString = $("#validationForm").serialize();
		<?php
		if($isEdit)
			echo "dataString += '&submit=Save';";
		else
			echo "dataString += '&submit=Add';";
		?>
		$.ajax
		({
			type: "POST",
			url: "tax_submit.php",
			data: dataString,
			dataType: "json",
			success: function(data)
			{
				if(data!=null)
				{	
					if(data.error == ""){
					$("#message").html("<div class='successMessage'><strong><?php if($isEdit) echo 'Tax Edited successfully'; else echo 'Tax Added successfully'; ?></strong></div>");
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
					}
				}
			} 
		});	
	}
	return false;
});
});
</script>
<script type="text/javascript">
$().ready(function() {
	$("#validationForm").validate({
	rules: {
		tax_name: "required",
	amount:{required : true,number : true}
	},
	messages: {
		tax_name: "Please enter name",
	amount: "Price field accept only Numeric."
	}
	});
	//$("#cancel").click(function(){window.location.reload();});
});
</script>
<?php
require('../../trailer.php');
?>