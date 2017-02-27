<?php

require('Application.php');
require($JSONLIB . 'jsonwrapper.php');
//echo $JSONLIB;
$manager_email = 'nweisman@i2net.com,nicole@i2net.com'; //emails send to neal,nicole

$error         = "";
$msg           = "";
$is_notes_mail = 0;
$is_po         = 0;
$log_desc      = '';
$log_id        = 0;
$log_module    = '';
$return_arr    = array();
extract($_POST);
$return_arr['error']         = "";
$return_arr['name']          = "";
$return_arr['id']            = 0;
$return_arr['sample_update'] = $hdn_sample_present;
$projectName                 = pg_escape_string($projectName);
$color                       = pg_escape_string($color);
$materialtype                = pg_escape_string($materialtype);
$projectnotes                = pg_escape_string($projectnotes);
$purchaseOrder               = pg_escape_string($purchaseOrder);
$sizeNeeded                  = pg_escape_string($sizeNeeded);
$garDescription              = pg_escape_string($garDescription);
$confirmation_number         = pg_escape_string($confirmation_number);
/* $elementstyle=pg_escape_string($elementstyle);
  $elementcolor=pg_escape_string($elementcolor); */

function explodeX($delimiters, $string)
{
    $return_array = Array($string); // The array to return
    $d_count = 0;
    while (isset($delimiters[$d_count]))
    { // Loop to loop through all delimiters
        $new_return_array = Array();
        foreach ($return_array as $el_to_split)
        { // Explode all returned elements by the next delimiter
            $put_in_new_return_array = explode($delimiters[$d_count], $el_to_split);
            foreach ($put_in_new_return_array as $substr)
            { // Put all the exploded elements in array to return
                $new_return_array[] = $substr;
            }
        }
        $return_array       = $new_return_array; // Replace the previous return array by the next version
        $d_count++;
    }
    return $return_array; // Return the exploded elements*/
}

