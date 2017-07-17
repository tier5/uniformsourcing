<?php
require('Application.php');
require('../../header.php');
$back_page = "project_mgm.list.php";
$paging = 'paging=1';
$closed = 0;
$tx='';
if(isset($_GET['close']))
$tx='_closed';
if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging = 'paging='.$_GET['paging'];
}
if(isset($_SESSION['page']))
{
	$current_page = $_SESSION['page'];
	$uri = '';
	switch($current_page)
	{
		case "reportGeneration.php":
		{
			if($_SESSION['page_type'] == 'report')
				$uri = $_SESSION['search_uri'];
			if($uri != "")
				$back_page = "../projectReport/reportGeneration.php$uri&$paging";
			else
				$back_page = "../projectReport/reportGeneration.php?$paging";
			
			$search_uri = $_SESSION['search_uri'];
			break;
		}
		case "project_mgm.list.php":
		{
			if($_SESSION['page_type'] == 'project_mgm')
				$uri = $_SESSION['search_uri'];
			if($uri != "")
				$back_page = "./project_mgm.list.php$uri&$paging";
			else
				$back_page = "./project_mgm.list.php?$paging";			
			$search_uri = "";
			break;
		}
		case "project_purchase.list.php":
		{
			if($_SESSION['page_type'] == 'project_purchase')
				$uri = $_SESSION['search_uri'];
			if($uri != "")
				$back_page = "./project_purchase.list.php$uri&$paging";
			else
				$back_page = "./project_purchase.list.php?$paging";
			$search_uri = "";
			break;
		}
		case "project_mgm.closed.php":
		{
			if($_SESSION['page_type'] == 'project_mgm')
				$uri = $_SESSION['search_uri'];
			if($uri != "")
				$back_page = "./project_mgm.closed.php$uri&$paging";
			else
				$back_page = "./project_mgm.closed.php?$paging";
			$search_uri = "";
			$closed = 1;
			break;
		}
		case "project_purchase.closed.php":
		{
			if($_SESSION['page_type'] == 'project_purchase')
				$uri = $_SESSION['search_uri'];
			if($uri != "")
				$back_page = "./project_purchase.closed.php$uri&$paging";
			else
				$back_page = "./project_purchase.closed.php?$paging";
			$search_uri = "";
			$closed = 1;
			break;
		}
	}
}

$is_session =0;
$emp_type ="";
$emp_id= "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =  $_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' style="visibility:hidden"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 5)) {
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id = $_SESSION['employeeID'];
//	$is_session = 1;
//	$style_price = ' disabled="disabled"';
}
$isEdit = 0;
$selectedtab = 0;
$pid= 0;
$patternId = 0;
$gradientId = 0;
$purchaseId = 0;
$sampleId = 0;
$pricingId = 0;
$elementId = 0;
$total_garment =0;
$status_query = 'status = 1';
if(isset($_GET['id']))
{
	$isEdit = 1;
	$status_query = 'status = 1';
	if($closed == 1)
		$status_query = 'status = 0';
	$pid = $_GET['id'];
}
$sql = "select tbl_prjorder_shipping.*,tbl_carriers.weblink from  tbl_prjorder_shipping left join tbl_carriers on tbl_carriers.carrier_id = tbl_prjorder_shipping.carrier_id where tbl_prjorder_shipping.status=1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_order_shipping[]=$row;
	}
	pg_free_result($result);
$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
}
pg_free_result($result);

$sql ='select firstname,lastname from "employeeDB" where "employeeID"='.$_SESSION["employeeID"].' and active =\'yes\'';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row_cnt = pg_fetch_array($result)){
		$data_employee_name=$row_cnt;
	}
	pg_free_result($result);
$sql="SELECT element_id, elements ".
		 "FROM tbl_elements ".
		 "WHERE status = '1' ".
		 "ORDER BY elements ASC ";
	if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_elements[]=$row;
}
pg_free_result($result);

$sql = "select id from tbl_prj_sample$tx where pid= $pid order by id";
if($sql !="")
{	
	if(!($result=pg_query($connection,$sql)))
	{
		$return_arr['error'] ="Error while storing sample information to database!";
		echo json_encode($return_arr);
		return;
	}
	
	while($row_cnt = pg_fetch_array($result)){
		$data_sample_present=$row_cnt;
	}
	if(count($data_sample_present)>0)
		$hdn_sample_present = 1;
	else
		$hdn_sample_present = 0;
}
/* carrier values*/
$sql = "select carrier_id,carrier_name  from  tbl_carriers where status=1";
if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_carrier[]=$row;
}
pg_free_result($result);	

 $sql = "Select id from tbl_prj_sample$tx where status=1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_sample[] = $row;
	}
	pg_free_result($result);
	if($data_sample[(count($data_sample)-1)]['id'] != "")
		$sampleId = $data_sample[(count($data_sample)-1)]['id'];
        
    /*    
 	$sql = 'Select "package","element_id" from "tbl_element_package" ';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql;
	while($row_elm = pg_fetch_array($result)){
		$data_package[] =$row_elm;
	}     */
?>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tabcontent.js"></script>

<table width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td align="left">
    		<input type="button" value="Back" onclick="location.href ='<?php echo $back_page;?>'" />
  		</td>  
  		<td>&nbsp;</td>
  	</tr>
</table>
<center>
<table width="60%">
	<tr>
		<td> <div style="height:30px;overflow:hidden;">
        <div align="center" id="message"></div>
        </div>
			
			<img id="loading" src="<?php echo $mydirectory;?>/images/loading.gif" style="display:none;">
			<input type="hidden" id="saveid" <?php if(isset($_GET['sav'])){ echo 'value="1"';}else{echo 'value="0"';}?> />
		</td>
	</tr>
</table>
</center>
<center>
<blockquote>
<font face="arial">
<font face="arial" size="+2"><b>Project Add</b></font>
</font>
</blockquote>
</center>
<input type="hidden" id="pid" name="pid" value="<?php echo $pid;?>" />
<input type="hidden" id="hdn_sample_present" name="hdn_sample_present" value="<?php echo $hdn_sample_present;?>" />
<input type="hidden" id="status_query" name="status_query" value="<?php echo $status_query;?>" />
<input type="hidden" id="isEdit" name="isEdit" value="<?php echo $isEdit;?>" />
<input type="hidden" id="currentTab" name="currentTab" value="0" />

<ul id="countrytabs" class="shadetabs">
<li onClick="javascript:load_tab('0'); document.getElementById('message').innerHTML = '';" ><a href="#" rel="proj_content0" class="selected">Basic</a></li>
<li onClick="javascript:load_tab('1'); document.getElementById('message').innerHTML = '';"><a href="#" rel="proj_content1">Purchase</a></li>
<?php 
if($emp_type != 2&&$emp_type != 1)
{
?>
<?php if($emp_type != 5): ?>
<li onClick="javascript:load_tab('2'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content2">Vendor</a></li>
<li onClick="javascript:load_div('14'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content14">BID</a></li>
<?php endif; ?>
<li onClick="javascript:load_tab('3'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content3">Samples</a></li>
<?php
}
?>
<li onClick="javascript:load_tab('4'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content4">Pricing</a></li>
<?php 
//if($emp_type != 1)
{
?>
<li onClick="javascript:load_tab('5'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content5">Production Milestone</a></li>
<?php
}
if($emp_type != 2)
{
?>
<li onClick="javascript:load_tab('6'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content6">Notes</a></li>
<?php
} 
if($emp_type != 2)
{
?>
<li onClick="javascript:load_tab('7'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content7">Elements</a></li>
<?php
}
?>
<li onClick="javascript:load_tab('8'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content8">Order & Shipping</a></li>
<?php 
if($emp_type != 2)
{
?>
<li onClick="javascript:load_tab('9'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content9">Uploads</a></li>
<?php 
}?>

<!--<li onClick="javascript:load_tab('13'); document.getElementById('message').innerHTML = '';"><a href="#" rel="proj_content13">Styles</a></li>
<?php /*if($emp_type != 5): */?>
<li onClick="javascript: load_div('12'); document.getElementById('message').innerHTML = '';"><a href="#" rel="proj_content12">Inventory</a></li>
--><?php
/*endif;*/
if($emp_type != 1 && $emp_type != 2 && $emp_type != 5)
{
?>
<li onClick="javascript:load_tab('10'); document.getElementById('message').innerHTML = ''; "><a href="#" rel="proj_content10">Notification</a></li>
<li onClick="javascript:load_tab('11'); document.getElementById('message').innerHTML = '';"><a href="#" rel="proj_content11">CSR/VSR</a></li>
<?php
}
?>
</ul>
<div style="clear:left;"></div>
<?php 
$adminURL = curPageURL();
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/"));
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/")+1);
?>
<form id="validationForm" name="validationForm">

<input type="hidden" id="proj_0" name="proj_0" value="0" />
<input type="hidden" id="proj_1" name="proj_1" value="0" />
<input type="hidden" id="proj_2" name="proj_2" value="0" />
<input type="hidden" id="proj_14" name="proj_14" value="0" />
<input type="hidden" id="proj_3" name="proj_3" value="1" />
<input type="hidden" id="proj_4" name="proj_4" value="0" />
<input type="hidden" id="proj_5" name="proj_5" value="0" />
<input type="hidden" id="proj_6" name="proj_6" value="0" />
<input type="hidden" id="proj_7" name="proj_7" value="0" />
<input type="hidden" id="proj_8" name="proj_8" value="0" />
<input type="hidden" id="proj_9" name="proj_9" value="0" />
<input type="hidden" id="proj_10" name="proj_10" value="0" />
<input type="hidden" id="proj_11" name="proj_11" value="0" />
<input type="hidden" id="proj_12" name="proj_12" value="0" />
<input type="hidden" id="proj_13" name="proj_13" value="0" />

<input  type="hidden" id="hidden_client_shipper" value="" />
<input type="hidden" id="hdn_track_update" name="hdn_track_update" value="" />

<div style="border:1px solid gray; width:132%;  margin-bottom: 1em; padding: 10px 10px 10px 10px; min-height:250px;">

<div id="processing" style="display:none; text-align:center; position:absolute; width:900px; z-index:100;">

<img src="../../images/animation_processing.gif" width="200" height="200" alt="processing" />

</div>

<br/>

<div id="proj_content0" align="center" class="tabcontent"></div>

<div id="proj_content1" align="center" class="tabcontent"></div>

<div id="proj_content2" align="center" class="tabcontent"></div>

<div id="proj_content14" align="center" class="tabcontent"></div>

<div id="proj_content3" align="center" class="tabcontent">
<div class="vmenu">
<ul id="sample_tab" class="vertabs">
<li onClick="javascript:loadSamples(0,0);"><a href="javascript:void(0);" rel="sample_tab0" >Add New Sample</a></li>
<?php

