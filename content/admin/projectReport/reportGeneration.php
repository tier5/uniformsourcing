<?php
$current_page ="reportGeneration.php";
$type = "report";
$prj_tbl='tbl_newproject';
if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging .= $_GET['paging'];
}
else
{
	$paging .= 1;
}
require('Application.php');

if(isset($_POST['reset']))
{
	$_SESSION['search_uri'] = "";
	header('location: reportGeneration.php');
}
$style_val ='';
if(isset($_SESSION['style_val']))
{	
	$style_val = $_SESSION['style_val'];
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
$join_column= "";
$_SESSION['page'] = $current_page;
$alphaKey = "";
$isAlphaSearch = 0;
$searchQuery = "";
$limit="";

if(isset($_GET['del']))
{
    
    
       
 $tbl_list=array(array('tbl_newproject','rr'),array('tbl_prjpurchase','purchaseId'),array('tbl_prjimage_file','tbl_prjimage_file'),
     array('tbl_prj_style_custom','tt'),array('tbl_prj_style','prj_style_id'),
  array('tbl_prjvendor','tbl_vendorid'),array('tbl_prjsample_uploads','upload_id'),
 array('tbl_prjsample_notes','notes_id'),array('tbl_prj_sample_po_items','id'),array('tbl_prj_sample_po','id'),array('tbl_prj_sample','sample_id'),
 array('tbl_prjpricing','pricingId'),array('tbl_prmilestone','id'),array('tbl_prj_elements','prj_element_id'),
 array('tbl_mgt_notes','notesid'),array('tbl_upload_pack','pid'),
 array('tbl_prjorder_shipping','shipping_id'), array('tbl_prjorder_track_no','track_id'));
	$pid=$_GET['del'];
	$qName="UPDATE tbl_newproject ".
			"SET ".
			"status = '0', closed_date = '".date('U')."' ,updateddate = '".date('U')."' ".
			"WHERE pid = ".$pid;
 $columns='';
  $values='';
    for($i=0;$i< count($tbl_list);$i++)
    {
if($qName!='')$qName.=';';   

    $sql = 'SELECT * from "'.$tbl_list[$i][0].'" where pid='.$pid;
//echo $sql;
    unset($prj_data);unset($row2);
 if (($r = pg_query($connection, $sql))) {
  
    while($row2 = pg_fetch_array($r))
    { 
           unset($prj_data);
           $prj_data = $row2;
$columns='';$values='';


          $j=0; 
 if($prj_data[0]!='')
 {
    
        foreach($prj_data as $key => $value) {
   
            if($j%2!=0 && $value!="")
            {
                 
        if($columns=='')  
         {
                $columns.='"'.$key.'"';
                $values.="'". pg_escape_string($value)."'";
         }
            else  
                { 
            $columns.=',"'.$key.'"';
            $values.=",'".pg_escape_string($value)."'";
            }
            }
$j+=1;            
}      
 
  if($columns!=""&&$values!="")
  {      
$qName.=';insert into "'.$tbl_list[$i][0].'_closed" ('.$columns.') values('.$values.')';  

    }  
  
 }
    }
   pg_free_result($r);
   if($tbl_list[$i][0]!='tbl_newproject') 
   $qName.=';delete from '.$tbl_list[$i][0].' where pid='.$pid;   
        $sql='';


        
    }     
    else
        echo pg_last_error($connection);
 }  
 $qName.=';delete from tbl_newproject where pid='.$pid;    
//echo $qName;
   if (!($result = pg_query($connection, $qName)))
        {
       echo pg_last_error($connection);
        exit(); 
        }   
        

  
   header("location: reportGeneration.php");
}
if(isset($_GET['close']))
{
     $ID=$_GET['close'];
	$query1="UPDATE tbl_newproject ".
			"SET ".
			"status = '0', closed_date = '".date('U')."' ,updateddate = '".date('U')."' ".
			"WHERE pid = ".$ID;
	if(!($result1=pg_query($connection,$query1))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	 header("location: reportGeneration.php?$paging");
}
$allAlphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
require('../../header.php');
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.js"></script>';

$sql = 'select Distinct(sample_id),id  from tbl_prj_sample as sr inner join tbl_newproject as p on sr.pid = p.pid where sr.status=1';
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_sample[]=$row1;
}
pg_free_result($result1);
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
$query3="SELECT \"vendorID\",\"vendorName\" from vendor where active='yes' ORDER BY \"vendorName\" asc";
if(!($result3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$vendorData[]=$row3;
}

$query3='SELECT distinct("employeeID"),firstname,lastname FROM tbl_newproject inner join "employeeDB" on tbl_newproject.project_manager="employeeDB"."employeeID" WHERE tbl_newproject.status=1';
if(!($result1=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result1)){
	$data_project_manager[]=$row3;
}
pg_free_result($result1);
/* The search query*/
			
		include('../../pagination.class.php');
		$proj_status = 1;
	if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="") {
		$search_sql=' and p.client ='.$_REQUEST['cid'].' ';
		$search_uri="?cid=".$_REQUEST['cid'];
		$_SESSION['search_uri'] = $search_uri;
	}
           $tx='';
	if(isset($_REQUEST['closed_purchase'])) {
         
$tx='_closed';
		$proj_status = 0;
		$prj_tbl='tbl_newproject_closed';
                if($search_uri)  {
                  $search_uri .="&closed_purchase=1";  
                }
                else
		$search_uri .="?closed_purchase=1";
		$_SESSION['search_uri'] = $search_uri;
	}
	if(isset($_REQUEST['past_due_purchase'])) {
		//$search_sql .=' and EXTRACT(EPOCH FROM pr.purchaseduedate::date) < EXTRACT(EPOCH FROM now())';
		$search_uri .="?past_due_purchase=1";
		$_SESSION['search_uri'] = $search_uri;
	}
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="") {
		$search_sql .=' and p.pid ='.$_REQUEST['pid'].' ';
		if($search_uri)  {
			 $search_uri.="&pid=".$_REQUEST['pid'];
		} else {
			$search_uri="?pid=".$_REQUEST['pid'];
		}
		$_SESSION['search_uri']= $search_uri;
	}	
	if(isset($_REQUEST['manager_id']) && $_REQUEST['manager_id']!="") {
		$search_sql .=' and p.project_manager ='.$_REQUEST['manager_id'].' ';
		if($search_uri)  {
			 $search_uri.="&manager_id=".$_REQUEST['manager_id'];
		} else {
			$search_uri.="?manager_id=".$_REQUEST['manager_id'];
		}
		$_SESSION['search_uri']= $search_uri;
	}	
	if(isset($_REQUEST['vendorId']) && $_REQUEST['vendorId']!="") {
		$search_sql .=' and pv.vid ='.$_REQUEST['vendorId'].' ';
		if($search_uri)  {
			 $search_uri.="&vendorId=".$_REQUEST['vendorId'];
		} else {
			$search_uri="?vendorId=".$_REQUEST['vendorId'];
		}
		$_SESSION['search_uri']= $search_uri;
	}
	if(isset($_REQUEST['purchase']) && $_REQUEST['purchase']!="") {
		$search_sql .=' and pr.purchaseorder  LIKE \'%' .$_REQUEST['purchase'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&purchase=".$_REQUEST['purchase'];
		} else {
			$search_uri.="?purchase=".$_REQUEST['purchase'];
		}
		$_SESSION['search_uri']= $search_uri;
	}
	if(isset($_REQUEST['style']) && $_REQUEST['style']!="") {
		$join_column = ',style.style';
		$search_sql .=' and style.style  ILIKE \'%' .$_REQUEST['style'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&style=".$_REQUEST['style'];
		} else {
			$search_uri.="?style=".$_REQUEST['style'];
		}
		$limit_style =" and limit 1";
		$_SESSION['search_uri']= $search_uri;
		$_SESSION['style_val']= $_REQUEST['style'];
	}
	if(isset($_REQUEST['color']) && $_REQUEST['color']!="") {
		$search_sql .=' and p.color  LIKE \'%' .$_REQUEST['color'].'%\' ';
		if($search_uri)  {
			 $search_uri.="&color=".$_REQUEST['color'];
		} else {
			$search_uri.="?color=".$_REQUEST['color'];
		}
		$_SESSION['search_uri']= $search_uri;
	}
	if(isset($_REQUEST['sampleNmbr']) && $_REQUEST['sampleNmbr']!="") {
		$search_sql .=' and sm.id =' .$_REQUEST['sampleNmbr'];
		if($search_uri)  {
			 $search_uri.="&sampleNumber=".$_REQUEST['sampleNmbr'];
		} else {
			$search_uri.="?sampleNumber=".$_REQUEST['sampleNmbr'];
		}
		$_SESSION['search_uri']= $search_uri;
	}
	if(isset($_REQUEST['notes']) && $_REQUEST['notes']!="") {
		$search_sql.=' and notes.notes LIKE \'%' .$_REQUEST['notes'].'%\' ';
		$search_uri.="?notes=".$_REQUEST['notes'];
		$_SESSION['search_uri'] = $search_uri;
	}
	if($proj_status == 1){
		$search_sql.=' and p.status = 1';
	}
		
	if($_SESSION['search_uri']!="")
	{		
		$_SESSION['page_type'] = $type;
	}
	/* Query to search all records within the date limit*/
			if (isset($_GET['alpha']))
			{	
				$alphaKey = $_GET['alpha'];
				$isAlphaSearch = 1;		
			}
			else if(isset($_POST['submit']) && $_POST['submit'] == "Search")
			{
				$searchQuery = "and UPPER(style) LIKE UPPER('".pg_escape_string($_POST['client'])."%')";
			}
			if(isset($_POST['cancel']))
			{
				$sql="";
				$search_sql="";
				$search_uri="";
				$_SESSION['search_uri'] = "";
			}
			if($isAlphaSearch ==1)
			{
				$sql = "SELECT * FROM tbl_prj_style$tx WHERE status=1 and UPPER(style) LIKE '$alphaKey%'";
			}
			if(isset($_REQUEST['fromDate']) && isset($_REQUEST['toDate']))
			{
				if($_REQUEST['fromDate']!="")
				{
					$toDate= strtotime($_REQUEST['toDate']);
					if($toDate=="")
						$toDate=date('U');
				$sql='select DISTINCT(p.projectname),p.pid,pr.purchaseorder,pr.purchaseduedate,c."client",sr."srID"'.$join_column.' from tbl_newproject'.$tx.' as p  inner join "clientDB" c on p.client=c."ID" left join tbl_prj_sample'.$tx.' as sm on sm.pid =p.pid left join tbl_prj_style'.$tx.' as style on style.pid = p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join tbl_mgt_notes'.$tx.' as notes on notes.pid = p.pid left join tbl_prjpurchase'.$tx.' as pr on pr.pid= p.pid  where p.projectname <> \'\' '.$search_sql.' and p.created_date between '.strtotime($_REQUEST['fromDate']).' and '.$toDate.' ';
					if($search_uri) 
					{
						 $search_uri.="&fromDate=".$_REQUEST['fromDate'];
						 if($_REQUEST['toDate'] == "")
							$search_uri.="&toDate=".$toDate; 
						 else
							$search_uri.="&toDate=".$_REQUEST['toDate']; 
					} 
					else 
					{
						$search_uri="?fromDate=".$_REQUEST['fromDate'];
						if($_REQUEST['toDate'] == "")
							$search_uri.="&toDate=".$toDate; 
						else
							$search_uri.="&toDate=".$_REQUEST['toDate']; 								
					}
				}
				else
				{
					$sql=' select DISTINCT(p.projectname), p.pid,c."client",pr.purchaseorder,pr.purchaseduedate'.$join_column.' from '.$prj_tbl.' as p inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase'.$tx.' as pr on pr.pid= p.pid left join tbl_prj_style'.$tx.' as style on style.pid = p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join tbl_mgt_notes'.$tx.' as notes on notes.pid = p.pid left join tbl_prj_sample'.$tx.' as sm on sm.pid =p.pid where p.projectname <> \'\' '.$search_sql.'  ';
				}
			}
			else 
			{
				$sql='select DISTINCT(p.projectname),p.pid,c."client",pr.purchaseorder,pr.purchaseduedate'.$join_column.' from '.$prj_tbl.' as p inner join "clientDB" c on p.client=c."ID" left join tbl_prjpurchase'.$tx.' as pr on pr.pid= p.pid left join tbl_prj_style'.$tx.' as style on style.pid = p.pid left join tbl_prjvendor pv on pv.pid=p.pid left join tbl_mgt_notes'.$tx.' as notes on notes.pid = p.pid left join tbl_prj_sample'.$tx.' as sm on sm.pid =p.pid where p.projectname <> \'\' '.$search_sql.' ';
			
			}
			//echo $sql;
			if($searchQuery != "")
			{
				$sql .= $searchQuery;
			}	
			//echo $sql;
			if(!($result=pg_query($connection,$sql))){
				print("Failed queryd: " . pg_last_error($connection));
				exit;
			}
	$items= pg_num_rows($result);
	if($items > 0) {
		$p = new pagination;
               
		$p->items($items);
		$p->limit(20);
                 // Limit entries per page
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
	$sql = $sql. " ". $limit;
	//echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed queryd: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$datalist[]=$row;
	}
