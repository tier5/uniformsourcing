<?php 
require('Application.php');
require('../../header.php');
$isEdit = 0;
$error = 0;
if(isset($_GET['iid']))
{
	$isEdit = 1;
}

$query=("SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ");
if(!($result=pg_query($connection,$query))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_vendor[]=$row;
}
pg_free_result($result);
if(isset($_GET['qid']) && $_GET['qid'] != "")
{
	$query=("SELECT \"quoteId\", \"priceAdj\", \"priceType\" ".
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
if($isEdit)
{
	$query=("SELECT \"itemId\", \"quoteId\", \"name\", \"itemNum\", \"vendor\", \"description\", \"price\", \"adjPrice\" ".
		 "FROM \"tbl_item\" ".
		 "WHERE \"itemId\" = '".$_GET['iid']."' ");
	if(!($result=pg_query($connection,$query))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_item=$row;
	}
	pg_free_result($result);
}
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='itemList.php?qid=<?php echo $_GET['qid'];?>';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%">
<tr><td>
<center><table><tr><td>  
    <div align="center" id="message"><?php if(!(isset($_GET['qid'])) || $_GET['qid'] == ""){$error = 1; echo 'You are redirected from unknown location. Please click \'Back\' button to continue.';}?></div>
    </td></tr></table></center>
</td></tr>
<?php 
if(!$error)
{
?>
<tr>
  <td align="left" valign="top"><center>
    <table width="100%">
        <tr>
          <td align="center" valign="top"><font size="5"><?php if($isEdit) echo 'EDIT'; else echo 'ADD';?> ITEM</font><font size="5"><br>
              <br>
            </font>
            <form id="validationForm">
            <table align="center">
              <tr>
                <td><font face="arial"><b>Name </b></font></td>
                <td><input type="text" name="itemName" id="itemName" size="20"  value="<?php echo $data_item['name'];?>"/></td>
              </tr>
              <tr>
                <td><font face="arial"><b>Item # </b></font></td>
                <td><input type="text" name="itemNum" size="20" value="<?php echo $data_item['itemNum'];?>" /></td>
              </tr>
              <tr>
                <td><font face="arial"><b>Vendor</b></font></td>
                <td><select name="vendor">
                <?php
				for($i=0;$i < count($data_vendor); $i++)
				{
					if($data_quote['vendor'] == $data_vendor[$i]['vendorID'])
					echo "<option selected=\"selected\" value=\"{$data_vendor[$i]['vendorID']}\">{$data_vendor[$i]['vendorName']}</option>";
					else
					echo "<option value=\"{$data_vendor[$i]['vendorID']}\">{$data_vendor[$i]['vendorName']}</option>";
				}
				?>
                </select>
                </td>
              </tr>
              <tr>
                <td><font face="arial"><b>Description</b></font></td>
                <td><input type="text" name="description" size="20" value="<?php echo $data_item['description'];?>"/></td>
              </tr>
              <tr>
                <td><font face="arial"><b>Price</b></font></td>
                <td><input type="text" name="price" id="price" size="20" value="<?php echo $data_item['price'];?>"/>
                <input type="hidden" name="priceDisc" id="priceDisc" value="<?php echo $data_item['adjPrice'];?>"/>                
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;<input type="hidden" name="qid"  <?php if($isEdit) echo ' value="'.$data_item['quoteId'].'"'; else echo ' value="'.$_GET['qid'].'"';?> /><?php if($isEdit) echo '<input type="hidden" name="iid"  value="'.$_GET['iid'].'" />'; ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="submit" type="submit"  onmouseover="this.style.cursor = 'pointer';"  <?php if($isEdit) echo ' value="Save"'; else echo ' value="Add"';?> />
                <input name="cancel" type="button"  onmouseover="this.style.cursor = 'pointer';" value="Cancel" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            </form></td>
        </tr>
      </table>
      <input type="hidden" id="priceAdj" value="<?php echo $data_quote['priceAdj'];?>"/>
      <p>
  </center></td>
</tr>
<?php
}//error condition
else
{
	require('../../trailer.php');
	exit;
}
?>
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
		var initPrice = 0;
		var currPrice = document.getElementById('price').value;
		var priceAdj = document.getElementById('priceAdj').value;
		var priceDisc = document.getElementById('priceDisc');
		<?php
		if($isEdit)
		{
			echo 'initPrice = '.$data_item['price'].';';
		}
		?>
		if(initPrice != currPrice)
		{
			<?php
			if($data_quote['priceType'] == '%')
			{			
				echo 'priceDisc.value = Number(currPrice) + (Number(currPrice)*Number(priceAdj)/100);';
			}
			else
			{				
				echo 'priceDisc.value = Number(currPrice) + Number(priceAdj);';				
			}
			?>
			if(priceDisc.value < 0)
				priceDisc.value = 0;
		}
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
			url: "itemSubmit.php",
			data: dataString,
			dataType: "json",
			success: function(data)
			{
				if(data!=null)
				{	
					if(data.error == ""){//$(location).attr('href','reportViewEdit.php?'+dataString);
					$("#message").html("<div class='errorMessage'><strong><?php if($isEdit) echo 'Item Edited successfully'; else echo 'Quote Added successfully'; ?></strong></div>");
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>Error while Adding item</strong></div>");
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
		itemNum: "required",
	price:{number : true}
	},
	messages: {
		itemName: "Please enter item name",
		itemNum: "Please enter item number",
	price: "Price field is Numeric."
	}
	});
	$("#cancel").click(function(){window.location.reload();});
	if($("#date")){$(function() {$("#date").datepicker();});}
});
</script>
<?php
require('../../trailer.php');
?>