for($i = 0; $i < count($data_sample); $i++){
	if($sampleId == $data_sample[$i]['id'])
	{	
		echo '<li onClick="javascript:loadSamples('.$data_sample[$i]['id'].','.($i+1).');"><a href="javascript:void(0);" rel="sample_tab'.($i+1).'" class="selected" >Sample - '.($i+1).'</a></li>';
		$selectedtab = $i+1;
	}
	else
		echo '<li onClick="javascript:loadSamples('.$data_sample[$i]['id'].','.($i+1).');"><a href="javascript:void(0);" rel="sample_tab'.($i+1).'" >Sample - '.($i+1).'</a></li>';
}
?>
</ul>
</div>
<div id="sample_div" >
<div id="sample_tab0" class="tabcontent"></div>
<?php
for($i = 0; $i < count($data_sample); $i++)
{
	echo '<div id="sample_tab'.($i+1).'" class="tabcontent"></div>';
}
?>
</div> 
<input type="hidden" id="sample_count" value="<?php echo count($data_sample); ?>" /> 
<input type="hidden" id="current_sample" value="0" />
<input type="hidden" id="sample_count" value="0" />
        <!--add time-->
        <div id="samplePop" class="popup_block">
<center><div ><strong>Sample Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><textarea id="notes" name="notesId[]" cols="60" rows="10"></textarea></td>
    </tr>
    <tr>
        <td align="center"><input type="button" name="notesSubmit[]" id="notesSubmit" value="Submit" onclick="javascript:sampleNotesSubmit('sample_notes',document.getElementById('notes'));Fade();" /><input type="hidden" id="sample_employee_name" value="<?php echo $data_employee_name['firstname'].$data_employee_name['lastname'];?>" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="sampleEditPop" class="popup_block">
<center><div ><strong>Sample Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">


<tr id="tr_sample_popEmpId" style="display:none">
<td width="100px" align="left"><strong>Added By : </strong></td>
<td width="5px">&nbsp;</td><td align="left" id="sample_td_popEmpId"></td>
</tr>
<tr id="tr_sample_popDateTimeId" style="display:none">
<td width="100px" align="left">
<strong>Added Date : </strong></td>
<td width="5px">&nbsp;</td>
<td align="left" id="td_sample_popDateTimeId"></td>
</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>



<tr>
<td width="100px" align="left"><strong>Notes :</strong></td>
<td>&nbsp;</td>
</tr>
  <tr>
    <td width="100"  align="left"><p id="edit_sample_PopId"></p></td>
    <td width="10">&nbsp;</td>
    
  </tr>
</table>
</div>



<div id="generatePO_div" class="popup_block" style="height:500px; width:600px;" ><div id="generatePO_div1" style="height:500px;overflow:scroll;"></div> </div>
	  <p> </p>
</div>

<div id="proj_content4" align="center" class="tabcontent"></div>

<div id="proj_content5" align="center" class="tabcontent"></div>

<div id="proj_content8" align="center" class="tabcontent"></div>

<div id="proj_content9" align="center" class="tabcontent"></div>

<div id="proj_content7" align="center" class="tabcontent"></div>


<div id="proj_content10" align="center" class="tabcontent"></div>

<div id="proj_content6" align="center" class="tabcontent"></div>
<div id="proj_content11" align="center" class="tabcontent"></div>
<div id="proj_content12" align="center" class="tabcontent"></div>
<div id="proj_content13" align="center" class="tabcontent"></div>
</div>
<table align="center">
<tr>
<td>
<input type="submit" id="submitButton" name="submitButton" value="Save" <?php if(isset($_GET['close'])) echo ' disabled="disabled" ';?> />
<input type="reset" id="reset" name="reset" value="Cancel" <?php if(isset($_GET['close'])) echo ' disabled="disabled" ';?>/>
</td>
</tr>
</table>

</form>
<input type="hidden" id="shipping_status" >
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script src="project.js" type="text/javascript"></script>
<?php 
if($isEdit)
{
echo '<script type="text/javascript">';

echo '</script>';
}
?>
<script type="text/javascript"> 
    function showCloseLink()
    {
        $("#del_img").show();
    }
    function viewPackFiles()
    {
		
		if($("#upload_pack").val()==0)
		return;
        var data=$("#upload_pack").val();
		
		flag=0;
			$('input[name="upload_packages[]"]').each(function(){
if($(this).val()==$("#upload_pack").val())
{
	
	flag=1;
}
});
  
  if(flag==1)
  {
	  alert("This Style Number is already selected...");
	  return;
  }
     $.ajax({
		   type: "POST",
		   url: "viewPackFiles.php",
		   data: {"pack_id":data},
		   timeout:60000,
		   success:function(data)
			{
           $("#pack_view").append(data);                
			}
			});
		
    }
    
    
    
var vtab = null;
var message_display = null;
$().ready(function() {
var countries=new ddtabcontent("countrytabs");
countries.setpersist(false);
countries.setselectedClassTarget("link");
countries.init();
load_div('0');
document.getElementById('hdn_track_update').value = 0;

vtab=new ddtabcontent("sample_tab");
vtab.setpersist(false);
vtab.setselectedClassTarget("link");
vtab.init();

<?php 
if($selectedtab > 0)
{
	echo "loadSamples('$sampleId','$selectedtab');";
}
else
{
	echo "loadSamples(0,0);";
}
?>
});
function show_msg()
{
	window.message_display = setInterval(function() {
  $("#message").fadeOut(1600,remove_msg);  
}, 6000);
}
function remove_msg()
{
	$("#message").html('');
	$("#message").fadeIn();
	clearInterval(window.message_display);
	window.message_display = null;
}
function show_weblink(obj, index)
{
	var sel = obj.options[obj.selectedIndex].value;
	if(obj.options[obj.selectedIndex].text !='Hand Delivered')
	{
		document.getElementById('processing').style.display= '';
                $("#d_date"+index).hide();
                $("#d_by"+index).hide();
                $(".ship_fields"+index).show();
		var dataString = "carrier_id="+sel+'&index='+index;
		$.ajax({
		 type: "POST",
		 url: "prj_carrier_weblink.php",
		 data: dataString,
		 dataType: "json",
		 timeout:60000,
		 success : function(data)
		 {
			 document.getElementById('processing').style.display= 'none';
			 if(data!=null)
			 {
				 if(data.error)
				 {				 
					 $("#message").html("<div class='errorMessage'><strong>Sorry, "+data.error +"</strong></div>");
					 show_msg();
				 }
				 else
				 {
					  $("#weblink_id"+data.index).html("");
					 if(data.weblink != "" && data.index >= 0)
					 {					
						  $("#weblink_id"+data.index).html('<div><a href="javascript:void(0);" onClick="javascript:popupWindow(\''+data.weblink+document.getElementById('track_num'+data.index).value+'\');" ><img src="<?php echo $mydirectory; ?>/images/courier_man.jpg" width="50" height="30px"/></a></div>');
					 }
				 }
			 }
			 else								   
			 {
				 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				 show_msg();
			 }
		 },
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			document.getElementById('processing').style.display= 'none';
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
			}
		 });
	}
	else
	{
        
		$("#weblink_id"+index).html("");
                $("#d_date"+index).show();
                $("#d_by"+index).show();
                $(".ship_fields"+index).hide();
	}
	return false;
}
</script>
<script type="text/javascript">
    //function to add packages when click the select box in elements tab 
    function SelectElementFields(id){
		flag=0;
    			$('input[name="element_packages[]"]').each(function(){
if($(this).val()==id)
{
	
flag=1;
}
});

if(flag==1)
{
	alert("This package is already selected...");
return;
}
$.ajax({
           type: 'POST',
           url: 'viewPackElements.php',
           datatype: 'json',
           data: {"pack_id":id},
           success: function(data){
               $("#pack_content").append(data.res);
			   //$("#pack_content").append('<input type="text" name="element_packages[]" value="'+i+'>');
			   
           }        
           });
	
    }
