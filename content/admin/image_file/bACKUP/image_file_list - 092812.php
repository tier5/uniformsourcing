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
if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) {
    $emp_id = $_SESSION['employee_type_id'];
    $emp_join = ' inner join tbl_prjvendor pv on pv.pid=prj.pid left join vendor v on v."vendorID"=pv.vid';
    $emp_sql = ' and v."vendorID" =' . $emp_id;
    $is_session = 1;
} else if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 2)) {
    $emp_id = $_SESSION['employee_type_id'];
    $emp_sql = ' and c."ID" =' . $emp_id;
    $is_session = 1;
}
if (isset($_GET['close'])) {
    $ID = $_GET['close'];
    $query1 = ("UPDATE tbl_newproject " .
            "SET " .
            "status = '0',  updateddate = '" . date('U') . "' " .
            "WHERE pid = '$ID'");
    if (!($result1 = pg_query($connection, $query1))) {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result1);
    header("location: project_mgm.list.php?$paging");
} else if (isset($_GET['del'])) {
    $pid = $_GET['del'];
    $sql = "delete from tbl_prjsample where pid=" . $pid . "; ";

    $sql .= "delete from tbl_prjpurchase where pid =" . $pid . "; ";

    $sql .= "delete from tbl_prjpricing where pid =" . $pid . "; ";

    $sql .= "delete from tbl_prjvendor where pid =" . $pid . "; ";

    $sql .= "delete from tbl_mgt_notes where pid =" . $pid . "; ";
    $sql .= "delete from tbl_prjorder_shipping where pid =" . $pid . "; ";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result);
    $sql = 'select elementfile,image from tbl_prj_elements where pid=' . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_file[] = $row;
    }
    pg_free_result($result);
    for ($i = 0; $i < count($data_file); $i++) {
        if (file_exists("$upload_dir" . "" . $data_file[$i]['elementfile'] . "")) {
            @ unlink("$upload_dir" . "" . $data_file[$i]['elementfile'] . "");
        }
        if (file_exists("$upload_dir" . "" . $data_file[$i]['image'] . "")) {
            @ unlink("$upload_dir" . "" . $data_file[$i]['image'] . "");
        }
    }
    $data_file[] = "";
    $sql = "delete from tbl_prj_elements where pid =" . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    $sql = "delete from tbl_prj_style where pid =" . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed delete_query_style: " . pg_last_error($connection));
        exit;
    }

    $sql = 'select file_name from tbl_prjimage_file where pid=' . $pid;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_file[] = $row;
    }
    for ($i = 0; $i < count($data_file); $i++) {
        if (file_exists("$upload_dir" . "" . $data_file[$i]['file_name'] . "")) {
            @ unlink("$upload_dir" . "" . $data_file[$i]['file_name'] . "");
        }
    }
    $sql = 'delete from tbl_prjimage_file where pid=' . $pid . "; ";

    $sql.= 'delete from tbl_newproject where pid=' . $pid . ";";
    if (!($result = pg_query($connection, $sql))) {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    pg_free_result($result);
    header("location: project_mgm.list.php?$paging");
}
require('../../header.php');
?>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min-1.4.2.js"></script>

<script type="text/javascript">
    var cIndex=0;
</script>
<?php
$query1 = 'SELECT distinct(c."client"),"ID", "clientID",  "active" FROM "clientDB" as c inner join tbl_newproject as prj on prj.client=c."ID" left join tbl_prjpurchase as prch on
 prch.pid = prj.pid  WHERE "active" = \'yes\' and prch.purchaseorder IS NULL and prj.status=1 and c."client" IS NOT NULL';
if ($_SESSION['employeeType'] == 2)
    $query1.=$emp_sql;
$query1.=' ORDER BY c."client" ASC';
//echo $query1;
if (!($result1 = pg_query($connection, $query1))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row1 = pg_fetch_array($result1)) {
    $data1[] = $row1;
}
pg_free_result($result1);
$query2 = 'SELECT distinct(lastname),"employeeID",firstname FROM tbl_newproject inner join "employeeDB" on tbl_newproject.project_manager="employeeDB"."employeeID" left join tbl_prjpurchase as prch on prch.pid = tbl_newproject.pid  WHERE prch.purchaseorder IS NULL and tbl_newproject.status=1 and "employeeDB".firstname IS NOT NULL';
//echo $query2;
if (!($result1 = pg_query($connection, $query2))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row2 = pg_fetch_array($result1)) {
    $data2[] = $row2;
}
pg_free_result($result1);


