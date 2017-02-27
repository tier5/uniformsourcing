<?php
require('Application.php');
require('../../header.php');
if (isset($_GET['oid'])) {
    $oid = $_GET['oid'];
    $sql = "select * from tbl_fill_orders where  oid = $oid";
    if (!($result = pg_query($connection, $sql))) {
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($row = pg_fetch_array($result)) {
        $datalist = $row;
    }
    pg_free_result($result);	
}

if ($isEdit) {
    $query = ("SELECT * from tbl_fill_orders WHERE oid = $oid ");            
    if (!($result = pg_query($connection, $query))) {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result)) {
        $datalist2 = $row2;
    }
    pg_free_result($result);
	 
}
if(isset($_GET['oid']))
{
	$isEdit = 1;
	$id = $_GET['oid'];
$sql = "Select vendor,client from  tbl_fill_orders WHERE oid = $oid";

	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_sample = $row;
	}
	
	pg_free_result($result);
}

$queryVendor="SELECT \"vendorID\", \"vendorName\" FROM vendor WHERE active = 'yes' ORDER BY \"vendorName\" ASC ";		  
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
} 
$query1 = ("SELECT \"ID\", \"clientID\", \"client\", \"active\" FROM \"clientDB\" WHERE active = 'yes' ORDER BY client ASC ");
        
if (!($result1 = pg_query($connection, $query1))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row1 = pg_fetch_array($result1)) {
    $data_client[] = $row1;
}
pg_free_result($result);
?>

<table width="90%">
    <tr>
        <td align="left">
            <input type="button" value="Back" onclick="location.href ='fill_orders_list.php'" />
        </td>  
        <td>&nbsp;</td>
    </tr>
</table>
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Incoming Fill Orders</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>

<div id="message" class="message_fixed"></div>
<div id="loading" class="message_fixed" style="display:none;"><img src="<?php echo $mydirectory; ?>/images/animation_processing.gif" alt="Loading..." /></div>

<form id="fill_orders">
<table align="right" border="0" cellspacing="0" cellpadding="0" id="image_view" >
        <tr valign="top"  id="img_tr" <?php if (!isset($datalist['upload']) || $datalist['upload'] == "") echo " style='display:none;'"; ?>><td>
         <strong>Uploads:</strong><br/>
                <img src="<?php echo $upload_dir . $datalist['upload']; ?>" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');" id="img_thumb" /> 
                 
                <a style="cursor:hand;cursor:pointer" onClick="javascript:  DeleteFile('I');">
                    <img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete" />
                </a> 
                <br /><?php echo $datalist['upload']; ?> 
                 <input type="hidden" id="elm_upload_img" name="elm_upload_img" value="<?php echo $datalist['upload']; ?>"/>         
            </td>
        </tr>        
        <tr>
            <td>
                <div id="img_file3"></div>
            </td>
        </tr>  
    </table>
  <table align="center">       
        <tr>
            <td align="right">Vendor:</td>
            <td><select name="vendor" style="width:240px">
				   <?php for($i=0; $i <count($data_Vendr); $i++)
				   {
					if($data_sample['vendor']==$data_Vendr[$i]['vendorID'])
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
					else 
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
                   }?> 
			      </select></td>
        </tr>
        <tr>
            <td align="right">Client:</td>
            <td><select name="clientname">                   
                    
                          <?php for($i=0; $i <count($data_client); $i++)
				   {
					if($data_sample['client']==$data_client[$i]['ID'])
						echo '<option value="'.$data_client[$i]['ID'].'" selected="selected">'.$data_client[$i]['client'].'</option>';
					else 
						echo '<option value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';
                   }?>  
                </select></td>
        </tr>  
        <tr>
            <td align="right">Project Name:</td>
            <td><input type="text" name="prj_name" id="prj_name" value="<?php echo stripslashes($datalist['prj_name']); ?>" size="20">
            <input type="hidden" name="oid" value= "<?php 
			if(isset($_GET['oid']) && $_GET['oid']!='') echo $_GET['oid']; ?>"  /></td>
        </tr>
        <tr>
            <td align="right">Purchase Order:</td>
            <td><input type="text" name="pu_order" value="<?php echo stripslashes($datalist['pu_order']); ?>" size="20"></td>
        </tr>