function AddElement(){
    var in_htm="";
	var table = document.getElementById('content_table');
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
        row.setAttribute('id','element'+rowCount);
	var count = (rowCount*2)+100;
        img_count = count+1;
	var cell1 = row.insertCell(0);        
	cell1.align="center";
      
//alert(count);

in_htm += '<input type="image" class="deleteTd" src="../../images/delete.png" onclick=" DeleteUploads(\'\',\''+ ++count +'\',\'\',\'I\',\'editTime\'); DeleteUploads(\'\',\''+ ++count +'\',\'\',\'F\',\'editTime\');"> <table width="80%" ></td></tr>';
in_htm+='<tr ><td valign="top"><table cellpadding="1" cellspacing="1" border="0">';

in_htm+='<tr><td align="right">Element Type:</td><td align="left"><select id="element_type'+count+'" onChange="showlocation('+count+');" name="elementtype[]"><?php	
for($i=0; $i < count($data_elements); $i++){?><option value="<?php echo $data_elements[$i]['element_id'];?>"><?php
echo $data_elements[$i]['elements'];?></option><?php
} ?></select></td></tr>';

in_htm+=' <tr id="location_tr'+count+'" >';
    in_htm+='  <td align="right">Location:</td>';
    in_htm+='  <td align="left"><input type="text" name="location[]" value=""  id="location'+count+'"  /></td>';
     in_htm+='</tr>';
     in_htm+='<tr id="yield_tr'+count+'" style="display:none;" >';
   in_htm+='  <td align="right">Yield:</td>';
    in_htm+=' <td align="left"><input type="text" name="yield[]"  value="" id="yield'+count+'"  /></td>';
    in_htm+='</tr>'; 

in_htm+='<tr><td align="right">Vendor:</td><td align="left"><select id="element_vendor" name="vendor_ID[]">	<?php
for($i=0; $i < count($data_Vendr); $i++){?><option value="<?php echo $data_Vendr[$i]['vendorID']; ?>"><?php
echo $data_Vendr[$i]['vendorName'];?></option><?php
} ?></select></td></tr>'
	  +'<tr><td align="right">Quantity:</td><td align="left"><input type="text" name="elemquantity[]" id="elemquantity" /></td></tr>'
	  +'<tr><td align="right">Style:</td><td align="left"><input type="text" name="elementstyle[]" '
      +'id="elementstyle" value="" /></td></tr><tr><td align="right">Color:</td><td align="left">'
      +'<input type="text" name="elementcolor[]" id="elementcolor" value="" /></td></tr><tr>'
      +'<td align="right">Cost:</td><td align="left"><input type="text" name="elementcost[]" id="elementcost" value="" />'
      +'</td></tr>'
	  +'<tr><td align="right">Labor:</td><td><input type"text" name="elementlabor[]" id="elementlabor" /></td></tr>'
	  +'<tr><td align="right">Order Date:</td><td align="left"><input type="text" name="order_date[]" onclick="javascript:showDate(this);" value="" /></td></tr>'
	   +'<tr><td align="right">Confirmation Number:</td><td><input type"text" name="element_conf_num[]" id="element_conf_num" /></td></tr>'
	   +'<tr><td align="right">Tracking Number:</td><td><input type"text" name="element_track_num[]" id="element_track_num" /></td></tr>'
	   +'<tr><td align="right">Delivered:</td><td><input type="checkbox" name="element_delivered[]" id="element_delivered"  /></td></tr>'
	  +'<tr><td align="right">Image:</td><td align="left"><input type="file" name="file'+ --count +'" id="file'+ count 
      +'" onchange="javascript:ajaxFileUpload('+ count +', \'I\', 960,720);" /><input type="hidden" id="file_name'+ count +
      '" name="element_file0[]" value=""/><input type="hidden" id="upload_type'+ count +
      '" name="element_type0[]" value="I"/><input type="hidden" id="upload_id'+ ++count +
      '" name="element_id0[]" value=""/></td></tr><tr><td align="right">File:</td><td align="left"><input type="file" name="file'+ count +
      '" id="file'+ count +'" onchange="javascript:ajaxFileUpload('+ count +', \'F\', 960,720);" /><input type="hidden" id="file_name'+ count +
      '" name="element_file1[]" value=""/><input type="hidden" id="upload_type'+ --count +
      '" name="element_type1[]" value="F"/></td></tr></tr></table><input type="hidden" id="element_id" name="element_id[]" value="0"/>'+
      '<input type="hidden" id="image_from'+img_count+'" name="image_from[]" value="CURR"/><input type="hidden" id="file_from'+
      (img_count+1)+'" name="file_from[]" value="CURR"/></td><td valign="top" align="right">'+
      '<table  border="0" cellspacing="0" cellpadding="0"><tr id="tr_id'+ count +
      '" style="display:none;"><td><strong>Image:</strong><br/><img id="img_file'+ count +
      '" width="101px" height="89px" src="" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" >'+
      '<a id="del_img" style="cursor:hand;cursor:pointer;" onClick=" javascript: DeleteUploads(\'\',\''+ count +
      '\',\'\',\'I\',\'editTime\'); document.getElementById(\'tr_id'+ count +
      '\').style.display=\'none\'; document.getElementById(\'file_name'+ ++count +'\').value=\'\'; " >'+
      '<img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete" /></a></td>	</tr><tr id="tr_id'+ count +
      '" style="display:none;" ></tr></table></td></tr></table>';
//alert(in_htm);
cell1.innerHTML= in_htm;	
}

 function showlocation(i)
{	
//alert($('#element_type'+i).val());
elm=$('#element_type'+i+' option:selected').text();

if(elm=="Fabric")
{
$('#location_tr'+i).hide();
$('#yield_tr'+i).show();
$('#location'+i).val('');
$('#yield'+i).focus();
}
else if(elm=="Cups & Pads"||elm=='Boning'||elm=='Buttons'||elm=='CMT'||
elm=='Grade'||elm=='Lap Dip'||elm=='Liner'||elm=='Marker'
||elm=='Packaging'||elm=='Pattern'||elm=='Underwire')
{
  $('#location_tr'+i).hide();
$('#yield_tr'+i).hide();
$('#yield'+i).val('');
$('#location'+i).val('');
}
else 
{
    $('#location_tr'+i).show();
$('#yield_tr'+i).hide();
$('#yield'+i).val('');
$('#location'+i).focus();
}
}
$(".deleteTd").live('click', function(event) {
        $(this).parent().parent().remove();
});

</script>
<script type="text/javascript">

function onNotesSubmit(tableId)
{
if($('#prj_note_type:checked').val()=='int' || $('#prj_note_type:checked').val()=='ext'){}else{alert('Please select a type..');return;}
	var textId = document.getElementById('prj_notes');
	var title = document.getElementById('title_id');
	var emp_name = document.getElementById('employee_name');
	var table = document.getElementById(tableId);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	cell1.width="50px";		
	cell1.innerHTML = "Notes "+rowCount+":";	
	var cell2 = row.insertCell(1);
	cell2.width="10px";		
	cell2.innerHTML = "&nbsp;";	
	
	
	var cell3 = row.insertCell(2);
	cell3.width="150px";	
	var d = new Date();
	var date = d.getDate();
	date<10 ? date = '0'+date : date;
	var month = d.getMonth();
	month+=1;
	month<10 ? month = '0'+month : month;
	var year = d.getFullYear();
	var hours = d.getHours();
	var minutes = d.getMinutes();
	minutes<10 ? minutes = '0'+minutes : minutes;
	cell3.innerHTML = month+'-'+date+'-'+year+"   "+hours+':'+minutes;
	
	
	var cell8 =row.insertCell(3);
	cell8.width ="100px";
	var element0 = document.createElement("label");
	element0.innerHTML = emp_name.value;
	cell8.appendChild(element0);


	var cell9 =row.insertCell(4);
	cell9.width = "150px";
	var element9 = document.createElement("label");

	var strong_tag =document.createElement("strong");	
	element9.innerHTML =strong_tag;
	strong_tag.innerHTML = title.value;
	cell9.appendChild(strong_tag);
		
	var cell7 = row.insertCell(5);
	cell7.width ="150px";
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "Read..";
	element1.onclick = function(){popOpen(rowCount,'EN');};
	cell7.appendChild(element1);
	
	
	var cell4 = row.insertCell(6);
	cell4.width="10px";
	cell4.innerHTML = "&nbsp;";
	
	var cell5 = row.insertCell(7);
	var element2 = document.createElement("textarea");
	element2.name = "textAreaName[]";
	element2.id = 'txtAreaId'+rowCount;
	element2.value = textId.value;
	element2.style.display = "none";
	
	var cell6 = row.insertCell(8);
	var element3 = document.createElement("input");
	element3.name = "title_name[]";
	element3.id = 'title_Id'+rowCount;
	element3.value = title.value;
	element3.style.display = "none";
	
	var element4 = document.createElement("input");
	element4.name = "hdnNotesName[]";
	element4.id = 'hdnNotesId'+rowCount;
	element4.value = 0;
	element4.style.display = "none";
        
        var element5 = document.createElement("input");
	element5.name = "prj_note_type[]";
	element5.id = 'prj_note_type'+rowCount;
	element5.value =$('#prj_note_type:checked').val();
    
	element5.style.display = "none";
	
	cell5.appendChild(element2);
	cell5.appendChild(element4);	
	cell6.appendChild(element3);
        cell6.appendChild(element5);
  $('.prj_note_type').removeAttr('checked');     
        
}
</script>
<script type="text/javascript">
    
function popOpen(rowIndex,type)
{  
	if(type == 'generatePO'){
		var popID = 'generatePO_div';
	}
	else if(type=='AN')
	{ 
		var popID = 'textPop'; //Get Popup Name
	}
	else if(type=='EN')
	{      
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML = document.getElementById('txtAreaId'+rowIndex).value;		
		document.getElementById('edit_title').innerHTML = document.getElementById('title_Id'+rowIndex).value;	
 if(document.getElementById('prj_note_type'+rowIndex).value=='int' || document.getElementById('prj_note_type'+rowIndex).value=='i')
                                {document.getElementById('editprjnote').innerHTML ='Internal';}
                                else if(document.getElementById('prj_note_type'+rowIndex).value=='ext' || document.getElementById('prj_note_type'+rowIndex).value=='e')
                                 {
                 document.getElementById('editprjnote').innerHTML ='External';                    
                                 } 
                           
		<?php 
		if($isEdit )
		{
		?>
                            
			var notesid = document.getElementById('hdnNotesId'+rowIndex).value;
			if(notesid >0 )
			{
                              
				document.getElementById('td_popEmpId').innerHTML =document.getElementById('empNameId'+rowIndex).value;
				document.getElementById('td_popDateTimeId').innerHTML =document.getElementById('dateTimeId'+rowIndex).value;
                                //alert(document.getElementById('prj_note_type'+rowIndex).value);                            
                                if(document.getElementById('prj_note_type'+rowIndex).value=='int' || document.getElementById('prj_note_type'+rowIndex).value=='i')
                                {document.getElementById('editprjnote').innerHTML ='Internal';}
                                else if(document.getElementById('prj_note_type'+rowIndex).value=='ext' || document.getElementById('prj_note_type'+rowIndex).value=='e')
                                 {document.getElementById('editprjnote').innerHTML ='External';}   
				document.getElementById('tr_popEmpId').style.display = '';
				document.getElementById('tr_popDateTimeId').style.display = ''; 
                               
			}
			else
			{ 
                    
				document.getElementById('td_popEmpId').innerHTML = "";
				document.getElementById('td_popDateTimeId').innerHTML = "";
                              //  document.getElementById('editprjnote').innerHTML = "";
				document.getElementById('tr_popEmpId').style.display = 'none';
				document.getElementById('tr_popDateTimeId').style.display = 'none';
			}
		<?php 
		}
		?>
	}
	else if(type =='PEC')
	{
		var popID = 'prj_estimatecost';
	}
	else if(rowIndex == null || rowIndex == "")
	{
		var popID = 'samplePop'; //Get Popup Name
	}
	else if(type =='SAMPLE'){
		var popID = 'sampleEditPop';
		document.getElementById('edit_sample_PopId').innerHTML = document.getElementById('sampletxtAreaId'+rowIndex).value;
		<?php 
		if($isEdit )
		{
		?>
			var sample_notesid = document.getElementById('hdn_sample_notesId'+rowIndex).value;
			if(sample_notesid >0 )
			{
				document.getElementById('sample_td_popEmpId').innerHTML = document.getElementById('empNameId'+rowIndex).value;
				document.getElementById('td_sample_popDateTimeId').innerHTML = document.getElementById('dateTimeId'+rowIndex).value;
				document.getElementById('tr_sample_popEmpId').style.display='';
				document.getElementById('tr_sample_popDateTimeId').style.display='';
			}
		<?php 
		}
		?>
		
	}
	
	popWidth = '600'; $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
			
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
	if(document.validationForm.notesId && document.validationForm.notes_title){
		document.validationForm.notesId.value="";
		document.validationForm.notes_title.value="";
	}
	if(document.getElementById("notes")){
		document.getElementById("notes").value="";
	}
}
function popupWindow(type) 
{
	var url = "";
	if(type == 'sample')
	{
	var hdn = document.getElementById('hdn_sampleNum').value;
	 if(hdn != "")
	  url = "<?php echo $adminURL;?>samplerequest/samplerequest_new.add.php?id="+hdn;
	 else
	  return;
	}
	else
	{
		url = type;
	}
 params  = 'width='+screen.width;
 params += ', height='+screen.height;
 params += ', top=0, left=0'
 params += ', fullscreen=yes';
 params += ', scrollbars=yes';

 newwin=window.open(url,'windowname4', params);
 if (window.focus) {newwin.focus()}
 return false;
}
</script>

