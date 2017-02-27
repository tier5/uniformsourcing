<?php 
require('Application.php');
if(isset($_GET['qid']))
{
	$id =$_GET['qid'];
	$sql="Delete from tbl_request_items where qid = ".$id.";";
	$sql.="Delete from tbl_request where qid = $id";
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed delete_quote: " . pg_last_error($connection));
		exit;
	}
	header('location:quoteList.php');
}
require('../../header.php');
$query="SELECT q.*,cmp.company,c.client FROM tbl_request q left join \"clientDB\" c on q.client_id=c.\"ID\" left join tbl_quot_company as cmp on q.company_id = cmp.company_id where q.status = 1 ORDER BY q.qid DESC ";
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
        <table width="80%" border="0" cellspacing="1" cellpadding="1">
            <thead >
                            <tr class="sortable">
             
                 <th class="sortableB">Client</th> 
                <th class="gridHeader">Company Name</th>
                <th class="gridHeader">Quote Number</th>
                <th class="gridHeader">Quote Date</th>
                <th class="gridHeader">Edit</th>
                 <th class="gridHeader">Delete</th>
              </tr>
            </thead><tbody>
              <?php
			  if(count($data_quote) > 0)
			  {
				  for($i = 0; $i < count($data_quote); $i++)
				  {
			  ?>
              <tr>
                <td class="grid001"><?php echo $data_quote[$i]['client'];?></td>
                <td class="grid001"><a href="purchaseorderpage.php?qid=<?php echo $data_quote[$i]['qid'];?>"><?php echo $data_quote[$i]['company'];?></a></td>
                <td class="grid001"><?php echo $data_quote[$i]['po_number'];?></td>
                <td class="grid001"><?php if($data_quote[$i]['po_date'] !=""){ echo date('m/d/Y',$data_quote[$i]['po_date']);}else {?>&nbsp;<?php }?></td>
                <td class="grid001"><a href="add_quote.php?qid=<?php echo $data_quote[$i]['qid'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                <td class="grid001"><a href="quoteList.php?qid=<?php echo $data_quote[$i]['qid'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
              </tr>              
             <?php
				  }
				  echo 	'</tbody><tr>
			<td width="100%" class="grid001" colspan="7">'.$p->show().'</td>			
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
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<?php
require('../../trailer.php');
?>