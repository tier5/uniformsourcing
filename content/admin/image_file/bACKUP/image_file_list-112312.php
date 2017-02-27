<?php
require('Application.php');

$current_page = "image_file_list.php";
$type = "project_mgm";
$paging = 'paging=';
$sql = "";
$is_session = 0;
$emp_join = "";
$emp_id = "";
$emp_sql = "";
$search_sql = "";
$limit = "";
$search_uri = "";
if (isset($_GET['paging']) && $_GET['paging'] != "") {
    $paging .= $_GET['paging'];
} else {
    $paging .= 1;
}





$_SESSION['page'] = $current_page;
$emp_join = ' left join tbl_prjvendor pv on pv.pid=prj.pid left join vendor v on v."vendorID"=pv.vid';

if (isset($_GET['close'])) {
    $ID = $_GET['close'];
    $query1 = 'DELETE FROM  "img_file_pack" ' 
            .'WHERE "pack_id" = '.$ID;
    
    $query1 .= '; DELETE FROM  "img_file_items" ' 
            .'WHERE "pack_id" = '.$ID;
    if (!($result1 = pg_query($connection, $query1))) {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result1);
    header("location: image_file_list.php?$paging");
} 
   

   

require('../../header.php');
?>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min-1.4.2.js"></script>

<script type="text/javascript">
    var cIndex=0;
</script>

<?php
include('../../pagination.class.php');

$where="";
$client_join = '';
$style_join = '';
if(isset($_GET['client'])&&$_GET['client']!="")
{
     $client_join = " left join img_file_clients as clients on clients.cid = (select c.cid from img_file_clients as c where c.pack_id = main.pack_id and c.cid=".$_GET['client']." limit 1)";
 if($where=="")
 {    
      $where.=" where clients.cid=".$_GET['client']; 
 }
 else
     $where.=" AND clients.cid=".$_GET['client']; 
}
if(isset($_GET['pack'])&&$_GET['pack']!="")
{
 if($where=="")
      $where.=" where main.pack_id=".$_GET['pack']; 
 else
     $where.=" AND main.pack_id=".$_GET['pack']; 
}

if(isset($_GET['style'])&& $_GET['style']!="")
{
    $style_join = " left join img_file_styles as styles on styles.style like '%".$_GET['style']."%'  ";
 if($where=="")
      $where.=" where styles.style like '%".$_GET['style']."%'"; 
 else
     $where.=" AND styles.style like '%".$_GET['style']."%'"; 
}

$sql = 'SELECT distinct main.*,(select client.client from img_file_clients as pack left join "clientDB" as client '.
 ' on client."ID"=pack.cid where pack.pack_id=main.pack_id limit 1) as client_name FROM "img_file_pack" as main'.$style_join. $client_join. $where;