<script type="text/javascript"> 
  
    function  deleteCustomStyle(style_id)
    {
        $('#processing').show();
           $.ajax({
           type: 'POST',
           url: 'getInventoryStyles.php?opt=delete',
           data:{"style_id":style_id},
           dataType:'json',
           success: function(data){
          
           if(data.success!="")
               $("#message").html("<div class='successMessage'><strong>"+data.success+"</strong><div>");
           $('#processing').hide();
                load_div('13'); 
           
           }
           
           });
    }
    function IsNumeric(strString) {

    var strValidChars = "0123456789";
   var strChar;
   var blnResult = true;

   if (strString.length == 0) return false;

   //  test strString consists of valid characters listed above
   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         {
         blnResult = false;
         }
      }
   return blnResult;

}

function pullFromInvButton()
{
  
    if($('#pid').val()=="")
        {
            alert("Save the project and continue");
            return;
        }
    data="pid="+$('#pid').val();
    $.ajax({
        
           type: 'POST',
           url: 'getInventoryData.php' ,
           data: data,
           dataType:'json',
           success: function(data){
             $("#inv_content").html(data.html);        
           }
           
           });      

}

function pullFromInv_style(style_id,obj)
{
    data="style_id="+style_id;
    
  if(obj.attr("checked")==true)
      {
  opt="pull_from_inv";
      }
    else if(obj.attr("checked")==false)
    {
   if(confirm("Unckeck Pull From Inventory will cause the lose of inventory data...  Do you want to do this ?"))
   {
    opt="leave_from_inv";   
   }
    }
    
  $.ajax({
        
           type: 'POST',
           url: 'getInventoryStyles.php?opt='+opt ,
           data:data,
           dataType:'json',
           success: function(data){
               load_div('13');
                     
           }
           
           });      


}



    function addStyleRow()
    {
   
        if($.trim($('#style_custom').val())==""){
            alert("Plase select a Style first...");
            return;
        }
       
   if($.trim($("#qty_custom").val())=="" || !IsNumeric(document.getElementById("qty_custom").value ) )
       {
     alert("Plase enter a numeric quantity...");    
     return;
       }
      $('#processing').show();
        data="style="+$('#style_custom').val()+"&color="+$('#color_custom').val()+"&size="+$('#size_custom').val()
        +"&length="+$('#len_custom').val()+"&qty="+$('#qty_custom').val()+"&pid="+document.getElementById("pid").value;
    
   // if($('#height_custom').val()!=null&&$('#height_custom').val()!="")
       data+="&height="+$('#height_custom').val();
  // else
       //data+="&height=-1";
        $.ajax({
           type: 'POST',
           url: 'getInventoryStyles.php?opt=save',
           data:data,
           dataType:'json',
           success: function(data){
               if(data.error!=''){
                   $("#message").html("<div class='errorMessage'><strong>"+data.error+"</strong><div>");
                   show_msg();
                }
                else{
                    $("#message").html("<div class='successMessage'><strong>"+data.success+"</strong><div>");
                   show_msg();
                }
                 $('#processing').hide();
                load_div('13');           
           }
           
           });
           
           
           
    }
   function style_opt(obj)
    {
     $.ajax({
           type: 'POST',
           url: 'getInventoryStyles.php?opt=all',
           data:{"styleid":obj.val()},
           dataType:'json',
           success: function(data){
            
              $("#color_custom").html(data.color);
              $("#size_custom").html(data.size);
              $("#len_custom").html(data.length);
              $("#height_custom").html(data.height);
           
           }
           
           });
    } 


