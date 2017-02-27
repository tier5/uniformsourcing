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

$sql = 'SELECT * FROM "img_file_pack"';
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
<?php
if ($is_session != 1) {
    ?>
  <tr><td align="center"><input type="button" value="Add New Picture/File Package" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='image_fileAddEdit.php'" style="cursor: pointer;"></td>

 
               
            </tr>
    <?php
}
?>
    </table>
    <br /><div align="center" id="message"></div><br />
  
    <form action="" name="frm_send_email" id="frm_send_email">
        <table width="100%" cellspacing="1" cellpadding="1" style="border:1px white solid;" >


            <thead style="border:1px white solid;">
                <tr  > 
                   
                    <th class="gridHeader" height="10">Package ID</th>
                    <th class="gridHeader" height="10">Package Name</th>
                    <th class="gridHeader">Edit</th>
                       <th class="gridHeader">Delete</th>

                </tr>
            </thead><tbody> 
                    <?php
                    if (count($datalist)) {
                        for ($i = 0; $i < count($datalist); $i++) {
                            echo "<tr>";
                          
                            echo '<td class="grid001">' . $datalist[$i]['pack_id'] . '<input type="hidden" id="project_name" value="' . $datalist[$i]['projectname'] . '" /><input type="hidden" id="project_pid" value="' . $datalist[$i]['pid'] . '" /></td>';
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
                        echo '<td align="left" colspan="13"><font face="arial"><b>No Project Found</b></font></td>';
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


   
<?php
require('../../trailer.php');
?>