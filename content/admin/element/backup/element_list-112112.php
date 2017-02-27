<?php
require('Application.php');
$search_uri = "";
if (isset($_GET['paging']) && $_GET['paging'] != "") {
    $paging .= $_GET['paging'];
} else {
    $paging .= 1;
}
if(isset($_GET['element_id']))
{
    $sql="Delete from tbl_element_pack_main where pack_id= ".$_GET['element_id'].";";
	$sql.="Delete from tbl_element_package where pack_id = ".$_GET['element_id'];
       // echo $sql;
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed delete_quote: " . pg_last_error($connection));
		exit;
	}
	header('location:element_list.php');
}
require('../../header.php');

include('../../pagination.class.php');

$where="";
if(isset($_GET['client'])&&$_GET['client']!="")
{
 if($where=="")
      $where.=" AND package.client=".$_GET['client']; 
 else
     $where.=" AND package.client=".$_GET['client']; 
}
if(isset($_GET['pack'])&&$_GET['pack']!="")
{
 if($where=="")
      $where.=" AND main.pack_id=".$_GET['pack']; 
 else
     $where.=" AND main.pack_id=".$_GET['pack']; 
}

if(isset($_GET['style'])&&$_GET['style']!="")
{
 if($where=="")
      $where.=" AND package.style like '%".$_GET['style']."%'"; 
 else
     $where.=" AND package.style like '%".$_GET['style']."%' "; 
}

//$query= 'Select main.*,elm.client from "tbl_element_pack_main" as main left join tbl_element_package as elm on elm.pack_id=main.pack_id';
$query= 'Select distinct main.*,(select client.client from tbl_element_package as pack left join "clientDB" as client '.
 ' on client."ID"=pack.client  '.
 ' where pack_id= main.pack_id limit 1) as client_name from "tbl_element_pack_main" as main inner join tbl_element_package as package on package.pack_id=main.pack_id ';
$query.= ' where package.pack_id=main.pack_id '; 
if($where!="")
    $query.= $where;

      $query.=' order by main.pack_id';   

//echo $query;
if(!($result=pg_query($connection,$query))){
	print("Failed quote: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$datalist[]=$row;
}
pg_free_result($result);

$sql = 'select pack_id,pack_name from "tbl_element_pack_main"';
  
   // echo  $sql;
    if (!($result1 = pg_query($connection, $sql))) {
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($rowp = pg_fetch_array($result1)) {
        $packlist[] = $rowp;
    }
    pg_free_result($result1);
    
    
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

?>
<?php
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Element Package</font><br/><br/>";
echo "</blockquote>";
echo "</font>";
?>
<table width="100%"> 
    <tr>
        <td align="left" valign="top"><center>
        <table width="100%">
            <tr>
                <td align="center" valign="top"><font size="5"><br>
                    <table width="80%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>&nbsp;</td>
                            <td align="center"><input type="button" value="Add New Element" onclick="location.href='element_add.php';" /></td>    
                        </tr>
                    </table>
                    <br>
                    </font>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td width="10">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                        </tr>
                   <tr><td colspn="3"><h3>Search Packages</h3></td></tr> 
                   
 <tr class="grid001"><td>Client: </td><td >Style Number:</td><td colspan="2">Package Name:</td></tr>                  
                   <tr class="grid001">
             <td><select width="20px" id="client" name="client[]" onchange="javascript:viewPackageName($(this).val());">
<option value="" selected="selected">--Select--</option>        
<?php for($i=0; $i < count($client); $i++){
if(isset($_GET['client'])&& $_GET['client']==$client[$i]['ID']) 
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
  
  <td><input type="text" onchange="javascript:viewPackageNameStyle($(this).val());" id="style_number" value="<?php 
  if(isset($_GET['style'])) 
      echo $_GET['style'];
                       ?>"/></td>
                       <td colspan="2"><?php
                   echo '<select   id="pack_list" >'.
'<option value="0">--Select--</option>';
for($i=0;$i<count($packlist);$i++)
{
echo '<option value="'.$packlist[$i]['pack_id'].'"';
       if(isset($_GET['pack'])&& $_GET['pack']==$packlist[$i]['pack_id']) 
         echo ' selected="selected" ';   
     echo '>';

     echo $packlist[$i]['pack_name'].'</option>';   
 }

 echo '</select>';
                   ?></td>
 
   </tr>     
        
   <tr><td colspan="4" align="center"><input type="button" value="Search" onclick="javascript:search();"/>
           <input type="button" value="Cancel" onclick="javascript:CancelSearch();"/>
       </td></tr>     
   
    <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td width="10">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="80%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                              <td class="gridHeader">Client</td> 
                        	<td class="gridHeader">Package Name</td> 
                                <td class="gridHeader">Edit</td> 
                                <td class="gridHeader">Delete</td> 
                            
                          
                            </tr>

        <?php
			  if(count($datalist) > 0)
			  {
				  for($i = 0; $i < count($datalist); $i++)
				  {
			  ?>
                                <tr>
                                    <td class="grid001"><?php echo $datalist[$i]['client_name'];?></td>
                                    <td class="grid001"><?php echo $datalist[$i]['pack_name'];?></td>
                                   
                                    
                                    <td class="grid001"><a href="element_add.php?element_id=<?php echo $datalist[$i]['pack_id'];?>"><img src="<?php echo $mydirectory;?>/images/edit.png" width="24" height="24" alt="edit" /></a></td>
                                     <td class="grid001"><a href="element_list.php?element_id=<?php echo $datalist[$i]['pack_id'];?>"><img src="<?php echo $mydirectory;?>/images/deact.gif" width="24" height="24" alt="edit" /></a></td>
                                </tr>              
        <?php
				  }
				  echo 	'<tr>
			<td width="100%" class="grid001" colspan="7"></td>			
		  </tr>';
			  }
			  else
			  {
				  echo '<tr><td colspan="7" class="grid001">No Quotes found</td><tr>';
			  }
			 ?>       
                        <tr>
                          <td colspan="5">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p>
    </center></td>
</tr>
</table>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery.min.js"></script>
<script type="text/javascript">
 function  search()
 {
data="";
if($("#client").val()!="")
    {
    if(data=="")
      data+='?client='+$("#client").val();  
    else
      data+='&client='+$("#client").val();    
    }
    
 if($.trim($("#style_number").val())!="")
    {
    if(data=="")
      data+='?style='+$.trim($("#style_number").val());  
    else
      data+='&style='+$.trim($("#style_number").val());    
    }   
     
    if($("#pack_list").val()!=0)
    {
    if(data=="")
      data+='?pack='+$("#pack_list").val();  
    else
      data+='&pack='+$("#pack_list").val();    
    }
    
    if(data!="")
    location.href='element_list.php'+data;
else
    location.href='element_list.php';
 }
 
 function CancelSearch()
 {
  $("#pack_list").val(0);   
  $("#style_number").val("");
  $("#client").val("");
  location.href='element_list.php';
 }
 
 function viewPackageName(client)
 {
     data='client='+client;
       $.ajax({
            type: "POST",
            url: "getSerchContents.php?opt=client",
            data: data,
            success:function(data)
            {
         $("#pack_list").html(data);    
            }
        });
 }
 
  function viewPackageNameStyle(style)
 {
     data='style='+style;
       $.ajax({
            type: "POST",
            url: "getSerchContents.php?opt=style",
            data: data,
            success:function(data)
            {
         $("#pack_list").html(data);    
            }
        });
 }
 
    </script>

<?php
require('../../trailer.php');
?>