function multipleStyle(id,type)
{
	var table = document.getElementById(id);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);
	var cell6 = row.insertCell(5);
	
	row.height = "25px";
		
	var element1 = document.createElement("input");
	element1.type = "text";	
	element1.name = "style[]";
	element1.id = "style"+rowCount;
	element1.setAttribute('onchange','javascript:isUnique('+rowCount+');');
	
	var element8 = document.createElement("input");
	element8.type = "text";	
	element8.name = "vendor_style[]";
	element8.id = "vendor_style"+rowCount;
	element8.setAttribute('onchange','javascript:isUnique('+rowCount+');');
	
	var element2 = document.createElement("input");
	element2.type = "text";	
	element2.name = "garments[]";
	element2.id = "garments"+rowCount;
	element2.setAttribute('onchange','javascript:<?php if($emp_type != 1){ echo "purchaseOrderCalculation('+rowCount+',1);"; } ?> isNumeric(this);');
	
	<?php if($emp_type != 1){
	echo 'var element3 = document.createElement("input");
	element3.type = "text";	
	element3.name = "priceunit[]";
	element3.id = "priceunit"+rowCount;
	element3.setAttribute(\'onchange\',\'javascript:purchaseOrderCalculation(\'+rowCount+\',1); isNumeric(this);\');
	
	var element4 = document.createElement("input");
	element4.type = "text";	
	element4.name = "retailprice[]";
	element4.id = "retailprice"+rowCount;
	element4.setAttribute(\'onchange\',\'javascript:purchaseOrderCalculation(\'+rowCount+\',1); isNumeric(this);\'); '; }?>
	
	var element5 =  document.createElement("input");
	element5.type = "hidden";	
	element5.name = type+"_id[]";	
	element5.value = 0;
	
	var element6 =  document.createElement("input");
	element6.type = "hidden";	
	element6.id = "rowSum"+rowCount;	
	element6.value = 0;

	var element7 =  document.createElement("input");
	element7.type = "hidden";	
	element7.id = "row_sum_target"+rowCount;	
	element7.value = 0;
	
	cell1.appendChild(element1);
	cell2.appendChild(element8);
	cell3.appendChild(element2);
	<?php if($emp_type != 1){
	echo 'cell4.appendChild(element3);
	cell5.appendChild(element4);'; } ?>
	cell4.appendChild(element5);
	cell4.appendChild(element6);
	cell4.appendChild(element7);
	cell6.innerHTML = "&nbsp;<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentNotesRow(this);\">Delete</a>";	
	cell6.align = "left";
	
}
function isUnique(rowCount){ 
	var flag=0;
	for(i=1;i<rowCount;i++){
		if(document.getElementById('style'+rowCount).value == document.getElementById('style'+i).value){
			flag++;
		}
	}
	/*if(flag>0){
		document.getElementById('style'+rowCount).value = '';
		alert("Sorry, Style already exist..");
		document.getElementById('style'+rowCount).focus();
	}*/
}
function calculateProjectCost(count)
{	
	var multiplyVal =0;
	var multiply_target_val =0;
	var total_sum = 0;
	var total_garments = 0;
	var estimated_cost = 0;	
	var completion_cost = 0;
	var hdn_unit_val =0;
	var hdn_target_val = 0;
	
	var shipping_cost = {value:'0'};
	var tax_cost = {value:'0'};
            if(document.getElementById('shipping_cost').value!="" )
		shipping_cost = document.getElementById('shipping_cost');
	if(document.getElementById('taxes').value!="")
		tax_cost = document.getElementById('taxes');
	var garments = document.getElementById('garments'+count);
	var priceunit = document.getElementById('priceunit'+count);
	var retailprice = document.getElementById('retailprice'+count);
	if(document.getElementById('style'+count))
		isUnique(count);
	if(garments.value >0 && priceunit.value >0)
		multiplyVal =  parseFloat(garments.value) *  parseFloat(priceunit.value);
	else 
		multiplyVal = 0;
	if(garments.value >0 && retailprice.value >0)
		multiply_target_val =  parseFloat(garments.value) *  parseFloat(retailprice.value);
	else 
		multiply_target_val = 0;
	document.getElementById('rowSum'+count).value = multiplyVal;
	document.getElementById('row_sum_target'+count).value = multiply_target_val;
	var total_count = document.getElementById('tbl_style').rows.length;
	for(var i=1; i<total_count; i++)
	{
		if(!isNaN(document.getElementById('rowSum'+i).value))
			total_sum += parseFloat(document.getElementById('rowSum'+i).value);
		if(!isNaN(document.getElementById('rowSum'+i).value))
			hdn_unit_val += parseFloat(document.getElementById('rowSum'+i).value);
		if(!isNaN(document.getElementById('row_sum_target'+i).value))
			hdn_target_val += parseFloat(document.getElementById('row_sum_target'+i).value);
		if(!isNaN(document.getElementById('garments'+i).value))
			total_garments += parseFloat(document.getElementById('garments'+i).value);
	}
	document.getElementById('hdn_unit_mulprice').value = hdn_unit_val;
	document.getElementById('hdn_target_mulprice').value = hdn_target_val;
	document.getElementById('hdn_garmenttotal').value = total_garments;
	document.getElementById('pcost').value = total_sum;
	
	cal_val =parseFloat(shipping_cost.value) + parseFloat(tax_cost.value);
	completion_cost = total_sum + cal_val;
	document.getElementById('pcompcost').value = completion_cost;
	if(total_garments >0 && !isNaN(total_garments))
	{							   
		estimated_cost = parseFloat(document.getElementById('pcost').value) / parseFloat(total_garments );
		document.getElementById('pestimate').value = estimated_cost.toFixed(2);
	}
	costProfit();
}
function costProfit()
{
	var completion_cost={value:'0'};
	var prestprft =0;
	var p_target_cost = document.getElementById('hdn_target_mulprice');
	if(document.getElementById('pcompcost').value !="")
		completion_cost= document.getElementById('pcompcost');
	if(completion_cost.value!="" && p_target_cost.value!="")
	{
		prestprft=p_target_cost.value - completion_cost.value;	
	}
	
	document.getElementById('pestprofit').value = prestprft.toFixed(2);
}

function isNumeric(id){
	string = id.value;
	flag = isNaN(string);
	if(flag){
		id.value = '';
		alert("Enter a valid number");
		id.focus();
	}
}
function calculateProjectComCost()
{
	var target_calculated_val = 0;
	var unit_calculated_val = 0;
	var pcom_cost = 0;
	var shipping_cost = 0;
	var tax_cost = 0;
	var pcost =0;
	
	if(!isNaN(document.getElementById('shipping_cost').value)&& document.getElementById('shipping_cost').value!="" )
	{
		ship_val = document.getElementById('shipping_cost');
		shipping_cost =ship_val.value;
	}
	if(!isNaN(document.getElementById('taxes').value)&& document.getElementById('taxes').value!="" )
	{
		tax_val = document.getElementById('taxes');
		tax_cost = tax_val.value;
	}
	if(document.getElementById('hdn_target_mulprice').value!="" )
		target_calculated_val = document.getElementById('hdn_target_mulprice').value;
	if(document.getElementById('hdn_unit_mulprice').value!="" )
		unit_calculated_val = document.getElementById('hdn_unit_mulprice').value;
		cal_val = parseFloat(shipping_cost) + parseFloat(tax_cost);
		
		pcom_cost = parseFloat(unit_calculated_val) + cal_val;
		document.getElementById('pcompcost').value = pcom_cost;

	    psetVal= parseFloat(target_calculated_val) - parseFloat(document.getElementById('pcompcost').value);
	    document.getElementById('pestprofit').value = psetVal.toFixed(2);
}

  function showDepositSent(obj)
    {
        
    
 obj.parents().children(".deposit_sent").show();  
 obj.parents().children("#deposit_sent1").val(1);
  $(".deposit_fields").children().children().removeAttr("disabled");
    }
    function hideDepositSent(obj)
    {
obj.parents().children(".deposit_sent").hide();   
 obj.parents().children("#deposit_sent1").val(0);
  $(".deposit_fields").children().children().attr("disabled","disabled");
    }
    
    function changeDepositFields()
    {
          
        if($("#notification_select").val()=='2')
            {
      $(".deposit_fields_head").show(); 
       $(".deposit_head").children().children().removeAttr("disabled");      
            }
            else{
                 $(".deposit_fields").children().children().attr("disabled","disabled");
                 $(".deposit_fields_head").children().children().attr("disabled","disabled");
             $(".deposit_fields").hide();
             $(".deposit_fields_head").hide();
            }
    //  load_div(2);
    
}



function AddAnotherVendor(tableID,Id,name,isNameRequired,tbl_vendorid)
{
     var vendor_sel = document.getElementById('vendorID');
       var val=vendor_sel.options[vendor_sel.selectedIndex].value;
    if(val==0)
        {
            alert("Please select a vendor...");
            return;
        }
        flag=0;
       $('input[name="vendorid[]"]').each(function() {
   if($(this).val()==val)
       {
       alert("You have selected a vendor which is already in list");
flag=1;
       }
});
 if(flag==1) return;
  if(val==0)
        {
            alert("Please select a vendor...");
            return;
        }
  app_str='<table><tr class="vendor_row_'+val+'"><td>Vendor Name:<td>'
  +'<td><input type="text" value="'+vendor_sel.options[vendor_sel.selectedIndex].text+'"/><input name = "vendorid[]" type="hidden" value="'+vendor_sel.options[vendor_sel.selectedIndex].value+'"/><td>'
  +'<td><a class="alink" href="#" onClick="DeleteVendorRow('+val+',0,0);">Delete</a></td></tr>';

//if($("#notification_select").val()==2)
    
app_str+='<tr  class="deposit_fields_head vendor_row_'+val+
  '"><td>Vendor PO#:</td><td>&nbsp;</td><td><input type="text" name="vendorPO[]"/></td></tr>'
+'<tr  class="deposit_fields_head vendor_row_'+val+
'"><td>Upload PO#:</td><td>&nbsp;</td><td><input type="file"  id="file'+Id+'" name="file'+Id+'" onchange="javascript:vendorFileUpload('+Id+
', \'F\', 960,720);"/></td><td><input type="hidden" value="" name="vendor_file_name[]" id="vendor_file_name'+Id+'"/><div id="vendor_file'+Id+'"></div></td></tr>'
+'<tr class="ddeposit_fields_head vendor_row_'+val+'"><td>Confirmation Number: </td><td>&nbsp;</td><td><input type="text" name="confirm_num[]" onchange="isNumeric(this);" /></tr>'
+'<tr class="deposit_fields_head vendor_row_'+val+'"><td>Deposit Sent:</td><td>&nbsp;</td><td>Yes<input type="radio" id="deposit_sent_yes" name="deposit_sent'+val+'" onclick="javascript:showDepositSent($(this))">'
+'No<input type="radio" id="deposit_sent_no" checked="checked" name="deposit_sent'+val+'" onclick="javascript:hideDepositSent($(this))"> <input type="hidden" name="deposit_sent1[]" id="deposit_sent1"/></td></tr>'
+'<tr class="deposit_fields deposit_sent vendor_row_'+val+'" style="display:none;"><td>Date: </td><td>&nbsp;</td><td><input type="text"  name="deposit_sent_date[]" id="order_on_'+val+'" onclick="javascript:showDate(this);" /></td></tr>'
+''
  +'<tr ><td>&nbsp;</td></tr>';
  
     app_str+=' </table>';
  $("#vendor_list").append(app_str);
if($("#notification_select").val()!='2')
{
 /* $(".deposit_fields_head").hide(); 
   $(".deposit_fields").children().children().attr("disabled","disabled");*/
}
}


function vendorFileUpload(index, type, width, height){
            
                  file_id_type="file"+index;
           
             
              var fileId = file_id_type;
              // document.getElementById('processing').style.display= '';
              $.ajaxFileUpload(
              {
                  url:'fileUpload.php',
                  secureuri:false,
                  fileElementId:fileId,
                  dataType: 'json',
                  async:false,
                  data:{fileId:fileId, type:type, index:index, width:width, height:height},
                  timeout:60000,
                  success: function (data, status)
                  {
			
                      if(data.error != '')
                      {
                            
                          $("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
                          show_msg();
                      }
                      else
                      {
             
                                     
                                  $("#vendor_file_name"+index).val(data.name);
                                  $("#vendor_file"+index).html('<td><strong>'+data.file_name+'</strong></td>');
                     
                        }
                      
                  },
                  error: function(data) {
               
                      document.getElementById('processing').style.display= 'none';
                      $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                      show_msg();
                  }
              });
  
              return false;
          }

function deleteElement(id,pid){
	document.getElementById('processing').style.display= '';
	var dataString = "id="+id+"&pid="+pid;
	$.ajax({
		   type: "POST",
		   url: "delete_element.php",
		   data: dataString,
		   dataType: "json",
		   timeout:60000,
		   success:function(data)
			{
				document.getElementById('processing').style.display= 'none';
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						show_msg();
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Element Removed...</strong></div>");				
						show_msg();
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				document.getElementById('processing').style.display= 'none';
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
			}
		});
	 return false;
}
function DeleteRows(id,pid,type)
{
	var dataString = "id="+id+"&pid="+pid+"&table_type="+type;
	document.getElementById('processing').style.display= '';
	$.ajax({
		   type: "POST",
		   url: "delete_rows.php",
		   data: dataString,
		   dataType: "json",
		   timeout:60000,
		   success:function(data)
			{
				document.getElementById('processing').style.display= 'none';
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						show_msg();
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>File Removed...</strong></div>");			
						show_msg();
                                                load_div('8');
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
				}				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				document.getElementById('processing').style.display= 'none';
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
			}
		});
	 return false;
}
function DeleteUploads(id,filename,prj_id,type,formtype)
{
	document.getElementById('processing').style.display= '';
	if(filename == 0 || filename == 1 || filename > 100){
		filename = document.getElementById('file_name'+filename).value;
	}
	var dataString = "filename="+filename+"&tableid="+id+"&pid="+prj_id+"&type="+type+"&formtype="+formtype;
	$.ajax({
		   type: "POST",
		   url: "delete_uploads.php",
		   data: dataString,
		   dataType: "json",
		   timeout:60000,
		   success:function(data)
			{
				document.getElementById('processing').style.display= 'none';
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						show_msg();
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>File Removed...</strong></div>");						
						show_msg();
					}
				}
				else
				{
                	$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
				}				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				document.getElementById('processing').style.display= 'none';
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
			}
		});
	 return false;
}
$(function(){$("#validationForm").submit(function(){
if($("#validationForm").valid())
	{
            $("#submitButton").attr("disabled","disabled");
		var pid = document.getElementById('pid').value;
		//var selected tab
		var dataString = "";
		dataString = $("#validationForm").serialize();
		document.getElementById('processing').style.display= '';
		$.ajax({
				type: "POST",
				url: "project_mgmSubmit.php",
				data: dataString+'&pid='+pid,
				dataType: "json",
				timeout:60000,
				success:function(data)
				{
                                 
					document.getElementById('processing').style.display= 'none';
					if(data!=null)
					{
						if(data.name || data.error)
						{
							$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
							show_msg();
						}
						else
						{
							if(document.getElementById('isEdit').value==1)
							{
								$("#message").html("<div class='successMessage'><strong>Project Management details Updated. Thank you.</strong></div>");
								show_msg();
							}
							else
							{
								$("#message").html("<div class='successMessage'><strong>New Project Management Information Added. Thank you.</strong></div>");
								show_msg();
							}
							document.getElementById("pid").value=data.id;
							document.getElementById('proj_2').value = 0;
							document.getElementById('proj_4').value = 0;
							document.getElementById('proj_6').value = 0;
							document.getElementById('proj_7').value = 0;
							document.getElementById('proj_8').value = 0;
							document.getElementById('proj_9').value = 0;
							curr_tab = document.getElementById("currentTab").value;
							if(curr_tab == 3){
								if(document.getElementById('current_sample').value == 0){
									addSample(data.sampleId);
								}
								else{
									loadSamples(data.sampleId,document.getElementById('current_sample').value);
								}
								document.getElementById('proj_0').value = 0;
								document.getElementById('proj_1').value = 0;
								if(data.sample_update){
									document.getElementById('hdn_sample_present').value=data.sample_update;
								}
							}
							else if(document.getElementById('proj_'+curr_tab).value == 0)
                                                        {
							load_div(curr_tab);                                                        
                                                        }
							if(data.purchaseId)
								document.getElementById("purchaseId").value = data.purchaseId;
							if(data.sampleId)
								document.getElementById("sampleId").value = data.sampleId;
							if(data.milestoneid)
								document.getElementById("milestone_id").value = data.milestoneid;
						}						
					} 
					else
					{
					 	$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");						 
						show_msg();
					}
                           $("#submitButton").removeAttr("disabled");               
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					document.getElementById('processing').style.display= 'none';
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
                                          $("#submitButton").removeAttr("disabled");
				}
		});
		  return false;
	  }
	   });
}); 
function DeleteCurrentNotesRow(obj)
{	
	var delRow = obj.parentNode.parentNode;
       var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
       DeleteRow(rowArray);
        
        //alert(obj.parentNode.parentNode+1);
         var delRow2 = obj.parentNode.parentNode+1;
          var rowArray2 = new Array(delRow2);
        DeleteRow(rowArray2);
}