//echo $sql;
if (!($resultp = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
$items = pg_num_rows($resultp);
if ($items > 0) {
    $p = new pagination;
    $p->items($items);
    $p->limit(10); // Limit entries per page
    $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&paging'));
    if (!$uri) {
        $uri = $_SERVER['REQUEST_URI'] . $search_uri;
    }
    //echo "uri==>".$uri;
    $p->target($uri);
    $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
    $p->calculate(); // Calculates what to show
    $p->parameterName('paging');
    $p->adjacents(1); //No. of page away from the current page

    if (!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = $_GET['paging'];
    }
    //Query for limit paging
    $limit = "LIMIT " . $p->limit . " OFFSET " . ($p->page - 1) * $p->limit;
}
$sql = $sql . " " . $limit;
if (!($resultp = pg_query($connection, $sql))) {
    print("Failed queryd: " . pg_last_error($connection));
    exit;
}
while ($rowd = pg_fetch_array($resultp)) {
    $datalist[] = $rowd;
}


    $query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$client[]=$row1;
}

$sql = 'select pack_id,pack_name from "img_file_pack"';
  
   // echo  $sql;
    if (!($result1 = pg_query($connection, $sql))) {
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($rowp = pg_fetch_array($result1)) {
        $packlist[] = $rowp;
    }
    pg_free_result($result1);
?>

<center>	
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Image File Package</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
    <table border="0" width="40%">

  <tr><td align="center"><input type="button" value="Add New Picture/File Package" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='image_fileAddEdit.php'" style="cursor: pointer;"></td>
               
            </tr>
    </table>
    
    <br /><div align="center" id="message"></div><br />
    
     <table width="100%" border="0" cellspacing="1" cellpadding="1">
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
                   <tr><td colspn="3"><h3>Search Packages</h3></td></tr> 
                   
 <tr class="grid001"><td>Client: </td><td >Style Number:</td><td colspan="3">Package Name:</td></tr>                  
                   <tr class="grid001">
             <td><select width="20px" id="client" name="client[]" onchange="javascript:viewPackageName($(this).val());">
<option value="" selected="selected">--Select--</option>        
<?php for($i=0; $i < count($client); $i++){
if(isset($_GET['client'])&& $_GET['client']==$client[$i]['ID']) 
        {
echo '<option value="'.$client[$i]['ID'].'" selected="selected">'.$client[$i]['client'].'</option>';
	 	}
                else
                    { 
echo '<option value="'.$client[$i]['ID'].'">'.$client[$i]['client'].'</option>';
	 }
         
         }
	
?>
</select>
  </td>             
  
  <td><input type="text" onchange="javascript:viewPackageNameStyle($(this).val());" id="style_number" value="<?php 
  if(isset($_GET['style'])) 
      echo $_GET['style'];
                       ?>"/></td>
                       <td colspan="3"><?php
                   echo '<select   id="pack_list" >'.
'<option value="0">--Select--</option>';
for($i=0;$i<count($packlist);$i++)
{
echo '<option value="'.$packlist[$i]['pack_id'].'"';
       if(isset($_GET['pack'])&& $_GET['pack']==$packlist[$i]['pack_id']) 
         echo ' selected="selected" ';   
     echo '>';

     echo $packlist[$i]['pack_name'].'</option>';   
 }

 echo '</select>';
                   ?></td>
 
   </tr>     
        
   <tr><td colspan="4" align="center"><input type="button" value="Search" onclick="javascript:search();"/>
           <input type="button" value="Cancel" onclick="javascript:CancelSearch();"/>
       </td></tr>     
   
    <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td width="10">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                        </tr>
                    </table>
  
    <form action="" name="frm_send_email" id="frm_send_email">
        <table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" >


            <thead style="border:1px white solid;">
                <tr  > 
                   
                    <th class="gridHeader" height="10">Client</th>
                    <th class="gridHeader" height="10">PackageName</th>
                    <th class="gridHeader">Edit</th>
                       <th class="gridHeader">Delete</th>

                </tr>
            </thead><tbody> 
                    <?php
                    if (count($datalist)) {
                        for ($i = 0; $i < count($datalist); $i++) {
                            echo "<tr>";
                          
                            echo '<td class="grid001">' . $datalist[$i]['client_name'] . '<input type="hidden" id="project_name" value="' . $datalist[$i]['projectname'] . '" /><input type="hidden" id="project_pid" value="' . $datalist[$i]['pid'] . '" /></td>';
                            echo '<td class="grid001">' . $datalist[$i]['pack_name'] . $datalist[$i]['lastname'] . '</td>';
                         
                                
                              
                         
                            echo '<td class="grid001"><a href="image_fileAddEdit.php?pack_id=' . $datalist[$i]['pack_id'] . '&' . $paging . '"><img src="' . $mydirectory . '/images/edit.png"  
alt="edit" /></a></td>';
                          
                                echo '<td class="grid001"><a href="image_file_list.php?close=' . $datalist[$i]['pack_id'] . '" onclick="javascript: if(confirm(\'Are you sure you want to close the project\')) { return true; } else { return false; }"><img src="' . $mydirectory . '/images/close.png" border="0"></a></td>';
                            
                          //  echo "</tr>";
                        
                       // echo '</tbody><tr>
					
		  echo '</tr>';
                    } 
                    echo '<tr><td width="100%" class="grid001" colspan="13">' . $p->show() . '</td></tr>';
                    }else {
                        echo "</tbody><tr>";
                        echo '<td align="left" colspan="13"><font face="arial"><b>No Packages Found</b></font></td>';
                        echo "</tr>";
                    }
                    ?>
        </table>
    </form>
</center>

<!--<div id="project_pop" class="popup_block"></div>-->
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tablesort.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="./project_popup.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery-ui.min.js"></script>


<script type="text/javascript">
 function  search()
 {
data="";
if($("#client").val()!="")
    {
    if(data=="")
      data+='?client='+$("#client").val();  
    else
      data+='&client='+$("#client").val();    
    }
    
 if($.trim($("#style_number").val())!="")
    {
    if(data=="")
      data+='?style='+$.trim($("#style_number").val());  
    else
      data+='&style='+$.trim($("#style_number").val());    
    }   
     
    if($("#pack_list").val()!=0)
    {
    if(data=="")
      data+='?pack='+$("#pack_list").val();  
    else
      data+='&pack='+$("#pack_list").val();    
    }
    
    if(data!="")
    location.href='image_file_list.php'+data;
else
    location.href='image_file_list.php';
 }
 
 function CancelSearch()
 {
  $("#pack_list").val(0);   
  $("#style_number").val("");
  $("#client").val("");
  location.href='image_file_list.php';
 }
 
 function viewPackageName(client)
 {
     data='client='+client;
       $.ajax({
            type: "POST",
            url: "getSerchContents.php1?opt=client",
            data: data,
            success:function(data)
            {
         $("#pack_list").html(data);    
            }
        });
 }
 
  function viewPackageNameStyle(style)
 {
     data='style='+style;
       $.ajax({
            type: "POST",
            url: "getSerchContents.php1?opt=style",
            data: data,
            success:function(data)
            {
         $("#pack_list").html(data);    
            }
        });
 }
 
    </script>
   
<?php
require('../../trailer.php');
?>