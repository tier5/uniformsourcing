<?php
require('Application.php');
$current_page ="project_purchase.closed.php";
$type = "project_purchase_closed";
$paging = 'paging=';
if(isset($_POST['cancel']))
{
	$sql="";
	$search_sql="";
	$search_uri="";
	$_SESSION['search_uri'] = "";
}
if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging .= $_GET['paging'];
}
else
{
	$paging .= 1;
}
if(isset($_SESSION['search_uri']) && $_SESSION['search_uri']!="")
{
	if($type !=$_SESSION['page_type'])
	{
		$_SESSION['search_uri'] = "";
	}
}
$search_sql="";
$limit="";
$search_uri="";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
	$search_sql=' and prj.client ='.$_REQUEST['cid'].' ';
	$search_uri="?cid=".$_REQUEST['cid'];	
	}
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="") {
	$search_sql .=' and prj.pid ='.$_REQUEST['pid'].' ';
	if($search_uri) 
	{
		 $search_uri.="&pid=".$_REQUEST['pid'];
	} 
	else
	{
		$search_uri.="?pid=".$_REQUEST['pid'];
	}
}
if(isset($_REQUEST['purchase']) && $_REQUEST['purchase']!="") {
		$search_sql .=' and prch.purchaseorder  LIKE \'%' .$_REQUEST['purchase'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&purchase=".$_REQUEST['purchase'];
		} else {
			$search_uri.="?purchase=".$_REQUEST['purchase'];
		}
	}
	
if(isset($_REQUEST['style']) && $_REQUEST['style']!="") {
		$search_sql .=' and tbl_prj_style.style  LIKE \'%' .$_REQUEST['style'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&style=".$_REQUEST['style'];
		} else {
			$search_uri.="?style=".$_REQUEST['style'];
		}
	}	
$_SESSION['search_uri'] = $search_uri;
if(isset($_GET['del']) && (strtolower($USERNAME) == 'neal' || strtolower($USERNAME) == 'nicole' || strtolower($USERNAME) == 'rejith'))
{
	$pid = $_GET['del'];
	
	$sql = "delete from tbl_prjsample_closed where pid=".$pid."; ";	
	$sql .= "delete from tbl_prjpurchase_closed where pid =".$pid."; ";	
	$sql .= "delete from tbl_prjpricing_closed where pid =".$pid."; ";	
	$sql .= "delete from tbl_prjvendor_closed where pid =".$pid."; ";	
	$sql .= "delete from tbl_mgt_notes_closed where pid =".$pid."; ";	
	$sql .= "delete from tbl_prj_style_closed where pid =".$pid."; ";	
	$sql .= "delete from tbl_prjorder_shipping_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prjsample_notes_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prjsample_uploads_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prj_sample_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prj_sample_po_items_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prj_sample_po_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_upload_pack_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prj_style_custom_closed where pid =".$pid."; ";
	$sql .= "delete from tbl_prmilestone_closed where pid =".$pid."; ";
        $sql .= "delete from tbl_qty_shipped_closed where pid =".$pid."; ";
	
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
$query1="SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" as c ".
		 "WHERE \"active\" = 'yes' ";
		 if($_SESSION['employeeType'] == 2)$query1.="$emp_sql";
		 $query1.=" ORDER BY \"client\" ASC";
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
	$sql = 'select elementfile,image from tbl_prj_elements where pid='.$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_file[]=$row;
	}
	pg_free_result($result);
	for($i=0; $i<count($data_file); $i++)
	{
		if(file_exists("$upload_dir"."".$data_file[$i]['elementfile']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['elementfile']."");
			}
			if(file_exists("$upload_dir"."".$data_file[$i]['image']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['image']."");
			}
	}
	$data_file[] = "";
	$sql = "delete from tbl_prj_elements where pid =".$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}

	$sql = 'select file_name from tbl_prjimage_file where pid='.$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_file[]=$row;
	}
	for($i=0; $i<count($data_file); $i++)
	{
		if(file_exists("$upload_dir"."".$data_file[$i]['file_name']."")) {
				@ unlink("$upload_dir"."".$data_file[$i]['file_name']."");
			}
	}
	$sql = 'delete from tbl_prjimage_file where pid='.$pid ."; ";
	
	$sql.= 'delete from tbl_newproject where pid='.$pid.";";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
}
if($_SESSION['search_uri']!="")
{		
	$_SESSION['page_type'] = $type;
}
require('../../header.php');

$_SESSION['page'] = $current_page;
?>

<script type="text/javascript">
var cIndex=0;
</script>
<?php
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
include('../../pagination.class.php');

if(isset($_SESSION['employeeType']) && $_SESSION['employeeType'] == 5){
    $emp_id = $_SESSION['employeeID'];
    $sales_person = ' and (prj."project_manager" = '.$emp_id.' or prj."project_manager1" = '.$emp_id.' or prj."project_manager2" = '.$emp_id.')';
}