<tr>
            <td align="right">Date Placed:</td>
            <td><input type="text" name="placed" id="placed" value="<?php echo stripslashes($datalist['placed']); ?>" size="20"></td>
        </tr>
        <tr>
            <td align="right">Date Expected:</td>
            <td><input type="text" name="expected" id="expected" value="<?php echo stripslashes($datalist['expected']); ?>" size="20"></td>
        </tr>
        <tr>
            <td align="right">Tracking Number:</td>
            <td><input type="text" name="trk_num" value="<?php echo stripslashes($datalist['trk_num']); ?>" size="20"></td>
        </tr>
         <tr>
            <td align="right">Upload:</td>
            <td><input type="file" name="img_file" id="img_file" onchange="javascript:ajaxFileUpload(2, 'I', 960,720);" />
            </td>
        </tr>        
        <tr>
        	<td colspan=5 align="center"><br>
          	<input type="button" value="Save" onclick="javascript:frmSubmit();" />
          <br></td>         
        </table>
        
        
        
 </form>
 
 
    <script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min.js"></script>    
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
	<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
    
    <script type="text/javascript">
 var loading = $("#loading");
 var msg = $("#message");
function frmSubmit()
{
	
dataString=$("#fill_orders").serialize();
showLoading();
   $.ajax({
            type: "POST",
            url: "fill_orders_submit.php",
            data: dataString,
            dataType: "json",
            success:function(data)
            {
				hideLoading();
               if(data.error != ''){show_msg('error', data.error);}
				else if(data.msg != '') {show_msg('success',data.msg);
				//location.href='add_chain.php?ch_id='+data.ch_id;
				}
            },
          
        });
}
 function showLoading(){loading .css({visibility:"visible"}) .css({opacity:"1"}) .css({display:"block"});msg .css({visibility:"hidden"})}
//hide loading bar
function hideLoading(){loading.fadeTo(1000, 0, function(){loading .css({display:"none"});msg .css({visibility:"visible"});});};
window.message_display = null;
function show_msg(cl,ms)
{
    msg.addClass(cl).html(ms).fadeIn();
    window.message_display = setInterval(function() {msg.fadeOut(1600,remove_msg);}, 6000);
}
function remove_msg()
{
    msg.removeClass('success').removeClass('error').html('');
    clearInterval(window.message_display);window.message_display = null;
}

$('#placed').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });

$('#expected').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });
</script>



<script type="text/javascript" >
function ajaxFileUpload(index, type, width, height){
              if(type=="I")
                  file_id_type="img_file";
              else
                  file_id_type="file";
   
              //if(document.getElementById(file_id_type).value != ""){
              var fileId = file_id_type;
              // document.getElementById('processing').style.display= '';
              $.ajaxFileUpload(
              {
                  url:'fileUpload.php',
                  secureuri:false,
                  fileElementId:fileId,
                  dataType: 'json',
                  async:false,
                  data:{fileId:fileId, type:type, index:index, width:width, height:height},
                  timeout:60000,
                  success: function (data, status)
                  {
                      // alert(data.file_name);
			
                      if(data.error != '')
                      {
                             
                          $("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
                          show_msg();
                      }
                      else
                      {                             
			
                          {                     
				
			
                              if(type=="I")
                              {
                                           
                                         
                                  $("#elm_upload_img").val(data.name);
                                  $("#img_thumb").attr("src","<?php echo $upload_dir; ?>"+data.name);
                                  $("#img_tr").show();
                              }
                              else
                              {
                                             
                                         
                                  $("#elm_upload_file").val(data.name);
                                  $("#file_thumb").html(data.file_name);
                                  $("#file_thumb").show();
                                  $("#file_tr").show();
                              }
				
                              //    add_thumbnail(label,data.name,0,data.file_name,0);
				 
				
                          } }
                      
                  },
                  error: function(data) {
               
                      document.getElementById('processing').style.display= 'none';
                      $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                      show_msg();
                  }
              });
  
              return false;
          }
		  
		   function DeleteFile(type)
          {
              switch(type)
              {
                  case "I":
                      $("#img_thumb").removeAttr("src");
                      $("#elm_upload_img").val("");
                      $("#img_tr").hide();
                      break;
                  case "F":
                      $("#file_thumb").html("");
                      $("#elm_upload_file").val("");
                      $("#file_tr").hide();
                      break;
              }
          }

</script>
<?php
require('../../trailer.php');
?>