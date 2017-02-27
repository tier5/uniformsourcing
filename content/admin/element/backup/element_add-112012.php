<?php
require('Application.php');
require('../../header.php');
$back_page = "element_list.php";
if (isset($_GET['element_id'])&& $_GET['element_id']!="") {
    $element_id = $_GET['element_id'];
    $sql = 'select * from "tbl_element_package"'.
   ' where  pack_id ='.$element_id;
   // echo  $sql;
    if (!($result1 = pg_query($connection, $sql))) {
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($rowp = pg_fetch_array($result1)) {
        $datalist[] = $rowp;
    }
    pg_free_result($result1);
}
//print_r($datalist);
$sql = 'Select "vendorID","vendorName" from "vendor" ';
if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_vendor[] = $row;
}
pg_free_result($result);

if (isset($_GET['element_id'])&&$_GET['element_id']!="") {
$sql = 'Select "pack_name" from "tbl_element_pack_main" where pack_id='.$element_id;;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
$row = pg_fetch_array($result) ;
    $package_name = $row['pack_name'];

pg_free_result($result);
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

pg_free_result($result1);


$element_list=array("Artwork","Beads/Crystals","Boning","Buttons","Cups/Pads","Fabric","Hardware","Liner","Labels","Other",
        "Thread","Trim/Piping","Zippers");


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




<div class="content" align="center">
    <form action="element_submit.php" method="post" id="package_form">
        <table >
           <tr>
           <td align="center">Package Name:</td>
           
      <td align="left"><input type="text" name="packagename" id="packagename" value="<?php
      if(isset($package_name))
      echo stripslashes($package_name); ?>"/></td>
        </tr>
        </table><table width="80%">
<?php $count = 101; 

for($j = 0; $j<count($datalist); $j++)
{
?>

<tr><td align="center">
     <input type="image" class="deleteTd" onclick="javascript:DeleteUploads('<?php 
echo $datalist[$j]['prj_element_id'];?>','<?php echo $count++;?>','<?php 
echo $pid;?>','I','element'); DeleteUploads('<?php echo $datalist[$j]['prj_element_id'];?>','<?php 
 echo $count--;?>','<?php echo $pid;?>','I','element'); deleteElement('<?php echo $datalist[$j]['prj_element_id'];?>','<?php 
echo $pid;?>')" src="../../images/delete.png">
     <table width="80%" >
	<tr>
	<td valign="top">
	<table cellpadding="1" cellspacing="1" border="0">
	<tr>
	<td align="right">Element Type:</td>
	<td align="left"><select name="elementtype[]" <?php echo $style_price;?>>
<?php 	for($i=0; $i < count($element_list); $i++){
		if($datalist[$j]['element_type']==$element_list[$i]){?>
<option value="<?php echo $element_list[$i];?>" selected="selected"> <?php echo $element_list[$i];?></option>
	<?php 	}else{ ?>
<option value="<?php echo $element_list[$i];?>"><?php echo $element_list[$i];?></option>
	<?php }}?>
	
	</select></td>
	</tr>
	<tr>
	<td align="right">Vendor: </td>
	<td align="left"><select name="vendor_ID[]" <?php echo $style_price;?>>
                <?php 
            
	for($i=0; $i < count($data_vendor); $i++)
        {
		if($datalist[$j]['vendor_id']==$data_vendor[$i]['vendorID']){?>
<option value="<?php echo $data_vendor[$i]['vendorID'];?>" selected="selected">
    <?php echo $data_vendor[$i]['vendorName']; ?></option>
	<?php 	}else {?>
		<option value="<?php echo $data_vendor[$i]['vendorID'];?>"><?php echo $data_vendor[$i]['vendorName'];?></option>
	<?php }} ?>
</select>
        </td>
	</tr>
	
<tr>
<td align="right">Client:</td>
<td align="left">
<select id="client" name="client[]" >
<?php for($i=0; $i < count($client); $i++){
 if($datalist[$j]['client']==$client[$i]['ID'])
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
</tr>
        
        
        
        <tr>
	<td align="right">Style:</td>
	<td align="left">
       <input type="text" name="elementstyle[]" id="elementstyle" value="<?php echo $datalist[$j]['style'];?>" <?php echo $style_price;?> /></td>
	</tr>
	<tr>
	<td align="right">Color:</td>
	<td align="left"><input type="text" name="elementcolor[]" id="elementcolor" 
                     value="<?php echo $datalist[$j]['color'];?>"  <?php echo $style_price;?> /></td>
	</tr>
	<tr>
	<td align="right">Cost:</td>
	<td align="left">
    <input type="text" name="elementcost[]" id="elementcost" value="<?php echo $datalist[$j]['cost'];?>" <?php 
    echo $style_price?>/></td>
	</tr>
	<tr>
	<td align="right">Image:</td>
	<td align="left" >
<input type="file" name="file<?php echo $count;?>" id="file<?php echo $count;?>" 
     onchange="javascript:ajaxFileUpload('<?php echo $count;?>','I', 960,720);" />
<input type="hidden" id="file_name<?php echo $count;?>" name="element_file0[]" value="<?php echo $datalist[$j]['image'];?>"/>
<input type="hidden" id="upload_type<?php echo $count;?>" name="element_type0[]" value="I"/>
<input type="hidden" id="upload_id<?php echo $count++;?>" name="element_id0[]" 
            value="<?php echo $datalist[$j]['prj_element_id'];?>"/>
	</td>
	</tr>
	<tr>
	<td align="right">File:</td>
	<td align="left">
<input type="file" name="file<?php echo $count;?>" id="file<?php echo $count;?>" 
   onchange="javascript:ajaxFileUpload(<?php echo $count;?>,'F', 960,720);" />
	<input type="hidden" id="file_name<?php echo $count;?>" 
       name="element_file1[]" value="<?php echo $datalist[$j]['file'];?>"/>
	<input type="hidden" id="upload_type'.$count--.'" name="element_type1[]" value="F"/>
	</td>
	</tr>
	</tr>
	</table>
	<input type="hidden" id="element_id<?php echo $count;?>" name="element_id[]" value="<?php 
        if (isset($datalist[$j]['prj_element_id']) && $datalist[$j]['prj_element_id']!="")
         echo $datalist[$j]['prj_element_id'];?>"/>
	</td>
	<td valign="top" align="right">
	<table  border="0" cellspacing="0" cellpadding="0">
	<tr id="tr_id<?php echo --$count;?>" 
<?php 	if(!(isset($datalist[$j]['image']) && $datalist[$j]['image']!='')){ ?>
		style="display:none;"
	<?php }?> >
	<td id="img_td"><strong>Image:</strong><br/>
<img id="img_file<?php echo $count;?>" width="101px" height="89px" 
       src="<?php
       if(isset($datalist[$j]['image'])){
	echo $upload_dir.$datalist[$j]['image'];
	}
        ?>"onClick="javascript:PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');" >
        <a style="cursor:hand;cursor:pointer;"
							
onClick=" javascript: $('#img_td').remove(); document.getElementById('tr_id<?php 
echo $count;?>').style.display='none'; document.getElementById('file_name<?php echo $count++;?>').value=''; " >
    <img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
</a>
	</td>
	</tr>
<tr id="tr_id<?php echo $count;?>" 
	 <?php if(!(isset($datalist[$j]['file']) && $datalist[$j]['file'] !='')){ ?>
		style="display:none"
	<?php }?>>
	<td id="file_td"><strong>File:<br/>
<?php echo (substr($datalist[$j]['file'], (strpos($datalist[$j]['file'], "-")+1)));?>

  </strong>      <a href="download.php?file=<?php echo $datalist[$j]['file'];?>">
  <img src="<?php echo $mydirectory;?>/images/Download.png" alt="download" /></a>
   
        
        <a href="javascript:void(0);" onClick="javascript:$('#file_td').remove(); document.getElementById('tr_id<?php 
   echo $count;?>').style.display='none'; document.getElementById('file_name<?php echo $count++;?>').value='';">
   <img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/>
   </a>
        </td>
	</tr>
	
	
	
	
	
	</table>
	</td>
	</tr>
	</table></td></tr>
<?php }?></table>

<input type="hidden" id="selectedDiv" value="'.$selectedtab.'"  />
<input type="hidden" id="selectedId" value="'.$elementId.'"  />
<input type="hidden" id="elementCount" value="'.$selectedtab.'" />
 <table id="content_table" width="100%" border="0" cellspacing="0" cellpadding="0">

</table>
    
<input type="button" value="Add New Element" onclick="AddElement()" />
 <input type="button" id="submitButton" name="submitButton" value="Save" onclick="javascript: submitForm();"/>



        </form></div>
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
        if($.trim($("#packagename").val())=="")
        {
            alert("Enter a Package Name...");
            return;
        }
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
          
   function AddElement(){
    var in_htm="";
	var table = document.getElementById('content_table');
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
        row.setAttribute('id','element'+rowCount);
	var count = (rowCount*2)+100;
        img_count = count+1;
	var cell1 = row.insertCell(0);        
	cell1.align="center";
      
//alert(count);

in_htm += '<input type="image" class="deleteTd" src="../../images/delete.png" onclick=" DeleteUploads(\'\',\''+ ++count +'\',\'\',\'I\',\'editTime\'); DeleteUploads(\'\',\''+ ++count +'\',\'\',\'F\',\'editTime\');"> <table width="80%" ></td></tr>';
in_htm+='<tr ><td valign="top"><table cellpadding="1" cellspacing="1" border="0">';
in_htm +='<tr><td align="right">';


 in_htm +='</td></tr>';
in_htm+='<tr><td align="right">Element Type:</td><td align="left"><select id="element_type" name="elementtype[]"><?php	
for($i=0; $i < count($element_list); $i++){?><option value="<?php echo $element_list[$i];?>"><?php
echo  $element_list[$i];?></option><?php
} ?></select></td></tr><tr><td align="right">Vendor:</td><td align="left"><select id="element_vendor" name="vendor_ID[]">	<?php
for($i=0; $i < count($data_vendor); $i++){?><option value="<?php echo $data_vendor[$i]['vendorID']; ?>"><?php
echo $data_vendor[$i]['vendorName'];?></option><?php
} ?></select></td></tr>';
      
  in_htm+= "<?php echo'<tr><td>Client:</td><td align=\'left\'><select id=\'client\' name=\'client[]\'>';
for($i=0; $i < count($client); $i++){
    echo '<option value=\''.$client[$i]['ID'].'\'>'.$client[$i]['client'].'</option>';
  //  echo $client[$i]['client'].'<br/>';
} 
echo '</select></td></tr>';
    ?>";
      
        in_htm+=  '<tr><td align="right">Style:</td><td align="left"><input type="text" name="elementstyle[]" '
      +'id="elementstyle" value="" /></td></tr><tr><td align="right">Color:</td><td align="left">'
      +'<input type="text" name="elementcolor[]" id="elementcolor" value="" /></td></tr><tr>'
      +'<td align="right">Cost:</td><td align="left"><input type="text" name="elementcost[]" id="elementcost" value="" />'
      +'</td></tr><tr><td align="right">Image:</td><td align="left"><input type="file" name="file'+ --count +'" id="file'+ count 
      +'" onchange="javascript:ajaxFileUpload('+ count +', \'I\', 960,720);" /><input type="hidden" id="file_name'+ count +
      '" name="element_file0[]" value=""/><input type="hidden" id="upload_type'+ count +
      '" name="element_type0[]" value="I"/><input type="hidden" id="upload_id'+ ++count +
      '" name="element_id0[]" value=""/></td></tr><tr><td align="right">File:</td><td align="left"><input type="file" name="file'+ count +
      '" id="file'+ count +'" onchange="javascript:ajaxFileUpload('+ count +', \'F\', 960,720);" /><input type="hidden" id="file_name'+ count +
      '" name="element_file1[]" value=""/><input type="hidden" id="upload_type'+ --count +
      '" name="element_type1[]" value="F"/></td></tr></tr></table><input type="hidden" id="element_id" name="element_id[]" value="0"/>'+
      '</td><td valign="top" align="right"  >'+
      '<table  border="0" cellspacing="0" cellpadding="0"><tr style="display:none;" id="tr_id'+ count +
      '"><td><strong>Image:</strong><br/><img id="img_file'+ count +
      '" width="101px" height="89px" src="" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" alt="img"/>'+
      '<a id="del_img" style="cursor:hand;cursor:pointer;" onClick=" javascript: DeleteUploads(\'\',\''+ count +
      '\',\'\',\'I\',\'editTime\'); document.getElementById(\'tr_id'+ count +
      '\').style.display=\'none\'; document.getElementById(\'file_name'+ ++count +'\').value=\'\'; " >'+
      '<img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete" /></a></td>	</tr><tr id="tr_id'+ Number(count) +
      '" " ></tr></table></td></tr>';
//alert(in_htm);
cell1.innerHTML= in_htm;	
}
$(".deleteTd").live('click', function(event) {
        $(this).parent().parent().remove();
});

          
         function ajaxFileUpload(index, type, width, height){
            
                  file_id_type="file"+index;
           
             
   
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
			
                      if(data.error != '')
                      {
                            
                          $("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
                          show_msg();
                      }
                      else
                      {
                             
			
                          
                               
				
			
                              if(type=="I")
                              {
                                           
                                   $("#tr_id"+data.index).show(); 
                                 $("#file_name"+data.index).val(data.name);
                                  $("#img_file"+data.index).attr("src","<?php echo $upload_dir; ?>"+data.name);
                              // $("#img_tr").show();
                              }
                              else
                              {
                              
                                         
                                  $("#tr_id"+data.index).show();       
                                  $("#file_name"+data.index).val(data.name);
                                  $("#tr_id"+data.index).html('<td><strong>'+data.file_name+'</strong></td>');
                                  $("#file_thumb").show();
                                  
                              }
				
                              //    add_thumbnail(label,data.name,0,data.file_name,0);
				 
				
                           }
                      
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