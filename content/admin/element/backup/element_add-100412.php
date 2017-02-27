<?php
require('Application.php');
require('../../header.php');
$back_page = "element_list.php";
if (isset($_GET['element_id'])) {
    $element_id = $_GET['element_id'];
    $sql = "select * from tbl_element_package where  element_id = '$element_id'";
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
$sql = 'Select "vendorID","vendorName" from "vendor" ';
if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_vendor[] = $row;
}
pg_free_result($result);
if ($isEdit) {
    $query = ("SELECT * from tbl_element_package " .
            "WHERE element_id = $element_id ");
    if (!($result = pg_query($connection, $query))) {
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $datalist = $row;
    }
    pg_free_result($result);
}
?>
<table width="90%" >
    <tr>
        <td align="left">
            <input type="button" value="Back" onclick="location.href ='<?php echo $back_page; ?>'" />
        </td>  
        <td>&nbsp;</td>
    </tr>
</table>
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Add/Edit Element Package</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
<form action="element_submit.php" method="post" id="package_form">
    <table width="90%" >
        <tr>
            <td valign="top">

                <table align="center">
                    <tr>
                        <td align="right">Package Name:</td>
                    <input type="hidden" name="element_id" value="<?php if (isset($_GET['element_id']) && $_GET['element_id'] != "") echo $_GET['element_id']; ?>" />
                    <td align="left"><input type="text" name="packagename" value="<?php echo stripslashes($datalist['package']); ?>"/></td>
        </tr>
        <tr>
            <td align="right">Element Type:</td>
<?php
$select = 0;
//echo $datalist['element_type'];
?>
            <td><select name="element" style="font-faimly:verdana;font-size:10;width:150px; height:25px;" >
                    <option value="">--- Select ------</option>

                    <option value="Artwork" <?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Artwork") {
    echo ' selected="selected" ';
}
?>>Artwork</option>
                    <option value="Beads/Crystals"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Beads/Crystals")
                echo ' selected="selected" ';
?>>Beads/Crystals</option>
                    <option value="Boning"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Boning")
                echo ' selected="selected" ';
?>>Boning</option>
                    <option value="Buttons"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Buttons")
                echo ' selected="selected" ';
?>>Buttons</option>
                    <option value="Cups/Pads"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Cups/Pads")
                                echo ' selected="selected" ';
?>>Cups/Pads</option>
                    <option value="Fabric"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Fabric")
                                echo ' selected="selected" ';
?>>Fabric</option>
                    <option value="Hardware"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Hardware")
                                echo ' selected="selected" ';
?>>Hardware</option>
                    <option value="Liner"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Liner")
                                echo ' selected="selected" ';
?>>Liner</option>
                    <option value="Labels"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Labels")
                                echo ' selected="selected" ';
?>>Labels</option>
                    <option value="Other"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Other")
                                echo ' selected="selected" ';
?>>Other</option>
                    <option value="Thread"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Thread")
                                echo ' selected="selected" ';
?>>Thread</option>
                    <option value="Trim/Piping"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Trim/Piping")
                                echo ' selected="selected" ';
?>>Trim/Piping</option>
                    <option value="Zippers"<?php if (isset($datalist['element_type']) && $datalist['element_type'] != "" && $datalist['element_type'] == "Zippers")
                                echo ' selected="selected" ';
