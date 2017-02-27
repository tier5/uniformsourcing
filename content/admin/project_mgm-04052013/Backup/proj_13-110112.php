<?php

require('Application.php');
//require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$html = "";
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['html'] = "";
$isEdit = 0;
$pricingId = 0;
$unit_retail_price = 0;
$target_retail_price = 0;
$is_session = 0;
$emp_type = "";
$emp_id = "";
if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 1)) {
    $emp_type = $_SESSION['employeeType'];
    $emp_id = $_SESSION['employee_type_id'];
    $is_session = 1;
    $style_price = ' style="visibility:hidden"';
} else if (isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] > 0 && $_SESSION['employeeType'] == 2)) {
    $emp_type = $_SESSION['employeeType'];
    $emp_id = $_SESSION['employee_type_id'];
    $is_session = 1;
    $style_price = ' disabled="disabled"';
}


if (isset($_POST['pid']) && $_POST['pid'] != 0) {
    //$pid=586;
    $isEdit = 1;
    
    $sql='select  style_cust.style_id as cust_id,style."styleNumber" as style,style1."styleNumber",color."name", '
    .'(select "scaleSize" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.size) as size, '
   .'(select "opt1Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.length) as length, '
  .'(select "opt2Size" from "tbl_invScaleSize" where "sizeScaleId" = style_cust.height) as height, style_cust.quantity'
.' from "tbl_prj_style_custom" as style_cust left join "tbl_invStyle" as style on style."styleId"=style_cust."style" '
   .' left join "tbl_invStyle" as style1  on style1."styleId"=style_cust."style" left join "tbl_invColor" as color on color."colorId" =style_cust."color" where  pid ='.$pid;

    if (!($result = pg_query($connection, $sql))) {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_prj_style[] = $row;
    }

    pg_free_result($result);
}

$query1 = 'SELECT "styleId","styleNumber" from "tbl_invStyle" where "isActive"=1 order by "styleNumber"';
if (!($result_cnt = pg_query($connection, $query1))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
$data_styleid = array();
//$row_cnt = pg_fetch_array($result_cnt);
while ($row_cnt = pg_fetch_array($result_cnt)) {
    $data_styleid[] = $row_cnt;
}
pg_free_result($result_cnt);

    $html = '<table width="90%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
  <table cellpadding="1" cellspacing="1" border="0" width="100%">
<!--New rows added for pt invoice,taxes etc-->
<tr id="style_edit">
<td colspan="3">';
    $html .= '<div style="width:100%;text-align:center;"><img style="cursor:hand;cursor:pointer;" src="' . $mydirectory . '"/images/add_but.png id="style_add" alt="Add Style" /></div>';
    $html .= '<table cellpadding="1" cellspacing="1" border="0" width="100%" id="tbl_style_custom">
<tr style="font-weight:bold;">
<td>Style</td>
<td>Color</td>
<td>';

    $html .= 'Size</td>
<td>';

    $html .= 'Length</td><td>Height</td><td>Quantity</td>
</tr>
<tr>';
    $html .= '<td><select id="style_custom" name"style_custom" onchange="javascript:style_opt($(this))"><option value="">--Select--<option>';
    foreach ($data_styleid as $style) {
        $html .= '<option value="' . $style['styleId'] . '">' . $style['styleNumber'] . '</option>';
    }
    $html .= '</select></td>';
    $html .= '<td><select name="color_custom" id= "color_custom" value=""><option>--Select--<option></select></td>';
    $html .= '<td><select name="size_custom" id= "size_custom" value=""><option>--Select--<option></select></td>';
    $html .= '<td><select name="len_custom" id= "len_custom" value=""><option>--Select--<option></select></td>';
    $html .= '<td><select name="height_custom" id="height_custom" value=""><option>--Select--<option></select></td>';
    $html .= '<td width="16px" ><input type="text" id="qty_custom" name="qty_custom" value="" size="5px" /></td>';
    $html .= '<td width="10px"><img src="' . $mydirectory . '/images/bullet_add.png" alt="add" height="25" onclick="javascript:addStyleRow();"/></td>';
    $html .= '</tr></table>';

    $html .= '<table cellpadding="1" cellspacing="1"  width="100%" > <tbody>';
    $html .= '<tr><th class="gridHeader">Style</th>';
    $html .= '<th class="gridHeader">Color</th>';
    $html .= '<th class="gridHeader">Size</th>';
    $html .= '<th class="gridHeader">Length</th>';
    $html .= '<th  class="gridHeader">Height</th>';
    $html .= '<th  class="gridHeader">Quantity</th>';
    $html .= '<th class="gridHeader">Delete</th></tr>';
    $count = count($data_prj_style);
    for ($i = 0; $i < $count; $i++) {
        $html .= '<tr style="font-weight:normal;"><td class="grid001">' . $data_prj_style[$i]['styleNumber'] . '</td>';
        $html .= '<td class="grid001">' . $data_prj_style[$i]['name'] . '</td>';
        if (isset($data_prj_style[$i]['size']))
            $html .= '<td class="grid001">' .  $data_prj_style[$i]['size'] . '</td>';
        else
            $html .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_prj_style[$i]['length']))
            $html .= '<td class="grid001">' . $data_prj_style[$i]['length'] . '</td>';
        else
            $html .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_prj_style[$i]['height']))
            $html .= '<td  class="grid001">' . $data_prj_style[$i]['height'] . '</td>';
        else
            $html .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_prj_style[$i]['quantity']))
            $html .= '<td class="grid001">' . $data_prj_style[$i]['quantity'] . '</td>';
        else
            $html .= '<td class="grid001">&nbsp;</td>';
        if (isset($data_prj_style[$i]['cust_id']))
            $html .= '<td class="grid001"><img src="' . $mydirectory . '/images/close.png"  alt="Delete" onclick=" deleteCustomStyle(' . $data_prj_style[$i]['cust_id'] . ')"'
                    . ' style="cursor:hand;cursor:pointer"/></td></tr>';
        else
            $html .= '<td class="grid001">&nbsp;</td>';
    }
    $html .= '</table> </tbody>';
    $html .='</table>';
    $html .= '</td>
</tr>

      </table></td>
    </tr>
  </table>
</div>';
//echo $html;

    $return_arr['html'] = $html;
    echo json_encode($return_arr);
    return;
    ?>