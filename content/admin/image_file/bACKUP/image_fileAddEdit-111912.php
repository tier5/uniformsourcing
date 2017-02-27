<?php
require('Application.php');
require('../../header.php');
$back_page = "image_file_list.php";
$error = "";
$msg = "";
$html = "";
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['html'] = "";
$is_session =0;
$emp_type ="";
$emp_id= "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =  $_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' style="visibility:hidden"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}

$isEdit = 0;
$patternId = 0;
$gradientId = 0;
if(isset($_GET['pack_id']) && $_GET['pack_id']!=0){
	
	
	$isEdit = 1;
	$sql = 'Select item.*,pack."pack_name" from "img_file_items" as item left join "img_file_pack" as pack on pack."pack_id"=item."pack_id" '.
          ' where item."pack_id"='.$_GET['pack_id'];
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql;
	while($row = pg_fetch_array($result)){
		$data_prjUploads[] =$row;
	}
//print_r($data_prjUploads);
	$imageArr = array();
	$fileArr = array();
	$pattern = "";
	$gradient = "";
	for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads); $i++)
	{
		 if($data_prjUploads[$i]['type'] == 'I')
		{
			$imageArr[$img]['id'] = $data_prjUploads[$i]['item_id'];
			$imageArr[$img++]['file'] = stripslashes($data_prjUploads[$i]['filename']);
		}
		else if($data_prjUploads[$i]['type'] == 'F')
		{
			$fileArr[$file]['id'] = $data_prjUploads[$i]['item_id'];
			$fileArr[$file++]['file'] = stripslashes($data_prjUploads[$i]['filename']);
		}
	}
	pg_free_result($result);
	
}

$count = 0;
?>
<table width="90%" >
 <tr>
    	<td align="left">
    		<input type="button" value="Back" onclick="location.href ='<?php echo $back_page;?>'" />
  		</td>  
  		<td>&nbsp;</td>
  	</tr>
</table>
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Add/Edit Image File Package</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>

<div id="processing" style="display:none; text-align:center; position:absolute; width:900px; z-index:100;">
    <div style="height:30px;overflow:hidden;">
        <div align="center" id="message"></div>
        </div>

<img src="../../images/animation_processing.gif" width="200" height="200" alt="processing" />

</div>
<form method="POST" id="package_form" action="image_fileAddEdit_submit.php">
<table width="90%" align="center">
   
<tr align="center">
<td valign="top">

<table cellpadding="1" cellspacing="1" border="0">
<tr>
<td align="right">Style Number:</td>
<td align="left">
<input type="text" name="pack_name" id="pack_name" <?php if(isset($data_prjUploads[0]['pack_name'])&&$data_prjUploads[0]['pack_name']!="") echo 'value="'.$data_prjUploads[0]['pack_name'].'"';?> /> 


</td>
</tr>

