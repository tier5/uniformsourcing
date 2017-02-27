<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
extract($_POST);
$return_arr = array();
$return_arr['error'] = "";
$return_arr['html'] = "";
$isEdit = 0;
$qid = 0;
$sample_id =0;
if(isset($_POST['sampleId']))
{
	$isEdit = 1;
	$sample_id = $_POST['sampleId'];
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
	$query=("SELECT * from tbl_prj_sample_po ".
		 "WHERE sample_id = $sample_id ");
	if(!($result=pg_query($connection,$query))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_quote=$row;
	}
	if($data_quote['id']!="")
		$qid = $data_quote['id'];
	pg_free_result($result);
	
	$query="Select * from tbl_prj_sample_po_items where status = 1 and sample_id = $sample_id";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_items[]=$row;
	}
	pg_free_result($result);
}
$html='';

          $html='<table width="100%">'.

                '<tr>'.
                 '<td align="center" valign="top">'.
		  '<table width="80%" border="0" cellspacing="0" cellpadding="0">'.
                        '<tr>'.
                          '<td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                            '<tr>'.
                              '<td align="right" valign="top" id="hello"><table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                                '<tr>'.
                                '<td width="50%" height="30" align="right">Company: <br /></td>'.
                              '<td width="1%">&nbsp;</td>'.
                              '<td align="left"><select name="company_val" style="width:200px;">'.
                                  '<option value="0">--- Select Company ------</option>';

									for($i=0;$i < count($data_company); $i++)
									{
										if($data_quote['company_id'] == $data_company[$i]['company_id'])
											$html.='<option selected="selected" value="'.$data_company[$i]['company_id'].'">'.$data_company[$i]['company'].'</option>';
										else
											$html.='<option value="'.$data_company[$i]['company_id'].'">'.$data_company[$i]['company'].'</option>';
									}
                               
                                $html.='</select></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="30" align="right">Vendor: </td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><select name="vendor_id" id="vendor_select_id" style="width:200px;">'.
                              '<option value="0">--- Select Vendor ------</option>';

									for($i=0;$i < count($data_vendor); $i++)
									{
										if($data_quote['vendor_id'] == $data_vendor[$i]['vendorID'])
										$html.='<option selected="selected" value="'.$data_vendor[$i]['vendorID'].'">'.$data_vendor[$i]['vendorName'].'</option>';
										else
										$html.='<option value="'.$data_vendor[$i]['vendorID'].'">'.$data_vendor[$i]['vendorName'].'</option>';
									}

                                $html.='</select></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="30" align="right">Client: </td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><select name="client" id="client_id" style="width:200px;">'.
                               '<option value="0">--- Select Client ------</option>';

								for($i=0;$i < count($data_client); $i++)
								{
									if($data_quote['client_id'] == $data_client[$i]['ID'])
									$html.='<option selected="selected" value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';
									else
									$html.='<option value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';
								}
                              $html.='</select></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="30" align="right">Ship To:</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><select name="ship_to_select" id="ship_to_id" onchange="javascript:shipToChange(this);">'.
                                  '<option value="0">--- Select ------</option>'.

                                  '<option value="1"'; if($data_quote['ship_to'] == 1) {$html.='selected="selected" '; } $html.='>Client</option>'.
                                  '<option value="2"'; if($data_quote['ship_to'] == 2) {$html.='selected="selected"';}$html.='>Vendor</option>'.
                                  '<option value="3"';if($data_quote['ship_to'] == 3) {$html.=' selected="selected"';  }$html.='>Other</option>'.
                              '</select></td>'.
                            '</tr>'.
                              '<tr id="OthrID"'; if($data_quote['ship_to'] != 3 || $data_quote['ship_to'] == "") { $html.='style="display:none;"'; }$html.='>'.
                              '<td height="30" colspan="3">'.
                              '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                                '<tr>'.
                                  '<td width="50%" height="25" align="right">Name: </td>'.
                                  '<td width="1%" align="left">&nbsp;</td>'.
                                  '<td align="left" ><input name="other_name" type="text" value="'.htmlentities($data_quote['other_name']).'"/></td>'.
                                '</tr>'.
                                '<tr>'.
                                  '<td height="25" align="right">Street:</td>'.
                                  '<td align="left">&nbsp;</td>'.
                                  '<td align="left"><input name="other_street" type="text" value="'.htmlentities($data_quote['other_street']).'" /></td>'.
                                '</tr>'.
                                '<tr>'.
                                  '<td height="25" align="right">City:</td>'.
                                  '<td align="left">&nbsp;</td>'.
                                  '<td align="left"><input name="other_city" type="text" value="'.htmlentities($data_quote['other_city']).'" /></td>'.
                                '</tr>'.
                                '<tr>'.
                                  '<td height="25" align="right">State:</td>'.
                                  '<td align="left">&nbsp;</td>'.
                                  '<td align="left"><input name="other_state" type="text" value="'.htmlentities($data_quote['other_state']).'" /></td>'.
                                '</tr>'.
                                '<tr>'.
                                  '<td height="25" align="right">Zip:</td>'.
                                  '<td align="left">&nbsp;</td>'.
                                  '<td align="left"><input name="other_zip" type="text" value="'.htmlentities($data_quote['other_zip']).'" /></td>'.
                                '</tr>'.
                              '</table></td>'.
                              '</tr>'.
							'<tr id="ClntID"'; if($data_quote['ship_to'] != 1 || $data_quote['ship_to']=="") {  $html.=' style="display:none;"';} $html.=' >'.
                            '<td height="30" colspan="3">'.
                            '<table cellpadding="0" cellspacing="0" border="0" width="100%">'.
                              '<tr>'.
                              '<td width="50%" height="25" align="right">Ship To:</td>'.
                              '<td width="1%" height="25" align="left">&nbsp;</td>'.
                              '<td height="25" align="left"><textarea name="client_shipto" id="client_shipto_id" style="width:200px;height:80px;">'.htmlentities($data_quote['ship_to_clientfield']).'</textarea></td>'.
                              '</tr>'.
                               '<tr>'.
                              '<td height="25" align="right">Client ID:</td>'.
                              '<td height="25" align="left">&nbsp;</td>'.
                              '<td height="25" align="left"><input name="client_customer_id" id="client_customer_id" type="text" value="'.htmlentities($data_quote['ship_to_customer_id']).'" /></td>'.
                              '</tr>'.
                              '</table></td>'.
                            '</tr>'.
                            '<tr id="VndrID"';if($data_quote['ship_to'] != 2 || $data_quote['ship_to'] =="") {$html.=' style="display:none;"'; }$html.=' >'.
                             '<td colspan="3" height="30">'.
                             '<table cellpadding="0" cellspacing="1" border="0" width="100%">'.
                              '<tr>'.
                              '<td width="50%" height="25" align="right">Ship To:</td>'.
                              '<td width="1%" height="25" align="left">&nbsp;</td>'.
                              '<td height="25" align="left"><textarea name="vendor_shipto" id="vendor_shipto_id" style="width:200px;height:80px;">'.htmlentities($data_quote['ship_to_vendorfield']).'</textarea></td>'.
                              '</tr>'.
                              '<tr>'.
                              '<td  align="right">Vendor ID:</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><input name="shipto_vendorId" type="text" onclick="javascript:nextSession();" value="'.htmlentities($data_quote['shipto_vendor_id']).'" /></td>'.
                              '</tr>'.
                              '</table></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td align="right">Quote Number: </td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><input name="po_number" id="po_number" type="text" value="'.$data_quote['po_number'].'" /><label id="po_messege" style=" color:red; display:none; " >Enter Quote Number..!</label></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">Internal Quote Request:</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><input name="internalpo" id="internalpo_id" type="text" readonly="readonly" value="'.htmlentities($data_quote['internal_po']).'" size="6" />'.
                                  '<input type="button" name="internal_po" id="internal_po" value="Quote Request" onclick="javascript:GenerateInternalPO();"  />'.
                             '</td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">Quote Date: </td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><input name="podate" id="podate" type="text" value="';if($data_quote['po_date'] !=""){$html.=date("m/d/Y",$data_quote['po_date']);}$html.='" onclick="javascript:showDate(this);" /></td>'.
                                '</tr>'.  
                              
                            
                              '</table>'.
                              
                                '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                                 
                            '<tr>'.
                              '<td width="50%" height="25" align="right">Good Thru: </td>'.
                              '<td width="1%">&nbsp;</td>'.
                              '<td align="left"><input id="goods_through_id" type="text" name="goods_through" value="'; if($data_quote['good_thru'] !=""){$html.= date("m/d/Y",$data_quote['good_thru']).'"'; }else $html.='"'; $html.='/></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">Payment Terms:</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><select name="payment_terms">'.
                               '<option value="0">--- Select Payment------</option>';

									for($i=0;$i < count($data_payment); $i++)
									{
										if($data_quote['payment_id'] == $data_payment[$i]['payment_id'])
										$html.='<option selected="selected" value="'.$data_payment[$i]['payment_id'].'">'.$data_payment[$i]['payment'].'</option>';
										else
										$html.='<option value="'.$data_payment[$i]['payment_id'].'">'.$data_payment[$i]['payment'].'</option>';
									}
						   $html.='</select></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">Sales Rep:</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left"><select name="salesrep">'.
                                  '<option value="0">--- Select Employee------</option>';

									for($i=0;$i < count($data_employee); $i++)
									{
										if($data_quote['sales_rep'] == $data_employee[$i]['employeeID'])
											$html.='<option selected="selected" value="'.$data_employee[$i]['employeeID'].'">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
										else
											$html.='<option value="'.$data_employee[$i]['employeeID'].'">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
									}

                              $html.='</select></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">&nbsp;</td>'.
                              '<td>&nbsp;</td>'.
                              '<td align="left">&nbsp;</td>'.
                                  '</tr>'.
                                '</table></td>'.
                            '</tr>'.
                            '<tr>'.
                              '<td height="25" align="right">'.
                                  '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                                    '<tr>'.
                                      '<td height="25" align="right">Item Number: </td>'.
                                      '<td>&nbsp;</td>'.
                                      '<td width="170" height="25" colspan="3" align="left"><input type="button" name="Submit" value="Add Item" onclick="javascript:item_visible();" /></td>'.
                                    '</tr>'.
									'<tr>'.
									'<td colspan="3">'.
              '<div style="display:none;" id="div_item" align="left">'.
              			  //<!--ItemTbl is the form which needs to go inside the POPUP -->
							  '<table width="100%" border="0" cellpadding="0" cellspacing="0" id="tbl_item" style="border:red solid 1px;">'.
                                  '<tr>'.
                                    '<td width="50%" height="25" align="right"> Item Number:</td>'.
                                    '<td width="1%">&nbsp;</td>'.
                                    '<td align="left"><input id="item_number_id" type="text" value="" /></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Description:<br /></td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><textarea id="description_id" style="width:150px;height:80px;"></textarea></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Unit Price:<br /></td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><input id="unit_price_id" type="text" value="" onchange="if(this.value !=\'\'){javascript:calculateAmount();isNumeric(this);}" /></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Quantity:</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><input id="quantity_id" type="text" value="" onchange="if(this.value !=\'\'){javascript:calculateAmount();isNumeric(this);}" /></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Tax Type:</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><select id="tax_typeid" value="" onchange="if(this.value !=\'\'){javascript:calculateAmount();}" >'.
                                      '<option value="0">----Select Tax----</option>';
									for($i=0;$i < count($data_tax); $i++)
									{
										if($data_items[$i]['tax_type'] == $data_tax[$i]['tax_amount'])
										$html.='<option selected="selected" value="'.$data_tax[$i]['tax_amount'].'">'.$data_tax[$i]['tax_name'].'</option>';
										else
										$html.='<option value="'.$data_tax[$i]['tax_amount'].'">'.$data_tax[$i]['tax_name'].'</option>';
									}
                                    $html.='</select></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Tax Amount:</td>'.
                                    '<td>&nbsp;</td>'.
                                   '<td align="left"><input id="tax_amount_id" type="text" value="" readonly="readonly"  onchange="isNumeric(this);"/></td>'.
                                  '</tr>'.
                                   '<tr>'.
                                    '<td height="25" align="right">Amount:</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><input id="amount_id" type="text" readonly="readonly"  onchange="isNumeric(this);"/></td>'.
                                  '</tr>'.
                                   '<tr>'.
                                    '<td height="25" align="right">&nbsp;</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><input id="hdnitemid" type="hidden" value=""/></td>'.
                                  '</tr>'.
                                    '<tr>'.
                                    '<td height="25" align="right">&nbsp;</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><input id="item_submit" type="button" value="Submit" /><input type="button" value="Cancel" onclick="document.getElementById(\'div_item\').style.display=\'none\';document.getElementById(\'tbl_item_add\').style.display=\'\';"/></td>'.
                                  '</tr>'.
                                  
                                '</table>'.
							    //<!--ItemTbl is the form which needs to go inside the POPUP -->
              '</div></td></tr>'.
                                     '<tr>'.
                                      '<td height="25" align="right">&nbsp;</td>'.
                                      '<td>&nbsp;</td>'.
                                      '<td height="25" align="left"><table cellpadding="0" cellspacing="0" width="100%" id="tbl_item_add"><tr><td colspan="3">&nbsp; </td></tr>';
							 		 for($i=0;$i<count($data_items);$i++)
									  {
										$html.='<tr id="tr_sample_notes'.($i+1).'"><td width="50%">Item Number<?php echo ($i+1);?>: </td>'.
                                        '<td width="1%" >&nbsp;</td>'.
                                        '<td width="49%" id="item_td_id'.($i+1).'" >'.$data_items[$i]['itemno'].'</td>'.
                                        '<td width="150px" ><a style="cursor:hand;cursor:pointer;" onclick="javascript:item_visible('.($i+1).');">View/Edit...</a></td>'.
                                        '<td style="display:none">'.
                                        '<input type="hidden"  id="item_id'.($i+1).'" name="item[]" value="'.stripslashes($data_items[$i]['itemno']).'" />'.
                                        '<input type="hidden"  id="desc_id'.($i+1).'" name="desc[]" value="'.stripslashes($data_items[$i]['description']).'" />'.
                                        '<input type="hidden"  id="unit_id'.($i+1).'" name="unitprice[]" value="'.$data_items[$i]['unit_price'].'" />'.
                                        '<input type="hidden"  id="quantity_id'.($i+1).'" name="quantity[]" value="'.$data_items[$i]['quantity'].'" />'.
                                        '<input type="hidden"  id="tax_id'.($i+1).'" name="tax_amount[]" value="'.$data_items[$i]['tax_amount'].'" />'.
                                        '<input type="hidden"  id="tax_type_id'.($i+1).'" name="tax_type[]" value="'.$data_items[$i]['tax_type'].'" />'.
                                       '<input type="hidden"  id="amount_id'.($i+1).'" name="amount[]" value="'.$data_items[$i]['amount'].'" />'.
                                       '<input type="hidden"  id="hdn_itemid'.($i+1).'" name="hdn_id[]" value="'.$data_items[$i]['id'].'" /></td>'.
			'<td><a class="alink" href="javascript:void(0);" onclick="document.getElementById(\'tr_sample_notes'.($i+1).'\').innerHTML = \'\';calculateSub(\'tbl_item_add\');DeleteItems('.$data_items[$i]['id'].');"><img style="width:32px;height:25px;" src="'.$mydirectory.'/images/delete.png" ></a></td></tr>';
									  }
                                      $html.='</table></td>'.
                                    '</tr>'.
                                        '<tr>'.
                                      '<td width="50%" height="25" align="right">AmountSubTotal:</td>'.
                                      '<td width="1%">&nbsp;</td>'.
                                      '<td><input name="amountsubtotal" id="amountsubtotal_id" readonly="readonly" type="text" value="'.$data_quote['amount_sub_total'].'" onchange="isNumeric(this);"/></td>'.
                                    '</tr>'.
                                        '<tr>'.
                                      '<td width="50%" height="25" align="right">TaxSubTotal:</td>'.
                                      '<td width="1%">&nbsp;</td>'.
                                      '<td width="49%"><input name="taxsubtotal" id="taxsubtotal_id" readonly="readonly" type="text" value="'.$data_quote['tax_sub_total'].'" onchange="isNumeric(this);" /></td>'.
                                    '</tr>'.
                                    '<tr>'.
                                      '<td width="50%" height="25" align="right">Total:</td>'.
                                      '<td width="1%">&nbsp;</td>'.
                                      '<td width="49%"><input name="total" type="text" id="total_id" readonly="readonly" value="'.$data_quote['total'].'" onchange="isNumeric(this);" /></td>'.
                                    '</tr>'.
                                  '</table>'.
                                '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.
                                    '<tr>'.
                                      '<td width="50%" height="25">&nbsp;</td>'.
                                      '<td width="1%">&nbsp;</td>'.
                                      '<td width="49%">&nbsp;</td>'.
                                    '</tr>'.
                                '</table>'.
                                '<table width="100%" border="0" cellpadding="0" cellspacing="0" id="ItemTbl">'.
                                  '<tr>'.
                                    '<td width="50%" height="25" align="right">&nbsp;</td>'.
                                    '<td width="1%">&nbsp;</td>'.
                                    '<td align="left">&nbsp;</td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right"> Ship Via:</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><select name="shipvia">'.
                                      '<option value="0">--- Select ------</option>';

									for($i=0;$i < count($data_ship_via); $i++)
									{
										if($data_quote['ship_via'] == $data_ship_via[$i]['ship_via_id'])
										$html.='<option selected="selected" value="'.$data_ship_via[$i]['ship_via_id'].'">'.$data_ship_via[$i]['shipvia'].'</option>';
										else
										$html.='<option value="'.$data_ship_via[$i]['ship_via_id'].'">'.$data_ship_via[$i]['shipvia'].'</option>';
									}
	                           $html.='</select></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Shipper #:<br /></td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left">'.
                              '<input name="other_shipper" id="other_shipper_id" type="text" value="'.$data_quote['shipperno'].'"';  if($data_quote['ship_to'] == 1) {$html.=' readonly="readonly" '; } $html.=' /></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Carrier:<br /></td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><select name="carrier">'.
                                      '<option value="0">--- Select Carrier------</option>';

									for($i=0;$i < count($data_carrier); $i++)
									{
										if($data_quote['carrier_id'] == $data_carrier[$i]['carrier_id'])
										$html.='<option selected="selected" value="'.$data_carrier[$i]['carrier_id'].'">'.$data_carrier[$i]['carrier'].'</option>';
										else
										$html.='<option value="'.$data_carrier[$i]['carrier_id'].'">'.$data_carrier[$i]['carrier'].'</option>';
									}

                                     $html.='</select></td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">Instructions/Notes: </td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left"><textarea name="instructions" style="width:200px;" rows="5" >'.htmlentities($data_quote['instruction_notes']).'</textarea></td>'.
                                 '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">&nbsp;</td>'.
                                   '<td>&nbsp;</td>'.
                                    '<td align="left">&nbsp;</td>'.
                                  '</tr>'.
                                  '<tr>'.
                                    '<td height="25" align="right">&nbsp;</td>'.
                                    '<td>&nbsp;</td>'.
                                    '<td align="left">'.
                                    '<input name="po_id" id="qid" type="hidden" value="'.$qid.'" />'.
                                    '<input name="save" type="button" value="Save" onclick="quote_check();" }/>'.
                                    '<input type="button" name="cancel" id="" value="Cancel" onclick="Fade();" /></td>'.
                                  '</tr>'.
                                '</table></td>'.
                           '</tr>'.

                          '</table></td>'.
                        '</tr>'.
                      '</table></td>'.
               '</tr>'.
              '</table>';
                                    //echo $html;
							 
$return_arr['html'] =$html;
echo json_encode($return_arr);
return;
?>