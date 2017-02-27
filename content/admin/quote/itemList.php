<?php 
require('Application.php');
require('../../header.php');
$error = 0;
if(isset($_GET['qid']) && $_GET['qid'] != "")
{
$query=("SELECT \"itemId\", \"quoteId\", \"name\", \"itemNum\", v.\"vendorName\", \"description\", \"price\", \"adjPrice\", \"disableAutoUpdate\" ".
		 "FROM \"tbl_item\" i inner join \"vendor\" v on i.\"vendor\"=v.\"vendorID\"".
		 "WHERE  \"quoteId\" = '".$_GET['qid']."'".
		 "ORDER BY \"createdDate\" DESC ");

include('../../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($result);
pg_free_result($result);
if($items > 0) {
	$p = new pagination;
	$p->items($items);
	$p->limit(10); // Limit entries per page
	//$uri=strstr($_SERVER['REQUEST_URI'], '&paging', true);
	//die($_SERVER['REQUEST_URI']);
	$uri= substr($_SERVER['REQUEST_URI'], 0,strpos($_SERVER['REQUEST_URI'], '&paging'));
	if(!$uri) {
		$uri=$_SERVER['REQUEST_URI'].$search_uri;
	}
	$p->target($uri);
	$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
	$p->calculate(); // Calculates what to show
	$p->parameterName('paging');
	$p->adjacents(1); //No. of page away from the current page
	
	if(!isset($_GET['paging'])) {
	$p->page = 1;
	} else {
	$p->page = $_GET['paging'];
	}
	//Query for limit paging
	$limit = "LIMIT " . $p->limit." OFFSET ".($p->page - 1) * $p->limit;
}
$query = $query. " ". $limit;
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_item[]=$row;
}
pg_free_result($result);
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
else
{
$error = 1;
echo 'You are redirected from unknown location. Please click \'Back\' button to continue.';
exit;
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
    <div align="center" id="message"><?php if(!(isset($_GET['qid'])) || $_GET['qid'] == ""){$error = 1; echo 'You are redirected from unknown location. Please click \'Back\' button to continue.';}?></div>
    </td></tr></table></center>
</td></tr>
<tr>
  <td align="left" valign="top"><center>
    <table width="100%">
        <tr>
          <td align="center" valign="top"><font size="5">QUOTES<br>
              <br>
            </font>
              <table width="80%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td height="35">&nbsp;</td>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="grid001">Price Adjustment :</td>
                <td class="grid001"><input name="priceAdj" readonly="readonly" onclick="javascript:popOpen('priceAdj-form','300','150');" type="text" value="<?php echo $data_quote['priceAdj'].' '.$data_quote['priceType'];?>"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="5" align="right"><input id="saveQuote" type="button"  onMouseOver="this.style.cursor = 'pointer';" value="Save Quote">
                  <input name="button2" type="button"  onMouseOver="this.style.cursor = 'pointer';" onclick="javascript:location.href='addItem.php?qid=<?php echo $_GET['qid']?>';" value="Add Item">
                  <input name="button4" type="button"  onmouseover="this.style.cursor = 'pointer';" onclick="javascript:popOpen('email-form', '800');" value="Email quote" /></td>
                </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            <form id="itemListfrm">
        <table width="80%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td  width="100" class="gridHeader">Name</td>
                <td  width="100" class="gridHeader">Item #</td>
                <td  width="200"class="gridHeader">Vendor</td>
                <td width="200" class="gridHeader">Description</td>
                <td class="gridHeader">Price </td>
                <td  class="gridHeader">Disable Price Adj.</td>
              </tr>
			<?php
			  if(count($data_item) > 0)
			  {
				  for($i = 0; $i < count($data_item); $i++)
				  {
			  ?>
              <tr>
                <td class="gridWhite"><a href="addItem.php?qid=<?php echo $data_quote['quoteId'];?>&iid=<?php echo $data_item[$i]['itemId'];?>"><?php echo $data_item[$i]['name'];?></a></td>
                <td class="gridWhite"><?php echo $data_item[$i]['itemNum'];?></td>
                <td class="gridWhite"><?php echo $data_item[$i]['vendorName'];?></td>
                <td class="gridWhite"><?php echo $data_item[$i]['description'];?></td>
                <td class="grid001">
                <input type="text" class="txBxGreyd" readonly="readonly" value="$"/>
                <input class="txBxGrey" type="text" name="price[]" value="<?php echo $data_item[$i]['adjPrice'];?>"/>
                <input type="hidden" name="itemId[]" value="<?php echo $data_item[$i]['itemId'];?>"/>
                <input type="hidden" name="acutalPrice[]" value="<?php echo $data_item[$i]['price'];?>"/>
                </td>
                <td class="grid001" ><center><input name="disable_<?php echo $data_item[$i]['itemId'];?>" type="checkbox" value="1" <?php if($data_item[$i]['disableAutoUpdate'] != 0) echo 'checked="checked"';?>/></center></td>
              </tr>              
              <?php
				  }
				  echo 	'<tr>
			<td width="100%" class="grid001" colspan="6">'.$p->show().'</td>			
		  </tr>';
			  }
			  else
			  {
				  echo '<tr><td colspan="6" class="grid001">No Items found</td><tr>';
			  }
			 ?>  
              <tr>
                <td colspan="6">&nbsp;</td>
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
<div id="priceAdj-form" title="Save price information" class="popup_block">
    <div align="center" id="message"></div>
			<p>All form fields are required.</p>  
			<fieldset>
            <form id="pricesubmitFrm">
           	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10">Price Adjustment: </td>
                <td>&nbsp;</td>
                <td><input name="priceAdj" id="priceAdj" type="text" value="<?php echo $data_quote['priceAdj'];?>" size="10"/><select style="height:25" name="adjType" >
                <?php 
				if($data_quote['priceType'] == '%')
					echo '<option selected=selected value="%">%</option>';
				else 
					echo '<option value="%">%</option>';
				if($data_quote['priceType'] == '$')
					echo '<option selected=selected value="$">$</option>'; 
				else 
					echo '<option value="$">$</option>';
				?>
                </select></td>
              </tr>
              <tr>
              	<td colspan="3" align="center"><br/><input type="hidden" name="qid" id="qid"  value="<?php echo $_GET['qid'];?>" /><input type="submit" name="quoteSubmit" id="priceSub" value="Save"/></td>
              </tr></table>
          </form>
              </fieldset>
</div>
<?php
$mailBody='<table width="100%">'.
        '<tr>'.
          '<td align="center" valign="top"><font size="5">QUOTES<br><br></font>'.            
              '<table width="80%" border="0" cellspacing="1" cellpadding="1">'.
              '<tr><td height="35">&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td width="10">&nbsp;</td>'.
                '<td width="120" >&nbsp;</td>'.
                '<td width="100" >&nbsp;</td>'.
              '</tr><tr>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#CCCCCC" height="25">Price Adjustment :</td>'.
                '<td><table width="100%" bgcolor="#CCCCCC" cellpadding="3"><tr><td bgcolor="#FFFFFF" height="25">'.$data_quote['priceAdj'].' '.$data_quote['priceType'].'</td></tr></table></td>'.
              '</tr>'.
              '<tr>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td></td>'.
              '</tr>'.
              '<tr>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
              '</tr>'.
              '<tr>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
                '<td>&nbsp;</td>'.
              '</tr>'.
            '</table>'.
        '<table width="80%" border="0" cellspacing="1" cellpadding="1">'.
              '<tr>'.
                '<td style="font-size:12px;color:#FFFFFF;" bgcolor="#333333" height="25">Name</td>'.
                '<td style="font-size:12px;color:#FFFFFF;" bgcolor="#333333" height="25">Item #</td>'.
                '<td style="font-size:12px;color:#FFFFFF;" bgcolor="#333333" height="25">Vendor</td>'.
                '<td style="font-size:12px;color:#FFFFFF;" bgcolor="#333333" height="25">Description</td>'.
                '<td style="font-size:12px;color:#FFFFFF;" bgcolor="#333333" height="25">Price </td>'.
              '</tr>';
			  if(count($data_item) > 0)
			  {
				  for($i = 0; $i < count($data_item); $i++)
				  {			 
              $mailBody .='<tr>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#FFFFFF" height="25">'.$data_item[$i]['name'].'</td>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#FFFFFF" height="25">'.$data_item[$i]['itemNum'].'</td>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#FFFFFF" height="25">'.$data_item[$i]['vendorName'].'</td>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#FFFFFF" height="25">'.$data_item[$i]['description'].'</td>'.
                '<td style="font-size:12px;color:#000000;" bgcolor="#CCCCCC" height="25">$&nbsp;'.$data_item[$i]['adjPrice'].'</td>'.
              '</tr>';
				  }
			  }
			$mailBody .='<tr>'.
			'<td style="font-size:12px;color:#000000;" bgcolor="#CCCCCC" height="25" width="100%" colspan="5">&nbsp;</td>'.
		  '</tr></table>'.
            '</td>'.
        '</tr>'.
      '</table>';
?>              
<div id="email-form" title="Submit By Email" class="popup_block">
    <div align="center" id="emailMSG"></div>			
            <p><small>Add mulitple email IDs seperated with '<b>,</b>' OR '<b>;</b>'. </small></p>  
			<fieldset>
           	
           	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
           	      <td width="10" height="30">&nbsp;</td>
           	      <td colspan="3" class="emailBG"><a style="cursor:hand;cursor:pointer;" onclick="javascript:SendMail();"><img src="<?php echo $mydirectory;?>/images/sendButon.jpg" width="68" height="24" alt="send" /></a><a  style="cursor:hand;cursor:pointer;" onclick="javascript:Fade();"><img src="<?php echo $mydirectory;?>/images/discardButton.jpg" width="68" height="24" alt="discard" /></a></td>
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
           	      <td class="emailBG"><input  name="subject" type="text" class="emailTxtBox" id="subject" value="Uniform sourcing Quote" size="33px" /></td>
           	      <td class="emailBG">&nbsp;</td>
           	      <td>&nbsp;</td>
       	        </tr>
           	    
       	      </table>
           	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
           	    <tr>
           	      <td width="10" align="left" valign="top">&nbsp;</td>
           	      <td width="10" align="left" valign="top" class="emailBG">&nbsp;</td>
           	      <td align="left" valign="top"><div id="divBody" style="width:750px;height:300px; overflow:scroll;"><?php echo $mailBody;?></div></td>
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
		</div> 
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
 <script type="text/javascript">
function SendMail()
{
  var email = '';
  var subject = '';
  var mailBody = '';
  email = document.getElementById('email').value;
  subject = document.getElementById('subject').value;
  mailBody = document.getElementById('divBody').innerHTML;
  dataString = "email="+email+"&subject="+subject;
  $.ajax({
		 type: "POST",
		 url: "email.php",
		 data: dataString,
		 dataType: "json",
		 success: function(data) 
		 {	
			 if(data!=null)
			 {	
				 if(data.name || data.error)
				 {
					 $("#emailMSG").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				 } 
				 else if(data.email != "")
				 {
					 $("#emailMSG").html("<div class='errorMessage'><strong>Email were not send to following email Id's "+ data.email +" </strong></div>"); 
				 }
				 else 
				 {
					 $("#emailMSG").html("<div class='successMessage'><strong>Email Send Successfully.</strong></div>");
				 }	
			 }
			 else
			 {
				 $("#emailMSG").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>"); 
			 }
		 }
		 });
  return false;
}

function popOpen(popID, popWidth, popHeight)
{
	//var popID = 'email-form'; //Get Popup Name	
	//popWidth = '800'; 
	if(popHeight != null && popHeight != "")
	{
		$('#' + popID).fadeIn().css({'height': Number( popHeight )});
	}
	$('#' + popID).fadeIn().css({ 'width': Number( popWidth )}).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
	   
	//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;
	//Apply Margin to Popup
	$('#' + popID).css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});	
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 		
	}
	//Close Popups and Fade Layer
	$('span.close, #fade, #cancel').live('click', function() { //When clicking on the close or fade layer...
	Fade();
	return false;
});
function Fade()
{
	$("#message").html('');
	document.getElementById('email').value="";
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
}
$(function(){$("#pricesubmitFrm").submit(function(){		
		dataString = $("#itemListfrm").serialize();
		dataString += '&quoteSubmit=Save&'+$("#pricesubmitFrm").serialize();
		$.ajax
		({
			type: "POST",
			url: "itemListSubmit.php",
			data: dataString,
			dataType: "json",
			success: function(data)
			{
				if(data!=null)
				{	
					if(data.error == ""){//$(location).attr('href','reportViewEdit.php?'+dataString);
					$("#message").html("<div class='errorMessage'><strong>price Adjustment updated successfully</strong></div>");window.location.reload();
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>Error while Adding quote</strong></div>");
					}
				}
			} 
		});		
	return false;
});
});
$(function(){$("#saveQuote").click(function(){
		dataString = $("#itemListfrm").serialize();
		dataString += '&itemSubmit=Save';
		$.ajax
		({
			type: "POST",
			url: "itemListSubmit.php",
			data: dataString,
			dataType: "json",
			success: function(data)
			{
				if(data!=null)
				{	
					if(data.error == ""){//$(location).attr('href','reportViewEdit.php?'+dataString);
					$("#message").html("<div class='errorMessage'><strong>price Adjustment updated successfully</strong></div>");
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>Error while Adding quote</strong></div>");
					}
				}
			} 
		});		
	return false;
});
});
</script>
<?php
$_SESSION['emailBody'] = $mailBody;
require('../../trailer.php');
?>