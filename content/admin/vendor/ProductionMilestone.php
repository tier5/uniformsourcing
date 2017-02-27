<?php 
		require('Application.php');
		require('../../header.php');
		$sql='select (Max(id)+1) as id from tbl_prMilestone';
		if(!($result_cnt=pg_query($connection,$sql))){
			print("Failed query1: " . pg_last_error($connection));
			exit;
		}
		while($row_cnt = pg_fetch_array($result_cnt)){
			$data_cnt=$row_cnt;
		}
		if(! $data_cnt['id']) { $data_cnt['id']=1; }
		
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
		else
		{
			$lapDip='';
			$lapDipApprvl ='';
			$estDelvry='';
			$pdctSampl='';
			$pdctSamplApprvl='';
			$szngLine='';
			$prdctnTrgtDelvry='';
		
		 }
		 $populate_query="SELECT * from tbl_prMilestone where tbl_prjcts_id=".$_SESSION['pid'];
		 
	$execute=pg_query($connection,$populate_query);
	while($row_count=pg_fetch_array($execute))
	{
		$data_count=$row_count;
		$lapDip=$data_count['lapdip'];
		$lapDipApprvl=$data_count['lapdipapproval'];
		$estDelvry=$data_count['estdelivery'];
		$pdctSampl=$data_count['prdtnsample'];
		$pdctSamplApprvl=$data_count['prdtnsampleapprval'];
		$szngLine=$data_count['szngline'];
		$prdctnTrgtDelvry=$data_count['prdtntrgtdelvry'];
	}
	extract($_POST);
	if(isset($_POST['sbtbutton']))
	{
		$query1="SELECT COUNT(tbl_prjcts_id) FROM tbl_prMilestone where tbl_prjcts_id='".$_SESSION['pid']."'";
		
			if(!pg_result(pg_query($connection,$query1),0))
			{
				$query="INSERT INTO tbl_prMilestone(\"tbl_prjcts_id\" ";
				if($lapDip) $query.=", \"lapdip\" ";
				if($lapDipApprvl) $query.=", \"lapdipapproval\" ";
				if($estDelvry) $query.=", \"estdelivery\" "; 
				if($pdctSampl) $query.=", \"prdtnsample\" "; 
				if($pdctSamplApprvl) $query.=", \"prdtnsampleapprval\" ";
				if($szngLine) $query.=", \"szngline\" ";
				if($prdctnTrgtDelvry) $query.=", \"prdtntrgtdelvry\" ";
				$query.=")";
				$query.="VALUES('".$_SESSION['pid']."'";
				if($lapDip) $query.=",'$lapDip' ";
				if($lapDipApprvl) $query.=",'$lapDipApprvl' ";
				if($estDelvry) $query.=",'$estDelvry'";
				if($pdctSampl) $query.=",'$pdctSampl'";
				if($pdctSamplApprvl) $query.=",'$pdctSamplApprvl'";
				if($szngLine) $query.=",'$szngLine'";
				if($prdctnTrgtDelvry) $query.=",'$prdctnTrgtDelvry'";
				 $query.=")";//echo $query;
			}
			else
			{
				$query="UPDATE tbl_prMilestone SET ";
				  if($lapDip) $query.="lapdip ='$lapDip',";
				  else $query.="lapdip='',";
				  if($lapDipApprvl)$query.="\"lapdipapproval\"='$lapDipApprvl',";
				  else $query.="lapdipapproval='',";
				  if($estDelvry)$query.="estdelivery='$estDelvry',";
				  else $query.="estdelivery='',";
			      if($pdctSampl) $query.="prdtnsample='$pdctSampl',";
				  else $query.="prdtnsample='',";
				  if($pdctSamplApprvl) $query.="prdtnsampleapprval='$pdctSamplApprvl',";
				  else $query.="prdtnsampleapprval='',";
				  if($szngLine) $query.="szngline='$szngLine',";
				  else $query.="szngline='',";
				  if($prdctnTrgtDelvry) $query.="prdtntrgtdelvry='$prdctnTrgtDelvry'";
				  else $query.="prdtntrgtdelvry=''";
				  $query.="WHERE tbl_prjcts_id=".$_SESSION['pid'];
				 // echo $query;
			}
			if(!($result1=pg_query($connection,$query))){
					print("Failed query: " . pg_last_error($connection));
					exit;
			}
			while($row1 = pg_fetch_array($result1)){
				$data1[]=$row1;
			}	
}	 ?>

<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<form name="prjdctMile" id="prdctMile" method="post" action="ProductionMilestone.php" enctype="multipart/form-data">
  <table width="100%">
    <tr>
      <td align="center"><font face="arial"><font size="5">P</font><font size="5">roduction Milestone
            </font>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="left"><input name="button2" type="button" onMouseOver="this.style.cursor = 'pointer';"  value="Back" onClick="javascript:location.href='editProject.php?ID=<?php echo $_SESSION['pid'];?>'"/><td/>
          </tr>
        </table>
        <font size="5">&nbsp;
        </font>
          <table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
            <tr>
              <td height="25" align="right" valign="top">Lap Dip:</td>
              <td width="10">&nbsp;</td>
              <td align="left" valign="top"><input id="lapDip" name="lapDip" type="text" class="textBox" readonly="true" value="<?php echo $lapDip;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Lap Dip Approval:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="lapDipApprvl" name="lapDipApprvl" type="ptrnsetup" class="textBox" value="<?php echo $lapDipApprvl;?>" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Estimated Fabric Delivery Date:</td>
              <td>&nbsp;</td>
            <td align="left" valign="top"><input id="estDelvry" name="estDelvry" type="text" class="textBox" value="<?php echo $estDelvry;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="pdctSampl" name="pdctSampl" type="text" class="textBox" value="<?php echo $pdctSampl;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample Approval: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="pdctSamplApprvl" name="pdctSamplApprvl" type="text" class="textBox" value="<?php echo $pdctSamplApprvl;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Sizing Line:  </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="szngLine" name="szngLine" type="text" class="textBox" value="<?php echo $szngLine;?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production target Delivery: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="prdctnTrgtDelvry" name="prdctnTrgtDelvry" type="text" class="textBox" value="<?php echo $prdctnTrgtDelvry;?>"/></td>
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
<script type="text/javascript">
function Redirect()
{
	window.location='editProject.php?ID=<?php echo $_SESSION['pid']; ?>';
}
</script>
<?php
require('../../trailer.php');
?>
</body>
</html>