?>>Zippers</option>
                </select></td>
        </tr>
        <tr>
            <td align="right">Vendor:</td>
            <td><select name="vendor">
                    <?php
                    for ($i = 0; $i < count($data_vendor); $i++) {
                        if (isset($data_vendor[$i]['vendorID']) && $data_vendor[$i]['vendorID'] != "")
                            echo '<option value="' . $data_vendor[$i]['vendorID'] . '"';
                        if (isset($datalist['vendorID']) && $datalist['vendor_id'] != "" && $datalist['vendorID'] == $data_vendor[$i]['vendorID'])
                            echo '"selected"=selected';
                        echo '>' . $data_vendor[$i]['vendorName'] . '</option>';
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td align="right">Style#:</td>
            <td align="left"><input type="text" name="elementstyle" value="<?php echo stripslashes($datalist['style']); ?>" /></td>
        </tr>
        <tr>
            <td align="right">Color:</td>
            <td align="left"><input type="text" name="elementcolor" value="<?php echo stripslashes($datalist['color']); ?>" /></td>
        </tr>
        <tr>
            <td align="right">Cost:</td>
            <td align="left"><input type="text" name="elementcost" value="<?php echo stripslashes($datalist['cost']); ?>" /></td>
        </tr>
        <tr>
            <td align="right">Image:</td>
            <td align="left">
                <input type="file" name="img_file" id="img_file" onchange="javascript:ajaxFileUpload(2, 'I', 960,720);" value="<?php echo stripslashes($datalist['image']); ?>" />
            </td>
        </tr>
        <tr>
            <td align="right">File:</td>
            <td align="left"><input type="file" name="file" id="file" onchange="javascript:ajaxFileUpload(3, 'F', 960,720);" value="<?php echo stripslashes($datalist['file']); ?>"/></td>
        </tr>
        <tr>
            <td colspan="2" align="center" valign="top">
                <input type="button" id="submitButton" name="submitButton" value="Save" onclick="javascript: submitForm();"/>
                <input type="reset" id="reset" name="reset" value="Cancel" />
            </td>
        </tr>
    </table>
</td>
<td valign="top" align="right">

<!--<table  border="0" cellspacing="0" cellpadding="0" id="image_view" >
<tr id="tr_id0" >

                                                                                                                                                                    
</tr>
    

<tr>
<td>
<div id="img_file3"></div>
</td>
</tr> 
</table>-->
    <table  border="0" cellspacing="0" cellpadding="0" id="image_view" >





        <tr  id="img_tr" <?php if (!isset($datalist['image']) || $datalist['image'] == "") echo " style='display:none;'"; ?>><td>
                Image:<br/>
                <img src="<?php echo $upload_dir . $datalist['image']; ?>"

                     width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');" id="img_thumb" /> 
                <a style="cursor:hand;cursor:pointer"
                   onClick="javascript:  DeleteFile('I');">

                    <img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete"

                         />
                </a>
                <!--   <a style="cursor:hand;cursor:pointer"
                   onClick="javascript:  DeleteSingleRow(this);
                       DeleteUploads('<?php //echo $imageArr[$i]['id']; ?>','<?php addslashes($datalist['image']); ?>','<?php //echo $pid;  ?>','I','upload');">
                                            
                                        <img src="<?php //echo $mydirectory;  ?>/images/close.png" alt="delete" />
                                        </a>-->
                <input type="hidden" id="file_name'.$count.'" name="upload_file[]" value="<?php echo $imageArr[$i]['file']; ?>"/>
                <input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="I"/>


            </td>
        </tr>

        <tr id="file_tr"
<?php if (!isset($datalist['file']) || $datalist['file'] == "") echo " style='display:none;'"; ?>
            >
            <td>File:<br/>



                <strong id="file_thumb"><?php echo (substr($datalist['file'], (strpos($datalist['file'], "-") + 1))); ?></strong>
                <a href="download.php?file=<?php echo $datalist['file']; ?>">
                    <img src="<?php echo $mydirectory; ?>/images/Download.png" alt="download"/></a>


                <a    href="javascript:void(0);" 
                      onClick="javascript: DeleteFile('F');">
                    <img src="<?php echo $mydirectory ?>/images/close.png" alt="delete"/></a>
                <input type="hidden" id="elm_upload_img" name="elm_upload_img" value="<?php echo $datalist['image']; ?>"/>
                <input type="hidden" id="elm_upload_file" name="elm_upload_file" value="<?php echo $datalist['file']; ?>"/>
                <input type="hidden" id="upload_type<?php echo $count; ?>" name="upload_type[]" value="F"/>



            </td>
        </tr>
<?php ?>










        <tr>
            <td>
                <div id="img_file3"></div>
            </td>
        </tr>  
    </table>



</td>
</tr>

</table>
</form>


<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/PopupBox.js"></script>
<script src="project.js" type="text/javascript"></script>
<script type="text/javascript">
    
    
    function DeleteUploads(id,filename,prj_id,type,formtype)
    {
        document.getElementById('processing').style.display= '';
        if(filename == 0 || filename == 1 || filename > 100){
            filename = document.getElementById('file_name'+filename).value;
        }
        var dataString = "filename="+filename+"&tableid="+id+"&pid="+prj_id+"&type="+type+"&formtype="+formtype;
        $.ajax({
            type: "POST",
            url: "delete_uploads.php",
            data: dataString,
            dataType: "json",
            timeout:60000,
            success:function(data)
            {
                document.getElementById('processing').style.display= 'none';
                if(data!=null)
                {
                    if(data.name || data.error)
                    {
                        $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
                        show_msg();
                    } 
                    else
                    {	
                        $("#message").html("<div class='successMessage'><strong>File Removed...</strong></div>");						
                        show_msg();
                    }
                }
                else
                {
                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                    show_msg();
                }				
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                document.getElementById('processing').style.display= 'none';
                $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                show_msg();
            }
        });
        return false;
    }
    
    function submitForm()
    {
        var  data=$("#package_form").serialize();
        data+="&pack_id=<?php
if (isset($_GET['element_id']) && $_GET['element_id'] != "")
    echo $_GET['element_id'];
else
    echo "0";
?>";
              $.ajax({
                  type: "POST",
                  url: "element_submit.php",
                  data: data ,
                  datatype: "json",
                  timeout:60000,
                  success:function(data)
                  {
                      //alert(data.pack_id);
                      // document.getElementById('processing').style.display= 'none';
                      if(data!=null)
                      {
                          if(data.name || data.error)
                          {
                              $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
                              show_msg();
                          } 
                          else
                          {	
                              $("#message").html("<div class='successMessage'><strong>Element Removed...</strong></div>");				
                              show_msg();
                              location.href="element_add.php?element_id="+data.pack_id;
                          }
                      }
                      else
                      {
                          $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                          show_msg();
                      }
                  },
                  error: function() {
                      document.getElementById('processing').style.display= 'none';
                      $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                      show_msg();
                  }
              });
     
     
          }
          function show_msg()
          {
              window.message_display = setInterval(function() {
                  $("#message").fadeOut(1600,remove_msg);  
              }, 6000);
          }
          function remove_msg()
          {
              $("#message").html('');
              $("#message").fadeIn();
              clearInterval(window.message_display);
              window.message_display = null;
          }

          function DeleteSingleRow(obj)
          {
              var delRow = obj.parentNode.parentNode;
              var tbl = delRow.parentNode.parentNode;
              var rIndex = delRow.sectionRowIndex;		
              var rowArray = new Array(delRow);
              DeleteRow(rowArray);
          }
          function DeleteRow(rowObjArray)
          {	
              for (var i=0; i<rowObjArray.length; i++) {
                  var rIndex = rowObjArray[i].sectionRowIndex;
                  rowObjArray[i].parentNode.deleteRow(rIndex);
              }	
          }
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

          function add_thumbnail(image_label,name,image_id,file_name,tableNum ) {
              //alert('label'+image_label);

              if(tableNum == 0) {
                  tableName = 'image_view';
                  upload_name = 'upload_file';
                  upload_id = 'upload_id';
                  upload_type = 'upload_type';
              }
              else if(tableNum == 1){
                  tableName = 'sample_uploads';
                  upload_name = 'sample_file_name';
                  upload_id = 'sample_file_id';
                  upload_type = 'sample_file_type';
              }
              if(image_label=='Image:'){
            
                  var table = document.getElementById(tableName);
                  var rowCount = table.rows.length;
                  var row = table.insertRow(rowCount);
		
                  var cell1 = row.insertCell(0);
                  cell1.width="200px";
                  var label = document.createElement('strong');
                  label.innerHTML = image_label+'<br/>';
                  cell1.innerHTML = '<input type="text" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="I"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
                  cell1.appendChild(label);
		
                  var img = document.createElement("img");
                  img.src = "<?php echo $upload_dir; ?>"+name;
                  img.style.width="101px";
                  img.style.height="89px";
                  img.onclick = function(){ PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge'); };
                  cell1.appendChild(img);
		
                  cell1.innerHTML += '<a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\');"><img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete"/></a>';
              }
              else{
                  var table = document.getElementById(tableName);
                  var rowCount = table.rows.length;
                  var row = table.insertRow(rowCount);
		
                  var cell1 = row.insertCell(0);
                  cell1.width="200px";
                  var label = document.createElement('strong');
                  label.innerHTML = image_label+'<br/>';
                  cell1.appendChild(label);
		
                  cell1.innerHTML += file_name+'<a href="download.php?file='+name+'"><img src="<?php echo $mydirectory; ?>/images/Download.png" alt="download" /></a><a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\'); "><img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete"/></a><input type="hidden" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="F"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
              }
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