if (isset($_POST['pid']))
{
    $pid = $_POST['pid'];
    if (isset($srID) && $srID != '' && $proj_3 == 1)
    {
        if ($hdn_sample_present == 0 && $projectName == "")
        {
            $projectName = pg_escape_string($srID);
        }
    }
    if ($projectName == "")
    {
        $return_arr['name'] = "Please Enter Project Name.";
        echo json_encode($return_arr);
        return;
    }

    if ($project_manager1 == "" && $project_manager2 != "")
    {
        $return_arr['name'] = "Please select  Project Manager 1.";
        echo json_encode($return_arr);
        return;
    }

    if ($project_manager1 != "" && ($project_manager == $project_manager1 || $project_manager == $project_manager2 || $project_manager1 == $project_manager2))
    {
        $return_arr['name'] = "Please select Distinct Project Managers";
        echo json_encode($return_arr);
        return;
    }

    $sql = "Select count(*) as n from tbl_newproject where projectname='$projectName'";
    if ($pid > 0)
        $sql .= " and pid <> $pid";

    if (!($result = pg_query($connection, $sql)))
    {
        print("Failed query: " . pg_last_error($connection));
        exit;
    }
    $projectCount = "";
    while ($row          = pg_fetch_array($result))
    {
        $projectCount = $row;
    }
    if ((int) $projectCount['n'] > 0)
    {
        $return_arr['error'] = "Project Name already exist";
        echo json_encode($return_arr);
        return;
    }
    $query_Name          = "";
    if ($pid == 0)
    {
        $sql        = "select nextval(('tbl_newproject_pid_seq'::text)::regclass) as pid ";
        if (!($result_sql = pg_query($connection, $sql)))
        {
            $return_arr['error'] = "Error while storing project id from database!";
            echo json_encode($return_arr);
            return;
        }
        $row                 = pg_fetch_array($result_sql);
        $pid                 = $row['pid'];
        $return_arr['id']    = $pid;
        pg_free_result($result_sql);
        $sql                 = "";
        $log_desc            = "Added To Basic :";
        $query1              = "INSERT INTO tbl_newproject (pid";
        if ($projectName != "")
        {
            $log_desc .= "project name";
            $project_or_po = $projectName;
            $query1.=" ,projectname ";
        }
        if ($clientID != "")
        {
            $log_desc .= ",client";
            $query1.=" ,client ";
        }
        if ($shippedonclient != "")
        {
            //$log_desc .= ",shiponclient";
            $query1.=" ,shiponclient ";
        }
        if ($color != "")
        {
            //$log_desc .=",color";
            $query1.=", color ";
        }
        if ($materialtype != "")
        {
            // $log_desc .=",material_type";
            $query1.=", materialtype ";
        }
        if ($project_manager != 0)
        {
            $log_desc .=",project_manager";
            $query1.=", project_manager ";
        }
        if ($project_manager1 != 0)
        {
            $log_desc .=",project_manager1";
            $query1.=", project_manager1";
        }
        if ($project_manager2 != 0)
        {
            $log_desc .=",project_manager2";
            $query1.=", project_manager2";
        }
        if ($order_on != "")
        {
            $log_desc .=",order placed on";
            $query1.=", order_placeon ";
        }
        if ($confirmation_number != "")
        {
            //  $log_desc .=",confirmation number";
            $query1.=", confirmation_number ";
        }
        if ($bid_number != "")
        {
            //  $log_desc .=",bid_number";
            $query1.=", bid_number ";
        }
        /*  if ($project_budget != "") {
          // $log_desc .=",project_budget";
          $query1.=", project_budget ";
          } */
        /*     if (isset($shiped_from) &&$shiped_from != "") 
          $query1.=", ship_from ";

          if (isset($shiped_to) &&$shiped_to != "")
          $query1.=", ship_to "; */

        $query1.=", status ";
        $query1.=", updateddate ";
        $query1.=", created_date ";
        $query1.=", createdby ";
        if ($notification_select > 0)
        {
            //  $log_desc .=",notification";
            $query1.=", stock_or_custom ";
        }
        if ($notification_radio != "")
            $query1.=", notification ";
        if (isset($upload_pack) && $upload_pack != "" && $upload_pack != 0)
            $query1.=", upload_pack ";
        if (isset($element_pack) && $element_pack != "" && $element_pack != 0)
            $query1.=", elm_pack ";
        $query1.=")";
        $query1.=" VALUES ($pid";
        if ($projectName != "")
            $query1.=" ,'" . pg_escape_string($projectName) . "' ";
        if ($clientID != "")
            $query1.=", '$clientID' ";
        if ($shippedonclient != "")
            $query1.=", '$shippedonclient' ";
        if ($color != "")
            $query1.=", '" . pg_escape_string($color) . "' ";
        if ($materialtype != "")
            $query1.=" ,'" . pg_escape_string($materialtype) . "' ";
        if ($project_manager != 0)
            $query1.=" ,'$project_manager'";
        if ($project_manager1 != 0)
            $query1.=" ,'$project_manager1'";
        else
            $$query1.=", project_manager1 = '0'";
        if ($project_manager2 != 0)
            $query1.=" ,'$project_manager2'";
        else
            $$query1.=", project_manager2 = '0'";
        if ($order_on != "")
            $query1.=" ,'" . pg_escape_string($order_on) . "' ";
        if ($confirmation_number != "")
            $query1.=" ,'$confirmation_number' ";
        if ($bid_number != "")
            $query1.=" ,'" . pg_escape_string($bid_number) . "' ";
        /*  if ($project_budget != "")
          $query1.=" ,'" . pg_escape_string($project_budget) . "' "; */
        /* if (isset($shiped_from) &&$shiped_from != "") 
          $query1.=" ,'" . pg_escape_string($shiped_from) . "' ";
          if (isset($shiped_to) &&$shiped_to != "")
          $query1.=" ,'" . pg_escape_string($shiped_to) . "' "; */
        $query1.=" ,1 ";
        $query1.=" ,'" . date('U') . "'";
        $query1.=" ,'" . date('U') . "'";
        $query1.=" ," . $_SESSION["employeeID"];
        if ($notification_select > 0)
            $query1.=", '$notification_select' ";
        if ($notification_radio != "")
            $query1.=", '$notification_radio' ";
        if (isset($upload_pack) && $upload_pack != "" && $upload_pack != 0)
            $query1.=", '$upload_pack' ";
        if (isset($element_pack) && $element_pack != "" && $element_pack != 0)
            $query1.=", '$element_pack' ";
        $query1.=" )";
        // echo $query1;
        if (!($result = pg_query($connection, $query1)))
        {
            $return_arr['error'] = "Error while storing project information to database!";
            echo json_encode($return_arr);
            return;
        }
        pg_free_result($result);
        $sql                 = "";
    }
    else if ($pid > 0)
    {
        if ($hdn_track_update == 0)
            $log_desc = "Basic tab updated";

        $return_arr['id'] = $pid;
        $project_or_po    = $projectName;
        $query_Name.="UPDATE tbl_newproject SET pid='$pid' ";
        $query_Name.=", projectname = '" . pg_escape_string($projectName) . "' ";
        if (isset($clientID) && $clientID != 0)
            $query_Name.=", client = '$clientID'";
        else if (isset($clientID))
            $query_Name.=", client = null";
        if (isset($shippedonclient) && $shippedonclient == 1)
            $query_Name.=", shiponclient  = '" . $shippedonclient . "' ";
        else
            $query_Name.=", shiponclient  = null";
        if ($color != "")
            $query_Name.=", color = '" . pg_escape_string($color) . "' ";
        else if (isset($color))
            $query_Name.=", color = null ";
        if (isset($materialtype) && $materialtype != "")
            $query_Name.=", materialtype = '" . pg_escape_string($materialtype) . "' ";
        else if (isset($materialtype))
            $query_Name.=", materialtype =  null ";
        if ($project_manager != "")
            $query_Name.=", project_manager = '$project_manager'";
        if ($project_manager1 != "")
            $query_Name.=", project_manager1 = '$project_manager1'";
        else
            $query_Name.=", project_manager1 = '0'";
        if ($project_manager2 != "")
            $query_Name.=", project_manager2 = '$project_manager2'";
        else
            $query_Name.=", project_manager2 = '0'";

        if ($proj_8 == 1 && $pid > 0)
        {
            if (isset($order_on) && $order_on != "")
                $query_Name.=", order_placeon = '" . pg_escape_string($order_on) . "' ";
            else if (isset($order_on))
                $query_Name.=", order_placeon = null ";
            if (isset($confirmation_number) && $confirmation_number != "")
                $query_Name.=", confirmation_number = '$confirmation_number' ";
            else if (isset($confirmation_number))
                $query_Name.=", confirmation_number = null ";
            if (isset($bid_number) && $bid_number != "")
                $query_Name.=", bid_number = '" . pg_escape_string($bid_number) . "' ";
            else if (isset($bid_number))
                $query_Name.=", bid_number = null ";
            /*    if (isset($project_budget) && $project_budget != "")
              $query_Name.=", project_budget = '" . pg_escape_string($project_budget) . "' ";
              else if (isset($project_budget))
              $query_Name.=", project_budget = null ";

              if (isset($shiped_to) &&$shiped_to != "")
              $query_Name.=", ship_to = '$shiped_to' ";
              if (isset($shiped_from) &&$shiped_from != "")
              $query_Name.=", ship_from = '$shiped_from' "; */
        }
        if (isset($notification_select) && $notification_select > 0)
            $query_Name.=", stock_or_custom = $notification_select ";
        else if (isset($notification_select))
            $query_Name.=", stock_or_custom = null ";
        if (isset($notification_radio) && $notification_radio != "")
            $query_Name.=", notification = $notification_radio ";
        else if (isset($notification_radio))
            $query_Name.=", notification = null ";
        if (isset($upload_pack) && $upload_pack != "" && $upload_pack != 0)
            $query_Name.=", upload_pack = $upload_pack ";
        else
            $query_Name.=", upload_pack= null ";

        if (isset($element_pack) && $element_pack != "" && $element_pack != 0)
            $query_Name.=", elm_pack = $element_pack ";
        else
            $query_Name.=",  elm_pack= null ";

        $query_Name.=", updateddate = " . date('U');
        $query_Name.=", createdby = " . $_SESSION["employeeID"];
        $query_Name.="  where pid='$pid'" . ";";

        if ($query_Name != "")
        {
            //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $query_Name          = "";
        }
    }
    $log_id              = $pid;
    $sql                 = "";
    $log_module          = "Project";
    if ($log_desc != "")
    {
        $sql    = "INSERT INTO tbl_change_record (";
        $sql.=" log_date ";
        if ($log_desc != "")
            $sql.=", log_desc ";
        if ($log_module != "")
            $sql.=", module ";
        if ($log_id != "")
            $sql.=", module_id ";
        if ($project_or_po != "")
            $sql.=", project_or_po ";
        $sql.=", status ";
        $sql.=", created_date ";
        $sql.=", employee_id ";
        $sql.=")";
        $sql.=" VALUES (";
        $sql.=" " . date('U');
        if ($log_desc != "")
            $sql.=", '" . $log_desc . "' ";
        if ($log_module != "")
            $sql.=", '" . $log_module . "' ";
        if ($log_id != "")
            $sql.=", $log_id ";
        if ($project_or_po != "")
            $sql.=", '" . $project_or_po . "' ";
        $sql.=" ,1 ";
        $sql.=" ," . date('U');
        $sql.=" ," . $_SESSION["employeeID"];
        $sql.=" )" . ";";
        //echo $sql;
        if (!($result = pg_query($connection, $sql)))
        {
            $return_arr['error'] = "Basic tab :" . pg_last_error($connection);
            echo json_encode($return_arr);
            return;
        }
        pg_free_result($result);
        $sql                 = "";
        $log_desc            = "";
    }
    if ($pid <= 0)
    {
        $return_arr['error'] = "System Error.Please refresh the page and try again";
        echo json_encode($return_arr);
        return;
    }
    if ($proj_1 == 1)
    {
        if (isset($purchaseOrder) && isset($purchaseId))
        {
            $query_Name = "";
            $sql        = "Select \"purchaseId\",createddate,purchaseorder from tbl_prjpurchase where pid = " . $pid . " limit 1";
            if (!($result     = pg_query($connection, $sql)))
            {
                print("Failed query1: " . pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result))
            {
                $data_prjPurchase = $row;
            }
            if ($data_prjPurchase['purchaseId'] > 0)
                $purchaseId       = $data_prjPurchase['purchaseId'];
            pg_free_result($result);
            $created_date     = date('U');
            if (isset($data_prjPurchase) && $data_prjPurchase['purchaseorder'] != '' && $data_prjPurchase['createddate'] != '')
                $created_date     = $data_prjPurchase['createddate'];

            if ($purchaseId > 0)
            {

                if ($hdn_track_update == 1)
                    $log_desc      = "Purchase Tab Updated";
                $project_or_po = pg_escape_string($purchaseOrder);
                $query_Name.="UPDATE tbl_prjpurchase SET  status=1 ";
                if ($purchaseOrder != "")
                    $query_Name.=", purchaseorder = '" . pg_escape_string($purchaseOrder) . "'";
                else
                    $query_Name.=", purchaseorder = null ";
                if ($poDueDate != "")
                    $query_Name.=", purchaseduedate = '" . pg_escape_string($poDueDate) . "' ";
                else
                    $query_Name.=", purchaseduedate = null ";
                if ($quanPeople != "")
                    $query_Name.=", qtypeople = '$quanPeople' ";
                else
                    $query_Name.=", qtypeople = null ";
                if ($totalGarments != "")
                    $query_Name.=", totalgarments = '" . pg_escape_string($totalGarments) . "' ";
                else
                    $query_Name.=", totalgarments = null ";
                if ($sizeNeeded != "")
                    $query_Name.=", sizeneeded = '" . pg_escape_string($sizeNeeded) . "' ";
                else
                    $query_Name.=", sizeneeded = null ";
                if ($garDescription != "")
                    $query_Name.=", garmentdesc = '" . pg_escape_string($garDescription) . "' ";
                else
                    $query_Name.=", garmentdesc = null ";
				if ($bid != "")
                    $query_Name.=", bid = '" . pg_escape_string($bid) . "' ";
                else
                    $query_Name.=", bid = null ";
				if ($project != "")
                    $query_Name.=", project_name = '" . pg_escape_string($project) . "' ";
                else
                    $query_Name.=", project_name = null ";
                $query_Name.=", createddate = " . $created_date;
                $query_Name.=", updateddate = " . date('U');
                if ($ptinvoice != "")
                    $query_Name.=",pt_invoice= '" . pg_escape_string($ptinvoice) . "' ";
                else
                    $query_Name.=", pt_invoice = null ";
                $query_Name.="  where pid='$pid' and \"purchaseId\"= $purchaseId " . ";";
            }
            else
            {
                $sql = "";
                $sql = "select nextval(('tbl_prjpurchase_purchaseId_seq'::text)::regclass) as purchaseid ";

                if (!($result_sql = pg_query($connection, $sql)))
                {
                    $return_arr['error']      = "Error while storing project id from database!";
                    echo json_encode($return_arr);
                    return;
                }
                $row                      = pg_fetch_array($result_sql);
                $purchaseId               = $row['purchaseid'];
                $return_arr['purchaseId'] = $purchaseId;
                $sql                      = "";
                $log_desc                 = "Added To Purchase:";
                $log_module               = 'Project';

                $query_Name = "INSERT INTO tbl_prjpurchase (";
                $query_Name.=" \"purchaseId\" ";
                $query_Name.=", pid ";
                if ($purchaseOrder != "")
                {
                    $log_desc.="purchase order";
                    $project_or_po = pg_escape_string($purchaseOrder);
                    $query_Name.=" ,purchaseorder ";
                }
                if ($poDueDate != "")
                {
                    // $log_desc.=",purchase due date";
                    $query_Name.=", purchaseduedate ";
                }
                if ($quanPeople != "")
                {
                    //   $log_desc.=",Quantity Of Peoeple";
                    $query_Name.=", qtypeople ";
                }
                if ($totalGarments != "")
                {
                    //    $log_desc.=",Total Garments";
                    $query_Name.=", totalgarments ";
                }
                if ($sizeNeeded != "")
                {
                    //   $log_desc.=",Sizes Needed";
                    $query_Name.=", sizeneeded ";
                }
                if ($garDescription != "")
                {
                    //  $log_desc.=",Garment Description";
                    $query_Name.=", garmentdesc ";
                }
				if ($bid != "")
                {
                    //   $log_desc.=",Sizes Needed";
                    $query_Name.=", bid ";
                }
				if ($project != "")
                {
                    //   $log_desc.=",Sizes Needed";
                    $query_Name.=", project_name ";
                }
                $query_Name.=", status ";
                $query_Name.=", createddate ";
                if ($ptinvoice != "")
                {
                    $log_desc.=",PT Invoice";
                    $query_Name.=", pt_invoice";
                }
                $query_Name.=")";
                $query_Name.=" VALUES (";
                $query_Name.=" $purchaseId ";
                $query_Name.=", $pid ";
                if ($purchaseOrder != "")
                    $query_Name.=", '" . pg_escape_string($purchaseOrder) . "' ";
                if ($poDueDate != "")
                    $query_Name.=", '" . pg_escape_string($poDueDate) . "' ";
                if ($quanPeople != "")
                    $query_Name.=", '$quanPeople' ";
                if ($totalGarments != "")
                    $query_Name.=", '" . pg_escape_string($totalGarments) . "' ";
                if ($sizeNeeded != "")
                    $query_Name.=", '" . pg_escape_string($sizeNeeded) . "' ";
                if ($garDescription != "")
                    $query_Name.=" ,'" . pg_escape_string($garDescription) . "' ";
				if ($bid != "")
                    $query_Name.=" ,'" . pg_escape_string($bid) . "' ";
				if ($project != "")
                    $query_Name.=" ,'" . pg_escape_string($project) . "' ";
                $query_Name.=" ,1 ";
                $query_Name.=" ,'" . date('U') . "'";
                if ($ptinvoice != "")
                    $query_Name.=" ,'" . pg_escape_string($ptinvoice) . "' ";
                $query_Name.=" )" . ";";
                $is_po = 1;
            }
            if ($query_Name != "")
            {
                // echo $query_Name;
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing project purchase information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query_Name          = "";
            }
        }
    }
    if ($log_desc != "")
    {
        $sql    = "INSERT INTO tbl_change_record (";
        $sql.=" log_date ";
        if ($log_desc != "")
            $sql.=", log_desc ";
        if ($log_module != "")
            $sql.=", module ";
        if ($log_id != "")
            $sql.=", module_id ";
        if ($project_or_po != "")
            $sql.=", project_or_po ";
        $sql.=", status ";
        $sql.=", created_date ";
        $sql.=", employee_id ";
        $sql.=")";
        $sql.=" VALUES (";
        $sql.=" " . date('U');
        if ($log_desc != "")
            $sql.=", '" . $log_desc . "' ";
        if ($log_module != "")
            $sql.=", '" . $log_module . "' ";
        if ($log_id != "")
            $sql.=", $log_id ";
        if ($project_or_po != "")
            $sql.=", '" . $project_or_po . "' ";
        $sql.=" ,1 ";
        $sql.=" ," . date('U');
        $sql.=" ," . $_SESSION["employeeID"];
        $sql.=" )" . ";";
        //echo "2".$sql;
        if (!($result = pg_query($connection, $sql)))
        {
            $return_arr['error'] = "Purchase Tab :" . pg_last_error($connection);
            echo json_encode($return_arr);
            return;
        }
        pg_free_result($result);
        $sql                 = "";
        $log_desc            = "";
    }
    if ($proj_9 == 1)
    {
        //echo 'entered';
        $log_module = "Project";
        $query      = "";
        $uploadflag = 0;
        $comma      = ",";
        for ($i          = 0; $i < 2; $i++)
        {
            //echo 'upload1';
            if ($upload_file[$i] != '' && $upload_id[$i] > 0)
            {
                if ($hdn_track_update == 9)
                    $log_desc = "Upload Tab Updated";
                $query.="UPDATE tbl_prjimage_file SET pid = '" . $pid . "', status = 1 ";
                if ($upload_file[$i] != "")
                {
                    if ($uploadflag != 0)
                        $log_desc .= pg_escape_string($upload_file[$i]) . $comma;
                    $query.=", file_name = '" . pg_escape_string($upload_file[$i]) . "' ";
                    $uploadflag++;
                }
                $query.=", \"type\" = '" . pg_escape_string($upload_type[$i]) . "' ";
                $query.=", createddate ='" . date('U') . "'";
                $query.=", updateddate ='" . date('U') . "'";
                $query.=" where \"prjimageId\" = '$upload_id[$i]' ;";
                //echo $query;
            }
            else if ($upload_file[$i] != '' && $upload_id[$i] == 0)
            {
                //  $log_desc = "The following files uploaded :";

                $query.= "Insert Into tbl_prjimage_file ( pid ";
                if ($upload_file[$i] != "")
                    $query.=", file_name";
                $query.=",\"type\"";
                $query.=", status";
                $query.=" )";
                $query.=" Values( ";
                $query.=" '$pid'";
                if ($upload_file[$i] != "")
                {
                    /* if ($uploadflag != 0)
                      $log_desc .= pg_escape_string($upload_file[$i]) . $comma; */
                    $query.=", '" . pg_escape_string($upload_file[$i]) . "'";
                    $uploadflag++;
                }
                $query.=", '" . pg_escape_string($upload_type[$i]) . "'";
                $query.=", '1'";
                $query.=" );";
            }
            if ($query != "")
            {
                //echo $query;
                if (!($result = pg_query($connection, $query)))
                {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query               = "";
            }
            $log_module          = "Project";
            if ($log_desc != "")
            {
                $sql    = "INSERT INTO tbl_change_record (";
                $sql.=" log_date ";
                if ($log_desc != "")
                    $sql.=", log_desc ";
                if ($log_module != "")
                    $sql.=", module ";
                if ($log_id != "")
                    $sql.=", module_id ";
                if ($project_or_po != "")
                    $sql.=", project_or_po ";
                $sql.=", status ";
                $sql.=", created_date ";
                $sql.=", employee_id ";
                $sql.=")";
                $sql.=" VALUES (";
                $sql.=" " . date('U');
                if ($log_desc != "")
                    $sql.=", '" . $log_desc . "' ";
                if ($log_module != "")
                    $sql.=", '" . $log_module . "' ";
                if ($log_id != "")
                    $sql.=", $log_id ";
                if ($project_or_po != "")
                    $sql.=", '" . $project_or_po . "' ";
                $sql.=" ,1 ";
                $sql.=" ," . date('U');
                $sql.=" ," . $_SESSION["employeeID"];
                $sql.=" )" . ";";
                //echo "3".$sql;
                if (!($result = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Upload Tab :" . pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $sql                 = "";
                $log_desc            = "";
            }
        }
        $sql                 = "";
        $uploadflag          = 0;
        $comma               = ",";
        for ($i                   = 2; $i < count($upload_file); $i++)
        {

            if ($upload_file[$i] != '' && $upload_id[$i] == 0)
            {
                $sql .= "Insert Into tbl_prjimage_file ( pid ";
                if ($upload_file[$i] != "")
                    $sql.=", file_name";
                $sql.=",\"type\"";
                $sql.=", status";
                $sql.=" )";
                $sql.=" Values( ";
                $sql.=" '$pid'";
                if ($upload_file[$i] != "")
                {
                    /*  if ($uploadflag != 0)
                      $log_desc .=pg_escape_string($upload_file[$i]) . $comma; */
                    $sql.=", '" . pg_escape_string($upload_file[$i]) . "'";
                    $uploadflag++;
                }
                $sql.=", '" . pg_escape_string($upload_type[$i]) . "'";
                $sql.=", '1'";
                $sql.=" );";


                $log_module = "Project";

                if ($log_desc != "")
                {
                    $sql = "INSERT INTO tbl_change_record (";
                    $sql.=" log_date ";
                    if ($log_desc != "")
                        $sql.=", log_desc ";
                    if ($log_module != "")
                        $sql.=", module ";
                    if ($log_id != "")
                        $sql.=", module_id ";
                    if ($project_or_po != "")
                        $sql.=", project_or_po ";
                    $sql.=", status ";
                    $sql.=", created_date ";
                    $sql.=", employee_id ";
                    $sql.=")";
                    $sql.=" VALUES (";
                    $sql.=" " . date('U');
                    if ($log_desc != "")
                        $sql.=", '" . $log_desc . "' ";
                    if ($log_module != "")
                        $sql.=", '" . $log_module . "' ";
                    if ($log_id != "")
                        $sql.=", $log_id ";
                    if ($project_or_po != "")
                        $sql.=", '" . $project_or_po . "' ";
                    $sql.=" ,1 ";
                    $sql.=" ," . date('U');
                    $sql.=" ," . $_SESSION["employeeID"];
                    $sql.=" )" . ";";
                }
            }
        }
        if ($sql != "")
        {
            //echo "4".$sql;
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "Upload Tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }

        $query_Name = "delete from tbl_upload_pack where upload_pack_u=1 and  pid=" . $pid;
        for ($i          = 0; $i < count($upload_packages); $i++)
        {
            $query_Name.=";INSERT INTO tbl_upload_pack (pid,upload_pack_u";




            if (isset($upload_packages[$i]) && $upload_packages[$i] != "")
            {
                // $log_desc.="Vendor,";
                $query_Name.=", pack_id  ";
            }

            $query_Name.= ") VALUES (" . $pid . ",1";



            if (isset($upload_packages[$i]) && $upload_packages[$i] != "")
            {
                $query_Name.=", '" . $upload_packages[$i] . "' ";

                $query_Name.=" )";
            }
        }
        if ($query_Name != "")
        {
            // echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing project element information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }
    }
    if (isset($srID) && $srID != '')
    {
        $query_Name = "";
        if ($hdn_sample_present == 0)
        {
            $sql = "select id from tbl_prj_sample where pid= $pid order by id";
            if ($sql != "")
            {
                if (!($result = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while storing prj_sample information to database!";
                    echo json_encode($return_arr);
                    return;
                }
            }
            while ($row_cnt             = pg_fetch_array($result))
            {
                $data_sample_present = $row_cnt;
            }
            $update_project      = 1;
            if (isset($data_sample_present) && count($data_sample_present) > 0 && $data_sample_present['id'] > 0)
            {
                $update_project = 0;
            }
            if ($update_project)
            {
                $prj_vendor_present = 0;

                $query_Name.="UPDATE tbl_newproject SET  status =1 ";
                if ($srID != "")
                    $query_Name.=", projectname = '" . pg_escape_string($srID) . "' ";
                if ($sampletype != "")
                    $query_Name.=", stock_or_custom = $sampletype ";
                if ($sample_color != "")
                    $query_Name.=", color = '" . pg_escape_string($sample_color) . "' ";
                if ($fabricType != "")
                    $query_Name.=", materialtype = '" . pg_escape_string($fabricType) . "' ";
                $query_Name.="  where pid='$pid'" . ";";


                $query_Name.="INSERT INTO tbl_prj_style (";
                $query_Name.=" pid";
                $query_Name.= ", style";
				$query_Name.= ", vendor_style";
                if ($sample_cost != "")
                    $query_Name.= ", retailprice";
                if ($customerTargetprice != "")
                    $query_Name.= ", priceunit";
                $query_Name.=" ,status";
                $query_Name.=" ,createddate";
                $query_Name.=" ) VALUES(";
                $query_Name.=" '$pid' ";
                $query_Name.=", '$sample_style' ";
				$query_Name.=", '$tbl_prj_style' ";
                if ($sample_cost != "")
                    $query_Name.=", '" . pg_escape_string($sample_cost) . "' ";
                if ($customerTargetprice != "")
                    $query_Name.=", '" . pg_escape_string($customerTargetprice) . "' ";
                $query_Name.=" ,'1' ";
                $query_Name.=" ," . date('U');
                $query_Name.=" );";

                $sql                   = "select \"purchaseId\" from tbl_prjpurchase where pid = $pid";
                $data_purchase_present = 0;
                if (!($result                = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while getting purchase information from database!";
                    echo json_encode($return_arr);
                    return;
                }
                while ($row_cnt             = pg_fetch_array($result))
                {
                    $data_purchase_present = $row_cnt['purchaseId'];
                }

                if ($data_purchase_present != "" && $data_purchase_present > 0)
                {
                    $query_Name.="UPDATE tbl_prjpurchase SET  status=1 ";
                    if ($detaildesc != "")
                        $query_Name.=", garmentdesc = '" . pg_escape_string($detaildesc) . "' ";
                    if ($sizerequest != "")
                        $query_Name.=", sizeneeded = '" . pg_escape_string($sizerequest) . "' ";
                    $query_Name.="  where \"purchaseId\"= $data_purchase_present;";
                }
                else
                {
                    $query_Name.="INSERT INTO tbl_prjpurchase (";
                    $query_Name.=" pid ";
                    if ($sizerequest != "")
                        $query_Name.=", sizeneeded ";
                    if ($detaildesc != "")
                        $query_Name.=", garmentdesc ";
                    $query_Name.=", status ";
                    $query_Name.=", createddate ";
                    $query_Name.=", updateddate ";
                    $query_Name.=")";
                    $query_Name.=" VALUES (";
                    $query_Name.=" $pid ";
                    if ($sizerequest != "")
                        $query_Name.=", '" . pg_escape_string($sizerequest) . "' ";
                    if ($detaildesc != "")
                        $query_Name.=" ,'" . pg_escape_string($detaildesc) . "' ";
                    $query_Name.=" ,1 ";
                    $query_Name.=" ,'" . date('U') . "' ";
                    $query_Name.=" ,'" . date('U') . "'";
                    $query_Name.=" )" . ";";
                }
                $sql                 = "select vid from tbl_prjvendor where pid = $pid and vid = $sample_vendorID";
                $data_vendor_present = 0;
                if (!($result              = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while getting vid information from database!";
                    echo json_encode($return_arr);
                    return;
                }
                while ($row_cnt             = pg_fetch_array($result))
                {
                    $data_vendor_present = $row_cnt['vid'];
                }
                if ($data_vendor_present == "" && $data_vendor_present == 0)
                {
                    $query_Name.="INSERT INTO tbl_prjvendor(";
                    $query_Name.=" pid ";
                    $query_Name.=", status ";
                    $query_Name.=", createddate ";
                    if ($sample_vendorID != 0)
                        $query_Name.= ", vid";
                    $query_Name.=" ) VALUES(";
                    $query_Name.=" '$pid' ";
                    $query_Name.=", '1' ";
                    $query_Name.=",'" . date('U') . "' ";
                    if ($sample_vendorID != 0)
                        $query_Name.=", '$sample_vendorID' ";
                    $query_Name.=");";
                }
                if ($query_Name != "")
                {
                    if (!($result = pg_query($connection, $query_Name)))
                    {
                        $return_arr['error'] = "Error while storing vendor information to database!";
                        echo json_encode($return_arr);
                        return;
                    }
                }
            }
            $sql                 = "";
            pg_free_result($result);
        }

        $return_arr['sample_update'] = 1;
        $query_Name                  = "";
        if ($sampleId > 0)
        {
            $return_arr['sampleId'] = $sampleId;
            if ($hdn_track_update == 3)
                $log_desc               = "Sample Tab Updated";

            $query_Name.="UPDATE tbl_prj_sample SET  status =1 ";
            $query_Name.=", pid  = '$pid '";
            if ($brand_manufac != "")
                $query_Name.=", brand_manufaturer  = '" . pg_escape_string($brand_manufac) . "' ";
            else
                $query_Name.=", brand_manufaturer = null ";
            if ($srID != "")
                $query_Name.=", sample_id  = '" . pg_escape_string($srID) . "' ";


            if ($sample_style != "")
                $query_Name.=", style_number  = '" . pg_escape_string($sample_style) . "' ";
            else
                $query_Name.=",style_number= null ";
            if ($sampletype != "")
                $query_Name.=", sampletype   = '" . $sampletype . "' ";
            else
                $query_Name.=",sampletype = null ";
            if ($sample_quantity != "")
                $query_Name.=", quantity   = '" . $sample_quantity . "' ";
            else
                $query_Name.=",quantity = null ";
            if ($briefdesc != "")
                $query_Name.=", brief_desc   = '" . pg_escape_string($briefdesc) . "' ";
            else
                $query_Name.=",brief_desc= null ";
            if ($detaildesc != "")
                $query_Name.=", detail_description   = '" . pg_escape_string($detaildesc) . "' ";
            else
                $query_Name.=",detail_description = null ";
            if ($sizerequest != "")
                $query_Name.=", size_requested  = '" . $sizerequest . "' ";
            else
                $query_Name.=",size_requested = null ";
            if ($dateneeded != "")
                $query_Name.=", dateneeded  = '" . $dateneeded . "' ";
            else
                $query_Name.=",dateneeded= null ";
            if ($sample_vendorID != "")
                $query_Name.=", vid  = '" . $sample_vendorID . "' ";
            else
                $query_Name.=",vid= null ";
            if ($mailvendor_check != "")
                $query_Name.=", mailvendor_check  = '" . $mailvendor_check . "' ";
            else
                $query_Name.=",mailvendor_check= null ";
            if ($sample_color != "")
                $query_Name.=", sample_color  = '" . pg_escape_string($sample_color) . "' ";
            else
                $query_Name.=",sample_color= null ";
            if ($fabricType != "")
                $query_Name.=", fabric  = '" . pg_escape_string($fabricType) . "' ";
            else
                $query_Name.=",fabric= null ";
            if ($sample_cost != "")
                $query_Name.=", fabric_cost  = '" . $sample_cost . "' ";
            else
                $query_Name.=",fabric_cost= null ";
            if ($customerTargetprice != "")
                $query_Name.=", quote_price  = '" . $customerTargetprice . "' ";
            else
                $query_Name.=",quote_price= null ";
            if ($embroidery != "")
                $query_Name.=", embroidery_new  = '" . $embroidery . "' ";
            else
                $query_Name.=",embroidery_new= null ";
            if ($silkscreening != "")
                $query_Name.=", silkscreening  = '" . $silkscreening . "' ";
            else
                $query_Name.=",silkscreening= null ";
            if ($generate_po != "")
                $query_Name.=", generate_po  = '" . $generate_po . "' ";
            else
                $query_Name.=",generate_po= null ";
            if ($customerpo != "")
                $query_Name.=", customer_po  = '" . $customerpo . "' ";
            else
                $query_Name.=",customer_po= null ";
            if (nternalpo != "")
                $query_Name.=", internal_po  = '" . $internalpo . "' ";
            else
                $query_Name.=",internal_po= null ";
            if ($invoiceno != "")
                $query_Name.=", invoicenumber  = '" . $invoiceno . "' ";
            else
                $query_Name.=",invoicenumber= null ";
            if ($order_confirmation != "")
                $query_Name.=", order_confirmation  = '" . pg_escape_string($order_confirmation) . "' ";
            else
                $query_Name.=",order_confirmation= null ";
            if ($order_on != "")
                $query_Name.=", order_on  = '" . $order_on . "' ";
            else
                $query_Name.=",order_on= null ";
            if ($bid_number != "")
                $query_Name.=", bid_number  = '" . pg_escape_string($bid_number) . "' ";
            else
                $query_Name.=",bid_number= null ";
            if ($project_budget != "")
                $query_Name.=", project_budget  = '" . $project_budget . "' ";
            else
                $query_Name.=",project_budget= null ";
            if ($carrier_shipping != "")
                $query_Name.=", carrier_shipping  = '" . $carrier_shipping . "' ";
            else
                $query_Name.=",carrier_shipping= null ";
            if ($shipperno != "")
                $query_Name.=", clientshipper_no  = '" . $shipperno . "' ";
            else
                $query_Name.=",clientshipper_no= null ";
            if ($returnauth != "")
                $query_Name.=", returnauthor  = '" . pg_escape_string($returnauth) . "' ";
            else
                $query_Name.=",returnauthor= null ";
            if ($tracking_number != "")
                $query_Name.=", tracking_number  = '" . pg_escape_string($tracking_number) . "' ";
            else
                $query_Name.=",tracking_number= null ";
            if ($shipped_on != "")
                $query_Name.=", shipped_on  = '" . $shipped_on . "' ";
            else
                $query_Name.=",shipped_on= null ";
            if ($is_mail != "")
                $query_Name.=", send_client_mail  = '" . $is_mail . "' ";
            else
                $query_Name.=",send_client_mail= null ";
            $query_Name.=", created_date = " . date('U');
            $query_Name.=", modified_date = " . date('U');
            $query_Name.="  where pid =$pid and id= '" . $sampleId . "';";
        }
        else
        {
            $sql        = "select nextval('tbl_prj_sample_id_seq'::regclass) as sampleid ";
            if (!($result_sql = pg_query($connection, $sql)))
            {
                $return_arr['error']    = "Error while storing project id from database!";
                echo json_encode($return_arr);
                return;
            }
            $row                    = pg_fetch_array($result_sql);
            $sampleId               = $row['sampleid'];
            $return_arr['sampleId'] = $sampleId;
            $sql                    = "";
            $log_desc               = "Added To Sample:";

            $query_Name.="INSERT INTO tbl_prj_sample (";
            $query_Name.=" id ";
            $query_Name.=" ,pid ";
            if ($brand_manufac != "")
            {
                // $log_desc .= "brand manufacturer,";
                $query_Name.=", brand_manufaturer ";
            }
            if ($srID != "")
            {
                // $log_desc .="Sample ID,";
                $query_Name.=", sample_id ";
            }
            if ($sample_style != "")
            {
                // $log_desc .="Sample Style,";
                $query_Name.=", style_number";
            }
            if ($sampletype != "")
            {
                //  $log_desc .="Sample Type,";
                $query_Name.=", sampletype ";
            }
            if ($sample_quantity != "")
            {
                //  $log_desc .="Quantity,";
                $query_Name.=", quantity ";
            }
            if ($briefdesc != "")
            {
                $log_desc .="Brief Description,";
                $query_Name.=", brief_desc ";
            }
            if ($detaildesc != "")
            {
                // $log_desc .="Detailed Description,";
                $query_Name.=", detail_description ";
            }
            if ($sizerequest != "")
            {
                // $log_desc .="Sizes Requested,";
                $query_Name.=", size_requested ";
            }
            if ($dateneeded != "")
            {
                //  $log_desc .="Date Needed,";
                $query_Name.=", dateneeded ";
            }
            if ($sample_vendorID != "")
            {
                //  $log_desc .="Vendor,";
                $query_Name.=", vid ";
            }
            if ($mailvendor_check != "")
                $query_Name.=", mailvendor_check ";
            if ($sample_color != "")
            {
                //  $log_desc .="Sample Color,";
                $query_Name.=", sample_color ";
            }
            if ($fabricType != "")
            {
                //  $log_desc .="Fabric,";
                $query_Name.=", fabric ";
            }
            if ($sample_cost != "")
            {
                //  $log_desc .="Fabric Cost,";
                $query_Name.=", fabric_cost ";
            }
            if ($customerTargetprice != "")
            {
                // $log_desc .="Quote Price,";
                $query_Name.=", quote_price ";
            }
            if ($embroidery != "")
            {
                //  $log_desc .="Embroidery,";
                $query_Name.=", embroidery_new ";
            }
            if ($silkscreening != "")
            {
                // $log_desc .="Silk Screening,";
                $query_Name.=", silkscreening ";
            }
            if ($generate_po != "")
            {
                //   $log_desc .="Generate PO,";
                $query_Name.=", generate_po ";
            }
            if ($customerpo != "")
            {
                //  $log_desc .="Customer Po,";
                $query_Name.=", customer_po ";
            }
            if ($internalpo != "")
            {
                //   $log_desc .="Internal PO,";
                $query_Name.=", internal_po ";
            }
            if ($invoiceno != "")
            {
                //   $log_desc .="Invoice Number,";
                $query_Name.=", invoicenumber ";
            }
            if ($order_confirmation != "")
            {
                //   $log_desc .="Sample ID,";
                $query_Name.=", order_confirmation ";
            }
            if ($order_on != "")
            {
                //  $log_desc .="Order On,";
                $query_Name.=", order_on ";
            }
            if ($bid_number != "")
            {
                // $log_desc .="Bid Number,";
                $query_Name.=", bid_number ";
            }
            if ($project_budget != "")
            {
                // $log_desc .="Project Budget,";
                $query_Name.=", project_budget ";
            }
            if ($carrier_shipping != "")
            {
                // $log_desc .="Carrier Shipping,";
                $query_Name.=", carrier_shipping ";
            }
            if ($shipperno != "")
            {
                // $log_desc .="Client Shipper Number,";
                $query_Name.=", clientshipper_no ";
            }
            if ($returnauth != "")
            {
                // $log_desc .="Return Authorisation,";
                $query_Name.=", returnauthor ";
            }
            if ($tracking_number != "")
            {
                $log_desc .="Tracking Number,";
                $query_Name.=", tracking_number ";
            }
            if ($shipped_on != "")
            {
                // $log_desc .="Shipped On,";
                $query_Name.=", shipped_on ";
            }
            if ($is_mail != "")
                $query_Name.=", send_client_mail ";
            $query_Name.=", created_date ";
            $query_Name.=", modified_date ";
            $query_Name.=", created_by ";
            $query_Name.=", status ";
            $query_Name.=")";
            $query_Name.=" VALUES (";
            $query_Name.="  " . $sampleId . "  ";
            $query_Name.="  ,$pid  ";
            if ($brand_manufac != "")
                $query_Name.=", '" . pg_escape_string($brand_manufac) . "' ";
            if ($srID != "")
                $query_Name.=", '" . pg_escape_string($srID) . "'  ";
            if ($sample_style != "")
                $query_Name.=", '" . pg_escape_string($sample_style) . "'  ";
            if ($sampletype != "")
                $query_Name.=", '" . $sampletype . " ' ";
            if ($sample_quantity != "")
                $query_Name.=", " . pg_escape_string($sample_quantity) . "  ";
            if ($briefdesc != "")
                $query_Name.=", '" . pg_escape_string($briefdesc) . "'  ";
            if ($detaildesc != "")
                $query_Name.=", '" . pg_escape_string($detaildesc) . " ' ";
            if ($sizerequest != "")
                $query_Name.=", '" . pg_escape_string($sizerequest) . "'  ";
            if ($dateneeded != "")
                $query_Name.=", '" . $dateneeded . "'  ";
            if ($sample_vendorID != "")
                $query_Name.=", " . pg_escape_string($sample_vendorID) . "  ";
            if ($mailvendor_check != "")
                $query_Name.=", '" . pg_escape_string($mailvendor_check) . "'  ";
            if ($sample_color != "")
                $query_Name.=", '" . pg_escape_string($sample_color) . "'  ";
            if ($fabricType != "")
                $query_Name.=", '" . pg_escape_string($fabricType) . "'  ";
            if ($sample_cost != "")
                $query_Name.=", '" . $sample_cost . "'  ";
            if ($customerTargetprice != "")
                $query_Name.=", '" . $customerTargetprice . "'  ";
            if ($embroidery != "")
                $query_Name.=", " . $embroidery . "  ";
            if ($silkscreening != "")
                $query_Name.=", " . $silkscreening . "  ";
            if ($generate_po != "")
                $query_Name.=", '" . pg_escape_string($generate_po) . "'  ";
            if ($customerpo != "")
                $query_Name.=", '" . $customerpo . "'  ";
            if ($internalpo != "")
                $query_Name.=", '" . $internalpo . "'  ";
            if ($invoiceno != "")
                $query_Name.=", '" . $invoiceno . "'  ";
            if ($order_confirmation != "")
                $query_Name.=", '" . pg_escape_string($order_confirmation) . "'  ";
            if ($order_on != "")
                $query_Name.=", '" . $order_on . "'  ";
            if ($bid_number != "")
                $query_Name.=", '" . pg_escape_string($bid_number) . "'  ";
            if ($project_budget != "")
                $query_Name.=", '" . $project_budget . "'  ";
            if ($carrier_shipping != "")
                $query_Name.=", '" . pg_escape_string($carrier_shipping) . "'  ";
            if ($shipperno != "")
                $query_Name.=", '" . pg_escape_string($shipperno) . "'  ";
            if ($returnauth != "")
                $query_Name.=", '" . pg_escape_string($returnauth) . "'  ";
            if ($tracking_number != "")
                $query_Name.=", '" . pg_escape_string($tracking_number) . "'  ";
            if ($shipped_on != "")
                $query_Name.=", '" . $shipped_on . "'  ";
            if ($is_mail != "")
                $query_Name.=", '" . $is_mail . "'  ";
            $query_Name.=" ,'" . date('U') . "' ";
            $query_Name.=" ,'" . date('U') . "'";
            $query_Name.=" ," . $_SESSION["employeeID"];
            $query_Name.=" ,1 ";
            $query_Name.=" )" . ";";
        }
        // echo "query:-".$query_Name;
        if ($query_Name != "")
        { //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing sample information to database!";
                echo json_encode($return_arr);
                return;
            }
        }
        $query_Name          = "";
        pg_free_result($result);

        if ($log_desc != "")
        {
            $sql    = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "5".$sql;
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "Sample Tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }

        for ($j = 0; $j < count($sample_file_name); $j++)
        {
            if ($sample_file_id[$j] == 0)
            {
                $sql        = "select nextval('tbl_prjsample_uploads_upload_id_seq'::regclass) as uploadid ";
                if (!($result_sql = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while storing project id from database!";
                    echo json_encode($return_arr);
                    return;
                }
                $row                 = pg_fetch_array($result_sql);
                $uploadId            = $row['uploadid'];
                $sql                 = "";
                // $log_desc = "Following Files Uploaded in Sample:";
                $upload              = 0;
                $comma               = ',';

                $query_Name.="INSERT INTO tbl_prjsample_uploads (";
                $query_Name.=" upload_id ";
                $query_Name.=" ,sample_id ";
                $query_Name.=" ,pid ";
                if ($sample_file_name[$j] != "")
                {
                    /* if ($upload != 0)
                      $log_desc .=$sample_file_name[$j] . $comma; */
                    $query_Name.=" ,filename ";
                    $upload++;
                }
                if ($sample_file_type[$j] != "")
                    $query_Name.=" ,uploadtype ";
                $query_Name.=" ,status ";
                $query_Name.=" ,createddate ";
                $query_Name.=" ,updateddate ";
                $query_Name.=" ,createdby ";
                $query_Name.=")";
                $query_Name.=" VALUES (";
                $query_Name.="  $uploadId  ";
                $query_Name.=" , " . $sampleId . "  ";
                $query_Name.=" , $pid  ";
                if ($sample_file_name[$j] != "")
                    $query_Name.=", '" . pg_escape_string($sample_file_name[$j]) . "' ";
                if ($sample_file_type[$j] != "")
                    $query_Name.=", '" . pg_escape_string($sample_file_type[$j]) . "'  ";
                $query_Name.=", 1 ";
                $query_Name.=" ,'" . date('U') . "' ";
                $query_Name.=" ,'" . date('U') . "'";
                $query_Name.=" ," . $_SESSION["employeeID"];
                $query_Name.=" );";
            }
        }
        if ($query_Name != "")
        { //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing sample uploads information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }
        if ($log_desc != "")
        {
            $sql    = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "6".$sql;
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "Sample Tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
        $upload              = 0;
        $comma               = ',';

        for ($j = 0; $j < count($hdn_sample_notesId); $j++)
        {
            $log_desc = "Added To Sample:";
            if ($hdn_sample_notesId[$j] == 0)
            {
                $sql        = "select nextval('tbl_prjsample_notes_notes_id_seq'::regclass) as notesid ";
                if (!($result_sql = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while storing project id from database!";
                    echo json_encode($return_arr);
                    return;
                }
                $row                 = pg_fetch_array($result_sql);
                $notesId             = $row['notesid'];
                $sql                 = "";
                $query_Name.="INSERT INTO tbl_prjsample_notes (";
                $query_Name.=" notes_id ";
                if ($sample_textAreaName[$j] != "")
                    $query_Name.=" ,sample_id ";
                $query_Name.=" ,pid ";
                $query_Name.=" ,created_date ";
                $query_Name.=" ,notes ";
                $query_Name.=" ,is_active ";
                $query_Name.=" ,created_by ";
                $query_Name.=")";
                $query_Name.=" VALUES (";
                $query_Name.="  " . $notesId . "  ";
                $query_Name.=" , " . $sampleId . "  ";
                $query_Name.=" , " . $pid . "  ";
                $query_Name.=" ,'" . date('U') . "' ";
                if ($sample_textAreaName[$j] != "")
                {
                    $log_desc .= "Notes";
                    $query_Name.=" , '" . pg_escape_string($sample_textAreaName[$j]) . "'  ";
                }
                $query_Name.=" , 1  ";
                $query_Name.=" ," . $_SESSION["employeeID"];
                $query_Name.=" );";
            }
        }
        if ($query_Name != "")
        { //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing sample notes information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }
        if ($log_desc != "")
        {
            $sql    = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "7".$sql;
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "Sample Tab Notes :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
        //db query for generate po	
        if ($po_id != "")
        {
            $sql    = "Select count(*) as n from tbl_prj_sample_po where po_number='$po_number' and pid =$pid ";
            if ($po_id > 0)
                $sql .= " and id <> $po_id";
            if (!($result = pg_query($connection, $sql)))
            {
                print("Failed query: " . pg_last_error($connection));
                exit;
            }
            $quoteCount = "";
            while ($row        = pg_fetch_array($result))
            {
                $quoteCount = $row;
            }
            if ((int) $quoteCount['n'] > 0)
            {
                $return_arr['error'] = "P.O Number already exist";
                echo json_encode($return_arr);
                return;
            }
            $query               = "";
            if ($po_id == 0)
            {
                $query  = "select nextval('tbl_prj_sample_po_id_seq'::regclass) as id ";
                if (!($result = pg_query($connection, $query)))
                {
                    $return_arr['error'] = "Error while getting quote information from database!" . pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                while ($row                 = pg_fetch_array($result))
                {
                    $po_id    = $row['id'];
                }
                pg_free_result($result);
                $log_desc = "Added To Sample:";

                $query = "INSERT INTO tbl_prj_sample_po (id,sample_id,pid, status";
                if ($company_val > 0)
                {
                    // $log_desc.="Company,";
                    $query.=",company_id ";
                }
                if ($vendor_id > 0)
                {
                    // $log_desc.="Vendor,";
                    $query.=", vendor_id ";
                }
                if ($client > 0)
                {
                    // $log_desc.="Client,";
                    $query.=", client_id ";
                }
                if ($ship_to_select > 0)
                {
                    // $log_desc.="Ship To,";
                    $query.=", ship_to ";
                }
                if ($other_name != "")
                {
                    // $log_desc.="Other Name,";
                    $query.=", other_name ";
                }
                if ($other_street != "")
                {
                    //  $log_desc.="Other Street,";
                    $query.=", other_street ";
                }
                if ($other_city != "")
                {
                    //   $log_desc.="Other City,";
                    $query.=", other_city ";
                }
                if ($other_state != "")
                {
                    //   $log_desc.="Other State,";
                    $query.=", other_state ";
                }
                if ($other_zip != "")
                {
                    //  $log_desc.="Other Zip,";
                    $query.=", other_zip ";
                }
                if ($client_shipto != "")
                {
                    //   $log_desc.="Ship To Client,";
                    $query.=", ship_to_clientfield ";
                }
                if ($client_customer_id != "")
                {
                    //  $log_desc.="Ship To Customer,";
                    $query.=", ship_to_customer_id ";
                }
                if ($vendor_shipto != "")
                {
                    //  $log_desc.="Ship To Vendor,";
                    $query.=", ship_to_vendorfield ";
                }
                if ($po_number != "")
                {
                    // $log_desc.="PO Number,";
                    $query.=", po_number ";
                }
                if ($podate != "")
                {
                    //  $log_desc.="PO Date,";
                    $query.=", po_date ";
                }
                if ($internalpo != "")
                {
                    //  $log_desc.="Internal PO,";
                    $query.=", internal_po ";
                }
                if ($shipto_vendorId != "")
                {
                    //  $log_desc.="Ship To Vendor,";
                    $query.=", shipto_vendor_id ";
                }
                if ($goods_through != "")
                {
                    // $log_desc.="Good Through,";
                    $query.=", good_thru ";
                }
                if ($payment_terms > 0)
                {
                    //  $log_desc.="Payment,";
                    $query.=", payment_id ";
                }
                if ($salesrep > 0)
                {
                    //  $log_desc.="Sales Rep,";
                    $query.=", sales_rep ";
                }
                if ($amountsubtotal != "")
                {
                    //   $log_desc.="Amount Sub Total,";
                    $query.=", amount_sub_total ";
                }
                if ($taxsubtotal != "")
                {
                    //   $log_desc.="Tax Sub Total,";
                    $query.=", tax_sub_total ";
                }
                if ($total != "")
                {
                    //  $log_desc.="Total,";
                    $query.=", total ";
                }
                if ($shipvia > 0)
                {
                    //  $log_desc.="Ship Via,";
                    $query.=", ship_via ";
                }
                if ($other_shipper != "")
                {
                    //  $log_desc.="Shipper Number,";
                    $query.=", shipperno ";
                }
                if ($carrier > 0)
                {
                    //   $log_desc.="Carrier ID,";
                    $query.=", carrier_id ";
                }
                if ($instructions != "")
                {
                    $log_desc.="Instruction Notes";
                    $query.=", instruction_notes ";
                }
                $query.=", createdby ";
                $query.=", createddate ";
                $query.=", updateddate ";
                $query.=")";
                $query.=" VALUES ($po_id,$sampleId,$pid, 1";
                if ($company_val > 0)
                    $query.=",$company_val ";
                if ($vendor_id > 0)
                    $query.=" ,$vendor_id ";
                if ($client > 0)
                    $query.=" ,$client ";
                if ($ship_to_select > 0)
                    $query.=" ,$ship_to_select ";
                if ($other_name != "")
                    $query.=" ,'" . pg_escape_string($other_name) . "' ";
                if ($other_street != "")
                    $query.=",'" . pg_escape_string($other_street) . "' ";
                if ($other_city != "")
                    $query.=" ,'" . pg_escape_string($other_city) . "' ";
                if ($other_state != "")
                    $query.=" ,'" . pg_escape_string($other_state) . "' ";
                if ($other_zip != "")
                    $query.=" ,'$other_zip' ";
                if ($client_shipto != "")
                    $query.=",'" . pg_escape_string($client_shipto) . "' ";
                if ($client_customer_id != "")
                    $query.=" ,'" . pg_escape_string($client_customer_id) . "' ";
                if ($vendor_shipto != "")
                    $query.=" ,'" . pg_escape_string($vendor_shipto) . "' ";
                if ($po_number != "")
                    $query.=" ,'" . pg_escape_string($po_number) . "' ";
                if ($podate != "")
                    $query.="," . strtotime($podate);
                if ($internalpo != "")
                    $query.=" ,'$internalpo' ";
                if ($shipto_vendorId != "")
                    $query.=" ,'" . pg_escape_string($shipto_vendorId) . "' ";
                if ($goods_through != "")
                    $query.=" ," . strtotime($goods_through);
                if ($payment_terms > 0)
                    $query.=" ,$payment_terms ";
                if ($salesrep > 0)
                    $query.=" ,$salesrep ";
                if ($amountsubtotal != "")
                    $query.=" ,$amountsubtotal ";
                if ($taxsubtotal != "")
                    $query.=" ,$taxsubtotal ";
                if ($total != "")
                    $query.=" ,$total ";
                if ($shipvia > 0)
                    $query.=" ,'$shipvia' ";
                if ($other_shipper != "")
                    $query.=" ,'" . pg_escape_string($other_shipper) . "' ";
                if ($carrier > 0)
                    $query.=" ,$carrier ";
                if ($instructions != "")
                    $query.=",'" . pg_escape_string($instructions) . "' ";
                $query.=" ,{$_SESSION['employeeID']} ";
                $query.=" ," . date(U) . " ";
                $query.=" ," . date(U) . " ";
                $query.=")";
            }
            else if ($po_id > 0)
            {
                if ($hdn_track_update == 3)
                    $log_desc = "Sample Tab Updated";
                $query    = "Update tbl_prj_sample_po SET ";
                $query.="status =1";
                $query.=",sample_id =$sampleId";
                $query.=",pid =$pid";
                if ($company_val > 0)
                    $query.=", company_id =$company_val";
                else
                    $query.=", company_id =0";
                if ($vendor_id > 0)
                    $query.=", vendor_id =$vendor_id";
                else
                    $query.=", vendor_id =0";
                if ($client > 0)
                    $query.=", client_id =$client ";
                else
                    $query.=", client_id =0 ";
                if ($ship_to_select > 0)
                    $query.=", ship_to =$ship_to_select ";
                else
                    $query.=", ship_to =0 ";
                if ($other_name != "")
                    $query.=", other_name ='" . pg_escape_string($other_name) . "'";
                else
                    $query.=", other_name =null";
                if ($other_street != "")
                    $query.=", other_street ='" . pg_escape_string($other_street) . "'";
                else
                    $query.=", other_street =null";
                if ($other_city != "")
                    $query.=", other_city ='" . pg_escape_string($other_city) . "'";
                else
                    $query.=", other_city =null";
                if ($other_state != "")
                    $query.=", other_state ='" . pg_escape_string($other_state) . "' ";
                else
                    $query.=", other_state =null ";
                if ($other_zip != "")
                    $query.=", other_zip ='" . pg_escape_string($other_zip) . "' ";
                else
                    $query.=", other_zip =null ";
                if ($client_shipto != "")
                    $query.=", ship_to_clientfield ='" . pg_escape_string($client_shipto) . "'";
                else
                    $query.=", ship_to_clientfield =null";
                if ($client_customer_id != "")
                    $query.=", ship_to_customer_id ='" . pg_escape_string($client_customer_id) . "'";
                else
                    $query.=", ship_to_customer_id =null";
                if ($vendor_shipto != "")
                    $query.=", ship_to_vendorfield ='" . pg_escape_string($vendor_shipto) . "' ";
                else
                    $query.=", ship_to_vendorfield = 0 ";
                if ($po_number != "")
                    $query.=", po_number ='" . pg_escape_string($po_number) . "'";
                else
                    $query.=", po_number =null";
                if ($internalpo != "")
                    $query.=", internal_po ='" . pg_escape_string($internalpo) . "'";
                else
                    $query.=", internal_po =null";
                if ($podate != "")
                    $query.=", po_date =" . strtotime($podate);
                else
                    $query.=", po_date =null";
                if ($shipto_vendorId != "")
                    $query.=", shipto_vendor_id ='" . pg_escape_string($shipto_vendorId) . "' ";
                else
                    $query.=", shipto_vendor_id =null ";
                if ($goods_through != "")
                    $query.=", good_thru =" . strtotime($goods_through);
                if ($payment_terms > 0)
                    $query.=", payment_id =$payment_terms ";
                else
                    $query.=", payment_id =0";
                if ($salesrep > 0)
                    $query.=", sales_rep =$salesrep";
                else
                    $query.=", sales_rep =$salesrep";
                if ($amountsubtotal != "")
                    $query.=", amount_sub_total =$amountsubtotal";
                else
                    $query.=", amount_sub_total =0";
                if ($taxsubtotal != "")
                    $query.=", tax_sub_total ='$taxsubtotal'";
                else
                    $query.=", tax_sub_total =0";
                if ($total != "")
                    $query.=", total ='$total'";
                else
                    $query.=", total =0";
                if ($shipvia > 0)
                    $query.=", ship_via =$shipvia";
                else
                    $query.=", ship_via =0";
                if ($other_shipper != "")
                    $query.=", shipperno ='" . pg_escape_string($other_shipper) . "'";
                else
                    $query.=", shipperno =null";
                if ($client_shipper > 0)
                    $query.=", client_shipper =$client_shipper";
                if ($carrier > 0)
                    $query.=", carrier_id =$carrier";
                else
                    $query.=", carrier_id =0";
                if ($instructions != "")
                    $query.=", instruction_notes ='" . pg_escape_string($instructions) . "'";
                else
                    $query.=", instruction_notes =null";
                $query.=", updatedby ='{$_SESSION['employeeID']}' ";
                $query.=", updateddate =" . date(U) . " ";
                $query.=" where id=" . $po_id;
            }
            if ($query != "")
            {
                if (!($result = pg_query($connection, $query)))
                {
                    $return_arr['error'] = "Error while storing quote information to database!" . pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
            }
            if ($log_desc != "")
            {
                $sql      = "INSERT INTO tbl_change_record (";
                $sql.=" log_date ";
                if ($log_desc != "")
                    $sql.=", log_desc ";
                if ($log_module != "")
                    $sql.=", module ";
                if ($log_id != "")
                    $sql.=", module_id ";
                if ($project_or_po != "")
                    $sql.=", project_or_po ";
                $sql.=", status ";
                $sql.=", created_date ";
                $sql.=", employee_id ";
                $sql.=")";
                $sql.=" VALUES (";
                $sql.=" " . date('U');
                if ($log_desc != "")
                    $sql.=", '" . $log_desc . "' ";
                if ($log_module != "")
                    $sql.=", '" . $log_module . "' ";
                if ($log_id != "")
                    $sql.=", $log_id ";
                if ($project_or_po != "")
                    $sql.=", '" . $project_or_po . "' ";
                $sql.=" ,1 ";
                $sql.=" ," . date('U');
                $sql.=" ," . $_SESSION["employeeID"];
                $sql.=" )" . ";";
                $log_desc = "";
                //echo "8".$sql;
                if (!($result   = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Sample Tab samplPo:" . pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $sql                 = "";
            }
            $return_arr['po_id'] = $po_id;
            $query               = "";
            for ($i                   = 0; $i < count($item); $i++)
            {
                $item[$i] = pg_escape_string($item[$i]);
                $desc[$i] = pg_escape_string($desc[$i]);
                if ($item[$i] != "" && $hdn_id[$i] != "" && $hdn_id[$i] > 0)
                {
                    $query .= "Update tbl_prj_sample_po_items SET status =1";
                    $query .= ", po_id = $po_id ";
                    $query .= ", sample_id = $sampleId ";
                    $query .= ", pid = $pid ";
                    $query .= ", itemno = '$item[$i]' ";
                    if ($desc[$i] != "")
                        $query .= ", description = '" . pg_escape_string($desc[$i]) . "' ";
                    else
                        $query .= ", description = null ";
                    if ($unitprice[$i] != "")
                        $query .= ", unit_price = $unitprice[$i] ";
                    else
                        $query .= ", unit_price =0 ";
                    if ($quantity[$i] != "")
                        $query .= ", quantity = $quantity[$i] ";
                    else
                        $query .= ", quantity = 0 ";
                    if ($tax_type[$i] > 0)
                        $query .= ", tax_type = $tax_type[$i] ";
                    else
                        $query .= ", tax_type = 0";
                    if ($tax_amount[$i] != "")
                        $query .= ", tax_amount = $tax_amount[$i] ";
                    else
                        $query .= ", tax_amount = 0 ";
                    if ($amount[$i] != "")
                        $query .= ", amount = $amount[$i] ";
                    else
                        $query .= ", amount = 0 ";
                    $query .=" where id=" . $hdn_id[$i] . " ; ";
                }
                else if ($hdn_id[$i] != "")
                {
                    $query .="INSERT INTO tbl_prj_sample_po_items ( ";
                    $query .=" po_id ";
                    $query .=", sample_id ";
                    $query .=", pid ";
                    $query .=", itemno ";
                    $query .=", description ";
                    if ($unitprice[$i] != "")
                        $query .=", unit_price ";
                    if ($quantity[$i] != "")
                        $query .=", quantity ";
                    if ($tax_type[$i] > 0)
                        $query .=", tax_type ";
                    $query .=", tax_amount ";
                    $query .=", amount ";
                    $query .=")";
                    $query .=" VALUES (";
                    $query .=" '$po_id' ";
                    $query .=" ,'$sampleId' ";
                    $query .=" ,'$pid' ";
                    $query .=", '" . pg_escape_string($item[$i]) . "' ";
                    $query .=", '" . pg_escape_string($desc[$i]) . "' ";
                    if ($unitprice[$i] != "")
                        $query .=",$unitprice[$i] ";
                    if ($quantity[$i] != "")
                        $query .=", $quantity[$i] ";
                    if ($tax_type[$i] > 0)
                        $query .=",$tax_type[$i] ";
                    $query .=", $tax_amount[$i] ";
                    $query .=",$amount[$i] ";
                    $query .="); ";
                }
            }
            if ($query != "")
            {
                if (!($result = pg_query($connection, $query)))
                {
                    $return_arr['error'] = "Error while storing item information to database!" . pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
            }
            $return_arr['qid']   = $po_id;
        }
    }
    if ($proj_4 == 1)
    {
        $log_desc   = "";
        $log_module = "Project";
        $query_Name = "";
        if (isset($pricingId))
        {
            if ($pricingId == 0)
            {
                $sql    = "Select \"pricingId\" from tbl_prjpricing where pid = " . $pid . " limit 1";
                if (!($result = pg_query($connection, $sql)))
                {
                    print("Failed query1: " . pg_last_error($connection));
                    exit;
                }
                while ($row = pg_fetch_array($result))
                {
                    $data_prjPricing = $row;
                }
                if ($data_prjPricing['pricingId'] > 0)
                    $pricingId       = $data_prjPricing['pricingId'];
                pg_free_result($result);
            }
            if ($pricingId > 0)
            {
                if ($hdn_track_update == 4)
                    $log_desc = "Pricing Tab Updated";
                $query_Name.="UPDATE tbl_prjpricing SET  status=1 ";
                if ($targetPriceunit != "")
                    $query_Name.=", targetpriceunit = '$targetPriceunit'";
                else
                    $query_Name.=", targetpriceunit = null";
                if ($pt_invoice != "")
                    $query_Name.=", pt_invoice = '$pt_invoice'";
                else
                    $query_Name.=", pt_invoice = null";
                if ($shipping_cost != "")
                    $query_Name.=", shipping_cost = '$shipping_cost'";
                else
                    $query_Name.=", shipping_cost = null";
                if ($taxes != "")
                    $query_Name.=", taxes = '$taxes'";
                else
                    $query_Name.=", taxes = null";
                if ($targetRetailPrice != "")
                    $query_Name.=", targetretail = '$targetRetailPrice' ";
                else
                    $query_Name.=", targetretail = null ";
                if ($projectQuote != "")
                    $query_Name.=", prjquote = '$projectQuote' ";
                else
                    $query_Name.=", prjquote = null ";
                if ($pcost != "")
                    $query_Name.=", prjcost = '$pcost' ";
                else
                    $query_Name.=", prjcost = null ";
                if ($pestimate != "")
                    $query_Name.=", prj_estimatecost = '$pestimate' ";
                else
                    $query_Name.=", prj_estimatecost = null ";
                if ($pcompcost != "")
                    $query_Name.=", prj_completioncost = '$pcompcost' ";
                else
                    $query_Name.=", prj_completioncost = null ";
                $query_Name.=", createddate = " . date('U');
                $query_Name.=", updateddate = " . date('U');
                if ($pestprofit != "")
                    $query_Name.=",prj_est_profit='$pestprofit'";
                else
                    $query_Name.=",prj_est_profit = null";
                $query_Name.=" where pid='$pid' and \"pricingId\" = '$pricingId'" . ";";
                //echo $query_Name;
            }
            else
            {
                $log_desc = "Added To Pricing:";
                $query_Name.="INSERT INTO tbl_prjpricing (";
                $query_Name.=" pid";
                if ($pt_invoice != "")
                {
                    //  $log_desc .="PT Invoice,";
                    $query_Name.=" ,pt_invoice ";
                }
                if ($shipping_cost != "")
                {
                    //  $log_desc .="Shipping Cost,";
                    $query_Name.=", shipping_cost ";
                }
                if ($taxes != "")
                {
                    //  $log_desc .="Taxe,";
                    $query_Name.=", taxes ";
                }
                if ($targetPriceunit != "")
                {
                    //   $log_desc .="Target Unit Price,";
                    $query_Name.=" ,targetpriceunit ";
                }
                if ($targetRetailPrice != "")
                {
                    //  $log_desc .="Target Retail Price,";
                    $query_Name.=", targetretail ";
                }
                if ($projectQuote != "")
                {
                    //   $log_desc .="Project Quote,";
                    $query_Name.=", prjquote ";
                }
                if ($pcost != "")
                {
                    //  $log_desc .="Project Cost,";
                    $query_Name.=", prjcost ";
                }
                if ($pestimate != "")
                {
                    //  $log_desc .="Project Estimate Cost,";
                    $query_Name.=", prj_estimatecost ";
                }
                if ($pcompcost != "")
                {
                    //  $log_desc .="Project Completion Cost,";
                    $query_Name.=", prj_completioncost ";
                }
                $query_Name.=", status ";
                $query_Name.=", createddate ";
                $query_Name.=", updateddate ";
                if ($pestprofit != "")
                {
                    //  $log_desc .="Project Estimate Profit";
                    $query_Name.=",prj_est_profit";
                }
                $query_Name.=")";
                $query_Name.=" VALUES (";
                $query_Name.="  $pid  ";
                if ($pt_invoice != "")
                    $query_Name.=", '$pt_invoice' ";
                if ($shipping_cost != "")
                    $query_Name.=", '$shipping_cost' ";
                if ($taxes != "")
                    $query_Name.=", '$taxes' ";
                if ($targetPriceunit != "")
                    $query_Name.=", '$targetPriceunit' ";
                if ($targetRetailPrice != "")
                    $query_Name.=", '$targetRetailPrice' ";
                if ($projectQuote != "")
                    $query_Name.=", '$projectQuote' ";
                if ($pcost != "")
                    $query_Name.=", '$pcost' ";
                if ($pestimate != "")
                    $query_Name.=", '$pestimate' ";
                if ($pcompcost != "")
                    $query_Name.=" ,'$pcompcost' ";
                $query_Name.=" ,1 ";
                $query_Name.=" ,'" . date('U') . "' ";
                $query_Name.=" ,'" . date('U') . "'";
                if ($pestprofit != "")
                    $query_Name.=",'$pestprofit'";
                $query_Name.=" )" . ";";
            }
            if ($query_Name != "")
            {
                //echo $query_Name;
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing project pricing information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                $query_Name          = "";
                pg_free_result($result);
            }
        }
        /* New query for estimated unit cost */
        $query_Name          = "";
        if (isset($prj_estimate_id))
        {
            if ($prj_estimate_id > 0)
            {
                $query_Name.="UPDATE \"projectEstimatedUnitCost\" SET  status =1 ";
                if ($ptrnsetup != "")
                    $query_Name.=", ptrnsetup = '$ptrnsetup'";
                else
                    $query_Name.=", ptrnsetup = 0 ";
                if ($grdngsetup != "")
                    $query_Name.=", grdngsetup = '$grdngsetup' ";
                else
                    $query_Name.=", grdngsetup = 0 ";
                if ($smplefeesetup != "")
                    $query_Name.=", smplefeesetup = '$smplefeesetup' ";
                else
                    $query_Name.=", smplefeesetup = 0 ";
                if ($fabric != "")
                    $query_Name.=", fabric = '$fabric' ";
                else
                    $query_Name.=", fabric = 0 ";
                if ($trimfee != "")
                    $query_Name.=", trimfee = '$trimfee' ";
                else
                    $query_Name.=", trimfee = 0 ";
                if ($labour != "")
                    $query_Name.=", labour = '$labour' ";
                else
                    $query_Name.=", labour = 0 ";
                if ($duty != "")
                    $query_Name.=", duty = '$duty' ";
                else
                    $query_Name.=", duty = 0 ";
                if ($frieght != "")
                    $query_Name.=", frieght = '$frieght' ";
                else
                    $query_Name.=", frieght = 0 ";
                if ($other != "")
                    $query_Name.=", other = '$other' ";
                else
                    $query_Name.=", other = 0 ";
                $query_Name.="  where pid='$pid' and prj_estimate_id= $prj_estimate_id " . ";";
            }
            else
            {
                $log_desc .="Added To Pricing:";

                $query_Name = "INSERT INTO \"projectEstimatedUnitCost\" ( status";
                $query_Name.=", pid";
                if ($ptrnsetup != "")
                {
                    //  $log_desc .="Pattern Set Up";
                    $query_Name.=" ,ptrnsetup ";
                }
                if ($grdngsetup != "")
                {
                    // $log_desc .="Grading Set Up,";
                    $query_Name.=", grdngsetup ";
                }
                if ($smplefeesetup != "")
                {
                    //  $log_desc .="Sample Fee Set Up,";
                    $query_Name.=", smplefeesetup ";
                }
                if ($fabric != "")
                {
                    //  $log_desc .="Fabric,";
                    $query_Name.=", fabric ";
                }
                if ($trimfee != "")
                {
                    //   $log_desc .="Trim Fee,";
                    $query_Name.=", trimfee ";
                }
                if ($labour != "")
                {
                    //   $log_desc .="Labou,";
                    $query_Name.=", labour ";
                }
                if ($duty != "")
                {
                    //   $log_desc .="Duty,";
                    $query_Name.=", duty ";
                }
                if ($frieght != "")
                {
                    //   $log_desc .="Frieght,";
                    $query_Name.=", frieght ";
                }
                if ($other != "")
                {
                    //   $log_desc .="Other";
                    $query_Name.=", other ";
                }
                $query_Name.=")";
                $query_Name.=" VALUES (1";
                $query_Name.=", $pid ";
                if ($ptrnsetup != "")
                    $query_Name.=", '$ptrnsetup' ";
                if ($grdngsetup != "")
                    $query_Name.=", '$grdngsetup' ";
                if ($smplefeesetup != "")
                    $query_Name.=", '$smplefeesetup' ";
                if ($fabric != "")
                    $query_Name.=", '$fabric' ";
                if ($trimfee != "")
                    $query_Name.=", '$trimfee' ";
                if ($labour != "")
                    $query_Name.=" ,'$labour' ";
                if ($duty != "")
                    $query_Name.=" ,'$duty' ";
                if ($frieght != "")
                    $query_Name.=" ,'$frieght' ";
                if ($other != "")
                    $query_Name.=" ,'$other' ";
                $query_Name.=" )" . ";";
            }
            if ($query_Name != "")
            {
                //echo $query_Name;
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing project estimated unit cost information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query_Name          = "";
            }
        }
        if ($log_desc != "")
        {
            $sql    = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "pricing tab projectEstForm:" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
        /* Project basic style add query */

        $query_Name = "";
        $index      = 0;

        for (; $index < count($style); $index++)
        {
            $style[$index] = pg_escape_string($style[$index]);
            if ($prjstyle_id[$index] != "" && $prjstyle_id[$index] > 0)
            {
                if ($style[$index] == "")
                {
                    $query_Name .= "DELETE from tbl_prj_style where prj_style_id = $prjstyle_id[$index] ; ";
                    continue;
                }
                else
                {
                    $query_Name.="UPDATE tbl_prj_style SET  status=1 ";
                    if ($pid != 0)
                        $query_Name.=", pid = '$pid'";
                    $query_Name.=", style = '" . pg_escape_string($style[$index]) . "' ";
					$query_Name.=", vendor_style = '" . pg_escape_string($vendor_style[$index]) . "' ";
                    if ($garments[$index] != "")
                        $query_Name.=", garments = '" . pg_escape_string($garments[$index]) . "' ";
                    else
                        $query_Name.=", garments = null ";
                    if ($retailprice[$index] != "")
                        $query_Name.=", retailprice = '$retailprice[$index]' ";
                    else
                        $query_Name.=", retailprice = null ";
                    if ($priceunit[$index] != "")
                        $query_Name.=", priceunit = '$priceunit[$index]' ";
                    else
                        $query_Name.=", priceunit = null ";
                    $query_Name.=", updateddate = " . date('U');
                    $query_Name.="  where pid='$pid' and prj_style_id = '$prjstyle_id[$index]';";
                }
            }
            else if ($style[$index] != "")
            {
                $log_desc = "Added To Pricing:";
                $query_Name.="INSERT INTO tbl_prj_style (";
                $query_Name.=" pid";
                $query_Name.= ", style";
				$query_Name.= ", vendor_style";
                if ($garments[$index] != "")
                {
                    //   $log_desc .="Garments,";
                    $query_Name.= ", garments";
                }
                if ($retailprice[$index] != "")
                {
                    // $log_desc .="Retail Price,";
                    $query_Name.= ", retailprice";
                }
                if ($priceunit[$index] != "")
                {
                    //  $log_desc .="Price Unit,";
                    $query_Name.= ", priceunit";
                }
                $query_Name.=" ,status";
                $query_Name.=" ,createddate";
                $query_Name.=" ) VALUES(";
                $query_Name.=" '$pid' ";
                $query_Name.=", '$style[$index]' ";
				$query_Name.=", '$vendor_style[$index]' ";
                if ($garments[$index] != "")
                    $query_Name.=", '$garments[$index]' ";
                if ($retailprice[$index] != "")
                    $query_Name.=", '$retailprice[$index]' ";
                if ($priceunit[$index] != "")
                    $query_Name.=", '$priceunit[$index]' ";
                $query_Name.=" ,'1' ";
                $query_Name.=" ," . date('U');
                $query_Name.=" );";
            }
        }
        if ($query_Name != "")
        {
            //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while mutiple style information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }
        if ($log_desc != "")
        {
            $sql    = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            if (!($result = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "pricing tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
        /* end of style add */
    }

    /* New query for production milestone */
    if ($proj_5 == 1)
    {
        $query_Name = "";
        $query_Name = "";
        if (isset($milestone_id))
        {
            if ($milestone_id == 0)
            {
                $sql    = "Select id from tbl_prmilestone where pid = " . $pid . " limit 1";
                if (!($result = pg_query($connection, $sql)))
                {
                    print("Failed query1: " . pg_last_error($connection));
                    exit;
                }
                while ($row = pg_fetch_array($result))
                {
                    $data_prjmilestone = $row;
                }
                if ($data_prjmilestone['id'] > 0)
                    $milestone_id      = $data_prjmilestone['id'];
                pg_free_result($result);
            }
            if ($milestone_id > 0)
            {
                if ($hdn_track_update == 5)
                    $log_desc = "Milestone Tab Updated";
                $query_Name.="UPDATE tbl_prmilestone SET  status=1 ";
                if ($lapDip != "")
                    $query_Name.=", lapdip = '$lapDip'";
                else
                    $query_Name.=", lapdip = null ";
                if ($lapDipApprvl != "")
                    $query_Name.=", lapdipapproval = '$lapDipApprvl' ";
                else
                    $query_Name.=", lapdipapproval = null ";
                if ($estDelvry != "")
                    $query_Name.=", estdelivery = '$estDelvry' ";
                else
                    $query_Name.=", estdelivery = null ";
                if ($pdctSampl != "")
                    $query_Name.=", prdtnsample = '$pdctSampl' ";
                else
                    $query_Name.=", prdtnsample = null ";
                if ($pdctSamplApprvl != "")
                    $query_Name.=", prdtnsampleapprval = '$pdctSamplApprvl' ";
                else
                    $query_Name.=", prdtnsampleapprval = null ";
                if ($szngLine != "")
                    $query_Name.=", szngline = '$szngLine' ";
                else
                    $query_Name.=", szngline = null ";
                if ($prdctnTrgtDelvry != "")
                    $query_Name.=", prdtntrgtdelvry = '$prdctnTrgtDelvry' ";
                else
                    $query_Name.=", prdtntrgtdelvry = null ";
                if ($DBcmplt != "")
                    $query_Name.=", desbordcmplt='$DBcmplt'";
                else
                    $query_Name.=",  desbordcmplt= null ";
                if ($DBaprove != "")
                    $query_Name.=",desbordappval='$DBaprove' ";
                else
                    $query_Name.=", desbordappval= null ";
                $query_Name.=", updated_by = " . $_SESSION["employeeID"];
                $query_Name.=", updated_date = " . date('U');
                if ($dbcalender != "")
                    $query_Name.=",design_board_calender='$dbcalender' ";
                else
                    $query_Name.=",design_board_calender = null ";
                $query_Name.="  where pid='$pid' and id= $milestone_id " . ";";
            }
            else
            {
                $sql        = "select nextval(('tbl_prmilestone_id_seq'::text)::regclass) as milestoneid ";
                if (!($result_sql = pg_query($connection, $sql)))
                {
                    $return_arr['error']       = "Error while storing project id from database!";
                    echo json_encode($return_arr);
                    return;
                }
                $row                       = pg_fetch_array($result_sql);
                $milestoneid               = $row['milestoneid'];
                $return_arr['milestoneid'] = $milestoneid;
                $sql                       = "";
                $log_desc                  = "Added To Production Milestone:";


                $query_Name = "INSERT INTO tbl_prmilestone ( status";
                $query_Name.=", id";
                $query_Name.=", pid";
                if ($lapDip != "")
                {
                    $log_desc .="Lap Dip,";
                    $query_Name.=" ,lapdip ";
                }
                if ($lapDipApprvl != "")
                {
                    $log_desc .="Lap Dip Approval,";
                    $query_Name.=", lapdipapproval ";
                }
                if ($estDelvry != "")
                {
                    $log_desc .="Estimated Delivery,";
                    $query_Name.=", estdelivery ";
                }
                if ($pdctSampl != "")
                {
                    $log_desc .="Production Sample,";
                    $query_Name.=", prdtnsample ";
                }
                if ($pdctSamplApprvl != "")
                {
                    $log_desc .="Production Sample Approval,";
                    $query_Name.=", prdtnsampleapprval ";
                }
                if ($szngLine != "")
                {
                    $log_desc .="Sizing Line,";
                    $query_Name.=", szngline ";
                }
                if ($prdctnTrgtDelvry != "")
                {
                    $log_desc .="Production Delivery,";
                    $query_Name.=", prdtntrgtdelvry ";
                }
                if ($DBcmplt != "")
                {
                    // $log_desc .="Design Board Completion,";
                    $query_Name.=", desbordcmplt";
                }
                if ($DBaprove != "")
                {
                    //   $log_desc .="Design Board Approval,";
                    $query_Name.=", desbordappval";
                }
                $query_Name.=", created_date ";
                $query_Name.=", created_by ";
                if ($dbcalender != "")
                    $query_Name.=",design_board_calender";
                $query_Name.=")";
                $query_Name.=" VALUES (1";
                $query_Name.=", $milestoneid ";
                $query_Name.=", $pid ";
                if ($lapDip != "")
                    $query_Name.=", '$lapDip' ";
                if ($lapDipApprvl != "")
                    $query_Name.=", '$lapDipApprvl' ";
                if ($estDelvry != "")
                    $query_Name.=", '$estDelvry' ";
                if ($pdctSampl != "")
                    $query_Name.=", '$pdctSampl' ";
                if ($pdctSamplApprvl != "")
                    $query_Name.=", '$pdctSamplApprvl' ";
                if ($szngLine != "")
                    $query_Name.=" ,'$szngLine' ";
                if ($prdctnTrgtDelvry != "")
                    $query_Name.=" ,'$prdctnTrgtDelvry' ";
                if ($DBcmplt != "")
                    $query_Name.=" ,'$DBcmplt' ";
                if ($DBaprove != "")
                    $query_Name.=" ,'$DBaprove' ";
                $query_Name.=" ,'" . date('U') . "' ";
                $query_Name.=" ," . $_SESSION["employeeID"];
                if ($dbcalender != "")
                    $query_Name.=",'$dbcalender' ";
                $query_Name.=" )" . ";";
            }
            if ($query_Name != "")
            {
                //echo $query_Name;
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing project milestone information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query_Name          = "";
            }
        }
        if ($log_desc != "")
        {
            $log_module = "Project";
            $sql        = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "12".$sql;
            if (!($result     = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "milestone tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
    }
    if ($proj_7 == 1)
    {
        $query_Name = "";
        for ($i          = 0; $i < count($elementtype); $i++)
        {
            if ($element_id[$i] > 0)
            {
                if ($hdn_track_update == 7)
                    $log_desc = "Element Tab Updated";
                $query_Name.="UPDATE tbl_prj_elements SET  status=1 ";
                if ($elementtype[$i] != 0)
                    $query_Name.=", elementtype = '$elementtype[$i]'";
                else
                    $query_Name.=", elementtype = null ";
                if ($vendor_ID[$i] != 0)
                    $query_Name.=", vid = '$vendor_ID[$i]' ";
                else
                    $query_Name.=", vid = null ";
                if ($elementstyle[$i] != "")
                    $query_Name.=", style = '" . pg_escape_string($elementstyle[$i]) . "' ";
                else
                    $query_Name.=", style = null ";
                if ($elementcolor[$i] != "")
                    $query_Name.=", color = '" . pg_escape_string($elementcolor[$i]) . "' ";
                else
                    $query_Name.=", color = null ";
                if ($element_file0[$i] != "")
                    $query_Name.=", image = '" . pg_escape_string($element_file0[$i]) . "' ";
                else
                    $query_Name.=", image = null ";
                if ($element_file1[$i] != "")
                    $query_Name.=", elementfile = '" . pg_escape_string($element_file1[$i]) . "' ";
                else
                    $query_Name.=", elementfile = null ";
                if ($elementcost[$i] != "")
                    $query_Name.=", element_cost = '$elementcost[$i]' ";
                else
                    $query_Name.=", element_cost = null ";
				if ($elementlabor[$i] != "")
                    $query_Name.=", element_labor = '$elementlabor[$i]' ";
                else
					$query_Name.=", element_labor = null ";
					
				if ($order_date[$i] != "")
                    $query_Name.=", order_date = '$order_date[$i]' ";
                else
                   	$query_Name.=", order_date = null ";
					
				if ($element_conf_num[$i] != "")
                    $query_Name.=", elem_conf_num = '$element_conf_num[$i]' ";
					
				if ($element_track_num[$i] != "")
                    $query_Name.=", elem_track_num = '$element_track_num[$i]' ";
                
                if ($location[$i] != "")
                    $query_Name.=", location = '$location[$i]' ";
                else
                    $query_Name.=", location = null ";
                if ($yield[$i] != "")
                    $query_Name.=", yield = '$yield[$i]' ";
                else
                    $query_Name.=", yield = null ";
                
				
				if ($elemquantity[$i] != "")
                    $query_Name.=", elem_quanity = '$elemquantity[$i]' ";
                else
					$query_Name.=", elem_quanity = null ";
					
                 if (isset($element_delivered[$i]))
                    $query_Name.=", elem_delivered='yes'";
                else
                   $query_Name.=", elem_delivered='no'";
                
                $query_Name.=", createddate = " . date('U');
                $query_Name.=", updateddate = " . date('U');
                $query_Name.="  where pid='$pid' and prj_element_id = '$element_id[$i]';";
            }
            else
            {
                $log_desc   = "Added To Elements:";
                $sql        = "select nextval(('tbl_prj_elements_prj_element_id_seq'::text)::regclass) as element_id ;";
                if (!($result_sql = pg_query($connection, $sql)))
                {
                    $return_arr['error'] = "Error while storing project id from database!";
                    echo json_encode($return_arr);
                    return;
                }
                $row                 = pg_fetch_array($result_sql);
                $eid                 = $row['element_id'];
                $return_arr['eid']   = $eid;
                pg_free_result($result_sql);
                $sql                 = "";
                $query_Name.="INSERT INTO tbl_prj_elements (";
                $query_Name.=" prj_element_id ";
                $query_Name.=" ,pid ";
                if ($elementtype[$i] != 0)
                {
                    $log_desc.="Element Type,";
                    $query_Name.=" ,elementtype ";
                }
                if ($vendor_ID[$i] != 0)
                {
                    // $log_desc.="Vendor,";
                    $query_Name.=", vid ";
                }
                if ($elementstyle[$i] != "")
                {
                    // $log_desc.="Style,";
                    $query_Name.=", style ";
                }
                if ($elementcolor[$i] != "")
                {
                    //  $log_desc.="Color,";
                    $query_Name.=", color ";
                }
                if ($element_file0[$i] != "")
                {
                    //   $log_desc.="Image,";
                    $query_Name.=", image ";
                }
                if ($element_file1[$i] != "")
                {
                    //$log_desc.="Element File,";
                    $query_Name.=", elementfile ";
                }
                if ($elementcost[$i] != "")
                {
                    //  $log_desc.="Element Cost,";
                    $query_Name.=", element_cost ";
                }
				if ($elementlabor[$i] != "")
                {
                    $query_Name.=", element_labor ";
                }
				if ($order_date[$i] != "")
                {
                    $query_Name.=", order_date ";
                }
				if ($element_conf_num[$i] != "")
                {
                    $query_Name.=", elem_conf_num ";
                }
				if ($element_track_num[$i] != "")
                {
                    $query_Name.=", elem_track_num ";
                }
				if ($elemquantity[$i] != "")
				{
					 $query_Name.=", elem_quanity";
				}
				if ($location[$i] != "")
				{
					 $query_Name.=", location";
				}
				if ($yield[$i] != "")
				{
					 $query_Name.=", yield";
				}
                $query_Name.=", status ";
                $query_Name.=", createddate ";
                $query_Name.=", updateddate ";
                $query_Name.=")";
                $query_Name.=" VALUES (";
                $query_Name.="  $eid  ";
                $query_Name.="  ,$pid  ";
                $query_Name.=" ,'$elementtype[$i]' ";
                if ($vendor_ID != 0)
                    $query_Name.=", '$vendor_ID[$i]' ";
                if ($elementstyle[$i] != "")
                    $query_Name.=", '" . pg_escape_string($elementstyle[$i]) . "' ";
                if ($elementcolor[$i] != "")
                    $query_Name.=", '" . pg_escape_string($elementcolor[$i]) . "' ";
                if ($element_file0[$i] != "")
                    $query_Name.=", '" . pg_escape_string($element_file0[$i]) . "' ";
                if ($element_file1[$i] != "")
                    $query_Name.=", '" . pg_escape_string($element_file1[$i]) . "' ";
                if ($elementcost[$i] != "")
                    $query_Name.=", '" . pg_escape_string($elementcost[$i]) . "' ";
				if ($elementlabor[$i] != "")
                    $query_Name.=", '" . pg_escape_string($elementlabor[$i]) . "' ";
				if ($order_date[$i] != "")
                    $query_Name.=", '" . pg_escape_string($order_date[$i]) . "' ";
				if ($element_conf_num[$i] != "")
                    $query_Name.=", '" . pg_escape_string($element_conf_num[$i]) . "' ";
				if ($element_track_num[$i] != "")
                    $query_Name.=", '" . pg_escape_string($element_track_num[$i]) . "' ";
				if ($elemquantity[$i] != "")
                    $query_Name.=", '" . pg_escape_string($elemquantity[$i]) . "' ";
				if ($location[$i] != "")
                    $query_Name.=", '" . pg_escape_string($location[$i]) . "' ";
				if ($yield[$i] != "")
                    $query_Name.=", '" . pg_escape_string($yield[$i]) . "' ";
                $query_Name.=" ,1 ";
                $query_Name.=" ,'" . date('U') . "' ";
                $query_Name.=" ,'" . date('U') . "'";
                $query_Name.=" )" . ";";
            }
        }
        if ($query_Name != "")
        {
            //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing project element information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }

        //------------------------------------------------------

        /* $query_Name ="UPDATE \"tbl_upload_pack\" SET \"pack_name\" = '$pack_name'".
          "WHERE \"pid\" = '$pid' ";
          // echo $query_Name;
          if (!($result = pg_query($connection, $query_Name))) {
          $return_arr['error'] = "Error while storing project element information to database!";
          echo json_encode($return_arr);
          return;
          }
          $query_Name = "";
          pg_free_result($result); */
        $query_Name = "delete from tbl_upload_pack where upload_pack_e=1 and pid=" . $pid;
        for ($i          = 0; $i < count($element_packages); $i++)
        {
            $query_Name.=";INSERT INTO tbl_upload_pack (pid,upload_pack_e";




            if (isset($element_packages[$i]) && $element_packages[$i] != "")
            {
                // $log_desc.="Vendor,";
                $query_Name.=", pack_id  ";
            }

            $query_Name.= ") VALUES (" . $pid . ",1";



            if (isset($element_packages[$i]) && $element_packages[$i] != "")
            {
                $query_Name.=", '" . $element_packages[$i] . "' ";

                $query_Name.=" )";
            }
        }
        if ($query_Name != "")
        {
            // echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing project element information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }


        $package_id = $pack_id;


//-------------------------------------------------------------		
        if ($log_desc != "")
        {
            $log_module = "Project";
            $sql        = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "13".$sql;
            if (!($result     = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "element tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
    }
    if ($proj_6 == 1)
    {
        $log_desc   = "Notes Tab Updated";
        $query_Name = "";
        if (count($textAreaName) > 0)
        {
            $log_desc    = "Added new notes";
            $count_notes = count($textAreaName);
            for ($i           = 0; $i < count($textAreaName); $i++)
            {
                $query_Name.="Insert into tbl_mgt_notes (";
                if ($textAreaName[$i] != "")
                {
                    // $log_desc .="Notes,";
                    $query_Name.="notes ,";
                }
                if ($title_name[$i] != "")
                {
                    //$log_desc .="Title";
                    $query_Name.="title ,";
                }
                $query_Name.=" pid";
                $query_Name .=", \"createdDate\"";
                $query_Name .=", \"createdTime\"";
                $query_Name .=", \"createdBy\"";
                $query_Name .=" )Values(";
                if ($textAreaName[$i] != "")
                    $query_Name .=" '" . pg_escape_string($textAreaName[$i]) . "',";
                if ($title_name[$i] != "")
                    $query_Name .=" '" . pg_escape_string($title_name[$i]) . "',";
                $query_Name .=" $pid";
                $query_Name .=", " . date("U");
                $query_Name .=", " . date("U");
                $query_Name .=", " . $_SESSION["employeeID"] . "";
                $query_Name .=" );";
            }
        }
        if ($query_Name != "")
        {
            //echo $query_Name;
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing project notes information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
            $is_notes_mail       = 1;
        }
        if ($log_desc != "")
        {
            $log_module = "Project";
            $sql        = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "14".$sql;
            if (!($result     = pg_query($connection, $sql)))
            {
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
    }
    if ($proj_2 == 1)
    {
        $sql    = "Select vid from tbl_prjvendor where pid=$pid";
        if (!($result = pg_query($connection, $sql)))
        {
            $return_arr['error'] = pg_last_error($connection);
            echo json_encode($return_arr);
            return;
        }
        pg_free_result($result);
        $sql                 = "";
        $query_Name          = "";
        /*   if ($vendorID > 0) {
          $log_desc .="Vendor Added";

          $query_Name = "INSERT INTO tbl_prjvendor(";
          $query_Name.=" pid ";
          $query_Name.=", status ";
          $query_Name.=", createddate ";
          if ($vendorID != 0) {
          $query_Name.= ", vid";
          }
          $query_Name.=" ) VALUES(";
          $query_Name.=" '$pid' ";
          $query_Name.=", '1' ";
          $query_Name.=",'" . date('U') . "' ";
          if ($vendorID != 0)
          $query_Name.=", '$vendorID' ";
          $query_Name.=");";
          } */
        $index               = 0;
        if (count($vendorid) > 0)
        {
            // $log_desc = "Vendor Added";

            for (; $index < count($vendorid); $index++)
            {
                $query_Name.="INSERT INTO tbl_prjvendor (";
                $query_Name.=" pid";
                if ($vendorid[$index] != "")
                    $query_Name.= ", vid";
                $query_Name.=" ,status";
                $query_Name.=" ,createddate";
                if (isset($vendorPO[$index]) && $vendorPO[$index] != "")
                    $query_Name.=" , vendor_po";
                if (isset($vendor_file_name[$index]) && $vendor_file_name[$index] != "")
                    $query_Name.=" , upload_file";
                //  if(isset($("deposit_sent".$vendorid[$index])))
                {
                    if (isset($deposit_sent_date[$index]) && $deposit_sent_date[$index] != "")
                        $query_Name.=" , sent_date";
                    if (isset($confirm_num[$index]) && $confirm_num[$index] != "")
                        $query_Name.=" , confirm_num";
                }
                $query_Name.=" ) VALUES(";
                $query_Name.=" '$pid' ";
                if ($vendorid[$index] != "")
                    $query_Name.=", '$vendorid[$index]' ";
                $query_Name.=" ,'1' ";
                $query_Name.=" ,'" . date('U') . "' ";
                if (isset($vendorPO[$index]) && $vendorPO[$index] != "")
                    $query_Name.=", '$vendorPO[$index]' ";
                if (isset($vendor_file_name[$index]) && $vendor_file_name[$index] != "")
                    $query_Name.=", '$vendor_file_name[$index]' ";
                //  if(isset($("deposit_sent".$vendorid[$index])))
                {
                    if (isset($deposit_sent_date[$index]) && $deposit_sent_date[$index] != "")
                        $query_Name.=", '$deposit_sent_date[$index]' ";
                    if (isset($confirm_num[$index]) && $confirm_num[$index] != "")
                        $query_Name.=", '$confirm_num[$index]' ";
                }
                $query_Name.=" );";
            }
            // echo $query_Name;
        }
        if ($query_Name != "")
        {
            if (!($result = pg_query($connection, $query_Name)))
            {
                $return_arr['error'] = "Error while storing project_vendor information to database!";
                echo json_encode($return_arr);
                return;
            }
            $query_Name          = "";
            pg_free_result($result);
        }
        if ($log_desc != "")
        {
            $log_module = "Project";
            $sql        = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            if (!($result     = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "Vendor tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
    }

    /* order and shipping multiple add */
    if ($proj_8 == 1)
    {
        $query_Name     = "";
        $num_of_queries = 15;
        $query_count    = 1;
        $i              = 0;

        for ($i = 0; $i < count($carrier_shipping_select); $i++)
        {
            if ($hdn_shipping_id[$i] == 0)
            {
                $log_desc = "Added Order & Shipping: ";
                $query_Name.="INSERT INTO tbl_prjorder_shipping (status";
                /* if ($track_shipping[$i] != "") {
                  $log_desc .="Tracking Number,";
                  $query_Name.=",tracking_number";
                  } */

                if (isset($delivered_by[$i]) && $delivered_by[$i] != '')
                {
                    $query_Name.= ",deliv_by";
                }

                if (isset($delivered_date[$i]) && trim($delivered_date[$i]) != "")
                {
                    $query_Name.= ",deliv_date";
                    //$d_date = strtotime(date('Y/m/d',$delivered_date[$i]));
                    //echo $d_date;
                }
                if (isset($shipon[$i]) && $shipon[$i] != "")
                {
                    //   $log_desc .="Shipped On,";
                    $query_Name.= ",shippedon";
                }
                if ($order_shipping_notes[$i] != "")
                {
                    //    $log_desc .="Shipping Notes,";
                    $query_Name.=",shipping_notes";
                }
                if ($shiped_from[$i] != "")
                {
                    $query_Name.=",ship_from";
                }
                if ($shiped_to[$i] != "")
                {
                    $query_Name.=",ship_to";
                }
                
               if (isset($shippedonclient[$i]))
                {
                    $query_Name.=",shiponclient";
                }
                $query_Name.=",pid";
                if ($carrier_shipping_select[$i] > 0)
                {
                    //    $log_desc .="Carrier ";
                    $query_Name.=",carrier_id ";
                }
                $query_Name.=" ,created_date";
                $query_Name.=", created_by ";
                $query_Name.=" ) VALUES(1";
                /* if ($track_shipping[$i] != "")
                  $query_Name.=",'" . pg_escape_string($track_shipping[$i]) . "'"; */


                if (isset($delivered_by[$i]) && $delivered_by[$i] != '')
                {
                    $query_Name.=",'" . $delivered_by[$i] . "'";
                }

                if (isset($delivered_date[$i]) && trim($delivered_date[$i]) != "")
                {
                    $date = explode('/', $delivered_date[$i]);
                    $dt   = strtotime($date[2] . '/' . $date[0] . '/' . $date[1]);
                    $query_Name.=",'" . $dt . "'";
                }

                if (isset($shipon[$i]) && $shipon[$i] != "")
                    $query_Name.=",'" . $shipon[$i] . "'";

                if ($order_shipping_notes[$i] != "")
                    $query_Name.=",'" . pg_escape_string($order_shipping_notes[$i]) . "' ";
                if ($shiped_from[$i] != "")
                    $query_Name.=",'" . pg_escape_string($shiped_from[$i]) . "' ";

                if ($shiped_to[$i] != "")
                   $query_Name.=",'".$shiped_to[$i]."'";
                
                  if (isset($shippedonclient[$i]))
                {
                       $query_Name.=",1 ";
  
                }
                
                
                $query_Name.=",'" . $pid . "' ";
                if ($carrier_shipping_select[$i] > 0)
                    $query_Name.=",'" . $carrier_shipping_select[$i] . "' ";
                $query_Name.=" ,'" . date('U') . "' ";
                $query_Name .=", " . $_SESSION["employeeID"] . "";
                $query_Name.=" );";
                
               // echo  $query_Name.'<br/>';
            }
            else
            {
                if ($hdn_track_update == 8)
                    $log_desc = "Order & Shipping Tab Updated";
                $query_Name.="UPDATE tbl_prjorder_shipping SET status=1 ";
                /* if ($track_shipping[$i] != "")
                  $query_Name.=",tracking_number='" . $track_shipping[$i] . "'";
                  else
                  $query_Name.=", tracking_number= null "; */


                if (isset($delivered_by[$i]) && $delivered_by[$i] != '')
                {
                    $query_Name.=", deliv_by='" . $delivered_by[$i] . "'";
                }
                else
                    $query_Name.=", deliv_by=null";

                if (isset($delivered_date[$i]) && trim($delivered_date[$i]) != "")
                {
                    $date = explode('/', $delivered_date[$i]);
                    $dt   = strtotime($date[2] . '/' . $date[0] . '/' . $date[1]);
                    $query_Name.=", deliv_date='" . $dt . "'";
                }
                else
                    $query_Name.=", deliv_date=null";

                if (isset($shipon[$i]) && $shipon[$i] != "")
                    $query_Name.=", shippedon='" . $shipon[$i] . "'";
                else
                    $query_Name.=",  shippedon= null ";
                if ($order_shipping_notes[$i] != "")
                    $query_Name.=", shipping_notes='" . pg_escape_string($order_shipping_notes[$i]) . "'";
                else
                    $query_Name.=", shipping_notes=null ";
                if ($shiped_from[$i] != "")
                {
                    $query_Name.=", ship_from='" . $shiped_from[$i] . "'";
                }
                if ($shiped_to[$i] != "")
                {
                    $query_Name.=", ship_to='" . $shiped_to[$i] . "'";
                }
                
                 
                  if (isset($shippedonclient[$i]))
                 {
                    $query_Name.=",shiponclient=1";
                }
                else {
                    $query_Name.=", shiponclient=0";
                }
                if ($carrier_shipping_select[$i] > 0)
                    $query_Name.=", carrier_id='" . $carrier_shipping_select[$i] . "'";
                else
                    $query_Name.=", carrier_id=null ";
                $query_Name.=", updated_by = " . $_SESSION["employeeID"];
                $query_Name.=", updated_date = " . date('U');
                $query_Name.=" where shipping_id=" . $hdn_shipping_id[$i] . ";";
            }
            if ($query_Name != "")
            {
                $query_count++;
                //echo $query_Name;
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing shipping information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                $query_Name          = "";
                pg_free_result($result);
            }


            if (isset($hdn_shipping_id[$i]) && $hdn_shipping_id[$i] != "" && $hdn_shipping_id[$i] != 0)
            {
                $tracking_no = $hdn_shipping_id[$i]; //echo $tracking_no."  tt  ";      
            }
            else
            {
                $q      = 'select shipping_id as sid from tbl_prjorder_shipping where pid=' . $pid . ' order by shipping_id desc limit 1';
                if (!($result = pg_query($connection, $q)))
                {
                    $return_arr['error'] = "Error while storing shipping information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                $r                   = pg_fetch_array($result);
                $tracking_no         = $r['sid'];
                $q                   = "";
                pg_free_result($result);
            }
            //$query_Name          = "delete from tbl_prjorder_track_no where shipping_id=" . $tracking_no ." and tracking_no='$track_shipping[$i][$j]'";

            $query_Name = '';
            for ($j=0; $track_shipping[$i][$j] != ""; $j++)
            {
                if (isset($track_shipping[$i][$j]) && $track_shipping[$i][$j] != "")
                    if (isset($hdn_track_id[$i][$j]) && $hdn_track_id[$i][$j] > 0)
                        $query_Name .= "UPDATE tbl_prjorder_track_no set pid=".$pid.",tracking_no = '" . $track_shipping[$i][$j] . "' where track_id='" . $hdn_track_id[$i][$j] . "'; ";
                    else
                        $query_Name.='insert into tbl_prjorder_track_no(pid,tracking_no,shipping_id) values('.$pid.',\'' . $track_shipping[$i][$j] . '\',' . $tracking_no . '); ';
            }
            //echo $query_Name."<br/>";
            if ($query_Name != "")
            {
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing shipping information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                $query_Name          = "";
                pg_free_result($result);
            }
            
            
          $query_Name = '';
            for ($j=0;isset($shipping[$i][$j]); $j++)
            {
           if($shipping[$i][$j]=='') $shipping[$i][$j]=0;
                    if (isset($qty_id[$i][$j]) && $qty_id[$i][$j] > 0)
                        $query_Name .= "UPDATE tbl_qty_shipped set qty_ship = '" . $shipping[$i][$j] . "' where qty_id='" . $qty_id[$i][$j] . "'; ";
                    else
                        $query_Name.='insert into tbl_qty_shipped(pid,shipping_id,qty_ship) values('.$pid.',\'' .  $tracking_no  . '\',\'' . $shipping[$i][$j] . '\'); ';
            }
            //echo $query_Name."<br/>";
            if ($query_Name != "")
            {
                if (!($result = pg_query($connection, $query_Name)))
                {
                    $return_arr['error'] = "Error while storing shipping information to database!";
                    echo json_encode($return_arr);
                    return;
                }
                $query_Name          = "";
                pg_free_result($result);
            }    
            
            
        }
        /*  if ($query_Name != "") {
          //echo $query_Name;
          if (!($result = pg_query($connection, $query_Name))) {
          $return_arr['error'] = "Error while storing project shipping information to database!";
          echo json_encode($return_arr);
          return;
          }
          $query_Name = "";
          pg_free_result($result);
          } */



        if ($log_desc != "")
        {
            $log_module = "Project";
            $sql        = "INSERT INTO tbl_change_record (";
            $sql.=" log_date ";
            if ($log_desc != "")
                $sql.=", log_desc ";
            if ($log_module != "")
                $sql.=", module ";
            if ($log_id != "")
                $sql.=", module_id ";
            if ($project_or_po != "")
                $sql.=", project_or_po ";
            $sql.=", status ";
            $sql.=", created_date ";
            $sql.=", employee_id ";
            $sql.=")";
            $sql.=" VALUES (";
            $sql.=" " . date('U');
            if ($log_desc != "")
                $sql.=", '" . $log_desc . "' ";
            if ($log_module != "")
                $sql.=", '" . $log_module . "' ";
            if ($log_id != "")
                $sql.=", $log_id ";
            if ($project_or_po != "")
                $sql.=", '" . $project_or_po . "' ";
            $sql.=" ,1 ";
            $sql.=" ," . date('U');
            $sql.=" ," . $_SESSION["employeeID"];
            $sql.=" )" . ";";
            //echo "16".$sql;
            if (!($result     = pg_query($connection, $sql)))
            {
                $return_arr['error'] = "order and shipping tab :" . pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $sql                 = "";
            $log_desc            = "";
        }
        /* end of order and shipping multiple add */
    }



    $return_arr['id'] = $pid;
}
$where_clause     = '';
if (isset($project_manager) && $project_manager != "")
{
    $where_clause .= " \"employeeID\"='$project_manager'";
}
if (isset($project_manager1) && $project_manager1 != "")
{
    if ($where_clause != '')
        $where_clause .= ' OR ';
    $where_clause .= "\"employeeID\"='$project_manager1'";
}

if (isset($project_manager2) && $project_manager2 != "")
{
    if ($where_clause != '')
        $where_clause .= ' OR ';
    $where_clause .= "\"employeeID\"='$project_manager2'";
}
if (strlen($where_clause) != '')
{
    $query  = "SELECT email FROM \"employeeDB\" where $where_clause";
    //echo $query;
    if (!($result = pg_query($connection, $query)))
    {
        print("Failed custom-query1 on mailing: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result))
    {
        if (trim($row['email']) != "")
            $manager_email.="," . $row['email'];
    }
}
//echo $manager_email;
$manager_email_to = array();

if ($isMailServer == 'true')
    require('../../mail.php');
else
    require($PHPLIBDIR . 'mailfunctions.php');
if ($is_po == 1)
{
    if (isset($purchaseOrder) && $purchaseOrder != "")
    {
        $subject    = 'A notification on Purchase Order add';
        $email_body = "A new ";
        $email_body .="<strong>Purchase Order&nbsp;(" . $purchaseOrder . ")</strong> has been added";

        $manager_email_to = explodeX(Array(",", ";"), $manager_email);

        for ($mail_index = 0; $mail_index < count($manager_email_to); $mail_index++)
        {
            //echo $manager_email_to[$mail_index].'<br/>';

            if ($isMailServer == 'true')
            {
                $mail = new PHPMailer();

                $mail->AddReplyTo("Do Not Reply", $name = "DO NOT REPLY");

                $mail->From = "admin@uniformsourcing.com";

                $mail->FromName = "";

                $mail->Subject = $subject;

                $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					

                $mail->MsgHTML($email_body);

                $mail->AddAddress($manager_email_to[$mail_index], $name = "");

                if (!$mail->Send())
                {
                    $return_arr['error'] = "Unable to send email. Please try again later";
                    echo json_encode($return_arr);
                    return;
                }
            }
            else
            {
                $headers = create_smtp_headers($subject, "admin@uniformsourcing.com", $manager_email_to[$mail_index], 'Uniform Sourcing', "", "text/html");
                $data    = $headers . "<html><BODY>" . $email_body . "</body></html>";
                if ((send_smtp($mailServerAddress, "admin@uniformsourcing.com", $manager_email_to[$mail_index], $data)) == false)
                {
                    $return_arr['error'] = "Unable to send email. Please try again later";
                    echo json_encode($return_arr);
                    return;
                }
            }
        }
        unset($manager_email_to);
    }
}
if ($is_notes_mail == 1)
{
    if (($notification_radio == 0) && ($notification_select != 0))
    {
        $query  = "SELECT client FROM \"clientDB\" where \"ID\"='$clientID'";
        if (!($result = pg_query($connection, $query)))
        {
            print("Failed custom-query1 on mailing: " . pg_last_error($connection));
            exit;
        }
        $client = pg_fetch_result($result, 0, 'client');
        pg_free_result($result);
        $query  = "SELECT firstname,lastname FROM \"employeeDB\" where \"employeeID\"='$project_manager'";
        if (!($result = pg_query($connection, $query)))
        {
            print("Failed custom-query2 on mailing: " . pg_last_error($connection));
            exit;
        }
        $manager = pg_fetch_array($result);
        pg_free_result($result);
        $query   = "SELECT firstname,lastname FROM \"employeeDB\" where \"employeeID\"='" . $_SESSION["employeeID"] . "'";
        if (!($result  = pg_query($connection, $query)))
        {
            print("Failed custom-query3 on mailing: " . pg_last_error($connection));
            exit;
        }
        $updated_by = pg_fetch_array($result);
        pg_free_result($result);

        $headers    = "From: PDF Imagewear" . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $subject    = "An update for {$client} {$projectName} has been made.";
        $email_body = "<p>You have recieved a new message from the enquiries form on your website.</p>
				  <p><strong>Client Name: </strong> {$client} </p>
				  <p><strong>Project/PO: </strong>";
        if ($purchaseOrder != "")
        {
            $email_body .=$purchaseOrder;
        }
        else
        {
            $email_body .=$projectName;
        }
        $email_body .= "</p>
				  <p><strong>Due Date: </strong> {$poDueDate} </p>
				  <p><strong>Project Manager:</strong> {$manager['firstname']} {$manager['lastname']}</p>
				  <p><strong>Updated By: </strong> {$updated_by['firstname']} {$updated_by['lastname']} </p>
				  <p><strong>Notes:</strong> {$textAreaName[$count_notes - 1]} </p>
					  <p><strong>Tracking Number:</strong> {$track_shipping[$count_track - 1]} </p>
				  <p><strong>Milestone Information </strong> </p>
				  <p><strong>Lap Dip: </strong> {$lapDip} </p>
				  <p><strong>Lap Dip Approval: </strong> {$lapDipApprvl} </p>
				  <p><strong>Estimated Fabric Delivery Date: </strong> {$estDelvry} </p>
				  <p><strong>Production Sample: </strong> {$pdctSampl} </p>
				  <p><strong>Production Sample Approval: </strong> {$pdctSamplApprvl} </p>
					  <p><strong>Sizing Line: </strong> {$szngLine} </p>
				  <p><strong>Production target Delivery: </strong> {$prdctnTrgtDelvry} </p>
				  
				  <p><strong>Please login to the system to view complete project or purchase order
					details and full updates, images and files.</strong></p>";
        //echo $email_body;

        if ($notification_select == 2)
        {
            $sent_to = "custom@uniforms.net";
            //$sent_to='charlesraj07@gmail.com';
        }
        else
        {
            $sent_to = "stock@uniforms.net";
        }

        if ($isMailServer == 'true')
        {
            $mail = new PHPMailer();

            $mail->AddReplyTo("admin@uniformsourcing.com", $name = "DO NOT REPLY");

            $mail->From = "admin@uniformsourcing.com";

            $mail->FromName = "";

            $mail->Subject = $subject;

            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					

            $mail->MsgHTML($email_body);

            $mail->to[0][0] = $sent_to;
            $mail->to[0][1] = '';

            if (!$mail->Send())
            {
                $return_arr['error'] = "Unable to send email. Please try again later";
                echo json_encode($return_arr);
                return;
            }
        }
        else
        {
            $headers = create_smtp_headers($subject, "admin@uniformsourcing.com", $sent_to, 'Uniform Sourcing', "", "text/html");
            $data    = $headers . "<html><BODY>" . $email_body . "</body></html>";
            if ((send_smtp($mailServerAddress, "admin@uniformsourcing.com", $sent_to, $data)) == false)
            {
                $return_arr['error'] = "Unable to send email. Please try again later";
                echo json_encode($return_arr);
                return;
            }
        }
    }
}
echo json_encode($return_arr);
return;
?>