function DeleteVendorRow(obj,Id,tbl_vendorid)
{
   
   $(".vendor_row_"+obj).remove();
    //$("'."+obj+"'").remove();
	document.getElementById('processing').style.display= '';
	
	var pid = document.getElementById('pid').value;
	var dataString = "vendorId="+tbl_vendorid+"&pid="+pid;
	$.ajax({
		   type: "POST",
		   url: "vendorDelete.php",
		   data: dataString,
		   dataType: "json",
		   timeout: 60000,
		   success:
	function(data)
	{
		document.getElementById('processing').style.display= 'none';
		if(data!=null)
		{
			if(data.name || data.error)
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				show_msg();
			}
			else 
			{
					$("#message").html("<div class='errorMessage'><strong>Vendor Removed...</strong></div>");
					show_msg();
			}
		}
		else
		{
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
		}
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('processing').style.display= 'none';
		$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		show_msg();
	}
});

}


function DeleteCurrentRow(obj<?php if($isEdit) {?>,Id<?php }?>,tbl_vendorid)
{
	document.getElementById('processing').style.display= '';
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	<?php
	if($isEdit)
	{ 
	?>	
	var pid = document.getElementById('pid').value;
	var dataString = "vendorId="+tbl_vendorid+"&pid="+pid;
	$.ajax({
		   type: "POST",
		   url: "vendorDelete.php",
		   data: dataString,
		   dataType: "json",
		   timeout: 60000,
		   success:
	function(data)
	{
		document.getElementById('processing').style.display= 'none';
		if(data!=null)
		{
			if(data.name || data.error)
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				show_msg();
			}
			else 
			{
					$("#message").html("<div class='errorMessage'><strong>Vendor Removed...</strong></div>");
					show_msg();
			}
		}
		else
		{
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
		}
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('processing').style.display= 'none';
		$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		show_msg();
	}
});
<?php }?>
	DeleteRow(rowArray);
        DeleteRow(rowArray2);
}
function DeleteRow(rowObjArray)
{	
	for (var i=0; i<rowObjArray.length; i++) {
		var rIndex = rowObjArray[i].sectionRowIndex;
		rowObjArray[i].parentNode.deleteRow(rIndex);
	}	
}
function DeleteSingleRow(obj)
{
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}


function addRow(tableID)
{
   dataString="pid="+document.getElementById('pid').value; 
 $.ajax({
		   type: "POST",
		   url: "getPriceTabStyleInfo.php",
		   data: dataString,
		   dataType: "json",
		   timeout: 60000,
		   success:
	function(data)
	{
         addRow2(tableID,data);
        }
        });   
    
}

function addRow2(tableID,data)
{
    
    
    
		var table = document.getElementById(tableID); 
        var rowCount = table.rows.length;
       	var row = table.insertRow(rowCount); row.id='row'+rowCount;
        var cell1 = row.insertCell(0);
        
        	
        html_cnt = '<table align="center" width="100%" cellpadding="0" cellspacing="10" border="0"><tr><td align="left" width="50%" height="25"><img src="<?php echo $mydirectory; ?>/images/x.jpg" border="0" alt="delete" class="delete" onmouseover="this.style.cursor = \'pointer\';" />&nbsp;&nbsp; <font color="red">Delete Row</font><br></td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%">&nbsp;</td><td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td></tr><tr><td width="50%" height="25" align="right">Carriers:</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"><select name="carrier_shipping_select[]" onchange="javascript:show_weblink(this, '+rowCount+');"><option value="0">----- select -----</option><?php
				for($i=0; $i < count($data_carrier); $i++)
				{
					echo"<option value=".$data_carrier[$i]['carrier_id']."> ".$data_carrier[$i]['carrier_name']." </option>";
				}
				
				?></select></td><td align="left" valign="top" height="25px">&nbsp;</td></tr> <tr class="ship_fields'+rowCount+'" ><td width="50%" height="25" align="right">Tracking Number:</td><td align="left" valign="top" height="25px">&nbsp;</td>'
                                            +'<td width="49%"><input class="ship_fields'+rowCount+'" name="track_shipping['+rowCount+'][]" id="track_num'+rowCount+'" type="text" value="">'
+'&nbsp;&nbsp; <img src="<?php echo $mydirectory;?>/images/bullet_add.png" alt="Add" onclick="javascript:AddTrackingNum('+rowCount+');"/></td>'
 +'<td valign="top" height="25px" align="center"><div id="weblink_id'+ rowCount+ '"><a href="javascript:void(0);" onclick="javascript:popupWindow(\'<?php 
 echo $data_order_shipping[$i]['weblink'].$data_order_shipping[$i]['tracking_number'];?>\');"><img src="<?php echo $mydirectory;
 ?>/images/courier_man.jpg" width="50" height="30px"/></a></div></td></tr>'
+'<tr class="ship_fields'+rowCount+'" ><td align="center" colspan="3"  ><table width="100%" id="track_'+rowCount+'"></table></td></tr>'
+'<tr class="ship_fields'+rowCount+'"><td width="50%" height="25" align="right">Shipped On:</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"> <input  name="shipon[]" onclick="javascript:showDate(this);" type="text"/></td><td align="left" valign="top" height="25px">&nbsp;</td></tr>'

+'<tr style="bottom:10px;display:none;" id="d_date'+rowCount+'">'+
'<td width="50%" height="25" align="right" >Date Delivered:</td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td>'+
'<td width="49%"><input type="text" id="delivered_date'+rowCount+'" name="delivered_date[]" onclick="javascript:showDate(this);" /></td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td></tr>'+ 
 '<tr style="bottom:10px;display:none;" id="d_by'+rowCount+'">'+
'<td width="50%" height="25" align="right" >Delivered By:</td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td>'+
'<td width="49%"><input type="text" id="delivered_by" name="delivered_by[]" value="" /></td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td></tr>'

+'<tr ><td width="50%" height="25" align="right">Shipped From</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"><select name="shiped_from[]">'+
         
'<option value="Vendor" >Vendor</option>'+
'<option value="Client" >Client</option>'+
'<option value="Office">Office</option>'+
'</select>'+
'</td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td>'+
'</tr>'

+'<tr><td width="50%" height="25" align="right">Shipped To</td><td align="left" valign="top" height="25px">&nbsp;</td><td width="49%"><select name="shiped_to[]">'+
         
'<option value="Vendor" >Vendor</option>'+
'<option value="Client" >Client</option>'+
'<option value="Office">Office</option>'+
'</select>'+
'</td>'+
'<td align="left" valign="top" height="25px">&nbsp;</td>'+
'</tr><tr><td>Style:</td><td>Size:</td><td>Qty Ordered:</td><td>Qty Shipped:</td></tr>';
if(data!=null && data!='null' && data!='')
    {
for(i=0;i<data.length;i++)
    {
html_cnt +='<tr><td><input type="text" readonly="readonly"  value="'+data[i].style+'" id="styles" /></td>'
     +'<td><input type="text" readonly="readonly" value="'+data[i].vendor_style+'" /></td>';
            
			
html_cnt +=' <td><input type="text" id="qty_order" class="qty_order_'+i+'" readonly="readonly" value="'+data[i].garments+'" />';
html_cnt +='<input type="hidden" name="prj_style_id['+rowCount+']['+i+']" value="'+data[i].prj_style_id+'"  />';				
	html_cnt +='</td>';
				
        html_cnt +='<td><input type="text" name="shipping['+rowCount+']['+i+']" id="qty_ship" class="shipping_'+i+'" value="" onchange="javascript:left_ship();" /></td>';
	html_cnt +=' </tr>';

}
}
html_cnt += '<tr><td width="50%" height="25" align="right">Shipping Notes:</td><td align="left" valign="top" height="25px">&nbsp;</td>'
+'<td width="49%"><textarea name="order_shipping_notes[]"></textarea><input name="hdn_shipping_id[]" type="hidden" value="0"></td>'
+'<td align="left" valign="top" height="25px">&nbsp;</td></tr>'

+' <tr>'+
'<td width="50%" height="25" align="right">Shipped On Clients Carrier Account:'+           
             '</td><td width="1%">&nbsp;</td>'+
'<td ><input type="checkbox" id="shippedonclient" name="shippedonclient['+(rowCount)+']"  onclick="javascript:shippingOn()" '+
'"/></td>'+
'<td width="50"><img src="../../images/spacer.gif" width="50" height="30" alt="spacer" /></td></tr>'+




'</table>';


cell1.innerHTML=html_cnt;

$(".delete").live('click', function(event) {
        $(this).parent().parent().parent().remove();
});
}




function AddTrackingNum(rowCount)
{
  htm='<tr><td width="50%" align="right">Tracking Number:</td><td width="1%">&nbsp;</td><td > <input name="track_shipping['+rowCount+'][]" id="track_num'+rowCount+'" type="text" value="">&nbsp;&nbsp;'
  +'<img src="<?php echo $mydirectory; ?>/images/x.jpg" border="0" alt="delete" onclick="javascript:$(this).parent().parent().remove();" '
  +' onmouseover="this.style.cursor = \'pointer\';" /></td>'+
  
  '<td><div id="weblink_id'+ rowCount+ '"><a href="javascript:void(0);" onclick="javascript:popupWindow(\'<?php 
 echo $data_order_shipping[$i]['weblink'].$data_order_shipping[$i]['tracking_number'];?>\');"><img src="<?php echo $mydirectory;
 ?>/images/courier_man.jpg" width="50" height="30px"/></a></div></td>'+
  '</tr>';  
  $('#track_'+rowCount).append(htm);
}




function showInventoryStyle(obj)
{
    if(obj.value==1)
   {
       $("#inventory_styleid").removeAttr("disabled");
            $.ajax({
           type: 'POST',
           url: 'getInventoryStyles.php?opt=style',
           success: function(data){
               $("#inventory_styleid").html(data);
           
           }
           
           });
   }
   else
   {
    $("#inventory_styleid").attr("disabled","disabled");
   }
}

