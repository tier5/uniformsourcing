<?php
session_start();
require('Application.php');
if (isset($_GET['qid']) && isset($_GET['opt']) && $_GET['opt'] == 1) {
    $id = $_GET['qid'];
    $sql = "Delete from tbl_quote_items where qid = " . $id . ";";
    $sql.="Delete from tbl_request where qid = $id";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed delete_quote: " . pg_last_error($connection));
        exit;
    }
    header('location:quoteList.php');
}
require('../../header.php');

if (isset($_REQUEST['cid']) && $_REQUEST['cid'] != "") {
    if($_REQUEST['cid']== -1)
        unset($_SESSION['cid']);
    else
    $_SESSION['cid']= $_REQUEST['cid'];
    
    }
    if (isset($_SESSION['cid']) && $_SESSION['cid'] != "")
     $search_sql .=' and q.client_id = ' . $_SESSION['cid'] . ' ';

if (isset($_REQUEST['companyid']) && $_REQUEST['companyid'] != "") {
     if($_REQUEST['companyid']==-1)
       unset($_SESSION['companyid']);
    else
     $_SESSION['companyid']=$_REQUEST['companyid'];}
   
     if (isset($_SESSION['companyid']) && $_SESSION['companyid'] != "")
     $search_sql .=' and q.company_id = ' . $_SESSION['companyid'] . ' ';
     
     
     if (isset($_REQUEST['q_id']) && $_REQUEST['q_id'] != "") {
     if($_REQUEST['q_id']==-1)
       unset($_SESSION['q_id']);
    else
     $_SESSION['q_id']=$_REQUEST['q_id'];}
   
     if (isset($_SESSION['q_id']) && $_SESSION['q_id'] != "")
     $search_sql .=" and q.po_number = '" . $_SESSION['q_id'] . "'";


     if (isset($_REQUEST['projectname']) && $_REQUEST['projectname'] != "") {
    if($_REQUEST['projectname']== -1)
        unset($_SESSION['projectname']);
    else
    $_SESSION['projectname']= $_REQUEST['projectname'];
    
    }
    if (isset($_SESSION['projectname']) && $_SESSION['projectname'] != "")
     $search_sql .=" and q.project_name = '" . $_SESSION['projectname'] . "'";
     

if(isset($_GET['sort'])&&$_GET['sort']!="")
{
 

     $sort= " ORDER BY  c.client  ".$_GET['sort'];
  // unset($_SESSION['sort']); 
}
//echo $search_sql;
$query = "SELECT q.*,cmp.company,c.client FROM tbl_request q left join \"clientDB\" c on q.client_id=c.\"ID\" left join tbl_quot_company as cmp on q.company_id = cmp.company_id where q.status = 1 ".$search_sql."    ".$sort;
//echo $query;
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
	$data_quote[]=$row;
}
pg_free_result($result);

$query='SELECT distinct "client","ID"  FROM "clientDB"'; 
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_client[]=$row;}
pg_free_result($result);

$query='SELECT distinct "company","company_id"  FROM "tbl_quot_company"'; 
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_cmp[]=$row;}
pg_free_result($result);

$query='SELECT distinct "po_number"  FROM "tbl_request"'; 
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_quote_number[]=$row;}
pg_free_result($result);

?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='../../index.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%">
<tr>
  <td align="left" valign="top"><center>
    <table width="100%">
        <tr>
          <td align="center" valign="top"><font size="5">Quote List<br>
          <table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  <td>&nbsp;</td>
    <td align="right"><input type="button" value="Add Quote" onclick="location.href='add_quote.php';" /></td>    
  </tr>
