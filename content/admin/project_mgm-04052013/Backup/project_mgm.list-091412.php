<?php
require('Application.php');
$current_page = "project_mgm.list.php";
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
if (isset($_SESSION['search_uri']) && $_SESSION['search_uri'] != "") {
    if ($type != $_SESSION['page_type']) {
        $_SESSION['search_uri'] = "";
    }
}
if (isset($_POST['cancel'])) {
    $_SESSION['search_uri'] = "";
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
        .' prc.prj_completioncost,pro.prdtntrgtdelvry from tbl_newproject as prj left join tbl_prjpurchase as prch on prch.pid = prj.pid'
        
        .' left join tbl_prjorder_shipping as ship on ship.shipping_id=(select tbl_prjorder_shipping.shipping_id from tbl_prjorder_shipping inner join'
  .' tbl_newproject on tbl_prjorder_shipping.pid = prj.pid order by tbl_prjorder_shipping.shipping_id desc limit 1)'
       
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
echo '<script type="text/javascript" src="' . $mydirectory . '/js/tablesort.js"></script>';
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
echo "<center><font size=\"5\">Projects</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
    <table border="0" width="40%">
<?php
if ($is_session != 1) {
    ?>
            <tr><td align="center"><input type="button" value="Add New Projects" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='project_mgm.add.php'" style="cursor: pointer;"></td>
    <?php
    echo '<td align="center">&nbsp;</td>';
    echo '<td valign="top" align="left"><input type="button" value="View Closed Projects" onmouseover="this.style.cursor = \'pointer\';" onclick="javascript:location.href=\'project_mgm.closed.php\'" style="cursor: pointer;"></td>';
    ?>
                <td align="center"><input type="button" value="Generate Spreadsheet" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:GenerateSpreadSheet();" style="cursor: pointer;"></td>
                <td align="center"><input type="button" value="Send Email" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:SendEmail();Fade();" style="cursor: pointer;"></td>
            </tr>
    <?php
}
?>
    </table>
    <br /><div align="center" id="message"></div><br />
    <form action="project_mgm.list.php" method="post" name="frmlist">
        <table width="100%" cellspacing="1" cellpadding="1" border="0">
            <tbody><tr>
                    <td height="35" colspan="5" ><strong>Search Projects </strong></td>
                </tr>
                <tr>
                    <td width="100px" class="grid001"> Project Manager: </td> 
                    <td width="300px" class="grid001"><select name="manager_id" id="manager_id" style="width:200px;">
                            <option value="">-----Select------</option>
    <?php
    for ($i = 0; $i < count($data2); $i++) {
        echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
    }
    ?>
                        </select> </td> 

                    <td width="100px" class="grid001"> Secondary Project Manager 1: </td> 
                    <td width="300px" class="grid001"><select name="manager_id1" id="manager_id1" style="width:200px;">
                            <option value="">-----Select------</option>
            <?php
            for ($i = 0; $i < count($data2); $i++) {
                echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
            }
            ?>
                        </select> </td> 
                    <td width="100px" class="grid001"> Secondary Project Manager 2: </td> 
                    <td width="300px" class="grid001"><select name="manager_id2" id="manager_id2" style="width:200px;">
                            <option value="">-----Select------</option>
<?php
for ($i = 0; $i < count($data2); $i++) {
    echo "<option value=\"" . $data2[$i]['employeeID'] . "\">" . $data2[$i]['firstname'] . " " . $data2[$i]['lastname'] . "</option>";
}
?>
                        </select> </td> 
                </tr><tr>
                    <td width="100px" class="grid001"> Client Name: </td>
                    <td width="300px" class="grid001"><select name="cid" id="cid" class="cid" style="width:200px;">
                            <option value="">-----Select------</option>
                            <?php
                            for ($i = 0; $i < count($data1); $i++) {
                                echo "<option value=\"" . $data1[$i]['ID'] . "\">" . $data1[$i]['client'] . "</option>";
                            }
                            ?>
                        </select> </td>
                        
           
                        
                     <td width="110px" class="grid001" <?php if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) 
                echo "style='display : none'"; ?> >Vendor: </td>
    <td class="grid001" <?php if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) 
                echo "style='display : none'"; ?> >
    <select id="vendorId" class="vid" name="vendorId" style="width:200px;">
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
                        
                        
                    <td width="110px" class="grid001">Project Name: </td>
                    <td class="grid001">
                        <select name="pid" id="pid" class="pid" style="width:200px;">
                            <option value="">-----Select------</option>
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
            </tbody></table>
    </form>
    <form action="" name="frm_send_email" id="frm_send_email">
        <table width="100%" cellspacing="0" cellpadding="0" style="border:1px white solid;" class="no-arrow rowstyle-alt">


            <thead style="border:1px white solid;">
                <tr class="sortable"> 
                    <th class="gridHeaderBClose" height="10">Select All <input type="checkbox" name="checkAllAuto" id="checkAllAuto" value="" /></th>
                    <th class="sortableB" height="10">Client </th>
                    <th class="sortableB" height="10">Project Manager</th>
                    <th class="sortableB">Project Name</th>
                    <th class="sortable-numericB">Project Estimated Unit Cost</th>
                    <th class="sortable-numericB">Project Estimated Profit</th>
                    <th class="sortable-numericB">Bid Number</th>
                    <th class="sortable-numericB">Tracking Number</th>
                     <th class="sortable-numericB">Target Delivery</th>

                        <th class="sortable-numericB">Mail</th>


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
                            echo '<td class="grid001B" width="75px">CSR<input type="checkbox" name="csr[]" value="' . $datalist[$i]['pid'] . '"><br />VSR<input type="checkbox" name="vsr[]" value="' . $datalist[$i]['pid'] . '"></td>';
                            echo '<td class="grid001B">' . $datalist[$i]['client'] . '<input type="hidden" id="project_name" value="' . $datalist[$i]['projectname'] . '" /><input type="hidden" id="project_pid" value="' . $datalist[$i]['pid'] . '" /></td>';
                            echo '<td class="grid001B">' . $datalist[$i]['firstname'] . $datalist[$i]['lastname'] . '</td>';
                            echo '<td class="grid001B">' . $datalist[$i]['projectname'] . '</td>';
                          
                            echo '<td class="grid001B">$' . $datalist[$i]['prj_estimatecost'] . '</td>';
                              echo '<td class="grid001B">$' . $datalist[$i]['prj_est_profit'] . '</td>';
                            echo '<td class="grid001B">' . $datalist[$i]['bid_number'] . '</td>';
                            echo '<td class="grid001B">' . $datalist[$i]['tracking_number'] . '</td>';
                           // if ($is_session != 1) 
                                {
                               echo '<td class="grid001B">' . $datalist[$i]['prdtntrgtdelvry'] . '</td>';
                                ;
                               
                                echo '<td class="grid001B"><a style="cursor:hand;cursor:pointer;"  onclick="javascript:popOpen(' . $datalist[$i]['pid'] . ');"><img src="' . $mydirectory . '/images/email.png" alt="send" /></a></td>';
                            }
                            echo '<td class="grid001B"><a href="project_mgm.add.php?id=' . $datalist[$i]['pid'] . '&' . $paging . '"><img src="' . $mydirectory . '/images/edit.png"  
   alt="edit" /></a></td>';
                            if ($is_session != 1) {
                                //echo '<td class="grid001B"><a href="project_mgm.list.php?close=' . $datalist[$i]['pid'] . '" onclick="javascript: if(confirm(\'Are you sure you want to close the project\')) { return true; } else { return false; }"><img src="' . $mydirectory . '/images/close.png" border="0"></a></td>';
                            }
                            echo "</tr>";
                        }
                        echo '</tbody><tr>
			<td width="100%" class="grid001B" colspan="13">' . $p->show() . '</td>			
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
<div id="dialog-form" title="Submit By Email" class="popup_block">
    <div align="center" id="msg_email"></div>
    <p>All form fields are required.</p>  
    <fieldset>
        <form id="pop" name="pop" method="post" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="10">&nbsp;</td>
                    <td colspan="3">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="10" height="30">&nbsp;</td>
                    <td colspan="3" class="emailBG"><a style="cursor:hand;cursor:pointer;" onclick="javascript:SendMail();"><img src="<?php echo $mydirectory; ?>/images/sendButon.jpg" width="68" height="24" alt="send" /></a><a  style="cursor:hand;cursor:pointer;" onclick="javascript:Fade();"><img src="<?php echo $mydirectory; ?>/images/discardButton.jpg" width="68" height="24" alt="discard" /></a></td>
                    <td width="10">&nbsp;</td>
                </tr>
                <tr>
                    <td width="10" height="30">&nbsp;</td>
                    <td width="75" class="emailBG"><label for="email">Email :</label></td>
                    <td class="emailBG"><input name="email" type="text" class="emailTxtBox" id="email" value="" size="35px"  /></td>
                    <td width="10" class="emailBG">&nbsp;</td>
                    <td width="10">&nbsp;</td>
                </tr>
                <tr>
                    <td height="40">&nbsp;</td>
                    <td class="emailBG"> <label for="subject">Subject :</label></td>
                    <td class="emailBG"><input  name="subject" type="text" class="emailTxtBox" id="subject" value="" size="35px" /><input type="hidden" id="email_pid" name="email_pid" value="0" /></td>
                    <td class="emailBG">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>


        </form>
        <p>

        </p>
    </fieldset>
</div>
<!--<div id="project_pop" class="popup_block"></div>-->
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
</script>
<?php
require('../../trailer.php');
?>