$sql=' select Distinct(prj.projectname),c.client,prj.pid,prj.order_placeon,prj.status,emp.firstname,emp.lastname'.
',prch.purchaseorder,prc.prjquote,prc.prjcost,prc.prj_completioncost,prc.prj_estimatecost,prc.prj_completioncost '.
'from tbl_newproject_closed as prj left join tbl_prjpurchase_closed as prch on prch.pid = prj.pid left join tbl_prjpricing_closed '.
'as prc on prc.pid = prj.pid left join "employeeDB" as emp on emp."employeeID"= prj.project_manager left join "clientDB" c on prj.client=c."ID" '.
' where prch.purchaseorder '
      .'<> \'\'   '.$search_sql.''.$sales_person.' order by prj."pid"  desc ';
 
//echo $sql;
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
$items= pg_num_rows($resultp);
if($items > 0) {
	$p = new pagination;
	$p->items($items);
	$p->limit(10); // Limit entries per page
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
$sql = $sql. " ". $limit;
if(!($resultp=pg_query($connection,$sql))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultp)){
	$datalist[]=$rowd;
}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.js"></script>';
?>
	<script type="text/javascript">
		/*function prjctName()
		{
			var val=frmlist.cid.options[frmlist.cid.options.selectedIndex].value; 
		self.location='project.list.php?slctIndex=' + val+'&cIndex='+frmlist.cid.options.selectedIndex;
		}*/
		
	</script>
    
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onClick="location.href='project_purchase.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center><div align="center" id="message" style="width:400px;"></div></center>
<center>
<?php 
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Closed Purchase Orders</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
<form action="project_purchase.closed.php" method="post" name="frmlist">
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td height="35" colspan="5" ><strong>Search Projects </strong></td>
 </tr>
<tr>
<td width="85" class="grid001">Client Name: </td>
 	<td width="315" class="grid001">
      <select name="cid" id="cid" class="cid">
		<option value="">Select</option>
<?php for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
} 
?>
 </select></td>
  <td width="15" class="grid001">&nbsp;</td>
  <td width="93" class="grid001">Project Name: </td>
  <td width="125" class="grid001">
  <select  name="pid" id="pid" class="pid">
	<option value="">Select</option>
    </select>
</td>
<td width="15" class="grid001">&nbsp;</td>
                        <td width="105" class="grid001">Purchase Order:</td>
                        <td width="163" class="grid001"><input type="text" name="purchase" value="<?php echo $_REQUEST['purchase'] ?>" /></td>
                        <td width="15" class="grid001">&nbsp;</td>
                    <!--    <td width="55" class="grid001">Style: </td>
                        <td width="261" class="grid001"><input type="text" name="style" id="style" value="<?php echo $_REQUEST['style'] ?>" /></td>-->
 </tr>
<tr>                 <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"> <input type="submit" value="Cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody></table>
</form>
<div style="width:100%;overflow-x:scroll;">
<table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">

	<thead style="border:1px white solid;" >
    <tr class="sortable" > 
			<th class="sortableB" height="10" style="width:300px;">Client </th>
            <th class="sortableB" height="10" style="width:200px;">Project Manager</th>
			<th class="sortableB" style="width:200px;">Project Name</th>
			<th class="sortable-numericB" style="width:150px;">Purchase Order</th>
            <th class="sortable-numericB" style="width:100px;">PO Due Date</th>
			<th class="sortable-numericB" style="width:100px;">Project Quote</th>
			<!--<th class="sortable-numericB" style="width:300px;">Style</th>-->
            <th class="sortable-numericB" style="width:100px;">Order Placed On</th>
			<th class="sortable-numericB" style="width:100px;">Proj. Est. Unit Cost</th>
			<th class="sortable-numericB" style="width:200px;">Billed</th>
			<th class="gridHeaderBClose" style="width:50px;">Edit</th>
            <th class="gridHeaderBClose" style="width:50px;">New Order</th>
            <?php if(strtolower($USERNAME) == 'neal' || strtolower($USERNAME) == 'nicole' || strtolower($USERNAME) == 'rejith') { ?>
            <th class="gridHeaderBClose" style="width:50px;">Delete</th>
            <?php } ?>
		  </tr>
 	
		  </thead><tbody>
		  <?php 
