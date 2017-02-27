<?php
require('Application.php');
require('../../header.php');
$target_dir = "../../uploadFiles/client/";
extract($_POST);
$id=$_POST['id'];
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$id'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}		 
$query2=('SELECT "styleId", "styleNumber" '.
		 "FROM \"tbl_invStyle\" ".
		 "WHERE \"clientId\" = '$id' AND \"isActive\" = 1 ");
if(!($result1=pg_query($connection,$query2))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data2[]=$row1;
}
?>		 
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>	
<script type="text/javascript">
$('document').ready(function(){
$('#styleNumber').change(function(){
 var u = '<?php echo $server_URL; ?>/content/inventory/styleAdd.php?ID='; 
 $('#url').val(u + $(this).val()+'&type=e');
 makeCode();
});

});
function Print()
    {
		$.get( "getboxnum.php", function( data ) {
		  //$( "#boxnum" ).val( data );
		  $("#style").html(data);
		  //alert($('#label').html());
		  Popup($('#label').html());
		});
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'label', 'height=400,width=600');
        mywindow.document.write('<html><head><title></title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>	 
<font face="arial">
<center>
<p>
<b>Client:&nbsp;&nbsp;</b>
<input type="text" name="client" size="30" value="<?php echo $data1[0]['client'];?>">
<br><br>
 <b><font face="arial">Style Number  : </font></b>
  <select name="styleNumber" id="styleNumber">
      <?php for($i=0; $i < count($data2); $i++){?>
		  <option value="<?php echo $data2[$i]['styleId'];?>"><?php echo $data2[$i]['styleNumber'];?></option>
      <?php } ?>
                </select>

<br><br>
<b><font face="arial">Logo  : </font></b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($data1[0]['logo']!='')
{
	echo '<img src="'.$target_dir.$data1[0]['logo'].'" width="150px">';
}?>
<br><br>
<input type="button" name="" value="Create Label" onClick="javascript: Print();"><br><br>
<div style="display:none;">
<div id="label" style="margin:auto;">
<div style=" float:left; width:50%; height:auto;"><img width="100%" src="<?php echo $target_dir.$data1[0]['logo'];?>"></div>
<input id="url" type="hidden" value="<?php echo $server_URL; ?>/content/inventory/styleAdd.php?ID=<?php echo $data2[0]['styleId'] ?>&type=e" style="width:80%" /><br />
<input id="boxnum" type="hidden" value="" />
<div id="qrcode" style=" width:200px; height:200px; float:right; "></div>
<div id="style" style=" width:90%; height: auto; font-size:200px; padding:1%; margin:auto; text-align:center; clear: both;">
	</div>
</div>
</div>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/qrcodejs/qrcode.js"></script>
<script type="text/javascript">

var qrcode = new QRCode("qrcode");

function makeCode () {      
    var elText = document.getElementById("url");
    
    if (!elText.value) {
        alert("Input a text");
        elText.focus();
        return;
    }
    
    qrcode.makeCode(elText.value);
}

makeCode();
</script></div>

<?php require('../../trailer.php'); ?>

