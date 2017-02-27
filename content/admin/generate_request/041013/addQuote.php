<?php 
require('Application.php');
require('../../header.php');
$isEdit = 0;
if(isset($_GET['qid']))
{
	$isEdit = 1;
}

$query=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result=pg_query($connection,$query))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_client[]=$row;
}
pg_free_result($result);
if($isEdit)
{
	$query=("SELECT \"quoteId\", \"name\", \"client\", \"date\", \"priceAdj\", \"priceType\" ".
		 "FROM \"tbl_quote\" ".
		 "WHERE \"quoteId\" = '".$_GET['qid']."' ");
	if(!($result=pg_query($connection,$query))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_quote=$row;
	}
	pg_free_result($result);
}
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='quoteList.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="100%">
<tr><td>
<center><table><tr><td>  
    <div align="center" id="message"></div>
    </td></tr></table></center>
</td></tr>
<tr>
  <td align="left" valign="top"><center>  
    <table width="100%">    
        <tr>
          <td align="center" valign="top"><font size="5"><?php if($isEdit) echo 'EDIT'; else echo 'ADD';?> QUOTE</font><font size="5"><br>
              <br>
            </font>
            <form id="validationForm">          
            <table align="center">
             <tr>
                <td align="right"><font face="arial"><b>Name </b></font></td>
                <td align="left"><input type="text" name="itemName" id="itemName" size="20" value="<?php echo $data_quote['name'];?>" /></td>                
              </tr>
              <tr>
                <td align="right"><font face="arial"><b>Client </b></font></td>
                <td align="left"><select name="client">
                <?php
				for($i=0;$i < count($data_client); $i++)
				{
					if($data_quote['client'] == $data_client[$i]['ID'])
					echo "<option selected=\"selected\" value=\"{$data_client[$i]['ID']}\">{$data_client[$i]['client']}</option>";
					else
					echo "<option value=\"{$data_client[$i]['ID']}\">{$data_client[$i]['client']}</option>";
				}
				?>
                </select>
                </td>                
              </tr>
              <tr>
                <td align="right"><font face="arial"><b>Date </b></font></td>
                <td align="left"><input type="text" name="date" id="date" size="20" <?php if($isEdit) echo "value=\"".$data_quote['date']."\""; else echo "value=\"".date('m/d/Y',date(U))."\""; ?>/></td>                
              </tr>
              <tr>
                <td align="right"><font face="arial"><b>Price Adjustment </b></font></td>
                <td align="left"><input type="text" name="priceAdj"  size="13" <?php if($isEdit) echo 'value="'.$data_quote['priceAdj'].'"'; else echo 'value="0"';?> />
                <select name="adjType" >
                <?php 
				if($isEdit){ 
				if($data_quote['priceType'] == '%')
					echo '<option selected=selected value="%">%</option>';
				else 
					echo '<option value="%">%</option>';
				if($data_quote['priceType'] == '$')
					echo '<option selected=selected value="$">$</option>'; 
				else 
					echo '<option value="$">$</option>';
				}
				else {echo '<option value="%">%</option>';echo '<option value="$">$</option>';}?>
                </select>
                </td>                
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;<?php if($isEdit) echo '<input type="hidden" name="qid"  value="'.$_GET['qid'].'" />'; ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="submit" type="submit" onmouseover="this.style.cursor = 'pointer';" <?php if($isEdit) echo ' value="Save"'; else echo ' value="Add"';?> />
                <input name="cancel" id="cancel" type="button"  onmouseover="this.style.cursor = 'pointer';" value="Cancel" /></td>
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
      <p>
  </center></td>
</tr>
</table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript">
$('#other').click(function() {
  $('#validationForm').submit();
});
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
			url: "quoteSubmit.php",
			data: dataString,
			dataType: "json",
			success: function(data)
			{
				if(data!=null)
				{	
					if(data.error == ""){//$(location).attr('href','reportViewEdit.php?'+dataString);
					$("#message").html("<div class='errorMessage'><strong><?php if($isEdit) echo 'Quote Edited successfully'; else echo 'Quote Added successfully'; ?></strong></div>");
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>Error while Adding quote</strong></div>");
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
		itemName: "required",
	priceAdj:{number : true}
	},
	messages: {
		itemName: "Please enter item name",
	priceAdj: "Price field accept only Numeric."
	}
	});
	$("#cancel").click(function(){window.location.reload();});
	if($("#date")){$(function() {$("#date").datepicker();});}
});
</script>
<?php
require('../../trailer.php');
?>