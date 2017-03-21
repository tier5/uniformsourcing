<!DOCTYPE html>
<html>
<head>
	<title>Warehouse | form</title>

<style>
		.black_overlay{
			display: none;
			position: absolute;
			top: 0%;
			left: 0%;
			width: 100%;
			height: 100%;
			background-color:#000;
			z-index:1001;
			-moz-opacity: 0.8;
			opacity:.80;
			filter: alpha(opacity=80);
		}
		.white_content {
			display: none;
			position: absolute;
			top: 25%;
			left: 25%;
			width: 50%;
			height: 300px;
			padding: 16px;
			border: 16px solid grey;
			background-color: white;
			z-index:1002;
			overflow: scroll;
			text-align:center;
		}
</style>	
</head>
<script language="javascript" type="text/javascript">
function showHideDiv(objectId)    {
var divstyle = new String();
divstyle = document.getElementById(objectId).style.display;
if(divstyle.toLowerCase()=="none" || divstyle == "")
{document.getElementById(objectId).style.display = "block";}
 else{document.getElementById(objectId).style.display = "none";}
}

function showHideMB(objectId)    {
var divstyle = new String();
divstyle = document.getElementById(objectId).style.display;
if(divstyle.toLowerCase()=="block" || divstyle == "")
{document.getElementById(objectId).style.display = "none";}
else{document.getElementById(objectId).style.display = "block";}
}
 </script>
<body>


<div class="container" class="col-md-10">
	<div class="col-md-8" style="padding: 30px">

		<label>Location : </label>
		<select>
			<option>--All Locations--</option>
			<option>Location1-- LC1</option>
			<option>Location2-- LC2</option>
			<option>Location3-- LC3</option>
		</select>
		
	</div>
	<div class="col-md-8" style="padding: 30px">
		
	<label>Warehouse : </label>
		<select>
			<option>--All Warehouse--</option>
			<option>Warehouse1--WH1</option>
			<option>Warehouse1--WH2</option>
			<option>Warehouse1--WH3</option>
		</select>

	</div>
	<div>
		
	<label>Row : </label>
		<input type="input" name="row_">

	<label>Room :</label>
		<input type="input" name="room_">

	<label>Rack :</label>
		<input type="input" name="rack_">

	<label>Shelf :</label>
		<input type="input" name="shelf_">

	<label>Box: </label>
		<input type="input" name="box_">
	</div>
</div>


</body>
</html>