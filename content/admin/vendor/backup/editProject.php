<?php
ob_start();require('Application.php');
require('../../header.php');
$_SESSION['pid']=$_GET['ID'];
$query1='SELECT "ID", "clientID", "client", "active" '.
		 ' FROM "clientDB" c inner join tbl_projects p on p.cid=c."ID"'.
		 ' WHERE p.pid='.$_GET['ID'];
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$row1 = pg_fetch_array($result1);
	$data1=$row1;
$query="SELECT * ".
		 "FROM \"tbl_projects\" ".
		 "WHERE \"pid\" =".$_GET['ID'];
if(!($result=pg_query($connection,$query))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data=$row;
}
$query2='SELECT "vendorID","vendorName" from vendor v inner join tbl_projects p on p.vid=v."vendorID" where p.pid='.$_GET['ID'];
if(!($result2=pg_query($connection,$query2))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$row2= pg_fetch_array($result2);
$vendorName=$row2['vendorName'];
if(! $data['pid']) { $data['pid']=$_GET['ID']; }
$length=strlen($data_cnt['pid'])+9;
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/ajaxfileupload.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/projectadd.js"></script>';
?>
<table border="0" width="100%" align="center">
	<tr><td align="left"><input type="button" value="Back" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='projectReportVendor.php'" style="cursor: pointer;"></td>
	
	</tr>
</table>
<font face="arial">
<blockquote>
<font face="arial" size="+2"><b><center>Project Edit</center></b></font>
<p>

<script src="<?php echo $mydirectory;?>/js/PopupBox.js" type="text/javascript"></script>
<script type="text/javascript">
popBoxWaitImage.src = "<?php echo $mydirectory;?>/images/spinner40.gif";
popBoxRevertImage = "<?php echo $mydirectory;?>/images/magminus.gif";
popBoxPopImage = "<?php echo $mydirectory;?>/images/magplus.gif";
</script> 

<table border="0" width="40%" align="center">
	<tr><td align="center"><input type="button" value="Project Esimated Unit Cost" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='projectEstimatedUnitCost.php'" style="cursor: pointer;"></td>
	<td align="center"><input type="button" value="Production Milestones" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='ProductionMilestone.php'"></td>
	</tr>
</table>
<?php 
if(count($_SESSION['edit_err'])){
	//print '<pre>';print_r($_SESSION['edit_err']);print '</pre>';
	@ extract($_SESSION['edit_err'][1]);
	if(count($_SESSION['edit_err'][0])) {
		echo '<ul style="color:Red;margin-left:100px;">';
		echo "<li>Please Correct Following Fields</li>";
		foreach($_SESSION['edit_err'][0] as $error) {
			echo "<li>".$error."</li>";
		}
		echo '</ul>';
	}
	unset($_SESSION['edit_err']);
	//unset($_SESSION['edit_err']);
	//print '<pre>';print_r($_SESSION['add_err']);print '</pre>';
} ?>
 <form action="editProject1.php" method="post" enctype="multipart/form-data">
<table align="center">
                <tr>
                  <td height="25" align='right'>Choose Client:</td>
                  <td align="left"><?php echo $data1['client'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Project Name:</td>
                  <td align="left"><input name="projectName" id="projectName" type="text" value="<?php echo $data['pname'];?>" readonly="readonly"/></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Purchase Order:</td>
                  <td align="left"><?php echo $data['purchaseOrder'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Purchase Order Due Date:</td>
                  <td align="left"><?php echo $data['poDueDate'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Quantity of People:</td>
                  <td align="left"><?php echo $data['quanPeople'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Garment Description:</td>
                  <td align="left"><?php echo $data['pdescription'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Sizes Needed:</td>
                  <td align="left"><?php echo $data['sizeNeeded'];?></td>
                </tr>
                <tr>
                  <td height="25" align="right">Vendor:</td>
                  <td align="left"><?php echo $vendorName;?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Samples Provided:</td>
                  <td align="left"><?php if( $data['samplesProvided']==1)echo yes;else No;?></td>
                </tr>
                <?php if($data['samplesProvided']!=""){?>
                <tr id="rowId3" style="display:none; color:#FF00FF">
                  <td height="25">&nbsp;</td>
                  <td align="left"><?php echo $data['production'];?></td>
                </tr>
               <?php }if($data['production']!=""){?>
                <tr id="prodId3" style="display:none; color:#FF00FF">
                  <td height="25">&nbsp;</td>
                  <td align="right"><table width="53%" align="left" class="prjctVenorTable">
                    <tr>
                      <td align="left">Date:</td>
                      <td align="left"><?php echo $data['prdctionDate'];?></td>
                    </tr>
                    <tr>
                      <td align="left">Sample Number: </td>
                      <td align="left"><?php echo $data['sampleNmbr'];?></td>
                    </tr>
                  </table></td>
                </tr>
                 <?php } ?>
                <tr>
                  <td height="25" align='right' class="lightBlue">Style:</td>
                 <?php
				 $styleNo=str_replace("\"","&quot;",$data['styleNumber']);
				  echo "<td align='left'><input name='styleNumber' type='text' value=\"".$styleNo."\"></td>";?>
                </tr>
                <tr>
                  <td height="25" align='right'>Color:</td>
                  <td align="left"><?php echo $data['color'];?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Type of Material:</td>
                  <?php
				  $material=str_replace("\"","&quot;",$data['typeMaterial']);
				  ?>
                  <td align="left"><input name="typeMaterial" type="text" value="<?php echo $material;?>"></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Embroidery:</td>
                  <td align="left"><?php if($data['embroidery']==1) echo Yes; else if($data['embroidery']==0) echo No; ?></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Silk Screening:</td>
                  <td align="left"><?php if($data['silkScreening']==1) echo Yes; else if($data['silkScreening']==0) echo No; ?></td>
                </tr>
                <tr id="loading3" style="display:none;">
                  <td height="25" colspan="2"><img src="<?php echo $mydirectory;?>/images/loading.gif"/></td>
                </tr>
 <tr>
            <td align='right' class="lightBlue">Pattern:</td>
            <td align="left">
				<?php 
				  if($data['pattern'])
				 			echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" 		                    id="thumb_image6" src="'.$mydirectory.'/projectimages/'.$data['pattern'].'"  />';
				else 
				echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image6" />';
				echo '<input type="file" name="pattern" id="pattern" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" 
				name="button666" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'pattern\','.$data['pid'].',6);" />';
				
				 ?> 
                 </td>
               </tr>
              
               <tr>
                  <td height="25" align='right' class="lightBlue">Grading:</td>
                  <td align="left"><?php 
				  if($data['grading']) 
				echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" 
				id="thumb_image7" src="'.$mydirectory.'/projectimages/'.$data['grading'].'"  />';
				else 
				echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image7" />';
				echo '<input type="file" name="grading" id="grading" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" 
				name="button777" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'grading\','.$data['pid'].',7);" />';
				  ?>
                  </td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Image Upload 1:</td>
                  <td align="left"><?php
				  if($data['image1']) 
					echo '<img id="thumb_image1" style="width: 129px; height: 96px;" class="PopBoxImageSmall" 
					onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" src="'.$mydirectory.'/projectimages/'.$data['image1'].'"/>';
				else 
				echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image1" />';
				echo '<input type="file" name="image1" id="image1" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" 
				name="button111" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image1\', '.$data['pid'].',1);" />';
				  ?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Image Upload 2:</td>
                  <td align="left"><?php if($data['image2']) 
					echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" id="thumb_image2" 
					onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" src="'.$mydirectory.'/projectimages/'.$data['image2'].'"/>';
				else
					echo '<img  style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image2" />';
					echo '<input type="file" name="image2" id="image2" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';"
					name="button222" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image2\','.$data['pid'].',2);" />';
?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Image Upload 3:</td>
                  <td align="left"><?php if($data['image3']) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" src="'.$mydirectory.'/projectimages/'.$data['image3'].'" />';
				else
				echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" />';
				echo '<input type="file" name="image3" id="image3" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" 
				name="button333" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image3\','.$data['pid'].',3);" />';
?>
</td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Image Upload 4:</td>
                  <td align="left"><?php if($data['image4'])
				echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" 
				onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" src="'.$mydirectory.'/projectimages/'.$data['image4'].'" />';
				else 
				echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" />';
				echo '<input type="file" name="image4" id="image4" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';"
				name="button444" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image4\','.$data['pid'].',4);" />';
?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Image Upload 5:</td>
                  <td align="left"><?php 
				 if($data['image5'])
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" src="'.$mydirectory.'/projectimages/'.$data['image5'].'" />';
else
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" />';
echo '<input type="file" name="image5" id="image5" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button555" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image5\','.$data['pid'].',5);" />';
				  ?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">File Upload 1:</td>
                  <td align="left"><?php
				  if($data['fileupld1'])
					{
					?>
						<div id="thumb_image16"><a href="<?php echo $mydirectory;?>/projectimages/<?php echo $data['fileupld1']; ?>"><?php echo substr($data['fileupld1'],$length); ?></a></div>
					<?php
					}
					 else 
					{?>
						<div id="thumb_image16" style="display:none;"></div>
					<?php }	
					echo '<input type="file" name="image16" id="image16" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload16" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image16\','.$data['pid'].',16);" />';
				  ?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">File Upload 2:</td>
                  <td align="left"><?php 
				 if($data['fileupld2'])
					{
					?>
						<div id="thumb_image17"><a href="<?php echo $mydirectory;?>/projectimages/<?php echo $data['fileupld2']; ?>"><?php echo substr($data['fileupld2'],$length); ?></a></div>
					<?php
					}
					 else 
					{?>
						<div id="thumb_image17" style="display:none;"></div>
					<?php }	
					
					echo '<input type="file" name="image17" id="image17" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload17" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image17\','.$data['pid'].',17);" />';
				  ?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">File Upload 3:</td>
                  <td align="left"><?php if($data['fileupld3'])
					{
				?>
					<div id="thumb_image18"><a href="<?php echo $mydirectory;?>/projectimages/<?php echo $data['fileupld3']; ?>"><?php echo substr($data['fileupld3'],$length); ?></a></div>
				<?php
				}
				 else 
				{?>
					<div id="thumb_image18" style="display:none;"></div>
				<?php }	
				echo '<input type="file" name="image18" id="image18" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload18" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image18\','.$data['pid'].',18);" />';?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">File Upload 4:</td>
                  <td align="left"><?php if($data['fileupld4'])
				{
			?>
				<div id="thumb_image19"><a href="<?php echo $mydirectory;?>/projectimages/<?php echo $data['fileupld4']; ?>"><?php echo substr($data['fileupld4'],$length); ?></a></div>
			<?php
			}
			 else 
			{?>
				<div id="thumb_image19" style="display:none;"></div>
			<?php }	
			
			echo '<input type="file" name="image19" id="image19" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload19" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image19\','.$data['pid'].',19);" />';?></td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">File Upload 5:</td>
                  <td align="left"><?php if($data['fileupld5'])
					{
				?>
					<div id="thumb_image20"><a href="<?php echo $mydirectory;?>/projectimages/<?php echo $data['fileupld5']; ?>"><?php echo substr($data['fileupld5'],$length); ?></a></div>
				<?php
				}
				 else 
				{?>
					<div id="thumb_image20" style="display:none;"></div>
				<?php }	
				
				echo '<input type="file" name="image20" id="image20" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload20" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image20\','.$data['pid'].',20);" />';?></td>
                </tr>
 <tr>
                  <td height="25" align='right' class="lightBlue">Project Comments:</td>
                  <td align="left"><textarea wrap="physical" name="projectComments" rows="7" cols="35"><?php echo $data['projectComments'];?></textarea></td>
                </tr>
                <tr>
                  <td height="25" align='right'>Production Sample:</td>
                  <td align="left">11/12/2010</td>
                </tr>
                <tr>
                  <td height="25" align='right' class="lightBlue">Lap Dip:</td>
                  <td align="left"><input type="text" name="lpDip" readonly="readonly" id="lpDip" value="<?php echo $data['lapDip'];?>" /></td>
                </tr>
                 <tr>
                  <td width="150" height="25" align='right'>ETA Production:</td>
                  <td width="544" align="left"><?php echo $data['etaProduction'];?></td>
                </tr>
                <tr>
                  <td colspan="2"><div align="center">
                      <input type="submit" name="submit" value=" Save Project "  />
                      <input type="reset" name="cancel" value=" Cancel Project " />
                    </div></td>
                </tr>
				<tr><td>			
			  <?php
			if($data['image1'])
				echo '<input type="hidden" name="hdnimage1" id="hdnimage1" value="'.$data['image1'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage1" id="hdnimage1" />';
			if($data['image2'])
				echo '<input type="hidden" name="hdnimage2" id="hdnimage2" value="'.$data['image2'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage2" id="hdnimage2" />';
			if($data['image3'])
				echo '<input type="hidden" name="hdnimage3" id="hdnimage3" value="'.$data['image3'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage3" id="hdnimage3" />';
			if($data['image4'])
				echo '<input type="hidden" name="hdnimage4" id="hdnimage4" value="'.$data['image4'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage4" id="hdnimage4" />';
			if($data['image5'])
				echo '<input type="hidden" name="hdnimage5" id="hdnimage5" value="'.$data['image5'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage5" id="hdnimage5" />';
			if($data['pattern'])
				echo '<input type="hidden" name="hdnimage6" id="hdnimage6" value="'.$data['pattern'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage6" id="hdnimage6" />';
			if($data['grading'])
				echo '<input type="hidden" name="hdnimage7" id="hdnimage7" value="'.$data['grading'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage7" id="hdnimage7" />';
			if($data['fileupld1'])
				echo '<input type="hidden" name="hdnimage16" id="hdnimage16" value="'.$data['fileupld1'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage16" id="hdnimage16" />';
			if($data['fileupld2'])
				echo '<input type="hidden" name="hdnimage17" id="hdnimage17" value="'.$data['fileupld2'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage17" id="hdnimage17" />';
			if($data['fileupld3'])
				echo '<input type="hidden" name="hdnimage18" id="hdnimage18" value="'.$data['fileupld3'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage18" id="hdnimage18" />';
			if($data['fileupld4'])
				echo '<input type="hidden" name="hdnimage19" id="hdnimage19" value="'.$data['fileupld4'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage19" id="hdnimage19" />';
			if($data['fileupld5'])
				echo '<input type="hidden" name="hdnimage20" id="hdnimage20" value="'.$data['fileupld5'].'" />';
			else 
				echo '<input type="hidden" name="hdnimage20" id="hdnimage20" />';?>
						  <input type="hidden" name="pid" id="pid" value="<?php echo $data['pid'];?>" />
						</form>
           </td>
        </tr>
      </table>
<?php require('../../trailer.php');
ob_end_flush();
?>