function showDate(obj)
{
	$(obj).datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(obj).datepicker('show'); });
	$(obj).datepicker('show');
}
function designboardVisible(obj)
{
	if(obj.value == 1)
	{
		document.getElementById('designboardcalender').style.display = "";
	}
	else 
	{
		document.getElementById('designboardcalender').style.display = "none";
	}
}
function load_div(num){
	document.getElementById('processing').style.display= ''; 
	var pid = "";
	var status_query = "";
	pid = document.getElementById('pid').value;
	var status_query = document.getElementById('status_query').value;
     var dataString = "pid="+pid+"&status_query="+status_query;
      var close="<?php 
      if(isset($_GET['close'])) 
          echo "1";
      else echo "";?>";
        if(close!="")
          dataString +='&close=1'; 
     //   var shipping_status="";
        //if(num==4&&$("#shipping_status").val==1)
           // shipping_status="?shipping_status=1";
	$.ajax({
		   type: "POST",
		   url: "proj_"+num+".php",
		   data: dataString,
		   dataType: "json",
		   timeout:60000,
		   success:
	function(data)
	{
		document.getElementById('processing').style.display= 'none';
		if(data!=null)
		{			
			if(data.name || data.error)
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
				show_msg();
			}
			else
			{
				if(num != 11)document.getElementById('proj_'+num).value=1;
				document.getElementById('proj_content'+num).innerHTML=data.html;
				if(num == 7 && !document.getElementById("elementstyle") )
					AddElement();
                                    if(num==4 &&($("#shipping_status").val()==1))
                                        {
                                         $("#shipping_cost_row").css("display","none");
                                        }
                                        
                     if(num==8)              
                         left_ship();
			}
		}
		else
		{
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
                      if(document.getElementById('pid').value>0)
                          location.href="project_mgm.add.php?id="+document.getElementById('pid').value+"&<?php echo $paging;?>";
		}
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('processing').style.display= 'none';
		$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		show_msg();
                if(document.getElementById('pid').value>0)
                   location.href="project_mgm.add.php?id="+document.getElementById('pid').value+"&<?php echo $paging;?>";
	}
});
}

function load_tab(num){
	document.getElementById("currentTab").value = num;
	document.getElementById('hdn_track_update').value = num;
	if(num == 3)
		document.getElementById('proj_'+num).value = 1;
	else if(document.getElementById('proj_'+num).value != 1 ){
		load_div(num);
	}
}

function ajaxFileUpload(index, type, width, height){
	if(document.getElementById('file'+index).value != ""){
	  var fileId = 'file'+index;
	  document.getElementById('processing').style.display= '';
	  $.ajaxFileUpload(
	  {
		  url:'fileUpload.php',
		  secureuri:false,
		  fileElementId:fileId,
		  dataType: 'json',
		  async:false,
		  data:{fileId:fileId, type:type, index:index, width:width, height:height},
		  timeout:60000,
		  success: function (data, status)
		  {
                     
			document.getElementById('processing').style.display= 'none';
			if(typeof(data.error) != 'undefined')
			{
			  if(data.error != '')
			  {
                             
			   $("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
			   show_msg();
			  }else
			  {
			   $("#message").html("<div class='successMessage'><strong>"+data.msg +"</strong></div>");
			   show_msg();
			   if(data.index != 'undefined' && data.index != "")
			   {
                                    
        data.name=encodeURIComponent(data.name);
        
        var str=String(data.name);
        
        str=str.replace("121212090909567845689","%26");
        
        data.name=str;
                                                                                                  
	document.getElementById("file"+data.index).value = '';
				 if(data.index >1 && data.index <100)
				 {
					 switch(data.index)
					 {
						case '0':
						label = "Pattern:";
						break;
						case '1':
						label = "Grading:";
						break;
						case '2':
						label = "Image:";
						break;
						case '3':
						label = "File:";
						break;
						default:
						label = "Image "+(data.index-2);
						break;
					 }
                                        
				 	 add_thumbnail(label,data.name,0,data.file_name,0);
				 }
				 else if(data.index > 100 && data.index < 200 && data.type == 'F' )
				 {
                                   
	document.getElementById('tr_id'+Number(data.index)).innerHTML='<td><strong>File:</strong><br/>'+data.file_name+'<a href="download.php?file='+data.name+'"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download" /></a><a <?php if($emp_type ==1){ echo 'style="visibility:hidden"';  } ?> href="javascript:void(0);" onClick="javascript:DeleteUploads(\'\',\''+escape(data.name)+'\',\'\',\'\',\'editTime\'); document.getElementById(\'tr_id'+data.index+'\').style.display=\'none\'; document.getElementById(\'file_name'+data.index+'\').value=\'\'; "><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a></td>';
					document.getElementById('file_name'+Number(data.index)).value=data.name;
					document.getElementById('tr_id'+Number(data.index)).style.display="";
				 	document.getElementById('file'+Number(data.index)).value="";
                                        if($('#file_from'+Number(data.index)))
                                         $('#file_from'+Number(data.index)).val("CURR");
				 }
				 else if(data.index >= 200)
				 {
					if(data.type == 'I')
						add_thumbnail("Image:",data.name,0,data.file_name,1);
					if(data.type == 'F')
						add_thumbnail("File:",data.name,0,data.file_name,1);
				 }
				 else
				 {
					document.getElementById('tr_id'+data.index).style.display="";
					document.getElementById('img_file'+data.index).src="<?php echo ($upload_dir);?>"+data.name;
					document.getElementById('file_name'+data.index).value = data.name;
				 	document.getElementById('file'+data.index).value="";
                                        if($('#image_from'+data.index))
                                            $('#image_from'+data.index).val("CURR");
				 }
			   }
			  }
			}
		  },
		error: function(XMLHttpRequest, textStatus, errorThrown) {
				document.getElementById('processing').style.display= 'none';
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
		}
	});
  }
  return false;
}

function add_thumbnail(image_label,name,image_id,file_name,tableNum ) {
	if(tableNum == 0) {
		tableName = 'image_view';
		upload_name = 'upload_file';
		upload_id = 'upload_id';
		upload_type = 'upload_type';
 	}
	else if(tableNum == 1){
		tableName = 'sample_uploads';
		upload_name = 'sample_file_name';
		upload_id = 'sample_file_id';
		upload_type = 'sample_file_type';
	}
	if(image_label=='Image:'){
		var table = document.getElementById(tableName);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var cell1 = row.insertCell(0);
		cell1.width="200px";
		var label = document.createElement('strong');
		label.innerHTML = image_label+'<br/>';
		cell1.innerHTML = '<input type="hidden" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="I"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
		cell1.appendChild(label);
		
		var img = document.createElement("img");
		img.src = "<?php echo $upload_dir;?>"+name;
		img.style.width="101px";
		img.style.height="89px";
		img.onclick = function(){ PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge'); };
		cell1.appendChild(img);
		
		cell1.innerHTML += '<a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a>';
	}
	else{
		var table = document.getElementById(tableName);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var cell1 = row.insertCell(0);
		cell1.width="200px";
		var label = document.createElement('strong');
		label.innerHTML = image_label+'<br/>';
		cell1.appendChild(label);
		
		cell1.innerHTML += file_name+'<a href="download.php?file='+name+'"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download" /></a><a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\'); "><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a><input type="hidden" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="F"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
	}
}
function limitTextArea(textareaid, charlimit, counter)
{ 
	var textarea = document.getElementById(textareaid);
	if (textarea.value.length > charlimit)
	{
		textarea.value = textarea.value.substring(0, charlimit);
	}
	else 
	{
		document.getElementById(counter).innerHTML = (charlimit - textarea.value.length) + " Characters left";
	}
} 

function sampleNotesSubmit(tableId,textId)
{
	var table = document.getElementById(tableId);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	cell1.width="50px";		
	cell1.innerHTML = "Notes "+rowCount+":";	
	var cell2 = row.insertCell(1);
	cell2.width="10px";		
	cell2.innerHTML = "&nbsp;";	
	
	var noteslimit=textId.value;
	
	if(noteslimit.length > 10)
	{
	 noteslimit= noteslimit.substr(0,10);
	}
	var cell3 = row.insertCell(2);
	cell3.width="150px";		
	cell3.innerHTML = noteslimit;	
	
	var cell7 = row.insertCell(3);
	cell7.width ="150px";
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "Read more...";
	element1.onclick = function(){popOpen(rowCount,'SAMPLE');};
	cell7.appendChild(element1);
	var cell4 = row.insertCell(4);
	cell4.width="10px";
	cell4.innerHTML = "&nbsp;";
	
	var cell5 = row.insertCell(5);
	var element2 = document.createElement("textarea");
	element2.name = "sample_textAreaName[]";
	element2.id = 'sampletxtAreaId'+rowCount;
	element2.value = textId.value;
	element2.style.display = "none";
	var element3 = document.createElement("input");
	element3.name = "hdn_sample_notesId[]";
	element3.id = 'hdn_sample_notesId'+rowCount;
	element3.value = 0;
	element3.style.display = "none";
	cell5.appendChild(element2);
	cell5.appendChild(element3);
	var cell6 = row.insertCell(6);	
	cell6.innerHTML="<a class=\"deleteTd\" href=\"javascript:;\" onClick=\"\"><img style=\"width:32px;height:25px;\" src=\"<?php echo $mydirectory;?>/images/delete.png\" ></a>";
	
	
}
function load_genaratePO(){
	if(document.getElementById('ship_to_id')){	
				popOpen('','generatePO');}
	else{
		var dataString = "sampleId="+document.getElementById('sampleId').value;
			$.ajax({
			   type: "POST",
			   url: "prj_sample_po.php",
			   data: dataString,
			   dataType: "json",
			   timeout: 60000,
			   success:
		function(data)
		{
			document.getElementById('processing').style.display= 'none';
			if(data!=null)
			{			
				if(data.name || data.error)
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
					show_msg();
				}
				else
				{
					document.getElementById('generatePO_div1').innerHTML=data.html;
					popOpen('','generatePO');
					nextSession();
				}
			}
			else
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			document.getElementById('processing').style.display= 'none';
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
		}
	});
	}
}
//functions for po
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
	document.getElementById('item_td_id'+count).innerHTML = document.getElementById('item_number_id').value;
	document.getElementById('item_id'+count).value = document.getElementById('item_number_id').value;
	document.getElementById('desc_id'+count).value = document.getElementById('description_id').value;	
	document.getElementById('unit_id'+count).value =document.getElementById('unit_price_id').value ;
	document.getElementById('quantity_id'+count).value=	document.getElementById('quantity_id').value ;
	document.getElementById('tax_id'+count).value =document.getElementById('tax_amount_id').value ;
	document.getElementById('tax_type_id'+count).value =taxtype.options[taxtype.selectedIndex].value ;
	document.getElementById('amount_id'+count).value=document.getElementById('amount_id').value;
}
function add_multiple_items(tableid)
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
	cell3.id="item_td_id"+rowCount;
	cell3.innerHTML = itemno;
	cell4.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;';
	
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "View/Edit...";
	element1.onclick = function(){item_visible(rowCount);};
	cell5.appendChild(element1);
	
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
	
	cell6.style.display="none";
	cell6.appendChild(element2);
	cell6.appendChild(element3);
	cell6.appendChild(element4);
	cell6.appendChild(element5);
	cell6.appendChild(element6);
	cell6.appendChild(element7);
	cell6.appendChild(element8);
	cell6.appendChild(element9);
	
	//var cell6 = row.insertCell(4);	
	//cell6.align="left";	
	//cell6.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentRow(this,0,'');\"><img style=\"width:32px;height:25px;\" src=\"< ?php echo $mydirectory; ?>/images/delete.png\" ></a>";
}
function DeleteRow(rowObjArray)
{	
	for (var i=0; i<rowObjArray.length; i++) {
		var rIndex = rowObjArray[i].sectionRowIndex;
		rowObjArray[i].parentNode.deleteRow(rIndex);
	}	
}


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
			if(document.getElementById('amount_id'+i) && (document.getElementById('amount_id'+i).value != '' || document.getElementById('tax_id'+i).value !='')){
				subAmountTotal+=parseFloat(document.getElementById('amount_id'+i).value);
				subTaxTotal +=parseFloat(document.getElementById('tax_id'+i).value);
			}
		}
		if(subAmountTotal != '' || subTaxTotal !='' )
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
						show_msg();
					} 
					else
					{	
						internalpo_val.value = data.value;
					}
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
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
						show_msg();
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
					show_msg();
				}
				
			}
		});
}
function DeleteItems(id)
{
	amountSubTotal = document.getElementById('amountsubtotal_id').value;
	taxSubTotal = document.getElementById('taxsubtotal_id').value;
	total = document.getElementById('total_id').value;
	po_id = document.getElementById('qid').value;
	var dataString ='item_id='+id+"&amountSubTotal="+amountSubTotal+"&taxSubTotal="+taxSubTotal+"&total="+total+"&po_id="+po_id;
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
						show_msg();
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Item Removed Successfully.</strong></div>");
						show_msg();
					}
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
				}
				
			}
		});
}
$('#podate').datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(this).datepicker('show'); });
$('#goods_through_id').datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(this).datepicker('show'); });
--></script>   
<script type="text/javascript">
$().ready(function() {
   
 <?php /*?>< ?php if($qid == 0) echo 'nextSession();'; ?><?php */?>
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
function item_visible(count) {
	document.getElementById('tbl_item_add').style.display='none';
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
		document.getElementById('item_submit').onclick = function(){edit_multiple(count);document.getElementById("div_item").style.display='none';calculateSub('tbl_item_add');document.getElementById('tbl_item_add').style.display='';};
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
		document.getElementById('item_submit').onclick = function(){add_multiple_items('tbl_item_add');document.getElementById("div_item").style.display='none';calculateSub('tbl_item_add');document.getElementById('tbl_item_add').style.display='';};
	}
	document.getElementById("div_item").style.display='';
}
function clearSamples()
{
	var count = document.getElementById('sample_count').value;
	for(i=0;i <=count;i++)
	{
		if(document.getElementById('sample_tab'+(i)) != null)
			document.getElementById('sample_tab'+(i)).innerHTML = "";
	}
}
function loadSamples(id,divId)
{
	var dataString = "id="+id+"&pid=<?php echo $pid; ?>";
         var close="<?php 
      if(isset($_GET['close'])) 
          echo "1";
      else echo "";?>";
        if(close!="")
          dataString +='&close=1'; 
	$.ajax({
	 type: "POST",
	 url: "project_sample.php",
	 data: dataString,
	 dataType: "json",
	 success:
	 function(data)
	 {
		 if(data!=null)
		 {
			 if(data.error)
			 {
				 $("#message").html("<div class='errorMessage'><strong>Sorry, "+data.error +"</strong></div>");
				show_msg();
			 }
			 else
			 {
				 if(data.html != "")
				 {
					 clearSamples();
					 document.getElementById('sample_tab'+divId).innerHTML = data.html;
					 document.getElementById('sample_tab'+divId).style.display='block';
					 document.getElementById('current_sample').value = divId;
					 load_client_shipper();
					 document.getElementById("generatePO_div1").innerHTML = '';
				 }
			 }
		 }
		 else								   
		 {
			 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
			show_msg();
		 }
	 }
	 });
	return false;
}
function GenerateInvoice(type)
{
	var invoiceval = document.getElementById('internalpo');
	var po_sequence_val = document.getElementById('generate_po');
	var dataString ='type='+type;
	$.ajax({
		   type: "POST",
		   url: "invoicegenerate.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						show_msg();
					} 
					else
					{	
						$("#message").html("<div class='successMessage'><strong>Invoice Generated.</strong></div>");
						show_msg();
						if(type == 'generate_po')
						po_sequence_val.value = data.value;
						else if(type == 'internal_po')
						invoiceval.value = data.value;
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
				}
				
			}
		});
}

