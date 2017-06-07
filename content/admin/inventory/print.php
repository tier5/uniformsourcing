 <!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/samplerequest.js"></script>
<title>Neck & Sleeve Sizes</title>

<style type="text/css">
	body{font-family: arial; font-size: 13px;}
	.wrapper{width: 98%; margin: 0 auto;}
	input[type="text"]{border:none; border-bottom: 1px solid #000; width: auto;}
	.top-table tr td{padding: 5px;}
	.top-table input[type="text"]{width: 50%;}
	.top-table table{border-collapse: collapse;}
	.top-table table tr td table tr td{padding: 0;}
	.common-table table{border-collapse: collapse;}
	.common-table table tr th{padding:5px 3px;}
	.common-table table tr td{border: 1px solid #878787; padding:5px 3px; width: 7.69%;}
</style>
</head>
<?php 
require('Application.php');
	$search = "";
	if(isset($_GET['styleId']))
	{
		$styleId 	= $_GET['styleId'];
	        $search = "";	
		if(isset($_GET['colorId']))
		{ 
			$clrId 		= $_GET['colorId'];
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
			$search = " and inv.\"colorId\"=$clrId ";
			if($opt1Id > 0)
			 	$search .= "and \"opt1ScaleId\"=$opt1Id ";
			if($opt2Id > 0)
			 	$search .= "and \"opt2ScaleId\"=$opt2Id ";
		}
	    if(isset($_GET['boxId'])&&$_GET['boxId']!='0')
	    {
	  		$search .= " and st.\"box\"='".$_GET['boxId']."'";      
	    }
	        
			
	}

	$sql ='select "clientId","styleId","sex","garmentId","barcode", "styleNumber", "scaleNameId", price, "locationIds" from "tbl_invStyle" where "styleId"='.$styleId;
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed StyleQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_style=$row; //--------------------------- data style----------------

	 //echo "<pre>";print_r($data_style);
	 //exit();

	$sql = 'select * from "tbl_garment" where "garmentID"='.$data_style["garmentId"];
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed StyleQuery: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_garment=$row;
	// echo "<pre>";print_r($data_garment);
	// exit();


	pg_free_result($result); 
	$query2='Select * from "tbl_invColor" where "styleId"='.$data_style['styleId'];
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2))
	{
		$data_color[]=$row2; // -------------------------- data_color ---------
	}
	pg_free_result($result2);

	if($data_style['scaleNameId']!="" )
	{	
		$query2='Select * from "tbl_invScaleName" where "scaleId"='.$data_style['scaleNameId'];
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
			$data_mainSize[]=$row2;
		}//---------------------------data_mainSize-----------------------------
		pg_free_result($result2);
		
		$query2='Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
		if(!($result2=pg_query($connection,$query2))){
			print("Failed OptionQuery: " . pg_last_error($connection));
			exit;
		}
		while($row2 = pg_fetch_array($result2)){
			$data_opt1Size[]=$row2;
			}//------------------------data_opt1Size----------------------------
		//echo("<pre>");print_r($data_opt1Size);
		pg_free_result($result2);
		
		$query2='Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
		if(!($result2=pg_query($connection,$query2)))
		{
			print("Failed OptionQuery: " . pg_last_error($connection));
			exit;
		}
		while($row2 = pg_fetch_array($result2))
		{
			$data_opt2Size[]=$row2;
		}
		pg_free_result($result2);
	        
	     

	   $sql='select "room","row","rack","shelf","box" from "tbl_invStorage" where "styleId"='.$_GET['styleId'];
	   
       if($_GET['colorId']>0)
       {
      	$sql.=' and "colorId"='.$_GET['colorId'];       
       }
       else if(count($data_color)>0)
       {
 		$sql.=' and "colorId"='.$data_color[0]['colorId'];       
       }
       if(isset($_GET['boxId']))
	   {
	   	$sql.=' and "box"='."'".$_GET['boxId']."'";
	   }
	   if(!($result_cnt9=pg_query($connection,$sql)))
	   {
			print("Failed InvData: " . pg_last_error($connection));
			exit;
	   }
	   while($row_cnt9 = pg_fetch_array($result_cnt9))
	   {
			$data_storage[]=$row_cnt9;
	   }
	   //echo "<pre>"; echo($data_storage[0]['row']);


	   

	   // // foreach ($data_storage[0] as $key => $value) {
	   // // 	print_r($value."<br>");
	   	
	   // // }
	   //exit();




	  //  $sql = 'select "row" , "rack" from "tbl_invStorage" where "box"='."'".$_GET['boxId']."'";
	  //  if(!($result_cnt9=pg_query($connection,$sql)))
	  //  {
			// print("Failed InvData: " . pg_last_error($connection));
			// exit;
	  //  }
	  //  while($row_cnt9 = pg_fetch_array($result_cnt9))
	  //  {
			// $data_storage[]=$row_cnt9;
	  //  }
	  //  echo "<pre>"; print_r($data_storage);
	  // exit();

	pg_free_result($result_cnt9); 
	}

	//echo "<pre>"; print_r($data_opt1Size);

	$totalScale = count($data_mainSize);
	$tableWidth = 0;

	$tableWidth = $totalScale * 100;

	$sql='select "inventoryId",quantity,"newQty","isStorage" from "tbl_inventory"';
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed invQuery: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_inv[]=$row;
	}
	// echo "<pre>";var_dump($data_inv);
	//exit();
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
		$query='select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
	        if(isset($_GET['boxId'])&&$_GET['boxId']!='0'){
	        $query.=',st."wareHouseQty" as st_quantity ';
	        }
	       
	         $query.=',inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
	        if(isset($_GET['boxId'])&&$_GET['boxId']!='0'){
	        $query.=' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
	        }
	        $query.=' where inv."styleId"='.$data_style['styleId'].' and inv."isActive"=1'.$search.' order by "inventoryId"';
		}
		else
		{
			$clrId = $data_color[0]['colorId'];
			$query='select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$data_color[0]['colorId'].'  and "isActive"=1 order by "inventoryId"';
		}	
	 // echo $query;      
		if(!($result=pg_query($connection,$query))){
			print("Failed invQuery: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result))
		{
			$data_inv[]=$row;
		}
		pg_free_result($result);
		 if(count($data_inv)>0)
		 {
			 for($l=0;$l<count($data_inv);$l++)
			 {
				 if(isset($data_inv[$l]['st_quantity'])&&$data_inv[$l]['st_quantity']!='')
				 {
				 $data_inv[$l]['quantity']=$data_inv[$l]['st_quantity'];    
				 }
			 }
		 }
	        
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
$sql = '';
$sql = 'SELECT * FROM "clientDB" where "ID"=' . $data_style['clientId'];
if (!($resultClient = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
$row = pg_fetch_array($resultClient);
$data_client = $row;

$exp = explode('_',$_GET['unitId']);
$keyAllLoc = preg_replace('/\d/', '', $exp[1] );
if($keyAllLoc == 'CV'){
   $typeBox = 'conveyor';
} elseif ($keyAllLoc == 'C'){
    $typeBox = 'container';
} else {
    $typeBox = 'warehouse';
}
$query = 'select * from "tbl_invStorage" WHERE unit=' . "'" . $_GET['unitId'] . "'";

if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($rowProduct = pg_fetch_array($resultProduct)) {
    $data_product[] = $rowProduct;
}
pg_free_result($rowProduct);
if(count($data_product) > 0) {
    $key_product = 0;
    foreach ($data_product as $dk => $dv) {
        if ($dv['updatedDate'] > $latest) {
            $latest = $dv['updatedDate'];
            $key_product = $dk;
        }
    }
    $sql = '';
    $sql = 'SELECT * FROM "employeeDB" where "employeeID"=' . $data_product[$key_product]['updatedBy'];
    if (!($resultClient = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($resultClient);
    $data_employee = $row;
} else {
    $data_employee = '';
    $latest = 0;
}
/*echo '<pre>';
print_r($data_employee);
exit();*/
	
?>
<body>
<br><br><br><br>
	
	<br><br>
<div class="wrapper">
	<div class="top-table">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 30%">Style #: <?php echo $data_style['styleNumber']; ?></td>
				<td style="width: 35%">Employee:<?php echo $data_employee['firstname'] . ' ' . $data_employee['lastname']; ?></td>
				<td style="width: 35%">Date Entered: <?php echo ($latest != '0')?date("F j, Y, g:i a", $latest):''; ?></td>
			</tr>
			<tr>
				<td style="width: 30%">Garment Type: <?php echo $data_garment["garmentName"]; ?></td>
				<td style="width: 35%">Color: <?php echo $_GET['colorId']; ?> </td>
				<td style="width: 35%">Gender :<?php echo (" ".$data_style['sex']); ?></td>
			</tr>
			<tr>
				<td>Client: <?php echo $data_client['client']; ?></td>
				<td colspan="2">Location: <?php echo $_GET['location']; ?></td>
			</tr>
			<tr>
                <?php
                    if ($typeBox == 'warehouse' || $typeBox == 'container'){
                        ?>
                        <td style="width: 30%">Box#: <?php echo $_GET['unitId']; ?></td>
                <?php
                    } else {
                ?>
                        <td style="width: 30%">Slot:  <?php echo $_GET['unitId']; ?></td>
                <?php
                    }
                ?>

				<td colspan="2" style="width: 70%">
					<table width="100%">
						<tr>
                            <?php
                                if($typeBox == 'warehouse') {
                                    ?>
                                    <td>
                                        Row: <?php echo $data_storage[0]['row'] == '' ? 'nil' : $data_storage[0]['row'] ?></td>
                                    <td>
                                        Rack: <?php echo $data_storage[0]['rack'] == '' ? 'nil' : $data_storage[0]['rack'] ?></td>
                                    <td>
                                        Shelf: <?php echo $data_storage[0]['shelf'] == '' ? 'nil' : $data_storage[0]['shelf'] ?></td>
                                    <?php
                                }
                            ?>
						</tr>	
					</table>

				</td>
				
			</tr>
			<tr>
				<td style="width: 30%"></td>
				<td style="width: 35%"></td>
				<td style="width: 35%"></td>
			</tr>
		</table>
	</div>
	<br><br>
	

		<?php


			// $pdf = new FPDF();
			// $pdf->AddPage();
			// $pdf->SetFont('Arial','B',16);
			// $pdf->Cell(40,10,'Hello World!');
			// $pdf->Output();

		
			$x_limit = sizeof($data_mainSize);
			$y_limit = sizeof($data_opt1Size);
			$data = array();
			$row_head = array();
			$col_head = array();

			foreach($data_mainSize as $j=>$each_data_main)
			{
				$row_head[$j] = $each_data_main['scaleSize'];
			}
			foreach($data_opt1Size as $i=>$each_data_op1)
			{
				$col_head[$i] = $each_data_op1['opt1Size'];	
			}
			$all_data = explode(",",$_GET['all_data']);
			$cnt = 0;

			for($i=0 ; $i<count($data_opt1Size) ; $i++)
			{
				for($j=0 ; $j<count($data_mainSize) ; $j++)
				{
					$data[$i][$j] = $all_data[$cnt++];
				}
			}

			//echo"<pre>";var_dump($data);
		?>

		<br><br>

		<div class="common-table">
		<div class="common-table">
		<table width="100%" cellpadding="0" cellspacing="0">

		<?php

			for($i=0 ; $i<= count($col_head); $i++)
			{
				
				for($j=0 ; $j<= count($row_head); $j++)
				{
					
					
					if($i == 0)
					{
						if($j==0)
						{
							echo("<tr><th></th>");
						}
						
							echo("<th>".$row_head[$j]."</th>");
						
					}
					else 
					{
						if($j==0)
						{
							echo "<tr>";
							echo ("<td>".$col_head[$i-1]."</td>");
						}
						else
						{
							echo ("<td>".$data[$i-1][$j-1]."</td>");
						}
					}
					if($j == sizeof($row_head))
							echo "</tr>";
				}
			}
			?>
			</tr>
		</table>

		<br><br>
		<button id="print_btn" onclick="print_me()" >Print</button>

		<br>
		<br>
			

	</div>






	<br><br><br><br>
	
	<br><br>
</div>	

<script type="text/javascript">




	function print_me()
	{
        //Get the print button and put it into a variable
        var printButton = document.getElementById("print_btn");
        //Set the print button visibility to 'hidden'
        printButton.style.visibility = 'hidden';
        //Print the page content
        window.print()
        //Set the print button to 'visible' again
        //[Delete this line if you want it to stay hidden after printing]
        printButton.style.visibility = 'visible';
	}

	function QtyDblClick(inventoryId)
	{

		$(location).attr('href',"storage.php?styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value+"&invId="+inventoryId+"<?php if(isset($_REQUEST['boxId']) && $_REQUEST['boxId']!='') echo '&boxId='.$_REQUEST['boxId'];?>");
	}
	function StoreInitialValues(locIndex,rowIndex,txtValue, newQty)
	{
		var td = document.getElementById('hdnVar');
		var element1 = document.createElement("input");
		element1.type = "hidden";
		element1.name = "hdnqty["+locIndex+"]["+rowIndex+"][]";
		element1.value = txtValue;
		var element2 = document.createElement("input");
		element2.type = "hidden";
		element2.name = "hdnNewQty["+locIndex+"]["+rowIndex+"][]";
		element2.value = newQty;
		td.appendChild(element1);
		td.appendChild(element2);
		
	}
	function AddQty(trId,type,cellId,locIndex,rowIndex,qty,invIdValue)
	{
		//alert(qty);
		//alert(invIdValue);
		switch(type)
		{
			case 'qty':
			{
				var tr = document.getElementById(trId);		
				var cell = tr.insertCell(cellId);
				var txtBox = document.createElement("input");
				cell.className = 'gridHeaderReportGrids';
				txtBox.type = "text";
				txtBox.name = "qty["+locIndex+"]["+rowIndex+"][]";
				txtBox.className = "txBxGrey";			
				txtBox.value = qty ;
				cell.appendChild(txtBox);
				
				txtBox = document.createElement("input");			
				txtBox.type = "hidden";
				txtBox.name = "invId["+locIndex+"]["+rowIndex+"][]";
				txtBox.value = invIdValue;
				cell.appendChild(txtBox);	
				if(invIdValue > 0 && qty > 0)
				{
					a = document.createElement("a");
					a.setAttribute("href", "#");
					img = document.createElement("img");
					img.width="15px";
					img. height="14px";
					img.className = "imgRght";
					img.src="<?php echo $mydirectory;?>/images/Btn_edit.gif";				
					img.setAttribute("onclick",'QtyDblClick('+invIdValue+');');						
					a.appendChild(img);
					cell.appendChild(a);
				}
				break;
			}
			case 'qtyDummy':
			{
				var trd = document.getElementById('qtyDummy'+locIndex);
				var cell = trd.insertCell(cellId);
				cell.className = 'gridHeaderReportGrids2';
				cell.innerHTML="&nbsp;";
				
				break;
			}
		}
	}
	function AddRow(type,cellId,value)
{
	switch(type)
	{
		case 'main':
		{
			var trTop = document.getElementById('mainSizeTop');
			var trBottom = document.getElementById('mainSizeBottom');
			var tr2Bottom = document.getElementById('adjBottom');
			var cell = trTop.insertCell(cellId);
			cell.className = 'gridHeaderReport';
			cell.innerHTML=value;
			cell = trBottom.insertCell(cellId);
			cell.className = 'gridHeaderReport';
			cell.innerHTML=value;			
			cell = tr2Bottom.insertCell(cellId);
			cell.className = 'txBxWhite';
			var txtBox = document.createElement("input");
			txtBox.type = "text";	
			txtBox.className = "txBxWhite";			
			txtBox.value = "";
			cell.appendChild(txtBox);
			break;
		}
		case 'price':
		{
			var trPrice = document.getElementById('priceTop');
			var cell = trPrice.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			var txtBox = document.createElement("input");
			txtBox.type = "text";	
			txtBox.className = "txBxWhite";
			txtBox.name = 'price[]';
			txtBox.value = value;
			cell.appendChild(txtBox);
			break;
		}		
		case 'column':
		{
			var trc = document.getElementById('columnSize');
			var cell = trc.insertCell(cellId);
			cell.className = 'gridHeaderReportBlue';
			cell.innerHTML=value;
			break;
		}
		case 'dummy':
		{	
			var trd = document.getElementById('dummy1');
			var cell = trd.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			cell.innerHTML=value;
			
			trd = document.getElementById('dummy2');
			cell = trd.insertCell(cellId);
			cell.className = 'gridHeaderReportGrids2';
			cell.innerHTML=value;
			break;
		}		
	}	
}
</script>
</body>
</html>