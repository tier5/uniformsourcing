<?php
require('Application.php');
require('../../header.php');
include('../../pagination.class.php');
$eid =0;
$search_sql="";
$limit="";
$search_uri="";
$paging = 'paging=';
$type ='';

if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging .= $_GET['paging'];
}
else
{
	$paging .= 1;
}
$query2='SELECT distinct(lastname),"employeeID",firstname FROM tbl_change_record inner join "employeeDB" on tbl_change_record.employee_id="employeeDB"."employeeID" WHERE tbl_change_record.status=1 and "employeeDB".firstname IS NOT NULL';

if(!($result1=pg_query($connection,$query2))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result1)){
	$data2[]=$row2;
}
pg_free_result($result1);
if(isset($_REQUEST['eid']) && $_REQUEST['eid']!="")
{
	$search_sql=' and employee_id ='.$_REQUEST['eid'].' ';
	if(!isset($_GET['eid']))
		$search_uri="?eid=".$_REQUEST['eid'];
	$eid = $_REQUEST['eid'];
}
$query="SELECT tbl_change_record.*,e.firstname,e.lastname,prch.status as po_status FROM tbl_change_record  left join \"employeeDB\" e on e.\"employeeID\"=tbl_change_record.employee_id left join tbl_prjpurchase as prch on prch.pid=tbl_change_record.module_id where tbl_change_record.status = 1 ".$search_sql." ORDER BY tbl_change_record.id DESC ";
//echo $query;
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
	$data_log[]=$row;
	
}
pg_free_result($result);
for($index=0;$index<count($data_log);$index++)
{
	
}
?>
<center>
<font size="5">
Track Log List</font></center>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='../index.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
 
<table width="90%" cellspacing="1" cellpadding="1" border="0">
<tbody><tr>
<td colspan="2" height="35" colspan="5" ><strong>Search Projects </strong></td>
</tr><tr >
<td class="grid001">
<form action="track_log.php" method="post" name="frmlist">
<table>
<tr>
	
    <td width="100px" class="grid001"> Employee Name: </td> 
    <td width="300px" class="grid001"><select name="eid" style="width:200px;">
        <option value="">-----Select------</option>
		<?php for($i=0; $i < count($data2); $i++){
			if($eid == $data2[$i]['employeeID'])
       			 echo "<option value=\"".$data2[$i]['employeeID']."\" selected=\"selected\">".$data2[$i]['firstname']." ".$data2[$i]['lastname']."</option>";
       		else 
       			echo "<option value=\"".$data2[$i]['employeeID']."\">".$data2[$i]['firstname']." ".$data2[$i]['lastname']."</option>";	 
        } ?>
    </select></td> 
    <td  align="left" class="grid001" ><input type="submit" value="Search" onmouseover="this.style.cursor = 'pointer';" name="button"></td> <td  align="left" class="grid001" ><input type="button" value="Cancel" id="cancel" onmouseover="this.style.cursor = 'pointer';" name="cancel"></td>
    </tr>
</table>
</form>
</td>
<td class="grid001">
<form action="delete_audit.php" method="post" name="frmlist">
<table>
<tr>
 <td  align="left" class="grid001" width="100px">Delete Audit Till:</td><td width="300px" class="grid001"><input type="hidden" name="eid" value="<?php echo $eid;?>"><input type="text" name="delete_date" onclick="javascript:showDate(this);"><input type="submit" value="Delete" onmouseover="this.style.cursor = 'pointer';"></td>
 </tr>
</table>
</form>
</td>
 </tr>
</tbody></table>




<table width="100%">
<tr>
  <td align="left" valign="top"><center>
    <table width="100%">
        <tr>
          <td align="center" valign="top">
             <table width="80%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td colspan="5">&nbsp;</td>
                </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
              </tr>
            </table>
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td class="gridHeader">Employee Name</td> 
                <td class="gridHeader">Module</td>
                <td class="gridHeader">Log Date/Time</td>
                <td class="gridHeader">log Description</td>
              </tr>
              <?php
              $module_text='';
			  if(count($data_log) > 0)
			  {
				  for($i = 0; $i < count($data_log); $i++)
				  {
				  	if($data_log[$i]['log_desc']!="")
				  	{
				 		switch($data_log[$i]['module'])
				 		{
				 			case 'Project':
			 				{
			 					$redirect_url="../project_mgm/project_mgm.add.php?id=";
			 					$type ='';
			 					break;
			 				}
				 			case 'Inventory':
				 			{
				 				$redirect_url="../inventory/styleAdd.php?ID=";
				 				$type ='&type=e';
			 					break;		
				 			}
				 			case 'SizeScale':
				 			{
				 				$redirect_url="../inventory/sizeScaleAdd.php?id=";
				 				$type = '&type=e';
			 					break;		
				 			}
				 			case 'Quote':
				 			{
				 				$redirect_url="../quote/add_quote.php?qid=";
				 				$type ='';
			 					break;		
				 			}
				 			case 'GenerateRequest':
				 			{
				 				$redirect_url="../generate_request/add_quote.php?qid=";
				 				$type ='';
			 					break;		
				 			}
				 			case 'SampleDatabase':
				 			{
				 				$redirect_url="../sample_database/database_sample_add.php?id=";
				 				$type ='';
			 					break;		
				 			}		
				 			
				 		}
			  ?>
              <tr>
                <td class="grid001"><?php echo $data_log[$i]['firstname']." ".$data_log[$i]['lastname'];?></td>
                <td class="grid001"><a href="<?php echo $redirect_url;?><?php echo $data_log[$i]['module_id'].$type;?>">
                
			  <?php
				if($data_log[$i]['module'] == 'Project')
				 {
					if($data_log[$i]['po_status']=='1')
						$module_text = 'PO : '.$data_log[$i]['project_or_po'];
					else 
						$module_text = 'Project : '.$data_log[$i]['project_or_po'];
					echo $module_text;
				  }
				  else
				  {
				  	echo $data_log[$i]['module'];
				  }
			  ?>
			  
			  </a>
			  </td>
                <td class="grid001"><?php if($data_log[$i]['log_date'] !=""){ echo date('m/d/Y h:i:s A',$data_log[$i]['log_date']);}else {?>&nbsp;<?php }?></td>
                <td class="grid001"><?php echo $data_log[$i]['log_desc'];?></td> </tr>              
             <?php
				  	}
				  }
				  echo 	'<tr>
			<td width="100%" class="grid001" colspan="7">'.$p->show().'</td>			
		  </tr>';
			  }
			  else
			  {
				  echo '<tr><td colspan="7" class="grid001">No Records found</td><tr>';
			  }
			 ?>          
              <tr>
                <td colspan="5">&nbsp;</td>
              </tr>
            </table>
            </td>
        </tr>
      </table>
      <p>
  </center></td>
</tr>
</table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript">
function showDate(obj)
{
	$(obj).datepicker({
            changeMonth: true,
            changeYear: true
        }).click(function() { $(obj).datepicker('show'); });
	$(obj).datepicker('show');
}
$("#cancel").click(function(){$(location).attr('href',"track_log.php");
});
</script>
<?php 
require('../../trailer.php');
?>