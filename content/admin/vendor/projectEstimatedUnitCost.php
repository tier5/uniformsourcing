<?php 
		require('Application.php');
		require('../../header.php');
		$sql='select (Max("pid")+1) as "pid" from "projectEstimatedUnitCost"';
		if(!($result_cnt=pg_query($connection,$sql))){
			print("Failed query1: " . pg_last_error($connection));
			exit;
		}
		while($row_cnt = pg_fetch_array($result_cnt)){
			$data_cnt=$row_cnt;
		}
		if(! $data_cnt['pid']) { $data_cnt['pid']=1; }
		
		if(count($_SESSION['add_err']))
		{
			extract($_SESSION['add_err'][1]);
			if(count($_SESSION['add_err'][0])) 
			{
				echo '<ul style="color:Red;margin-left:100px;">';
				echo "<li>Please Correct Following Fields</li>";
				foreach($_SESSION['add_err'][0] as $error)
				 {
					echo "<li>".$error."</li>";
				 }
				echo '</ul>';
	    	}
			unset($_SESSION['add_err']);
		//print '<pre>';print_r($_SESSION['add_err']);print '</pre>';
		}
 		else {
		$ptrnsetup='';
		$grdngsetup ='';
		$smplefeesetup='';
		$fabric='';
		$trimfee='';
		$labour='';
		$duty='';
		$frieght='';
		$other='';
		 }
			// if($debug == "on"){
//			require('../../header.php');
//			foreach($_POST as $key=>$value) {
//				if($key!="submit") { echo "$key = $value<br/>"; }
//			}
//		}
	$populate_query="SELECT * from \"projectEstimatedUnitCost\" where \"tbl_projects_id\"='".$_SESSION['pid']."'";
	$execute=pg_query($connection,$populate_query);
	while($row_count=pg_fetch_array($execute))
	{
		$data_count=$row_count;
		$ptrnsetup=$data_count['ptrnsetup'];
		$grdngsetup=$data_count['grdngsetup'];
		$smplefeesetup=$data_count['smplefeesetup'];
		$fabric=$data_count['fabric'];
		$trimfee=$data_count['trimfee'];
		$labour=$data_count['labour'];
		$duty=$data_count['duty'];
		$frieght=$data_count['frieght'];
		$other=$data_count['other'];
	}
 	$quan='SELECT "quanPeople" FROM "tbl_projects" where pid='.$_SESSION['pid'];
	$quanPeople=pg_result(pg_query($connection,$quan),0);
	echo "<script type=\"text/javascript\">var jsQtyPeople;</script>";
	echo "<script type=\"text/javascript\">jsQtyPeople = \"$quanPeople\";</script>";