<tr>
<td align="right">Image:</td>
<td align="left">
<input type="file" name="img_file" id="img_file" onchange="javascript:ajaxFileUpload(2, 'I', 960,720);" />
</td>
</tr>
<tr>
<td align="right">File:</td>
<td align="left">
<input type="file" name="file" id="file" onchange="javascript:ajaxFileUpload(3, 'F', 960,720);"/>
</td>
</tr>
<tr><td colspan="2" align="center"><br/><br/><input type="button" value="Save" align="right" onclick="javascript: submitForm();"/></td></tr>
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
        
        <?php
     
        if (count($imageArr)) {
                 echo " <tr><td>Images:</td></tr>";
            for ($i = 0; $i < count($imageArr); $i++, $count++) {?>
        <tr><td>
     <img src="<?php echo $upload_dir.$imageArr[$i]['file']; ?>"
 width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
    
       <a style="cursor:hand;cursor:pointer"
       onClick="javascript:  DeleteSingleRow(this);">
                                
                            <img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete" />
                            </a>
                                <input type="hidden" id="file_name'.$count.'" name="upload_file[]" value="<?php echo $imageArr[$i]['file']; ?>"/>
                            <input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="I"/>
                            <input type="hidden" id="upload_id'.$count.'" name="upload_id[]" value="'.$imageArr[$i]['id'].'"/>
                            
                                
                        </td>
                    </tr>
        <?php
        }
            
    }
    
    
       if (count($fileArr)) {
      echo " <tr><td>&nbsp;</td></tr><tr><td>Files:</td></tr>";
        for ($i = 0; $i < count($fileArr); $i++, $count++) {
            ?>
                        
                    <tr id="tr_id<?php echo $count; ?>">
                        <td>
                            
            <?php if ($fileArr[$i]['file'] != "") {?>
                                    
        <strong><?php echo (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-") + 1))); ?> </strong>
       <a href="download.php?file=<?php echo $fileArr[$i]['file']; ?>">
       <img src="<?php echo $mydirectory; ?>/images/Download.png" alt="download"/></a>
                              
      
  <a    href="javascript:void(0);" 
   onClick="javascript: DeleteSingleRow(this);">
     <img src="<?php echo $mydirectory?>/images/close.png" alt="delete"/></a>
        <input type="hidden" id="file_name<?php echo $count;?>" name="upload_file[]" value="<?php echo $fileArr[$i]['file']; ?>"/>
          <input type="hidden" id="upload_type<?php echo $count; ?>" name="upload_type[]" value="F"/>
           <input type="hidden" id="upload_id<?php echo $count; ?>" name="upload_id[]" value="<?php echo $fileArr[$i]['id']; ?>"/>
                                        
                <?php } ?>
                         
                            </td>
                        </tr>
                            <?php
                            
               } 
         } 
         ?>
    


        
        
        
 
    
    
    
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


<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script src="project.js" type="text/javascript"></script>
<script type="text/javascript">
    
    function submitForm()
    {
        
    if($.trim($("#pack_name").val())=="" || $.trim($("#pack_name").val())==null)
        {
            alert("Please enter a package name...");
            return;
        }
  var  data=$("#package_form").serialize();
  data+="&pack_id=<?php if(isset($_GET['pack_id'])&& $_GET['pack_id']!="") 
      echo $_GET['pack_id'];
  else echo "0";
      ?>";
     $.ajax({
		   type: "POST",
		   url: "image_fileAddEdit_submit.php",
		   data: data ,
		   datatype: "json",
		   timeout:60000,
		   success:function(data)
			{
			//alert(data.pack_id);
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
						$("#message").html("<div class='successMessage'><strong>Element Removed...</strong></div>");				
						show_msg();
                       location.href="image_fileAddEdit.php?pack_id="+data.pack_id;
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
   
	if(document.getElementById(file_id_type).value != ""){
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
                    /*  alert(data.msg);
                       alert(data.error);
                      alert(data.index);*/
                      
                  
			//document.getElementById('processing').style.display= 'none';
			//if(typeof(data.error) != 'undefined')
			{
			  if(data.error != '')
			  {
                             
			   $("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
			   show_msg();
			  }
                          else
			  {
                             
			 //  $("#message").html("<div class='successMessage'><strong>"+data.msg +"</strong></div>");
			  // show_msg();
			   if(data.index != 'undefined' && data.index != "")
			   {
                               
				// document.getElementById("file"+data.index).value = '';
				 if(data.index >1 && data.index <100)
				 {
					 switch(data.index)
					 {
						case '2':
						label = "Image:";
                                                
						break;
						case '3':
						label = "File:";
						break;
						default:
						label = "Image "+(data.index-2);
						break;
					 }
                    //  alert(label);
                       add_thumbnail(label,data.name,0,data.file_name,0);
				 }
				else if(data.index > 100 && data.index < 200 && data.type == 'F' )
				 {
					document.getElementById('tr_id'+data.index).innerHTML='<td><strong>File:</strong><br/>'+data.file_name+'<a href="download.php?file='+data.name+'"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download" /></a><a <?php if($emp_type ==1){ echo 'style="visibility:hidden"';  } ?> href="javascript:void(0);" onClick="javascript:DeleteUploads(\'\',\''+escape(data.name)+'\',\'\',\'\',\'editTime\'); document.getElementById(\'tr_id'+data.index+'\').style.display=\'none\'; document.getElementById(\'file_name'+data.index+'\').value=\'\'; "><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a></td>';
					document.getElementById('file_name'+data.index).value=data.name;
					document.getElementById('tr_id'+data.index).style.display="";
				 	document.getElementById('file'+data.index).value="";
				 }
				 else if(data.index >= 200)
				 {
					if(data.type == 'I')
						add_thumbnail("Image:",data.name,0,data.file_name,1);
					if(data.type == 'F')
						add_thumbnail("File:",data.name,0,data.file_name,1);
				 }
				 else
				 {
					document.getElementById('tr_id'+data.index).style.display="";
					document.getElementById('img_file'+data.index).src="<?php echo ($upload_dir);?>"+data.name;
					document.getElementById('file_name'+data.index).value = data.name;
				 	document.getElementById('file'+data.index).value="";
				 }
			   }
			  }
			}
		  },
		error: function(data) {
                 // alert(data);
				document.getElementById('processing').style.display= 'none';
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				show_msg();
		}
	});
  }
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
		cell1.innerHTML = '<input type="hidden" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="I"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
		cell1.appendChild(label);
		
		var img = document.createElement("img");
		img.src = "<?php echo $upload_dir;?>"+name;
		img.style.width="101px";
		img.style.height="89px";
		img.onclick = function(){ PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge'); };
		cell1.appendChild(img);
		
		cell1.innerHTML += '<a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a>';
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
		
		cell1.innerHTML += file_name+'<a href="download.php?file='+name+'"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download" /></a><a href="javascript:void(0);" onClick="DeleteSingleRow(this); javascript:return DeleteUploads(\'\',\''+escape(name)+'\',\'\',\'\',\'editTime\'); "><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a><input type="hidden" id="'+upload_name+rowCount+'" name="'+upload_name+'[]" value="'+name+'"/><input type="hidden" id="'+upload_type+rowCount+'" name="'+upload_type+'[]" value="F"/><input type="hidden" id="'+upload_id+rowCount+'" name="'+upload_id+'[]" value="0"/>';
	}
        }
        
    </script>
<?php 
require('../../trailer.php');
?>