$query3="SELECT \"vendorID\",\"vendorName\" from vendor where active='yes' ORDER BY \"vendorName\" asc";
if(!($result3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$vendorData[]=$row3;
}
pg_free_result($result3);

include('../../pagination.class.php');
if (isset($_REQUEST['cid']) && $_REQUEST['cid'] != "") {
    $search_sql = ' and prj.client =' . $_REQUEST['cid'] . ' ';
    $search_uri = "?cid=" . $_REQUEST['cid'];
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != "") {
    $search_sql .=' and prj.pid =' . $_REQUEST['pid'] . ' ';
    if ($search_uri) {
        $search_uri.="&pid=" . $_REQUEST['pid'];
    } else {
        $search_uri.="?pid=" . $_REQUEST['pid'];
    }
    $_SESSION['search_uri'] = $search_uri;
}
if (isset($_REQUEST['manager_id']) && $_REQUEST['manager_id'] != "") {
    $search_sql .=' and prj.project_manager =' . $_REQUEST['manager_id'] . ' ';
    if ($search_uri) {
        $search_uri.='&manager_id=' . $_REQUEST['manager_id'];
    } else {
        $search_uri.="?manager_id=" . $_REQUEST['manager_id'];
    }
    $_SESSION['search_uri'] = $search_uri;
}

if (isset($_REQUEST['manager_id1']) && $_REQUEST['manager_id1'] != "") {
    $search_sql .=' and prj.project_manager1 =' . $_REQUEST['manager_id1'] . ' ';
    if ($search_uri) {
        $search_uri.='&manager_id1=' . $_REQUEST['manager_id1'];
    } else {
        $search_uri.="?manager_id1=" . $_REQUEST['manager_id1'];
    }
    $_SESSION['search_uri'] = $search_uri;
}

if (isset($_REQUEST['manager_id2']) && $_REQUEST['manager_id2'] != "") {
    $search_sql .=' and prj.project_manager2 =' . $_REQUEST['manager_id2'] . ' ';
    if ($search_uri) {
        $search_uri.='&manager_id2=' . $_REQUEST['manager_id2'];
    } else {
        $search_uri.="?manager_id2=" . $_REQUEST['manager_id2'];
    }
    $_SESSION['search_uri'] = $search_uri;
}

if (isset($_REQUEST['vendorId']) && $_REQUEST['vendorId'] != "") {
    $search_sql .=' and pv.vid =' . $_REQUEST['vendorId'] . ' ';
    if ($search_uri) {
        $search_uri.='&vendorId=' . $_REQUEST['vendorId'];
    } else {
        $search_uri.="?vendorId=" . $_REQUEST['vendorId'];
    }
    $_SESSION['search_uri'] = $search_uri;
}

if ($_SESSION['search_uri'] != "") {
    $_SESSION['page_type'] = $type;
}
$sql = 'select Distinct(prj.projectname), prj.bid_number,prj.project_budget,c.client,prj.pid,prj.order_placeon,prj.status,ship.tracking_number ,'
.'emp.firstname,emp.lastname,prch.purchaseorder,prc.prjquote,prc.prjcost,prc.prj_completioncost,prc.prj_est_profit,prc.prj_estimatecost,'
        .' prc.prj_completioncost,tbl_carriers.weblink,pro.prdtntrgtdelvry from tbl_newproject as prj left join tbl_prjpurchase as prch on prch.pid = prj.pid'
        
        .' left join tbl_prjorder_shipping as ship on ship.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join'
  .' tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1)'
       .'  left join tbl_carriers on tbl_carriers.carrier_id = ship.carrier_id '
        .' left join tbl_prjpricing as prc on prc.pid = prj.pid left join "employeeDB" as emp on emp."employeeID"= prj.project_manager'
        .' left join tbl_prmilestone as pro on pro.pid = prj.pid  left join "clientDB" c on prj.client=c."ID" '. $emp_join  
        .' left join vendor  on v."vendorID"=pv.vid where prch.purchaseorder IS NULL and prj.status =1   ' 
        . $search_sql . $emp_sql . ' order by prj."pid" desc ';
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
<script type="text/javascript">
    /*function prjctName()
        {
                var val=frmlist.cid.options[frmlist.cid.options.selectedIndex].value; 
        self.location='project.list.php?slctIndex=' + val+'&cIndex='+frmlist.cid.options.selectedIndex;
        }*/
		
</script>
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
        <table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">


            <thead style="border:1px white solid;">
                <tr > 
                    <th class="gridHeaderB" height="10">Select All <input type="checkbox" name="checkAllAuto" id="checkAllAuto" value="" /></th>
                    <th class="gridHeaderB" height="10">pack_id</th>
                    <th class="gridHeaderB" height="10">pack_name</th>
                   


                    <th class="sortable-numericB">Edit</th>
<?php
if ($is_session != 1) {
    ?>            
                        <th class="gridHeaderBClose">Close</th>
    <?php
}
?>
                </tr>
            </thead><tbody> 
                    <?php
                    if (count($datalist)) {
                        for ($i = 0; $i < count($datalist); $i++) {
                            echo "<tr>";
                            echo '<td class="grid001" width="75px">CSR<input type="checkbox" name="csr[]" value="' . $datalist[$i]['pid'] . '"><br />VSR<input type="checkbox" name="vsr[]" value="' . $datalist[$i]['pid'] . '"></td>';
                            echo '<td class="grid001">' . $datalist[$i]['client'] . '<input type="hidden" id="project_name" value="' . $datalist[$i]['projectname'] . '" /><input type="hidden" id="project_pid" value="' . $datalist[$i]['pid'] . '" /></td>';
                            echo '<td class="grid001">' . $datalist[$i]['firstname'] . $datalist[$i]['lastname'] . '</td>';
                            echo '<td class="grid001">' . $datalist[$i]['projectname'] . '</td>';
                          
                            echo '<td class="grid001">$' . $datalist[$i]['prj_estimatecost'] . '</td>';
                              echo '<td class="grid001">$' . $datalist[$i]['prj_est_profit'] . '</td>';
                            echo '<td class="grid001">' . $datalist[$i]['bid_number'] . '</td>';
 echo '<td class="grid001B"><a href="javascript:void(0);" onclick="javascript:popupWindow(\''.$datalist[$i]['weblink'].$datalist[$i]['tracking_number'].'\');">'.$datalist[$i]['tracking_number'].'</a></td>';                           
  //  echo '<td class="grid001B" onclick="javascript:popupWindow(\''.$datalist[$i]['weblink'].$datalist[$i]['tracking_number'].'\');">' . $datalist[$i]['tracking_number'] . '</td>';
                           // if ($is_session != 1) 
                                {
                               echo '<td class="grid001">' . $datalist[$i]['prdtntrgtdelvry'] . '</td>';
                                ;
                               
                                echo '<td class="grid001"><a style="cursor:hand;cursor:pointer;"  onclick="javascript:popOpen(' . $datalist[$i]['pid'] . ');"><img src="' . $mydirectory . '/images/email.png" alt="send" /></a></td>';
                            }
                            echo '<td class="grid001"><a href="project_mgm.add.php?id=' . $datalist[$i]['pid'] . '&' . $paging . '"><img src="' . $mydirectory . '/images/edit.png"  
   alt="edit" /></a></td>';
                            if ($is_session != 1) {
                                echo '<td class="grid001"><a href="project_mgm.list.php?close=' . $datalist[$i]['pid'] . '" onclick="javascript: if(confirm(\'Are you sure you want to close the project\')) { return true; } else { return false; }"><img src="' . $mydirectory . '/images/close.png" border="0"></a></td>';
                            }
                            echo "</tr>";
                        }
                        echo '</tbody><tr>
			<td width="100%" class="grid001" colspan="13">' . $p->show() . '</td>			
		  </tr>';
                    } else {
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
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/PopupBox.js"></script>
<script type="text/javascript"><!--
    $(document).ready(function()
    {
        $("#manager_id").change(function()
        {
            if(document.getElementById('manager_id').value)
            {
                var id=$(this).val();
                var dataString = 'number=0&prj_manager='+ id+'&client='; 
                var clientVal=document.getElementById('cid').value;
                if(clientVal)
                    dataString += clientVal;
                else
                    dataString += "";
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
                        if(clientVal=="")
                        {
                            $.ajax
                            ({
                                type: "POST",
                                url: "prj_clientname.list.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#cid").html(html);
					
                                } 
                            });
                        }
                    } 
                });
            }
        });

        $("#manager_id1").change(function()
        {
            if(document.getElementById('manager_id1').value)
            {
                var id=$(this).val();
                var dataString = 'number=1&prj_manager1='+ id+'&client='; 
                var clientVal=document.getElementById('cid').value;
                if(clientVal)
                    dataString += clientVal;
                else
                    dataString += "";
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
                        if(clientVal=="")
                        {
                            $.ajax
                            ({
                                type: "POST",
                                url: "prj_clientname.list.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#cid").html(html);
					
                                } 
                            });
                        }
                    } 
                });
            }
        });



        $("#manager_id2").change(function()
        {
            if(document.getElementById('manager_id2').value)
            {
                var id=$(this).val();
                var dataString = 'number=2&prj_manager2='+ id+'&client='; 
                var clientVal=document.getElementById('cid').value;
                if(clientVal)
                    dataString += clientVal;
                else
                    dataString += "";
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
                        if(clientVal=="")
                        {
                            $.ajax
                            ({
                                type: "POST",
                                url: "prj_clientname.list.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#cid").html(html);
					
                                } 
                            });
                        }
                    } 
                });
            }
        });


        $("#cid").change(function()
        {
            if(document.getElementById('cid').value)
            {
                var id=$(this).val();
                var dataString = 'client='+ id; 
		
                $.ajax
                ({
                    type: "POST",
                    url: "prj_projectname.list.php",
                    data: dataString,
                    cache: false,
                    success: function(html)
                    {
                        $("#pid").html(html);
			
                    } 
                });
            }
        });
    });
    function GenerateSpreadSheet()
    {
        dataString ='';
<?php
if ($search_uri != "") {
    echo "dataString=\"" . substr($search_uri, 1) . "\";";
}
?>	
                $.ajax({
                    type: "POST",
                    url: "spreadsheet_project_mgm.php",
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
            $('#checkAllAuto').click(
            function()
            {
                $("INPUT[type='checkbox']").attr('checked', $('#checkAllAuto').is(':checked'));   
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
            
            
            function popupWindow(type) 
{
	var url = "";
	url = type;
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
<?php
require('../../trailer.php');
?>