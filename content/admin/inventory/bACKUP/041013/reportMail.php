<?php 
require('Application.php');
require($PHPLIBDIR.'mailfunctions.php');
//$sql='select st."styleId" st."styleNumber",st."scaleNameId",st."scaleNameId" from "tbl_invStyle" st left join tbl_inventory inv on st."styleId"=inv."styleId" where st."styleId"='.$_GET['StyleId'];
$search = "";
if(isset($_GET['styleId']))
{
	$styleId 	= $_GET['styleId'];	
	if(isset($_POST['colorId']))
	{
		$clrId 		= $_POST['colorId'];
		$opt1Id 	= $_GET['opt1Id'];
		$opt2Id 	= $_GET['opt2Id'];
	}
	else
	{
		$clrId 		= 0;
		$opt1Id 	= 0;
		$opt2Id 	= 0;
	}
	if($clrId > 0)
	{
		$search = " and \"colorId\"=$clrId ";
		if($opt1Id > 0)
		 	$search .= "and \"opt1ScaleId\"=$opt1Id ";
		if($opt2Id > 0)
		 	$search .= "and \"opt2ScaleId\"=$opt2Id ";
	}
	else
		$search = "";	
}
$sql ='select "styleId","barcode", "styleNumber", "scaleNameId", price, "locationIds" from "tbl_invStyle" where "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
	print("Failed StyleQuery: " . pg_last_error($connection));
	exit;
}
$row = pg_fetch_array($result);
$data_style=$row;
pg_free_result($result);
$query2='Select * from "tbl_invColor" where "styleId"='.$data_style['styleId'];
if(!($result2=pg_query($connection,$query2))){
	print("Failed OptionQuery: " . pg_last_error($connection));
	exit;
}
//echo $query2;
while($row2 = pg_fetch_array($result2)){
	$data_color[]=$row2;}
       // print_r($data_color);
