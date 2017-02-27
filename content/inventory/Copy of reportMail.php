<?php 
require('Application.php');
$styleId = $_GET['styleId'];
$colorId = $_GET['colorId'];

$sql ='select "styleId", "styleNumber", "scaleNameId", price, "locationIds" from "tbl_invStyle" where "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql)))
{
	print("Failed StyleQuery: " . pg_last_error($connection));
	exit;
}
$row = pg_fetch_array($result);
$data_style=$row;
pg_free_result($result);
$query2='Select "colorId","name",image from "tbl_invColor" where "colorId"='.$colorId;
if(!($result2=pg_query($connection,$query2)))
{
	print("Failed colorQuery: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2))
{
	$data_color=$row2;
}
	pg_free_result($result2);
if($data_style['scaleNameId']!="" )
{	

	$query2='Select "opt1Name","opt2Name" from "tbl_invScaleName" where "scaleId"='.$data_style['scaleNameId'];
	if(!($result=pg_query($connection,$query2))){
		print("Failed MainOptionQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_optionName=$row;
	pg_free_result($result);
	
	$query2='Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2)))
	{
		print("Failed Option1Query: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2))
	{
		$data_mainSize[]=$row2;
	}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2)))
	{
		print("Failed Option2Query: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2))
	{
		$data_opt1Size[]=$row2;
	}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2)))
	{
		print("Failed SizeScleQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2))
	{
		$data_opt2Size[]=$row2;
	}
	pg_free_result($result2);
}

		$clrId = $data_color['colorId'];
		$query='select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$clrId.'  and "isActive"=1 order by "inventoryId"';
	if(!($result=pg_query($connection,$query)))
	{
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_inv[]=$row;
	}
	pg_free_result($result);
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

$tableWidth = $totalScale * 101;

 $print='<table width="80%" align="center" border="0">
                <tr>
                  <td align="center"><font size="5">Report</font><font size="5"> View/Edit   <br>
                      <br>
                    </font>
                    <fieldset style="margin:10px;">
                    <table width="100%" border="0" align="center">
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <td>
                        <td width="65">Style:</td>
                        <td width="100"><h2>'.$data_style['styleNumber'].'</h2></td>
                        <td width="20">&nbsp;</td>
                        <td>
						<div class="color">Color:&nbsp;';
						if($data_color['colorId'] == $clrId)
						{
							$imageName = $data_color['image'];
							$color = $data_color['name'];
								
                           $print .= '<label>'. $data_color['name'].'</label>';
						}
                          $print .= '  </div></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                    </fieldset>
<fieldset style="margin:10px;">
  <table width="100%">
 <tr>
    <td>
    <table width="20%" border="0">
          <tr>
            <td><img id="imgView" src="http://'.$_SERVER['SERVER_NAME'].'/content/uploadFiles/inventory/images/'.$data_color['image'].'" alt="thumbnail" width="150" height="230" border="0" /></td>
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

  <tr>
    <td>
    <table class="HD001" width="250" style="float:left; border:1px solid white" >
              <tr>
                <td bgcolor="#ffffff" style="text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
                <td bgcolor="#333333" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;" width="150px" height="25px">sizes </td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style=" text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
                <td bgcolor="#333333" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;" width="150px" height="25px">prices</td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style="text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
                <td bgcolor="#ffffff" style="text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style="text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
                <td bgcolor="#ffffff" style="text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style="text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
                <td bgcolor="#333333" style="text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="150px" height="25px">'.$data_optionName['opt1Name'].'</td>
              </tr>
            </table>
            </td>
            
           
    <td>
    <table width="'. $tableWidth.'px" style="float:left; border:1px solid white">';
    

    
if($data_style['scaleNameId']!="")
{
	$print.='<tr>';

	for($i=0; $i< count($data_mainSize); $i++)
	{
 		$print.=' <td  bgcolor="#333333" width="100px" height="25px" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;">'. $data_mainSize[$i]['scaleSize'].'</td>';
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
					$print.='<td width="100px" height="25px" bgcolor="#333333" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;">'.$data_inv[$j]['price'].'</td>';

				}
				break;
			}
		}
		if(!$invPrice)
		{
			$print.='<td width:100px; height:25px; style="background-color:#333333; text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;">'.$data_style['price'].'</td>';
		}	
	}
            $print.='  </tr>
                <tr>';
				for($i=0; $i< count($data_mainSize); $i++)
				{
                  $print.='<td style="background-color:#ffffff; text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="100px" height="25px">&nbsp;</td>';
				}
               $print.=' </tr>
                <tr>';
				for($i=0; $i< count($data_mainSize); $i++)
				{
                   $print.='<td style="background-color:#ffffff; text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="100px" height="25px">&nbsp;</td>';
				}
               $print.=' </tr>
                <tr> ';
				$columnSize = 0;
				for($i =0; $i < count($data_mainSize); $i++)
				{
					if($i < count($data_opt2Size) )
					{		
						$print.='<td style="background-color:#0099CC; text-align:center; 
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="100px" height="25px">'.$data_opt2Size[$i]['opt2Size'].'</td>';
					}
					else
					{
						$print.=' <td>&nbsp;</td>';
						break;
					}
				}
				 $print.='</tr>
              </table>
    </td>
  </tr>
</table>
		  
		  
         <table width="'. $tableWidth.'px" border="0" cellspacing="0" cellpadding="0">
  <tr>
  
    <td valign="top">
    <table width="250px" style="border:1px solid white">';

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
          <td style="background-color:#ffffff; text-align:left; vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="150px">'.$data_loc[$loc_i]['name'].'</td>';
		if(count($data_opt1Size) > 0)
		{
			for($j=0; $j < count($data_opt1Size); $j++)
			{			
				if($j != 0)
				{
        $print.=' <tr>
		<td  style="background-color:#ffffff; text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#000000; padding-left:3px;" width="150px" height="25px">&nbsp;</td>
		<td style="background-color:#333333; text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;" width="150px" height="25px">'. $data_opt1Size[$j]['opt1Size'] .'</td>
        </tr>';			
				}
				else
				{
				
       $print.=' <td style="background-color:#333333; text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;" width="150px" height="25px">'. $data_opt1Size[$j]['opt1Size'] .'</td>
        </tr>';			
				}
			}
		}
		else
		{
       $print.=' <td style="visibility:hidden;" style="background-color:#ffffff; text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="100px" height="25px">&nbsp;</td>
        </tr>';
		}
     $print.=' <tr>
        <td style="background-color:#ffffff; text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;" width="100px" height="25px">&nbsp;</td>
        <td style="background-color:#ffffff; text-align:center;
vertical-align:top; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px;
color:#000000; padding-left:3px;" width="100px" height="25px">&nbsp;</td>
      </tr> ';      
	}
}
                   
                 $print.=' </table>
    </td>
    <td  align="right" valign="top" >
    
 <table width="'. $tableWidth.'px'.'" id="values" style="border:1px solid white">';

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
											$print.='<td bgcolor="#cccccc" width="100px" style=" text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;" width="100px" height="25px">'. $data_inv[$m]['quantity'].'</td>';
											}
											else
											{
												$print.='<td bgcolor="#cccccc" width="100px"  style="background-color:#cccccc; width:100px; text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;" width="100px" height="25px">'.$data_inv[$m]['newQty'].'</td>';
											}
										}
										else
										{
											$print.='<td bgcolor="#cccccc" style="background-color:#cccccc; width:100px; text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;" width="100px" height="25px">0</td>';
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
											$print.='<td height="25px" bgcolor="#cccccc" width="100px" style=" text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;">'. $data_inv[$m]['quantity'].'</td>';
											}
											else
											{
												$print.='<td height="25px" bgcolor="#cccccc" width="100px" style="text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;">'.$data_inv[$m]['newQty'].'</td>';
											}
										}
										else
										{
											$print.='<td height="25px" bgcolor="#cccccc" width="100px" style="text-align:center; font-family:Tahoma,Verdana,Arial, Helvetica; font-size:12px; color:#ffffff;">0</td>';
										}
										break;
									}
								}			
							}
						}
						if(!$invFound)
						{
								$print.='<td height="25px" bgcolor="#cccccc" width="100px" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#ffffff;">0</td>';
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
									$print.='<td height="25px" bgcolor="#cccccc" width="100px" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;">'. $data_inv[$m]['quantity'].'</td>';
									}
									else
									{
										$print.='<td height="25px" bgcolor="#cccccc" width="100px" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;">'.$data_inv[$m]['newQty'].'</td>';
									}
								}
								else
								{
									$print.='<td height="25px" bgcolor="#cccccc" width="100px" style=" text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;">0</td>';
								}
								break;
							}
						}
						if(!$invFound)
						{
								$print.='<td height="25px" bgcolor="#cccccc" width="100px"   style=" text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px;">0</td>';
						}
						$mainIndex++;		
					}
		}
			$print.='<tr>
              <td height="25px">&nbsp;</td>
            </tr>';
	}
}	      
                   
     $print.='</table>
    </td>
  </tr>