?>
<center>
<div style="width:50%" id="message"></div></center>
<center><font size="5">Report Generation <br/>
                      <br/>
                    </font></center>
          <form name="frmReport" method="post" action="reportGeneration.php" autocomplete="off">
          <table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><input type="button" value="Genereate Spreadsheet" onclick="javascript:GenerateSpreadSheet();" /><input type="button" value="Send Email" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:SendEmail();" style="cursor: pointer;"></td>
    <td>&nbsp;</td>
  </tr>
</table>
            <table width="100%">
                <tr>
                  <td align="center">
                   <table width="95%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td width="150" height="35"><strong>Search Projects </strong></td>
                        <td width="200">&nbsp;</td>
                        <td width="10">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">From Date : </td>
                        <td class="grid001"><input type="text" name="fromDate" id="fromDate" value=""  readonly="readonly"/></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">To Date : </td>
                        <td class="grid001"><input type="text" name="toDate" id="toDate" readonly="readonly"/>&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Client: </td>
                        <td class="grid001"><select  id="cid" class="cid" name="cid" style="width:200px;" >
                          <option value="">---- Select Client ----</option>
                          <?php 
						  	for($i=0; $i<count($data1); $i++)
							{
						  ?>
                          <option value="<?php echo $data1[$i]['ID'];?>"><?php echo $data1[$i]['client'];?></option>
                          <?php 
							}
						  ?>
                        </select>
                        </td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Vendor:</td>
                        <td class="grid001"><select id="vendorId" class="vid" name="vendorId" style="width:200px;">
                          <option value="">---- Select Vendor ----</option>
                           <?php 
						  	for($i=0; $i<count($vendorData); $i++)
							{
						  ?>
                          <option value="<?php echo $vendorData[$i]['vendorID'];?>"><?php echo $vendorData[$i]['vendorName'];?></option>
                          <?php 
							}
						  ?>
                        </select></td>
                      </tr>
                      <tr>
                        <td class="grid001">Project Manager:</td>
                        <td class="grid001"><select name="manager_id" id="manager_id" style="width:200px;">
        <option value="">-----Select------</option>
		<?php for($i=0; $i < count($data_project_manager); $i++){
        echo "<option value=\"".$data_project_manager[$i]['employeeID']."\">".$data_project_manager[$i]['firstname']." ".$data_project_manager[$i]['lastname']."</option>";
        } ?>
    </select> </td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Project Name : </td>
                        <td class="grid001"><select id="pid" class="pid" name="pid" style="width:200px;">
                          <option value="">---- Select Project ----</option>
                        </select>&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Style: </td>
                        <td class="grid001"><input type="text" name="style" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" value="<?php echo $style_val;?>" /></td>
                        <!--  -->
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Color:</td>
                        <td class="grid001"><input type="text" name="color" />                      
                     </tr>
                      <tr>
                        <td class="grid001">&nbsp;</td>
        <td class="grid001" align="left" valign="top"><div class="suggestionsBox" id="suggestions" style="display: none;">
				<img src="<?php echo $mydirectory;?>/images/upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />
				<div class="suggestionList" id="autoSuggestionsList">
					&nbsp;
				</div>
			</div></td>  
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Purchase Order:</td>
                        <td class="grid001"><input type="text" name="purchase" /></tr>
                      <tr>
                        <td class="grid001">Sample Number:</td>
                        <td class="grid001"><select name="sampleNmbr" id="sampleNmbr" style="width:200px;">
                          <option value="">---Select---</option>
                          <?php
						$sampleIndex = "";
						for($i=0; $i < count($data_sample); $i++)
						{
							echo '<option value="'.$data_sample[$i]['id'].'">'.$data_sample[$i]['sample_id'].'</option>';
						}