if(isset($_POST['sbtbutton']))
  {
		extract($_POST);
		$flag=0;
		if($quanPeople!=0)
			{
				$txtptrnsetup=$_POST['ptrnsetup'];
				$txtgrdngsetup=$_POST['grdngsetup'];
				$txtsmplefeesetup=$_POST['smplefeesetup'];
				
				if($txtptrnsetup!="")
				{
					if(!($txtptrnsetup*1))
					{
						$txtptrnsetup=0;
						$other = ($_POST['ptrnsetup'] + $_POST['grdngsetup']+
						$_POST['smplefeesetup']) /$quanPeople;	
					}
					$flag=1;
				}	
				if($txtgrdngsetup!="")
				{
					if(!($txtgrdngsetup*1))
					{
						$txtgrdngsetup=0;
						$other = ($_POST['ptrnsetup'] + $_POST['grdngsetup']+
						$_POST['smplefeesetup']) /$quanPeople;	
					}
					$flag=1;
				}
				if($txtsmplefeesetup!="")
				{
					if(!($txtsmplefeesetup*1))
					{
						$txtsmplefeesetup=0;
						$other = ($_POST['ptrnsetup'] + $_POST['grdngsetup']+
						$_POST['smplefeesetup']) /$quanPeople;	
					}
					$flag=1;
				}
			}	
			$query1="SELECT COUNT(tbl_projects_id) FROM \"projectEstimatedUnitCost\" where \"tbl_projects_id\"='".$_SESSION['pid']."'";
			if(!pg_result(pg_query($connection,$query1),0))
			{
				$query="INSERT INTO \"projectEstimatedUnitCost\"(\"tbl_projects_id\"";
				if($ptrnsetup) $query.=", \"ptrnsetup\" ";
				if($grdngsetup) $query.=", \"grdngsetup\" ";
				if($smplefeesetup) $query.=", \"smplefeesetup\" "; 
				if($fabric) $query.=", \"fabric\" "; 
				if($trimfee) $query.=", \"trimfee\" ";
				if($labour) $query.=", \"labour\" ";
				if($duty) $query.=", \"duty\" ";
				if($frieght) $query.=", \"frieght\" ";
				if($other) $query.=", \"other\" ";
				$query.=")";
				$query.="VALUES('".$_SESSION['pid']."'";
				if($ptrnsetup) $query.=",'$ptrnsetup' ";
				if($grdngsetup) $query.=",'$grdngsetup' ";
				if($smplefeesetup) $query.=",'$smplefeesetup'";
				if($fabric) $query.=",'$fabric'";
				if($trimfee) $query.=",'$trimfee'";
				if($labour) $query.=",'$labour'";
				if($duty) $query.=",'$duty'";
				if($frieght) $query.=",'$frieght'";
				if($other) $query.=",'$other'";
				$query.=")";
			}
			else
			{
				 $query="UPDATE \"projectEstimatedUnitCost\" SET";
				  if($ptrnsetup) $query.="\"ptrnsetup\"='$ptrnsetup',";
				  else $query.="\"ptrnsetup\"=0,";
				  if($grdngsetup)$query.="\"grdngsetup\"='$grdngsetup',";
				  else $query.="\"grdngsetup\"=0,";
				  if($smplefeesetup)$query.="\"smplefeesetup\"='$smplefeesetup',";
				  else $query.="\"smplefeesetup\"=0,";
			      if($fabric) $query.="\"fabric\"='$fabric',";
				  else $query.="\"fabric\"=0,";
				  if($trimfee) $query.="\"trimfee\"='$trimfee',";
				  else $query.="\"trimfee\"=0,";
				  if($labour) $query.="\"labour\"='$labour',";
				  else $query.="\"labour\"=0,";
				  if($duty) $query.="\"duty\"='$duty',";
				  else $query.="\"duty\"=0,";
				  if($frieght) $query.="\"frieght\"='$frieght',";
				  else $query.="\"frieght\"=0,";
				  if($other) $query.="\"other\"='".round($other,2)."'";
				  else $query.="\"other\"=0";
				  $query.="WHERE \"tbl_projects_id\"=".$_SESSION['pid'];
			}
			if(!($result1=pg_query($connection,$query))){
				print("Failed query: " . pg_last_error($connection));
				exit;
			}
			while($row1 = pg_fetch_array($result1)){
				$data1[]=$row1;
			}
			$prjctEstCost="SELECT (";
			if($ptrnsetup) $prjctEstCost.="ptrnsetup";
			if($grdngsetup) $prjctEstCost.="+grdngsetup";
			if($smplefeesetup) $prjctEstCost.="+smplefeesetup";
			if($fabric) $prjctEstCost.="+fabric";
			if($trimfee) $prjctEstCost.="+trimfee";		
			if($labour) $prjctEstCost.="+labour";
			if($duty) $prjctEstCost.="+duty";
			if($frieght) $prjctEstCost.="+frieght";
			if($other) $prjctEstCost.="+other";
			$prjctEstCost.=") FROM\"projectEstimatedUnitCost\" 
							where \"tbl_projects_id\"=".$_SESSION['pid'];
							
							
			$prjctCost=pg_result(pg_query($connection,$prjctEstCost),0);
			
			$prjctCostUpdate="UPDATE \"tbl_projects\" SET \"pestimate\"=".$prjctCost."where \"pid\"=".$_SESSION['pid'];
			
			$ExecuteUpdate=pg_result(pg_query($connection,$prjctCostUpdate),0);
}	
	 ?>

<script type="text/javascript">	
	function FillOtherField()
	{	
			var ptrnset=0;
			var grdngsetup=0;
			var smplefeesetup=0;
			if(document.prjctEst.ptrnsetup)
			ptrnset=document.prjctEst.ptrnsetup.value;
			if(document.prjctEst.grdngsetup)
			grdngsetup=document.prjctEst.grdngsetup.value;
			if(document.prjctEst.smplefeesetup)
			smplefeesetup=document.prjctEst.smplefeesetup.value;			
			
				if(ptrnset=="")
				{
					ptrnset = 0;
				}
				else if(isNaN(ptrnset))							
				{
					alert("Enter Pattern Fee in Digits");
					document.prjctEst.ptrnset.value = "";
					document.prjctEst.ptrnset.focus();					
					ptrnset = 0;
				}				
				if(grdngsetup=="") 
				{
					grdngsetup=0;
				}
				else if(isNaN(grdngsetup))							
				{
					alert("Enter Grading Fee in Digits");
					document.prjctEst.grdngsetup.value = "";
					document.prjctEst.grdngsetup.focus();	
					grdngsetup = 0;
				}
				if(smplefeesetup=="")
				{
					smplefeesetup = 0;
				}
				if(isNaN(smplefeesetup))							
				{	
					alert("Enter Grading Fee in Digits");
					document.prjctEst.smplefeesetup.value = "";
					document.prjctEst.smplefeesetup.focus();					
					smplefeesetup = 0;
				}
					
				var sum=parseFloat(ptrnset)+parseFloat(grdngsetup)+
				parseFloat(smplefeesetup);
				if(jsQtyPeople!=0)
				{
			 	var otherfield=parseFloat(sum)/(jsQtyPeople);
				document.getElementById('other').value=otherfield.toFixed(2);
				}
				else
				document.prjctEst.other.value=0;
	}
	function Redirect()
	{
		window.location='editProject.php?ID=<?php echo $_SESSION['pid']; ?>';
	}
