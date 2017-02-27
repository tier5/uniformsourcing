<?php 
require('Application.php');
require('../../header.php');
$isEdit = 0;
$qid = 0;
if(isset($_GET['qid']))
{
	$isEdit = 1;
	$qid = $_GET['qid'];
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
$query=("SELECT \"ID\", \"clientID\", \"client\",shipperno,address, \"active\" ".
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

$query="Select * from tbl_tax where status = 1";
if(!($result=pg_query($connection,$query))){
	print("Failed tax_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_tax[]=$row;
}
pg_free_result($result);

$query="Select * from tbl_quot_company where status = 1";
if(!($result=pg_query($connection,$query))){
	print("Failed tax_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_company[]=$row;
}
pg_free_result($result);

$query="Select * from tbl_quote_payment where status = 1";
if(!($result=pg_query($connection,$query))){
	print("Failed payment_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_payment[]=$row;
}
pg_free_result($result);

$query="Select  firstname,lastname,\"employeeID\" from \"employeeDB\" where active = 'yes'";
if(!($result=pg_query($connection,$query))){
	print("Failed tax_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_employee[]=$row;
}
pg_free_result($result);

$query="Select  ship_via_id,shipvia from tbl_ship_via where status = 1";
if(!($result=pg_query($connection,$query))){
	print("Failed shipvia_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_ship_via[]=$row;
}
pg_free_result($result);

$query="Select  carrier_id,carrier from tbl_quote_carrier where status = 1";
if(!($result=pg_query($connection,$query))){
	print("Failed tax_query: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_carrier[]=$row;
}
pg_free_result($result);


if($isEdit)
{
	$query=("SELECT * from tbl_request ".
		 "WHERE qid = $qid ");
	if(!($result=pg_query($connection,$query))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_quote=$row;
	}
	pg_free_result($result);
	
	$query="Select * from tbl_request_items where status = 1 and qid = $qid";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_items[]=$row;
	}
	pg_free_result($result);
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='quoteList.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center ><div style="width:500px;" align="center" id="message"></div></center>
<table width="100%">

                <tr>
                  <td align="center" valign="top"><font size="5"><?php if($isEdit){?>Edit <?php }else{?> Create<?php }?> Quote Request </font><font size="5"> <br />
                      <br>
                      </font>
                      <form id="validationForm">
                    <table width="80%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                <td width="50%" height="30" align="right">Company: <br /></td>
                              <td width="1%">&nbsp;</td>
                              <td align="left"><select name="company_val">
                                  <option value="0">--- Select Company ------</option>
<?php
									for($i=0;$i < count($data_company); $i++)
									{
										if($data_quote['company_id'] == $data_company[$i]['company_id'])
										echo "<option selected=\"selected\" value=\"{$data_company[$i]['company_id']}\">{$data_company[$i]['company']}</option>";
										else
										echo "<option value=\"{$data_company[$i]['company_id']}\">{$data_company[$i]['company']}</option>";
									}
?>                                  
                                </select></td>
                            </tr>
                            <tr>
                              <td height="30" align="right">Vendor: </td>
                              <td>&nbsp;</td>
                              <td align="left"><select name="vendor_id" id="vendor_select_id">
                              <option value="0">--- Select Vendor ------</option>
<?php
									for($i=0;$i < count($data_vendor); $i++)
									{
										if($data_quote['vendor_id'] == $data_vendor[$i]['vendorID'])
										echo "<option selected=\"selected\" value=\"{$data_vendor[$i]['vendorID']}\">{$data_vendor[$i]['vendorName']}</option>";
										else
										echo "<option value=\"{$data_vendor[$i]['vendorID']}\">{$data_vendor[$i]['vendorName']}</option>";
									}
?>
                                </select></td>
                            </tr>
                            <tr>
                              <td height="30" align="right">Client: </td>
                              <td>&nbsp;</td>
                              <td align="left"><select name="client" id="client_id">
                               <option value="0">--- Select Client ------</option>
<?php
								for($i=0;$i < count($data_client); $i++)
								{
									if($data_quote['client_id'] == $data_client[$i]['ID'])
									echo "<option selected=\"selected\" value=\"{$data_client[$i]['ID']}\">{$data_client[$i]['client']}</option>";
									else
									echo "<option value=\"{$data_client[$i]['ID']}\">{$data_client[$i]['client']}</option>";
								}
?>
                              </select></td>
                            </tr>
                            <tr>
                              <td height="30" align="right">Quote To:</td>
                              <td>&nbsp;</td>
                              <td align="left"><select name="ship_to_select" id="ship_to_id" onchange="javascript:shipToChange(this);">
                                  <option value="0">--- Select ------</option>

                                  <option value="1"<?php if($data_quote['ship_to'] == 1) {?> selected="selected" <?php }?>>Client</option>
                                  <option value="2"<?php if($data_quote['ship_to'] == 2) {?> selected="selected" <?php }?>>Vendor</option>
                                  <option value="3"<?php if($data_quote['ship_to'] == 3) {?> selected="selected" <?php }?>>Other</option>
                              </select></td>
                            </tr>

                            <tr id="OthrID"<?php if($data_quote['ship_to'] != 3 || $data_quote['ship_to'] == "") {?> style="display:none;"<?php }?>>
                              <td height="30" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="50%" height="25" align="right">Name: </td>
                                  <td width="1%" align="left">&nbsp;</td>
                                  <td align="left" ><input name="other_name" type="text" value="<?php echo htmlentities($data_quote['other_name']);?>" /></td>
                                </tr>
                                <tr>
                                  <td height="25" align="right">Street:</td>
                                  <td align="left">&nbsp;</td>
                                  <td align="left"><input name="other_street" type="text" value="<?php echo htmlentities($data_quote['other_street']);?>" /></td>
                                </tr>
                                <tr>
                                  <td height="25" align="right">City:</td>
                                  <td align="left">&nbsp;</td>
                                  <td align="left"><input name="other_city" type="text" value="<?php echo htmlentities($data_quote['other_city']);?>" /></td>
                                </tr>
                                <tr>
                                  <td height="25" align="right">State:</td>
                                  <td align="left">&nbsp;</td>
                                  <td align="left"><input name="other_state" type="text" value="<?php echo htmlentities($data_quote['other_state']);?>" /></td>
                                </tr>
                                <tr>
                                  <td height="25" align="right">Zip:</td>
                                  <td align="left">&nbsp;</td>
                                  <td align="left"><input name="other_zip" type="text" value="<?php echo htmlentities($data_quote['other_zip']);?>" /></td>
                                </tr>
                              </table></td>
                              </tr>
                           
                            <tr id="ClntID"<?php if($data_quote['ship_to'] != 1 || $data_quote['ship_to']=="") {?>  style="display:none;"<?php }?> >
                            <td height="30" colspan="3"><table cellpadding="0" cellspacing="0" border="0" width="100%">
                              <tr>
                              <td width="50%" height="25" align="right">Ship To:</td>
                              <td width="1%" height="25" align="left">&nbsp;</td>
                              <td height="25" align="left"><textarea name="client_shipto" id="client_shipto_id" style="width:200px;height:80px;"><?php echo htmlentities($data_quote['ship_to_clientfield']);?></textarea></td>
                              </tr>
                               <tr>
                              <td height="25" align="right">Client ID:</td>
                              <td height="25" align="left">&nbsp;</td>
                              <td height="25" align="left"><input name="client_customer_id" id="client_customer_id" type="text" value="<?php echo htmlentities($data_quote['ship_to_customer_id']);?>" /></td>
                              </tr>
                              </table></td>
                       
                            </tr>
                            <tr id="VndrID"<?php if($data_quote['ship_to'] != 2 || $data_quote['ship_to'] =="") {?> style="display:none;"<?php }?> >
                             <td colspan="3" height="30">
                             <table cellpadding="0" cellspacing="0" border="0" width="100%">
                               <tr>
                              <td width="50%" height="25" align="right">Ship To:</td>
                              <td width="1%" height="25" align="left">&nbsp;</td>
                              <td height="25" align="left"><textarea name="vendor_shipto" id="vendor_shipto_id" style="width:200px;height:80px;"><?php echo htmlentities($data_quote['ship_to_vendorfield']);?></textarea></td>
                              </tr>
                               <tr>
                                  <td  align="right">Vendor ID:</td>
                              <td>&nbsp;</td>
                              <td align="left"><input name="shipto_vendorId" type="text" onclick="javascript:nextSession();" value="<?php echo htmlentities($data_quote['shipto_vendor_id']);?>" /></td>
                            </tr>
                              </table></td>
                            </tr>
                             
                            <tr>
                              <td align="right">Quote Number: </td>
                              <td>&nbsp;</td>
                              <td align="left"><input name="po_number" type="text" value="<?php echo $data_quote['po_number'];?>" /></td>
                            </tr>
                            <tr>
                              <td height="25" align="right">Internal Quote Request:</td>
                              <td>&nbsp;</td>
                              <td align="left"><input name="internalpo" id="internalpo_id" type="text" readonly="readonly" value="<?php echo htmlentities($data_quote['internal_po']);?>" />
                                  <input type="button" name="internal_po" id="internal_po" value="Quote Request" onclick="javascript:GenerateInternalPO();"  />
                             </td>
                            </tr>
                            <tr>
                              <td height="25" align="right">Quote Date: </td>
                              <td>&nbsp;</td>
                              <td align="left"><input name="podate" id="podate" type="text" value="<?php if($data_quote['po_date'] !=""){echo date("m/d/Y",$data_quote['po_date']);}?>" /></td>
                                </tr>
                              </table>
                              
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                 
                            <tr>
                              <td width="50%" height="25" align="right">Good Thru: </td>
                              <td width="1%">&nbsp;</td>
                              <td align="left"><input id="goods_through_id" type="text" name="goods_through" value="<?php  if($data_quote['good_thru'] !=""){ echo date("m/d/Y",$data_quote['good_thru']); } ?>" /></td>
                            </tr>
                            <tr>
                              <td height="25" align="right">Payment Terms:</td>
                              <td>&nbsp;</td>
                              <td align="left"><select name="payment_terms">
                               <option value="0">--- Select Payment------</option>
<?php
									for($i=0;$i < count($data_payment); $i++)
									{
										if($data_quote['payment_id'] == $data_payment[$i]['payment_id'])
										echo "<option selected=\"selected\" value=\"{$data_payment[$i]['payment_id']}\">{$data_payment[$i]['payment']}</option>";
										else
										echo "<option value=\"{$data_payment[$i]['payment_id']}\">{$data_payment[$i]['payment']}</option>";
									}
?>							   </select></td>
                            </tr>
                            <tr>
                              <td height="25" align="right">Sales Rep:</td>
                              <td>&nbsp;</td>
                              <td align="left"><select name="salesrep">
                                  <option value="0">--- Select Employee------</option>
<?php
									for($i=0;$i < count($data_employee); $i++)
									{
										if($data_quote['sales_rep'] == $data_employee[$i]['employeeID'])
										echo "<option selected=\"selected\" value=\"{$data_employee[$i]['employeeID']}\">{$data_employee[$i]['firstname']}{$data_employee[$i]['lastname']}</option>";
										else
										echo "<option value=\"{$data_employee[$i]['employeeID']}\">{$data_employee[$i]['firstname']}{$data_employee[$i]['lastname']}</option>";
									}
?>

                              </select></td>
                            </tr>
                            <tr>
                              <td height="25" align="right">&nbsp;</td>
                              <td>&nbsp;</td>
                              <td align="left">&nbsp;</td>
                                  </tr>
                                </table></td>
                            </tr>
                            <tr>
                              <td height="25" align="left">
							  
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    

                                    <tr>
                                      <td height="25" align="right">Item Number: </td>
                                      <td>&nbsp;</td>
                                      <td width="170" height="25" colspan="3" align="left"><input type="button" name="Submit" value="Add Item" onclick="javascript:popOpen(null);" /></td>
                                    </tr>
                                     <tr>
                                      <td height="25" align="right">&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td height="25" align="left"><table cellpadding="0" cellspacing="0" width="100%" id="tbl_item_add"><tr><td colspan="3">&nbsp; </td></tr>
<?php 
									  for($i=0;$i<count($data_items);$i++)
									  {
?>
										<tr><td width="50%">Item Number<?php echo ($i+1);?>: </td>
                                        <td width="1%" >&nbsp;</td>
                                        <td width="49%" ><?php echo $data_items[$i]['itemno'];?></td>
                                        <td width="150px" ><a style="cursor:hand;cursor:pointer;" onclick="javascript:popOpen(<?php echo ($i+1);?>);">View/Edit...</a></td>
                                        <td style="display:none">
                                        <input type="hidden"  id="item_id<?php echo($i+1);?>" name="item[]" value="<?php echo stripslashes($data_items[$i]['itemno']);?>" />
                                        <input type="hidden"  id="desc_id<?php echo($i+1);?>" name="desc[]" value="<?php echo stripslashes($data_items[$i]['description']);?>" />
                                        <input type="hidden"  id="unit_id<?php echo($i+1);?>" name="unitprice[]" value="<?php echo $data_items[$i]['unit_price'];?>" />
                                        <input type="hidden"  id="quantity_id<?php echo($i+1);?>" name="quantity[]" value="<?php echo $data_items[$i]['quantity'];?>" />
                                        <input type="hidden"  id="tax_id<?php echo($i+1);?>" name="tax_amount[]" value="<?php echo $data_items[$i]['tax_amount'];?>" />
                                        <input type="hidden"  id="tax_type_id<?php echo($i+1);?>" name="tax_type[]" value="<?php echo $data_items[$i]['tax_type'];?>" />
                                       <input type="hidden"  id="amount_id<?php echo($i+1);?>" name="amount[]" value="<?php echo $data_items[$i]['amount'];?>" />
                                       <input type="hidden"  id="hdn_itemid<?php echo($i+1);?>" name="hdn_id[]" value="<?php echo $data_items[$i]['item_id'];?>" /></td>
			<td ><a class="alink" href="javascript:void(0);" onclick="DeleteItems(<?php echo $data_items[$i]['item_id'];?>);DeleteCurrentRow(this);"><img style="width:32px;height:25px;" src="<?php echo $mydirectory;?>/images/delete.png" ></a></td></tr>
<?php
									  }
?>
                                      </table></td>
                                    </tr>
                                        <tr>
                                      <td width="50%" height="25" align="right">AmountSubTotal:</td>
                                      <td width="1%">&nbsp;</td>
                                      <td><input name="amountsubtotal" id="amountsubtotal_id" readonly="readonly" type="text" value="<?php echo $data_quote['amount_sub_total'];?>" /></td>
                                    </tr>
                                        <tr>
                                      <td width="50%" height="25" align="right">TaxSubTotal:</td>
                                      <td width="1%">&nbsp;</td>
                                      <td width="49%"><input name="taxsubtotal" id="taxsubtotal_id" readonly="readonly" type="text" value="<?php echo $data_quote['tax_sub_total'];?>" /></td>
                                    </tr>
                                    <tr>
                                      <td width="50%" height="25" align="right">Total:</td>
                                      <td width="1%">&nbsp;</td>
                                      <td width="49%"><input name="total" type="text" id="total_id" readonly="readonly" value="<?php echo $data_quote['total'];?>" /></td>
                                    </tr>
                                  </table>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td width="50%" height="25">&nbsp;</td>
                                      <td width="1%">&nbsp;</td>
                                      <td width="49%">&nbsp;</td>
                                    </tr>
                                </table>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="ItemTbl">
                                  <tr>
                                    <td height="25" align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td width="50%" height="25" align="right"> Quote Via:</td>
                                    <td width="1%">&nbsp;</td>
                                    <td width="49%" align="left"><select name="shipvia">
                                      <option value="0">--- Select ------</option>
<?php
									for($i=0;$i < count($data_ship_via); $i++)
									{
										if($data_quote['ship_via'] == $data_ship_via[$i]['ship_via_id'])
										echo "<option selected=\"selected\" value=\"{$data_ship_via[$i]['ship_via_id']}\">{$data_ship_via[$i]['shipvia']}</option>";
										else
										echo "<option value=\"{$data_ship_via[$i]['ship_via_id']}\">{$data_ship_via[$i]['shipvia']}</option>";
									}
?>                               </select></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Shipper #:<br /></td>
                                    <td>&nbsp;</td>
                                    <td align="left">
                              <input name="other_shipper" id="other_shipper_id" type="text" value="<?php echo $data_quote['shipperno'];?>" <?php if($data_quote['ship_to'] == 1) {?> readonly="readonly" <?php }?>  /></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Carrier:<br /></td>
                                    <td>&nbsp;</td>
                                    <td align="left"><select name="carrier">
                                      <option value="0">--- Select Carrier------</option>
<?php
									for($i=0;$i < count($data_carrier); $i++)
									{
										if($data_quote['carrier_id'] == $data_carrier[$i]['carrier_id'])
										echo "<option selected=\"selected\" value=\"{$data_carrier[$i]['carrier_id']}\">{$data_carrier[$i]['carrier']}</option>";
										else
										echo "<option value=\"{$data_carrier[$i]['carrier_id']}\">{$data_carrier[$i]['carrier']}</option>";
									}
?>
                                     </select></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Instructions/Notes: </td>
                                    <td>&nbsp;</td>
                                    <td align="left"><textarea name="instructions" cols="40" rows="10" ><?php echo htmlentities($data_quote['instruction_notes']);?></textarea></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left">
                                    <input name="qid" id="qid" type="hidden" value="<?php echo $qid;?>" />
                                    <input name="save" type="submit" value="Save" />
                                    <input type="reset" name="cancel" id="main_cancel" value="Cancel" /></td>
                                  </tr>
                                </table></td>
                            </tr>

                          </table></td>
                        </tr>
                      </table>
                      </form></td>
                </tr>
                
              </table>
             
              <div class="popup_block" id="div_item" align="left">
              			  <!--ItemTbl is the form which needs to go inside the POPUP -->
							  <table width="100%" border="0" cellpadding="0" cellspacing="0" id="tbl_item">
                                  <tr>
                                    <td width="40%" height="25" align="right"> Item Number:</td>
                                    <td width="1%">&nbsp;</td>
                                    <td align="left"><input name="item_name" id="item_number_id" type="text" value="" /></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Description:<br /></td>
                                    <td>&nbsp;</td>
                                    <td align="left"><textarea name="desc" id="description_id" style="width:150px;height:80px;"></textarea></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Unit Price:<br /></td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input name="unitprice" id="unit_price_id" type="text" value="" onchange="javascript:calculateAmount();" /></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Quantity:</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input name="quantity" id="quantity_id" type="text" value="" onchange="javascript:calculateAmount();" /></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Tax Type:</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><select name="tax_type" id="tax_typeid" onchange="javascript:calculateAmount();" >
                                      <option value="0">----Select Tax----</option>
<?php
									for($i=0;$i < count($data_tax); $i++)
									{
										if($data_items[$i]['tax_type'] == $data_tax[$i]['tax_amount'])
										echo "<option selected=\"selected\" value=\"{$data_tax[$i]['tax_amount']}\">{$data_tax[$i]['tax_name']}</option>";
										else
										echo "<option value=\"{$data_tax[$i]['tax_amount']}\">{$data_tax[$i]['tax_name']}</option>";
									}
?>
                                    </select></td>
                                  </tr>
                                  <tr>
                                    <td height="25" align="right">Tax Amount:</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input name="tax_amount" id="tax_amount_id" type="text" value="" readonly="readonly"  /></td>
                                  </tr>
                                   <tr>
                                    <td height="25" align="right">Amount:</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input name="amount" id="amount_id" type="text" readonly="readonly" /></td>
                                  </tr>
                                   <tr>
                                    <td height="25" align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input name="hdn_id" id="hdnitemid" type="hidden" value=""/></td>
                                  </tr>
                                    <tr>
                                    <td height="25" align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><input id="item_submit" type="button" value="Submit" /><input type="reset" value="Cancel" id="cancel"/></td>
                                  </tr>
                                  
                                </table>
							    <!--ItemTbl is the form which needs to go inside the POPUP -->
              </div>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script type="text/javascript">

function edit_multiple(count)
{
	if(isNaN(document.getElementById('unit_price_id').value))
	{
		document.getElementById('unit_price_id').value =0;
	}
	if(isNaN(document.getElementById('quantity_id').value))
	{
		document.getElementById('quantity_id').value =0;
	}
	var taxtype = document.getElementById('tax_typeid');
	document.getElementById('item_id'+count).value = document.getElementById('item_number_id').value;
	document.getElementById('desc_id'+count).value = document.getElementById('description_id').value;	
	document.getElementById('unit_id'+count).value =document.getElementById('unit_price_id').value ;
	document.getElementById('quantity_id'+count).value=	document.getElementById('quantity_id').value ;
	document.getElementById('tax_id'+count).value =document.getElementById('tax_amount_id').value ;
	document.getElementById('tax_type_id'+count).value =taxtype.options[taxtype.selectedIndex].value ;
	document.getElementById('amount_id'+count).value=document.getElementById('amount_id').value;
}
function add_multiple(tableid)
{
	if(isNaN(document.getElementById('unit_price_id').value))
	{
		document.getElementById('unit_price_id').value =0;
	}
	if(isNaN(document.getElementById('quantity_id').value))
	{
		document.getElementById('quantity_id').value =0;
	}
	var itemno = document.getElementById('item_number_id').value; 
	var desc = document.getElementById('description_id').value;
	var unit_price = document.getElementById('unit_price_id').value;
	var quantity = document.getElementById('quantity_id').value;
	var taxtype = document.getElementById('tax_typeid');
	var taxamount = document.getElementById('tax_amount_id').value;
	var amount = document.getElementById('amount_id').value;
	
	var table = document.getElementById(tableid);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);
	var cell6 = row.insertCell(5);
		
	cell1.width="130px";	
	cell1.align="right";	
	cell1.innerHTML = "Item Number "+rowCount +":";	
	
	cell2.width="15px";		
	cell2.innerHTML = "&nbsp;";	

	cell3.align="left";	
	cell3.innerHTML = itemno+'&nbsp;&nbsp;&nbsp;&nbsp;';	
	
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "View/Edit...";
	element1.onclick = function(){popOpen(rowCount);};
	cell3.appendChild(element1);
	
	var element2 = document.createElement("input");
	element2.name = "item[]";
	element2.id = "item_id"+rowCount;
	element2.value = itemno;
	
	var element3 = document.createElement("textArea");
	element3.name = "desc[]";
	element3.id = "desc_id"+rowCount;
	element3.innerHTML = desc;
	
	var element4 = document.createElement("input");
	element4.name = "unitprice[]";
	element4.id = "unit_id"+rowCount;
	element4.value = unit_price;
	
	var element5 = document.createElement("input");
	element5.name = "quantity[]";
	element5.id = "quantity_id"+rowCount;
	element5.value = quantity;
	
	var element6 = document.createElement("input");
	element6.name = "tax_amount[]";
	element6.id = "tax_id"+rowCount;
	element6.value = taxamount;
	
	var element7 = document.createElement("input");
	element7.name = "tax_type[]";
	element7.id = "tax_type_id"+rowCount;
	element7.value = taxtype.options[taxtype.selectedIndex].value;
	
	var element8 = document.createElement("input");
	element8.name = "amount[]";
	element8.id = "amount_id"+rowCount;
	element8.value = amount;
	
	var element9 = document.createElement("input");
	element9.name = "hdn_id[]";
	element9.id = 'hdn_itemid'+rowCount;
	element9.value = 0;
	element9.style.display = "none";
	
	cell4.style.display="none";
	cell4.appendChild(element2);
	cell4.appendChild(element3);
	cell4.appendChild(element4);
	cell4.appendChild(element5);
	cell4.appendChild(element6);
	cell4.appendChild(element7);
	cell4.appendChild(element8);
	cell4.appendChild(element9);
	
	var cell6 = row.insertCell(4);	
	cell6.align="left";	
	cell6.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentRow(this,0,'');\"><img style=\"width:32px;height:25px;\" src=\"<?php echo $mydirectory; ?>/images/delete.png\" ></a>";
	//alert(document.getElementById(tableid).innerHTML);
}
function DeleteCurrentRow(obj)
{	
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}
function DeleteRow(rowObjArray)
{	
	for (var i=0; i<rowObjArray.length; i++) {
		var rIndex = rowObjArray[i].sectionRowIndex;
		rowObjArray[i].parentNode.deleteRow(rIndex);
	}	
}
</script>
<script type="text/javascript">
function popOpen(count)
{
	var popID = 'div_item'; //Get Popup Name
	if(count != null || count > 0)
	{
		document.getElementById('item_number_id').value =document.getElementById('item_id'+count).value;
		document.getElementById('description_id').value =document.getElementById('desc_id'+count).value;
		document.getElementById('unit_price_id').value =document.getElementById('unit_id'+count).value;
		document.getElementById('quantity_id').value =document.getElementById('quantity_id'+count).value;
		document.getElementById('tax_amount_id').value =document.getElementById('tax_id'+count).value;
		selCount = document.getElementById('tax_typeid').options.length;
		for(i = 0; i < selCount; i++)
		{
			if(document.getElementById('tax_typeid').options[i].value == document.getElementById('tax_type_id'+count).value)
			{
				document.getElementById('tax_typeid').selectedIndex = i;
				break;
			}
		}
		document.getElementById('amount_id').value =document.getElementById('amount_id'+count).value;
		document.getElementById('item_submit').onclick = function(){edit_multiple(count);Fade();calculateSub('tbl_item_add');};
	}
	else
	{
		document.getElementById('item_number_id').value ="";
		document.getElementById('description_id').value ="";
		document.getElementById('unit_price_id').value ="";
		document.getElementById('quantity_id').value ="";
		document.getElementById('tax_typeid').value = 0;
		document.getElementById('tax_amount_id').value ="";
		document.getElementById('amount_id').value ="";
		document.getElementById('item_submit').onclick = function(){add_multiple('tbl_item_add');Fade();calculateSub('tbl_item_add');};
	}
	
	popWidth = '500';
	//alert('enters');
	$('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
	$('#' + popID).height('200');
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
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
	return false;
});
function Fade()
{
	$('#fade , .popup_block').fadeOut();
}
$(function(){$("#validationForm").submit(function(){
if($("#validationForm").valid())
{
	var pid = document.getElementById('pid');
	  dataString = $("#validationForm").serialize();
	  <?php
		if($isEdit)
			echo "dataString += '&submit=Save';";
		else
			echo "dataString += '&submit=Add';";
		?>
	  $.ajax({
		 type: "POST",
		 url: "quote_submit.php",
		 data: dataString,
		 dataType: "json",
		 success:function(data)
		 {
			 if(data!=null)
			 {
				if(data.name || data.error)
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
				}
				else
				{
					if(data.error == "")
					{
							$("#message").html("<div class='successMessage'><strong><?php if($isEdit) echo 'Quote Edited successfully'; else echo 'Quote Added successfully'; ?></strong></div>");
					}
					else
					{
						$("#message").html("<div class='errorMessage'><strong>Error while Adding quote</strong></div>");
					}
				}
			  } 
			else
			{
			 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			}
		}
		});
		  return false;
  }
   });
});
function nextSession() {
    var ret = new Date();
    ret.setDate(ret.getDate() + 61);
	if(ret.getDay() == 6 )
	ret.setDate(ret.getDate() + 2);
	else if(ret.getDay() == 0)
	ret.setDate(ret.getDate() + 1);
	
  var curr_date = (ret.getDate() > 9 ) ? ret.getDate(): '0'+ret.getDate();
  var curr_month = ret.getMonth()+1; //months are zero based
  curr_month = (curr_month > 9 ) ? curr_month: '0'+curr_month;
  var curr_year = ret.getFullYear();	
  document.getElementById('goods_through_id').value = curr_month+'/'+curr_date+'/'+curr_year;
}
function calculateSub(tableid)
{
	
		var table = document.getElementById(tableid);
		var rowCount = table.rows.length;
		var subAmountTotal = 0;
		var subTaxTotal = 0;
		var total = 0;
		for(i=1;i<rowCount; i++)
		{
			subAmountTotal+=parseFloat(document.getElementById('amount_id'+i).value);
			subTaxTotal +=parseFloat(document.getElementById('tax_id'+i).value);
		}
		total = parseFloat(subAmountTotal)+parseFloat(subTaxTotal);
		document.getElementById('amountsubtotal_id').value = subAmountTotal;
		document.getElementById('taxsubtotal_id').value = subTaxTotal.toFixed(2);
		document.getElementById('total_id').value = total.toFixed(2);
}
function calculateAmount()
{
		var unitprice = document.getElementById('unit_price_id').value;
		var quantity = document.getElementById('quantity_id').value ;
		var taxtype = document.getElementById('tax_typeid').value ;
		var unit_price = 0;
		var quant = 0;
		var tax_type =0;
		if(unitprice !="" && unitprice >0 )
			unit_price = unitprice;
		else
			unitprice = 0;
		if(quantity != "" && quantity >0)
			quant = quantity;
		else
			quant = 0;
		if(taxtype!="" && taxtype >0)
			tax_type = taxtype;
		else
			tax_type = 0
		var taxamount = unit_price * quant * tax_type;
		var amount = unit_price * quant;
		document.getElementById('tax_amount_id').value  = taxamount;
		document.getElementById('amount_id').value  = amount;
}
function shipToChange(shipto)
{
	document.getElementById('ClntID').style.display = "none";
	document.getElementById('VndrID').style.display = "none";
	document.getElementById('OthrID').style.display = "none";
	document.getElementById('other_shipper_id').readOnly = false;
	document.getElementById('other_shipper_id').value = "";
	if(shipto.value == 1)
	{
		var clientVal = document.getElementById('client_id');
		if(clientVal.options[clientVal.selectedIndex].value == 0)
		{
			alert('Please select a client');
			shipto.selectedIndex = 0;
			return;
		}
		 document.getElementById('ClntID').style.display = "";
		 document.getElementById('other_shipper_id').readOnly = true;
		 shipToAddressFill(clientVal.options[clientVal.selectedIndex].value,'shipto_client');
	}
	else if(shipto.value == 2)
	{
		var vendorVal= document.getElementById('vendor_select_id');
		if(vendorVal.options[vendorVal.selectedIndex].value == 0)
		{
			alert('Please select a vendor');
			shipto.selectedIndex = 0;
			return;
		}
		 document.getElementById('VndrID').style.display = "";
		  shipToAddressFill(vendorVal.options[vendorVal.selectedIndex].value,'shipto_vendor');
	}
	else if(shipto.value ==3)
	{
		 document.getElementById('OthrID').style.display = "";
	}
}
function GenerateInternalPO()
{
	var internalpo_val = document.getElementById('internalpo_id');
	var dataString ='';
	$.ajax({
		   type: "POST",
		   url: "internalpogenerate.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						internalpo_val.value = data.value;
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
function shipToAddressFill(id,filename)
{
	var dataString ='id='+id;
	$.ajax({
		   type: "POST",
		   url: filename+".php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						if(data.type == "client")
						{
							document.getElementById('client_shipto_id').value = data.value;
							document.getElementById('other_shipper_id').value =data.shipper;
							document.getElementById('client_customer_id').value =data.client_id;
							
						}
						else if(data.type == "vendor")
						{
							document.getElementById('vendor_shipto_id').value = data.value;
						}
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
function DeleteItems(id)
{
	
	var dataString ='item_id='+id;
	$.ajax({
		   type: "POST",
		   url: "delete_item.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Item Removed Successfully.</strong></div>");
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
$('#podate').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });
$('#goods_through_id').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });
</script>   
<script type="text/javascript">
$().ready(function() {
 <?php if($qid == 0) echo 'nextSession();'; ?>
	$("#validationForm").validate({
	rules: {
		podate: {date: true},
		po_number:"required",
		goods_through : {date: true}
		},
	messages: {
		podate: "Please enter valid date",
		po_number: "Qoute Number is required",
		goods_through :"Please enter valid date"
	}
	});	
	$("#main_cancel").click(function(){window.location.reload();});
	
});
</script> 
<?php 	  
require('../../trailer.php');
?>