pg_free_result($result2);
if($data_style['scaleNameId']!="" )
{	

	$query2='Select "opt1Name","opt2Name" from "tbl_invScaleName" where "scaleId"='.$data_style['scaleNameId'];
	if(!($result=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_optionName=$row;
	pg_free_result($result);
	
	$query2='Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_mainSize[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt1Size[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt2Size[]=$row2;}
	pg_free_result($result2);
}

$totalScale = count($data_mainSize);
$tableWidth = 0;

$tableWidth = $totalScale * 100;

$sql='select "inventoryId",quantity,"newQty","isStorage" from "tbl_inventory"';
if(!($result=pg_query($connection,$sql))){
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;}
		for($i=0; $i<count($data_inv); $i++)
		{
			if($data_inv[$i]['newQty']>0)
			{
				if(($data_inv[$i]['quantity']!="" && $data_inv[$i]['quantity']>0))
				{
					$sql='update "tbl_inventory" set "isStorage"=1 ,"newQty"=0';
						if(!($result=pg_query($connection,$sql))){
						print("Failed invUpdateQuery: " . pg_last_error($connection));
						exit;
					}
				}
				else if(($data_inv[$i]['quantity']=="" || $data_inv[$i]['quantity']==0))
				{
					$sql='Delete from "tbl_inventory" where "inventoryId"='.$data_inv[$i]['inventoryId'];
						if(!($result=pg_query($connection,$sql))){
						print("Failed deleteInvQuery: " . pg_last_error($connection));
						exit;
					}
				}
			}
		}
if(count($data_color) > 0)
{	
	if($search != "")
	{
		$query='select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "isActive"=1'.$search.' order by "inventoryId"';
	}
	else
	{
		$clrId = $data_color[0]['colorId'];
		$query='select "inventoryId","sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$data_color[0]['colorId'].'  and "isActive"=1 order by "inventoryId"';
	}	
	if(!($result=pg_query($connection,$query))){
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;}
	pg_free_result($result);
}
$query='select * from "tbl_invLocation" order by "locationId"';
if(!($result=pg_query($connection,$query)))
{
	print("Failed invQuery: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result))
{
	$data_loc[]=$row;
}
pg_free_result($result);
$locArr = array();
if($data_style['locationIds'] != "")
{
	$locArr = explode(",",$data_style['locationIds']);
}
$totalScale = count($data_mainSize);
$tableWidth = 0;

$tableWidth = $totalScale * 100;


 $print='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
 $print.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
 
  $print.='<body><table width="80%" align="center" border="0">
                <tr>
                  <td align="center">
				  <font size="5">Report</font><font size="5"> View/Edit   <br>
                      <br>
                    </font>
                    <fieldset style="margin:10px;">
                    <table width="100%" border="0" align="center">
					 <font face="Tahoma,Geneva,sans-serif" size="-1">
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="65">Style:</td>
                        <td width="100"><h2>'.$data_style['styleNumber'].'</h2></td>
                        <td width="20">&nbsp;</td>
                        <td>
						<div class="color">Color:&nbsp;';

  for ($i = 0; $i < count($data_color); $i++) {
    if ($data_color[$i]['name'] != "") {
        if ($data_color[$i]['colorId'] == $clrId) {
            $imageName = $data_color[$i]['image'];
            $print .= '<label>' . $data_color[$i]['name'] . '</label>';
            $color = $data_color[$i]['name'];
            continue;
        }
    }
  }
                          $print .= '  </div>
						  </td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
					 </font>
                    </table>
                    </fieldset>

<fieldset style="margin:10px;">
  <table width="100%">
 
 <tr>
    <td>
    <table width="20%" border="0">
          <tr>
         
            <td><img id="imgView" src="http://internal.uniformsourcing.com/content/uploadFiles/inventory/images/'.rawurlencode(trim($imageName)).'" alt="thumbnail" width="150" height="230" border="0" /></td>
            </tr>
          <tr>
            <td height="100">&nbsp;</td>
            </tr>
        </table>
        </td>
    <td>
    <table width="80%" border="0">
      <tr>
        <td>
<table width="50%" border="0">
 <font face="Tahoma,Geneva,sans-serif" size="-1">
  <tr>
    <td>
    <table width="250" style="border:1px solid white;" >
	 <font face="Tahoma,Geneva,sans-serif" size="-1">
              <tr>
                <td bgcolor="#ffffff" width="100" height="25" align="left" >&nbsp;</td>
                <td bgcolor="#333333" width="150" height="25" align="center" ><font color="#FFFFFF" >sizes </font></td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" width="100" height="25" align="left" >&nbsp;</td>
                <td bgcolor="#333333" width="150" height="25" align="center"><font color="#FFFFFF" >prices</font></td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" width="100" height="25" align="left" >&nbsp;</td>
                <td bgcolor="#ffffff" width="150" height="25" align="left" >&nbsp;</td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" width="100" height="25" align="left" >&nbsp;</td>
                <td bgcolor="#ffffff" width="150" height="25" align="left" >&nbsp;</td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" width="100" height="25" align="left" >&nbsp;</td>
                <td bgcolor="#ffffff" width="150" height="25" align="center" > <font color="#000000" >'.$data_optionName['opt1Name'].'</font></td>
              </tr>
			  </font>
            </table>
            </td>
            
           
    <td>
    <table width="'. $tableWidth.'" style="border:1px solid white;" >
     <font face="Tahoma,Geneva,sans-serif" size="-1">';

    
if($data_style['scaleNameId']!="")
{
	$print.='<tr>';

	for($i=0; $i< count($data_mainSize); $i++)
	{
 		$print.=' <td bgcolor="#333333" width="100" height="25" align="center" > <font color="#FFFFFF" >'. $data_mainSize[$i]['scaleSize'].'</font></td>';
	}

  $print.=' </tr> ';
}
 
$print.='<tr>';
  for($i=0; $i< count($data_mainSize); $i++)
	{
		$invPrice = 0;
		for($j=0;$j < count($data_inv);$j++)
		{
			if($data_inv[$j]['sizeScaleId'] == $data_mainSize[$i]['mainSizeId'])
			{
				if($data_inv[$j]['price'] != "" || $data_inv[$j]['price'] > 0)
				{
					$invPrice = 1;
					$print.='<td bgcolor="white" width="100" height="25" align="center" ><font color="black" >'.$data_inv[$j]['price'].'</font></td>';

				}
				break;
			}
		}
		if(!$invPrice)
		{
			$print.='<td bgcolor="white" width="100" height="25" align="center" ><font color="black" >'.$data_style['price'].'</font></td>';
		}	
	}
            $print.='  </tr>
                <tr>';
				for($i=0; $i< count($data_mainSize); $i++)
				{
                  $print.='<td bgcolor="white" width="100" height="25" align="center" >&nbsp;</td>';
				}
               $print.=' </tr>
                <tr>';
				for($i=0; $i< count($data_mainSize); $i++)
				{
                   $print.='<td bgcolor="white" width="100" height="25" align="center" >&nbsp;</td>';
				}
               $print.=' </tr>
                <tr> ';
				$columnSize = 0;
				for($i =0; $i < count($data_mainSize); $i++)
				{
					if($i < count($data_opt2Size) )
					{		
						$print.='<td bgcolor="#0099CC"  width="100" height="25" align="center" ><font color="#000000" >'.$data_opt2Size[$i]['opt2Size'].'</font></td>';
					}
					else
					{
						$print.=' <td width="100" height="25" align="center" >&nbsp;</td>';						
					}
				}
				 $print.='</tr>
				 </font>
              </table>
    </td>
  </tr>
  </font>
</table>  
		  
         <table width="'. $tableWidth.'" border="0">
		  <font face="Tahoma,Geneva,sans-serif" size="-1">
  <tr>
  
    <td valign="top">
    <table width="250" style="border:1px solid white;">
 <font face="Tahoma,Geneva,sans-serif" size="-1">';
if($locArr[0] > 0 && $locArr[0] != "")
{	
	$loc_i = 0;
	for($i=0; $i < count($locArr); $i++, $loc_i++)
	{ 	
		for(;$loc_i < count($data_loc);$loc_i++)
		{
			if($locArr[$i] == $data_loc[$loc_i]['locationId'])
				break;
		}
        $print.='<tr>
          <td bgcolor="#FFFFFF" width="150" height="25" align="left" ><font color="#000000" >'.$data_loc[$loc_i]['name'].'</font></td>';
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{			
				if($j != 0)
				{
        $print.=' <tr>
		<td  bgcolor="#FFFFFF" width="100" height="25" align="left" >&nbsp;</td>
		<td bgcolor="#333333" width="150" height="25" align="center" ><font color="#ffffff" >'. $data_opt1Size[$j]['opt1Size'] .'</font></td>
        </tr>';			
				}
				else
				{
       $print.=' <td bgcolor="#333333" width="150" height="25" align="center" ><font color="#ffffff" >'. $data_opt1Size[$j]['opt1Size'] .'</font></td>
        </tr>';			
				}
			}
		}
		else
		{
       $print.=' <td bgcolor="#ffffff" width="100" height="25" align="center" >&nbsp;</td>
        </tr>';
		}
     $print.=' <tr>
        <td bgcolor="#ffffff" width="100" height="25" align="center" >&nbsp;</td>
        <td bgcolor="#ffffff" width="100" height="25" align="center" >&nbsp;</td>
      </tr> ';      
	}
}
                   
                 $print.=' </font></table>
    </td>
    <td  align="right" valign="top" >
    
 <table width="'. $tableWidth.'" id="values" style="border:1px solid #ffffff;"> 
 <font face="Tahoma,Geneva,sans-serif" size="-1">';

if($locArr[0] > 0 && $locArr[0] != "")
{		
	for($i=0; $i< count($locArr); $i++)
	{	
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{
				 $print.='<tr>';
				
				$mainIndex = 0;
				for($k=0; $k< count($data_mainSize);$k++)
					{
						$invFound=0;
						for($m=0; $m < count($data_inv);$m++)
						{
							if(count($data_opt1Size) > 0)
							{
								if($data_opt1Size[$j]['opt1SizeId'] > 0)
								{
									if(($data_inv[$m]['sizeScaleId'] == $data_mainSize[$k]['mainSizeId']) && ($locArr[$i] ==$data_inv[$m]['locationId']) && ($data_opt1Size[$j]['opt1SizeId'] == $data_inv[$m]['opt1ScaleId']))
									{
										$invFound = 1;
										if($data_inv[$m]['inventoryId'] != "")
										{
											if($data_inv[$m]['quantity'] != "" )
											{	
											$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >'. $data_inv[$m]['quantity'].'</font></td>';
											}
											else
											{
												if($data_inv[$m]['newQty'] !="")
												{
												$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >'.$data_inv[$m]['newQty'].'</font></td>';
												}
												else
												{
													$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
												}
												
											}
										}
										else
										{
											$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
										}
										break;
									}
								}			
							}
							else
							{
								if($data_opt1Size[$j]['opt1SizeId'] > 0)
								{
									if(($data_inv[$m]['sizeScaleId'] == $data_mainSize[$k]['mainSizeId']) && ($locArr[$i] ==$data_inv[$m]['locationId']) && ($data_opt1Size[$j]['opt1SizeId'] == $data_inv[$m]['opt1ScaleId']))
									{
										$invFound = 1;
										if($data_inv[$m]['inventoryId'] != "")
										{
											if($data_inv[$m]['quantity'] != "" )
											{	
											$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >'. $data_inv[$m]['quantity'].'</font></td>';
											}
											else
											{
												if($data_inv[$m]['newQty'] !="")
												{
												$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >'.$data_inv[$m]['newQty'].'</font></td>';
												}
												else
												{
													$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
												}
											}
										}
										else
										{
											$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
										}
										break;
									}
								}			
							}
						}
						if(!$invFound)
						{
								$print.='<td bgcolor="#cccccc"  width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
						}
						$mainIndex++;		
					}
    $print.='</tr>';
			}
		}
		else
		{
			$mainIndex = 0;
				for($k=0; $k< count($data_mainSize);$k++)
					{
						$invFound=0;
						for($m=0; $m < count($data_inv);$m++)
						{
							if(($data_inv[$m]['sizeScaleId'] == $data_mainSize[$k]['mainSizeId']) && ($locArr[$i] ==$data_inv[$m]['locationId']) && ($data_opt1Size[$j]['opt1SizeId'] == $data_inv[$m]['opt1ScaleId']))
							{
								$invFound = 1;
								if($data_inv[$m]['inventoryId'] != "")
								{
									if($data_inv[$m]['quantity'] != "" )
									{	
									$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >'. $data_inv[$m]['quantity'].'</font></td>';
									}
									else
									{
										if($data_inv[$m]['newQty'] !="")
										{
										$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >'.$data_inv[$m]['newQty'].'</font></td>';
										}
										else
										{
											$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
										}
									}
								}
								else
								{
									$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
								}
								break;
							}
						}
						if(!$invFound)
						{
								$print.='<td bgcolor="#cccccc" width="100" height="25" align="center" ><font color="#000000" >0</font></td>';
						}
						$mainIndex++;		
					}
		}
			$print.='<tr>';
			for($n=0; $n< count($data_mainSize);$n++)
			{
              $print.='<td bgcolor="#ffffff" width="100" height="25" align="center" > &nbsp; </td>';
			}
            $print.='</tr>';
	}
}	      
                   
     $print.='</font></table>
    </td>
	</tr>
	</font>
</table>
<table width="100%" style="border:1px solid white;">
 <font face="Tahoma,Geneva,sans-serif" size="-1">
<tr>
<td width="250">
<table width="250" style="border:1px solid white;">
 <font face="Tahoma,Geneva,sans-serif" size="-1">
<tr>
  <td bgcolor="#ffffff" width="100" height="25" >&nbsp;</td>
  <td bgcolor="#333333" width="150" height="25" align="center" ><font color="#ffffff" >sizes </font></td>
</tr>
</font>
</table>
</td>
<td = width="'.$tableWidth.'">
<table style="border:1px solid white;" width="'.$tableWidth.'" >
 <font face="Tahoma,Geneva,sans-serif" size="-1">
 <tr>';
 				for($i=0; $i< count($data_mainSize); $i++)
				{
					$print.='<td bgcolor="#333333" width="100" height="25" align="center" ><font color="#ffffff" >'.$data_mainSize[$i]['scaleSize'].'</font></td>';
				}
$print.='  </tr>
</font>
 </table></td></tr>
 </font>
</table></td>
      </tr>
    </table>
	 </td>
    </tr>
    </table>
    <br />
</fieldset></font></td>
                </tr>
              </table>';

$print.="</body></html>";
//echo $print;
if(isset($_REQUEST['email']) && isset($_REQUEST['subject']))	
{
	extract($_POST);
	$body='There are no reports made yet..';
	if($print)
	{ 
		$body=$print;
	}
	$to = explode(',;',$email);
	for($k=0 ; $k < count($to); $k++)
	{
		if($isMailServer == 'true')
		{
                    $mailServerAddress = '127.0.0.1';
		
                    
                    
		}
	
		
			
			$headers=create_smtp_headers($subject, "inventoryreports@i2net.com", $to[$k], "Uniform Inventory","","text/html");
			$data=$headers. $body;
			if((send_smtp("mail.i2net.com","inventoryreports@i2net.com",$to[$k], $data)) == false)
			{
				global $last_output;
				echo "ERROR sending message d00d. $last_output<br>";
				exit;
			}	
			foreach ($_REQUEST as $i => $value) 
			{
				unset($_REQUEST[$i]);
			}
		
	}
}
header("location: reportViewEdit.php?styleId=$styleId&colorId=$colorId");
?>