function addSample(s_id){
	if(document.getElementById("srID").value){
	var ul = document.getElementById("sample_tab");
	var rowCount = $(ul).find('li').length;
	document.getElementById('sample_count').value = rowCount;
	
	var cell1 = document.createElement("li");
	cell1.onclick = function(){loadSamples(s_id,rowCount);};
	cell1.innerHTML = '<a href="javascript:void(0);" rel="sample_tab'+rowCount+'" class="selected">Sample - '+rowCount+'</a>';
	ul.appendChild(cell1);
	var div = document.getElementById("sample_div");
	var element1 = document.createElement("div");
	element1.id = "sample_tab"+rowCount;
	element1.setAttribute("class","tabcontent");
	div.appendChild(element1);
	
	loadSamples(s_id,rowCount);
	}
}
function quote_check(){
	if(document.getElementById('po_number').value != ''){
		Fade();
		
	}else{
		document.getElementById('po_messege').style.display='';
		document.getElementById('po_number').focus();
	}
}
function load_client_shipper()
{
	if(document.getElementById('clientID'))
	{
		var client_id = document.getElementById('clientID');
		document.getElementById('hidden_client_shipper').value = document.getElementById('clientID').value;

		var dataString ='client_id='+client_id.value;
	$.ajax({
		   type: "POST",
		   url: "client_shipper_number.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
						show_msg();
					} 
					else
					{	
						document.getElementById('shipperno').value = data.sample_client_shipper;
					}
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					show_msg();
				}
				
			}
		});
	
		

	}
	}


function get_project() {
	data ='project_name='+$("#bid").val();
	    $.ajax({
		 type: "POST",
		 datatype:'json',
		 url: "get_project_name.php",
		 data: data,
		  success: function(data) 
		 {
			
			 //if(data.reg!='')
         $('#project').val(data.qid);
         $('#project_text').val(data.project);
		 		}	
		 });
	
}

function shippingOn()
{
if(($('#shipped_on_client').attr('checked'))==true)
    {
       $("#shipping_cost_row").css("display","none");
        $("#shipping_status").val(1);
        
    }
  else  if(($('#shipped_on_client').attr('checked'))==false)
    {
       $("#shipping_cost_row").show();
        $("#shipping_status").val(o);
        
    }

    
}
function delete_trackno(id){
$.ajax({
    type: "POST",
    url: "delete_trackno.php",
    data: 'id='+id,
    dataType: "json",
    success:function(data)
    {
    if(data!=null)
    {
            if(data.value || data.error)
            {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.value + data.error +"</strong></div>");
                    show_msg();
            } 
            else
            {	
                    $("#message").html("<div class='successMessage'><strong>Tracking Number Removed Successfully.</strong></div>");
                    load_div('8');
                    
                    show_msg();
            }
    }
    else
    {
            $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
            show_msg();
    }
}
});
}

function left_ship(){    
    var qnty =0;

	for(i=0;;i++)
	{
if($(".qty_order_"+i).length>0)
{
	qnty=0;
	//$("input[name=shipping_"+i+"]").each(function(){
        $(".shipping_"+i).each(function(){
         if($.trim($(this).val())!='')   
		{ qnty+=parseInt($(this).val());
                }
		  });
  // alert($(".qty_order_"+i).val()+"--"+qnty);
    var res = parseInt($(".qty_order_"+i).val()) - qnty;
if(res<0 || $(".qty_order_"+i).val()=='') res=0;
	
	document.getElementById('left_ship_'+i).innerHTML = res;	
	
	}
	else{

	break;
	}
	}
   
}
function  purchaseOrderCalculation(count,type){
    var shipping_cost = 0;
    var tax_cost = 0;
    if(document.getElementById('shipping_cost').value!="" )
           shipping_cost = document.getElementById('shipping_cost').value;
    if(document.getElementById('taxes').value!="")
           tax_cost = document.getElementById('taxes').value;
    if(type == 1){
        var multiplyVal =0;
        var multiply_target_val =0;
        var total_sum = 0;
        var total_garments = 0;
        var estimated_cost = 0;	
        var completion_cost = 0;
        var hdn_unit_val =0;
        var hdn_target_val = 0;
	var garments = document.getElementById('garments'+count);
	var priceunit = document.getElementById('priceunit'+count);
	var retailprice = document.getElementById('retailprice'+count);
        if(document.getElementById('style'+count))
		isUnique(count);
	if(garments.value >0 && priceunit.value >0)
		multiplyVal =  parseFloat(garments.value) *  parseFloat(priceunit.value);
	else 
		multiplyVal = 0;
	if(garments.value >0 && retailprice.value >0)
		multiply_target_val =  parseFloat(garments.value) *  parseFloat(retailprice.value);
	else 
		multiply_target_val = 0;
	document.getElementById('rowSum'+count).value = multiplyVal;
	document.getElementById('row_sum_target'+count).value = multiply_target_val;
	var total_count = document.getElementById('tbl_style').rows.length;
        for(var i=1; i<total_count; i++)
	{
		if(!isNaN(document.getElementById('rowSum'+i).value))
			total_sum += parseFloat(document.getElementById('rowSum'+i).value);
		if(!isNaN(document.getElementById('rowSum'+i).value))
			hdn_unit_val += parseFloat(document.getElementById('rowSum'+i).value);
		if(!isNaN(document.getElementById('row_sum_target'+i).value))
			hdn_target_val += parseFloat(document.getElementById('row_sum_target'+i).value);
		if(!isNaN(document.getElementById('garments'+i).value))
			total_garments += parseFloat(document.getElementById('garments'+i).value);
	}
	document.getElementById('hdn_unit_mulprice').value = hdn_unit_val;
	document.getElementById('hdn_target_mulprice').value = hdn_target_val;
	document.getElementById('hdn_garmenttotal').value = total_garments;
    } else {
        var hdn_unit_val = document.getElementById('hdn_unit_mulprice').value;
        var hdn_target_val = document.getElementById('hdn_target_mulprice').value;
        var total_garments = document.getElementById('hdn_garmenttotal').value;
    }
    var purchaseOrder = (parseFloat(hdn_target_val) + parseFloat(tax_cost) + parseFloat(shipping_cost)).toFixed(2);
    document.getElementById('projectQuote').value = purchaseOrder;
    var completionCost = parseFloat(hdn_unit_val);
    var profit = parseFloat(purchaseOrder) - parseFloat(completionCost);
    document.getElementById('pcompcost').value = completionCost;
    document.getElementById('pestprofit').value = profit;
}
</script> 
<?php 
require('../../trailer.php');
?>