</table>
              <br>
            </font>
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
            <form action="quoteList.php" method="post" name="frmlist">
             <table width="100%" cellspacing="1" cellpadding="1" border="0">
            <tr>
                    <td width="100px" class="grid001"> Client Name: </td>
                    <td width="300px" class="grid001"><select name="cid" id="cid" class="cid" style="width:200px;" >
                            <option value="-1">-----Select Client------</option>
                            <?php
                            for ($i = 0; $i < count($data_client); $i++) {
                                $html= "<option value=\"" . $data_client[$i]['ID']."\" "; 
                                       if (isset($_SESSION['cid']) && $_SESSION['cid'] ==$data_client[$i]['ID'] )
                                   $html.=" selected=\"'selected'\" ";         
                                  $html.=  ">" . $data_client[$i]['client'] . "</option>";
                                  echo $html;
                            }
                            ?>
                        </select> </td>
                        
           
                        
                     <td width="110px" class="grid001" <?php if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) 
                echo "style='display : none'"; ?> >Company Name: </td>
    <td class="grid001" <?php if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) 
                echo "style='display : none'"; ?> >
    <select id="companyid" class="vid" name="companyid" style="width:200px;">
                          <option value="-1">---- Select Company ----</option>
                           <?php 
						  	for($i=0; $i<count($data_cmp); $i++)
							{
                                     $html= "<option value=\"" . $data_cmp[$i]['company_id']."\" "; 
                                       if (isset($_SESSION['companyid']) && $_SESSION['companyid'] ==$data_cmp[$i]['company_id'] )
                                   $html.=" selected=\"'selected'\" ";         
                                  $html.=  ">" . $data_cmp[$i]['company'] . "</option>";
                                  echo $html;                        
						  	}
						  ?>
                     
                          
						
                        </select></td>
                        
                        
                    <td width="110px" class="grid001">Quote Number: </td>
                    <td class="grid001">
                        <select name="q_id" id="qid" class="pid" style="width:200px;">
                            <option value="-1">-----Select------</option>
                               <?php 
						  	for($i=0; $i<count($data_quote_number); $i++)
							{
						  
                           $html= "<option value=\"" . $data_quote_number[$i]['po_number']."\" "; 
                                       if (isset($_SESSION['q_id']) && $_SESSION['q_id'] ==$data_quote_number[$i]['po_number'] )
                                   $html.=" selected=\"'selected'\" ";         
                                  $html.=  ">" . $data_quote_number[$i]['po_number'] . "</option>";
                                  echo $html;  
                                                            
                                                        }
                          
							
						  ?>
                        </select></td>
                </tr>
                <tr><td>&nbsp;</td>
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
             </table>
            </form>
        <table width="80%" border="0" cellspacing="1" cellpadding="1">
            <thead >
                            <tr class="sortable">
                            <th class="gridHeader"><a href="quoteList.php?sort=<?php if(isset($_GET['sort'])){
                                if($_GET['sort']=='ASC') echo 'DESC'; else echo 'ASC';
                                    
                              } else echo "ASC"?>"> Client</a></th> 
                            <th class="gridHeader">Company Name</th>
                            <!--<th class="gridHeader">Project Name</th>-->
                            <th class="gridHeader">Quote Number</th>
                            <th class="gridHeader">Quote Date</th>
                            <th class="gridHeader">Edit</th>
                            <th class="gridHeader">Delete</th>
                            <th class="gridHeader">Copy</th>
                            </tr>
                        </thead><tbody>
<?php
if (count($data_quote) > 0) {
    for ($i = 0; $i < count($data_quote); $i++) {
        ?>
                                <tr>
                                    <td class="grid001"><?php echo $data_quote[$i]['client']; ?></td>
                                    <td class="grid001"><a href="purchaseorderpage.php?qid=<?php echo $data_quote[$i]['qid']; ?>"><?php echo $data_quote[$i]['company']; ?></a></td>
                                  <!--  <td class="grid001"><?php echo $data_quote[$i]['project_name']; ?></td>-->
                                    <td class="grid001"><?php echo $data_quote[$i]['po_number']; ?></td>
                                    <td class="grid001"><?php if ($data_quote[$i]['po_date'] != "") {
            echo date('m/d/Y', $data_quote[$i]['po_date']);
        } else { ?>&nbsp;<?php } ?></td>
                                    <td class="grid001"><a href="add_quote.php?qid=<?php echo $data_quote[$i]['qid']; ?>"><img src="<?php echo $mydirectory; ?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                                    <td class="grid001"><a href="quoteList.php?qid=<?php echo $data_quote[$i]['qid']; ?>&opt=1"><img src="<?php echo $mydirectory; ?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
                                    <td class="grid001"><a href="quoteList.php?qid=<?php echo $data_quote[$i]['qid']; ?>&opt=2"><img src="<?php echo $mydirectory; ?>/images/copy.jpg" width="24" height="24" alt="copy" /></a></td>
                                </tr>              
        <?php
    }
    echo '</tbody><tr>
			<td width="100%" class="grid001" colspan="8">' . $p->show() . '</td>			
		  </tr>';
			  }
			  else
			  {
				  echo '</tbody><tr><td class="grid001" colspan="7" class="grid001">No Quotes found</td><tr>';
			  }
			 ?>          
              <tr>
                <td colspan="5">&nbsp;</td>
              </tr></tbody>
            </table>
            </td>
        </tr>
      </table>
      <p>
  </center></td>
</tr>
</table>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<?php
require('../../trailer.php');
?>