?>
                        </select></td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Notes:</td>
                        <td class="grid001"><input type="text" name="notes" value="<?php echo $_REQUEST['notes']; ?>" /></td>
                      </tr>
                      <tr>
                        <td class="grid001">&nbsp;</td>
                        <td colspan="3" class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                      </tr>
                        <tr>
                        <td class="grid001"><b>Report Options</b></td>
                        <td colspan="3" class="grid001">&nbsp;</td>
                        <td class="grid001">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="grid001">Report Includes:</td>
                        <td class="grid001" style="width:300px;"><input type="checkbox" name="closed_purchase" id="closed_purchase" <?php if (isset($_REQUEST['closed_purchase'])) echo 'checked="checked"'; ?>/> Closed Purchase Orders<br /><input type="checkbox" name="past_due_purchase" id="past_due_purchase" <?php if (isset($_REQUEST['past_due_purchase'])) echo 'checked="checked"'; ?> /> Past Due Purchase Orders
</td>
                        <td class="grid001">&nbsp;</td>
                        <td class="grid001">Summary:</td>
                        <td class="grid001"><input type="checkbox" name="total" id="total" /> Price Total
                        </td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input name="search" type="submit" onMouseOver="this.style.cursor = 'pointer';" value="Search">
                        <input name="reset" type="submit"  onMouseOver="this.style.cursor = 'pointer';" value="Reset"></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                    </td>
                </tr>
              </table>
              </form>
              <form action="" name="frm_send_email" id="frm_send_email">
                  <table width="95%" cellspacing="1" cellpadding="1" border="0" class="no-arrow rowstyle-alt">

                    <thead>
                    <tr class="sortable"> 
                     <th class="gridHeader" height="10"><center>Select All</center>CSR<input type="checkbox" name="csrAll" id="csrAll" /> VSR<input type="checkbox" name="vsrAll" id="vsrAll" /></th>
                            <th class="sortable">Client Name </th>
                            <th class="sortable">Project Name</th>
                            <th class="sortable">Purchase Order</th>
                            <th class="sortable-numeric">PO Due Date</th>
                          <?php if (!isset($_REQUEST['closed_purchase']))echo '<th class="gridHeader">Close</th>';?>
                            <th class="gridHeader">Deactivate</th>
                          </tr>
                          </thead>
                          <tbody>
							  <?php 
                    if(count($datalist)) 
                    {
                        for($i=0; $i < count($datalist); $i++)
                        {
							$po_style='style="color:#000;"';
							if(trim($datalist[$i]['purchaseduedate'])!= '')
							{
								$today = strtotime(date("Y-m-d"));								
								$timestamp = strtotime($datalist[$i]['purchaseduedate']);
								if($timestamp < $today)
									$po_style='style="color:red;"';
							}
                          ?><tr>
                        	 <td class="grid001">CSR<input type="checkbox" name="csr[]" value="<?php echo $datalist[$i]['pid']?>"> VSR<input type="checkbox" name="vsr[]" value="<?php echo $datalist[$i]['pid']?>"></td>
                            <td class="grid001"><?php echo $datalist[$i]['client'];?></td>
                            <td class="grid001"><a href="<?php echo $mydirectory;?>/admin/project_mgm/project_mgm.add.php?id=<?php echo $datalist[$i]['pid'];?>&<?php echo $paging; 
                            if(isset($_REQUEST['closed_purchase'])) {
                                echo "&close=1";
                            } 
                            ?>"><?php echo $datalist[$i]['projectname'];?></a></td>
                            <td class="grid001"><?php echo $datalist[$i]['purchaseorder'];?></td>
                            <td class="grid001" <?php echo $po_style;?>><?php echo $datalist[$i]['purchaseduedate'];?></td>
                          <?php if (!isset($_REQUEST['closed_purchase'])){ ?>	<td class="grid001"><a href="reportGeneration.php?close=<?php echo $datalist[$i]['pid'];?>"><img src="<?php echo $mydirectory;?>/images/close.png" alt="close" width="32" height="32"></a></td> <?php } ?>
                            <td class="grid001"><a href="reportGeneration.php?del=<?php echo $datalist[$i]['pid'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" alt="deactivate" width="24" height="24"></a></td>
                           </tr>
                    <?php } ?>
                       </tbody>
                                <tr>
                                <td width="100%" class="grid001" colspan="11"><?php echo $p->show();?></td>			
                              </tr>	
                    <?php } 
                    else 
                    {?>
                        <tr>
                        <td align="left" colspan="11"><font face="arial"><b>No Project Found</b></font></td>
                        </tr>
                    <?php }?> 
                    </table>
      </form>             