if(count($datalist)) 
{
	for($i=0; $i < count($datalist); $i++)
	{
		echo "<tr>";
		echo '<td class="grid001B">'.$datalist[$i]['client'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['firstname'].$datalist[$i]['lastname'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['projectname'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['purchaseorder'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['purchaseduedate'].'</td>';
		echo '<td class="grid001B">$'.$datalist[$i]['prjquote'].'</td>';
		//echo '<td class="grid001B">'.$datalist[$i]['style'].'</td>';
		echo '<td class="grid001B">'.$datalist[$i]['order_placeon'].'</td>';		
		echo '<td class="grid001B">$'.$datalist[$i]['prj_estimatecost'].'</td>';
		echo '<td class="grid001B" id="bill_'.$i.'" > <div onclick="javascript:editBilledinfo('.$datalist[$i]['pid'].', \'load\', '.$i.');" >';
		if($datalist[$i]['is_billed'] != '' && $datalist[$i]['is_billed'] > 0)
			echo '&nbsp;Yes&nbsp;:&nbsp;'.date('m/d/Y',$datalist[$i]['bill_date']);
		else 	
			echo '&nbsp;No&nbsp;';
		echo '</div></td>';
		echo '<td class="grid001B"><a href="project_mgm.add.php?close=1&id='.$datalist[$i]['pid'].'&'.$paging.'"><img src="'.$mydirectory.'/images/edit.png" alt="edit" /></a></td>';
		echo '<td class="grid001B"><a href="#" onclick="javascript:popOpen('.$datalist[$i]['pid'].');"><img src="'.$mydirectory.'/images/bullet_add.png" alt="reopen" /></a></td>';
		 if(strtolower($USERNAME) == 'neal' || strtolower($USERNAME) == 'nicole' || strtolower($USERNAME) == 'rejith') { 
		 $q_string = '?del='. $datalist[$i]['pid'];
		 if($_SESSION['search_uri'] != '')
		 	$q_string = $_SESSION['search_uri'].'&del='. $datalist[$i]['pid'];
		 $q_string .= '&'.$paging;
		echo '<td class="grid001B"><a href="project_purchase.closed.php'. $q_string .'" onclick="javascript: if(confirm(\'Are you sure you want to delete the project\')) { return true; } else { return false; }"><img src="'.$mydirectory.'/images/delete.png" alt="delete" /></a></td>';
		 }
		echo "</tr>";
	}
	echo 	'</tbody><tr>
			<td width="100%" class="grid001B" colspan="13=">'.$p->show().'</td>			
		  </tr>';	
} 
else 
{
	echo "</tbody><tr>";
	echo '<td align="left" colspan="11"><font face="arial"><b>No Project Found</b></font></td>';
	echo "</tr>";
}
?>
</tbody>
</table>
</div></center>
<?php require('../../trailer.php'); ?>


<div id="textPop" class="popup_block">
<fieldset style="padding:15px;">
<table width="100%" border="0" cellspacing="2" cellpadding="0">
<tr height="25px">
<td>Project Name :</td>
    <td align="left"><input name="project_name" id="project_name" value="" type="text" /><input name="project_id" id="project_id" value="" type="hidden" /></td>
    </tr>
     <tr>
  <td height="25px">&nbsp;</td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr height="25px">
          <td colspan="2" align="center"><input type="button" id="desc_submit_id" value="Submit" onClick="javascript:copyPurchaseOrder();Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</fieldset>
</div>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script type="text/javascript">
function copyPurchaseOrder()
{
	var project_name = document.getElementById('project_name').value;
	var project_id = document.getElementById('project_id').value;
	dataString = "prj_name="+project_name+"&prj_id="+project_id;
	
	$.ajax({
	 type: "POST",
	 url: "new_order.add.php",
	 data: dataString,
	 dataType: "json",
	 success:
	function(data)
	{
		if(data!=null)
		{
			if(data.name || data.error)
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
			}
			else 
			{
				alert('Purchase Order Copied');
				$(location).attr('href',"project_purchase.closed.php");
			}
		}
		else
		{
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		}
	}
});
}
function popOpen(id)
{
	//here textId acts as count of row
	var popID = 'textPop'; //Get Popup Name
	
	popWidth = '500';
	document.getElementById('project_id').value = id;
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
	document.getElementById('project_id').value="";
	document.getElementById('project_name').value="";
	$('#fade , .popup_block').fadeOut();
}
$(document).ready(function()
{
  $(".cid").change(function()
	{
		
		var id=$(this).val();
		var dataString = 'clientid='+ id+"&list_type=1&status=0";
		$.ajax
		({
			type: "POST",
			url: "projectname.list.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$(".pid").html(html);
			} 
		});
	});
});
function showDate(obj)
{
	$(obj).datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(obj).datepicker('show'); });
	$(obj).datepicker('show');
}
function editBilledinfo(pid,type,td_id)
{
	dataString ='pid='+pid+'&type='+type+'&td='+td_id;
	if(type == 'save')
	{
		if($('#is_billed_'+td_id).is(":checked"))
			dataString += '&is_billed=1';
		else
			dataString += '&is_billed=0';
		if($('#bill_date_'+td_id).val() != '') dataString += '&bill_date='+$('#bill_date_'+td_id).val(); else dataString += '&bill_date=0';
	}	
	$.ajax({
		   type: "POST",
		   url: "edit_bill.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					$('#bill_'+data.td).html(data.msg);
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}

</script>