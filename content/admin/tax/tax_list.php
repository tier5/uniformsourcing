<?php 
require('Application.php');
$id = 0;
if($_GET['id'])
{
	$id = $_GET['id'];
	$query="Delete from tbl_tax where tax_id=$id ";
	if(!($result=pg_query($connection,$query))){
		print("Failed tax_query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
}
require('../../header.php');

include('../../pagination.class.php');
$search_sql="";
$limit="";
$search_uri="";
$query="Select * from tbl_tax where status = 1";
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
	$data_list[]=$row;
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
          <td align="center" valign="top"><font size="5">TAX LIST<br>
          <table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  <td>&nbsp;</td>
    <td align="right"><input type="button" value="Add Tax" onclick="location.href='add_tax.php';" /></td>    
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
              <tr>
                <td class="gridHeader">Name</td>
                <td class="gridHeader">Amount</td>
                <td class="gridHeader">Edit</td>
                <td class="gridHeader">Delete</td>
              </tr>
              <?php
			  if(count($data_list) > 0)
			  {
				  for($i = 0; $i < count($data_list); $i++)
				  {
			  ?>
              <tr>
                <td class="grid001"><?php echo $data_list[$i]['tax_name'];?></td>
                <td class="grid001"><?php echo $data_list[$i]['tax_amount'];?></td>
                <td class="grid001"><a href="add_tax.php?id=<?php echo $data_list[$i]['tax_id'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                <td class="grid001"><a href="tax_list.php?id=<?php echo $data_list[$i]['tax_id'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
              </tr>              
             <?php
				  }
				  echo 	'<tr>
			<td width="100%" class="grid001" colspan="5">'.$p->show().'</td>			
		  </tr>';
			  }
			  else
			  {
				  echo '<tr><td colspan="5" class="grid001">No Taxes found</td><tr>';
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
<?php
require('../../trailer.php');
?>