</table>

                  
                    
              <table style="float:left; width:250px; border:1px solid white">
                <tr>
                  <td bgcolor="#ffffff" width="150px" style=" text-align:left; vertical-align:top;
font-family:Tahoma, Verdana, Arial, Helvetica; font-ize:12px; color:#000000; padding-left:3px;">&nbsp;</td>
                  <td bgcolor="#333333" width="150px"  height="25px" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;">sizes </td>
                </tr>
              </table>
                  <table style="float:left; border:1px solid white; width="'. $tableWidth.'px'.'" >
                      <tr>';
 				for($i=0; $i< count($data_mainSize); $i++)
				{
					$print.='<td  bgcolor="#333333" colspan="2" style="text-align:center; font-family:Tahoma, Verdana, Arial, Helvetica; font-size:12px; color:#FFFFFF; padding-left:3px; line-height:25px;" width="100px"  height="25px">'.$data_mainSize[$i]['scaleSize'].'</td>';
				}
$print.=' </tr>
 </table></td>
      </tr>
    </table>
	 </td>
    </tr>
    </table>
    <br />
</fieldset></td>
                </tr>
              </table>';
			//echo $print;
if(isset($_REQUEST['email']) && isset($_REQUEST['subject']))	
{
	//print '<pre>';print_r($_POST);print '</pre>';
	require($PHPLIBDIR.'mailfunctions.php');
	extract($_POST);	
	/*// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
	$headers .= 'X-Mailer: PHP/' . phpversion();
	// Additional headers
	$headers .= 'From: Inventory Report <donotreply@pdf-imagewear.com>' . "\r\n";
	// Mail it
	$body='There are no reports made yet..';
	if($print) 
	{ 
		$body=$print;
	}
	@ mail($email, $subject, $body, $headers);
	*/
	$body='There are no reports made yet..';
	if($print)
	{ 
		$body=$print;
	}
	$headers=create_smtp_headers($subject, "inventoryreports@i2net.com", $email, "Uniform Inventory","","text/html");
	$data=$headers. "<html>".$body."</html>";
	if((send_smtp("mail.i2net.com","inventoryreports@i2net.com",$email, $data)) == false)
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
header("location: reportViewEdit.php?styleId=$styleId&colorId=$colorId");
?>