<?php 
   	require('../../trailer.php');
?>
   <script type="text/javascript">
function GenerateSpreadSheet()
{
	dataString ='';
	<?php 
	if($search_uri !="")
	{
		echo "dataString=\"".substr($search_uri,1)."\";";
	}
	?>	
	$.ajax({
		   type: "POST",
		   url: "generateSpreadsheet.php",
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
						$("#message").html("<div class='successMessage'><strong>Spread sheet generated successfully...</strong></div>");
						location.href='download.php?file='+data.fileName;
					}
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}

$(document).ready(function()
{
	$(".cid").change(function()
	{
		if(document.getElementById('cid').value)
		{
	var id=$(this).val();
	var dataString = 'clientid='+ id+'&vendorid='; 
	var vendorVal=document.getElementById('vendorId').value;
	if(vendorVal)
	dataString += vendorVal;
	else
	dataString += "";
		$.ajax
		({
			type: "POST",
			url: "projectNameList.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
			$(".pid").html(html);
			if(vendorVal=="")
			{
				$.ajax
				({
					type: "POST",
					url: "vendorNameList.php",
					data: dataString,
					cache: false,
					success: function(html)
					{
					$(".vid").html(html);
					
					} 
				});
			}
			} 
		});
		}
	});
	$(".vid").change(function()
	{
		if(document.getElementById('vendorId').value)
		{
			var id=$(this).val();
			var dataString = 'vendorid='+ id +'&client='; 
			var clientVal=document.getElementById('cid').value;
			if(clientVal)
			dataString += clientVal;
			else
			dataString += "";
						$.ajax
						({
							type: "POST",
							url: "vendorNameList.php",
							data: dataString,
							cache: false,
							success: function(html)
							{
							$(".pid").html(html);
							if(clientVal=="")
							{
									$.ajax
								({
									type: "POST",
									url: "clientNameList.php",
									data: dataString,
									cache: false,
									success: function(html)
									{
									$(".cid").html(html);
									}
								});
							}
						}
						});
		}
	});
	$("#manager_id").change(function()
	{
	if(document.getElementById('manager_id').value)
	{
		var id=$(this).val();
		var dataString = 'manager_id='+ id; 
		$.ajax
		({
			type: "POST",
			url: "projectNameList.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$(".pid").html(html);
			} 
		});
		}
	});
	
	
	
	if($("#fromDate")) {
	$(function() {
		$("#fromDate").datepicker();
	});
}
if($("#toDate")) {
	$(function() {
		//$("#deliveryDate").datepicker();
		$('#toDate').datepicker( {
			onSelect: function(date) {
				var reDt = $("#fromDate").val();
            	if( !( Date.parse(reDt) <= Date.parse(date)) ){
					alert("Enter a date greater than the 'From Date'");
					$("#toDate").val("");
				}				
			}
		});
	});
}		

});

$('#csrAll').change(function() {
	 $("INPUT[name='csr\\[\\]']").attr('checked', $('#csrAll').is(':checked'));   
   }
)
$('#vsrAll').change(function() {
	 $("INPUT[name='vsr\\[\\]']").attr('checked', $('#vsrAll').is(':checked'));   
   }
)
function SendEmail()
{
	dataString ='';
	dataString = $("#frm_send_email").serialize();
	$.ajax({
		   type: "POST",
		   url: "send_email.php",
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
						$("#message").html("<div class='successMessage'><strong>Mail Send successfully...</strong></div>");
					}
				}
				else
				{
					$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
	function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$.post("style_rpc.php", {queryString: ""+inputString+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	} // lookup
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
</script>