function Validate()
	{
		if((document.prjctEst.fabric.value) && (isNaN(document.prjctEst.fabric.value)))	
		{	
					alert("Enter fabric Fee in Digits");
					document.prjctEst.fabric.value = "";
					document.prjctEst.fabric.focus();
					return false;
		}	
		else if((document.prjctEst.trimfee.value) && (isNaN(document.prjctEst.trimfee.value)))
		{	
					alert("Enter trimfee Fee in Digits");
					document.prjctEst.trimfee.value = "";
					document.prjctEst.trimfee.focus();
					return false;
			
		}	
		else if((document.prjctEst.labour.value) && (isNaN(document.prjctEst.labour.value)))
		{		
		 			alert("Enter labour Fee in Digits");
					document.prjctEst.labour.value = "";
					document.prjctEst.labour.focus();
					return false;
		}	
		else if((document.prjctEst.duty.value) && (isNaN(document.prjctEst.duty.value)))
		{			
					alert("Enter duty Fee in Digits");
					document.prjctEst.duty.value = "";
					document.prjctEst.duty.focus();
					return false;
		}	
		
		else if((document.prjctEst.frieght.value) && (isNaN(document.prjctEst.frieght.value)))
		{		
					alert("Enter frieght Fee in Digits");
					document.prjctEst.frieght.focus();
					return false;
		}	
		return true;
}
</script>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<form name="prjctEst" id="prjctEst" method="post" action="projectEstimatedUnitCost.php" enctype="multipart/form-data">
  <table width="100%">
    <tr>
      <td align="center"><font face="arial"><font size="5">P</font><font size="5">roject Estimated Unit Cost
            </font>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="left"><input name="button2" type="button" onMouseOver="this.style.cursor = 'pointer';"  value="Back" onClick="javascript:location.href='editProject.php?ID=<?php echo $_SESSION['pid'];?>'"/><td/>
          </tr>
        </table>
        <font size="5">&nbsp;
        </font>
          <table width="80%" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td height="25" align="right" valign="top">Pattern Set-Up:</td>
              <td width="10">&nbsp;</td>
              <td align="left" valign="top"><input id="ptrnsetup" name="ptrnsetup" type="text" class="textBox" value="<?php echo $ptrnsetup; ?>" onBlur="javascript:FillOtherField();"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Grading Set-Up:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="grdngsetup" name="grdngsetup" type="ptrnsetup" class="textBox" value="<?php echo $grdngsetup; ?>" onBlur="javascript:FillOtherField();"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Sample Fee Set Up:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="smplefeesetup" name="smplefeesetup" type="text" class="textBox" value="<?php echo $smplefeesetup; ?>" onBlur="javascript:FillOtherField();"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Fabric:</td>
              <td>&nbsp;</td>
            <td align="left" valign="top"><input id="fabric" name="fabric" type="text" class="textBox" value="<?php echo $fabric;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Trim:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="trimfee" name="trimfee" type="text" class="textBox" value="<?php echo $trimfee; ?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Labor:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="labour" name="labour" type="text" class="textBox" value="<?php echo $labour; ?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Duty: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="duty" name="duty" type="text" class="textBox" value="<?php echo $duty; ?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Freight:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="frieght" name="frieght" type="text" class="textBox" value="<?php echo $frieght; ?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Other : </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="other" name="other" type="text" class="textBox" value="<?php echo round($other,2);?>" readonly="readonly"/></td>
            </tr>
            <tr>
              <td height="25" align="right"><input name="sbtbutton"  id="sbtbutton"type="submit" onMouseOver="this.style.cursor = 'pointer';" value="Save" onClick="javascript:return Validate();"/></td>
              <td>&nbsp;</td>
              <td align="left"><input name="button2" type="button" onMouseOver="this.style.cursor = 'pointer';"  value="Cancel" onClick="Redirect();" /></td>
            </tr>
      </table></td>
    </tr>
  </table>
</form>
<?php
require('../../trailer.php');
?>
</body>
</html>