<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$styleId=$_REQUEST['styleId'];
$colorId=$_REQUEST['colorId'];
$boxId=$_REQUEST['boxId'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Global Uniform Sourcing Internal Intranet</title>
<link rel="stylesheet" type="text/css" href="<?php echo $mydirectory;?>/style.css" media="all"/>
<head>
    <script type="text/JavaScript" src="<?php echo $mydirectory;?>/js/tabcontent.js"></script>
    <script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>

    <link href="<?php echo $mydirectory;?>/tabcontent.css" rel="stylesheet" type="text/css" />

    <!--Styles needed for the LightBoxPopup -->
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
    <!--Styles needed for the ShowHide Feature of the Storage Forms -->
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


</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<table align="center">
    <tr>
        <td>
            Room:
        </td>
        <td>
            <input type="text" name="room" id="room"/>
        </td>
    </tr>
    <tr>
        <td>
            Row:
        </td>
        <td>
            <input type="text" name="row" id="row"/>
        </td>
    </tr>
    <tr>
        <td>
            Rack:
        </td>
        <td>
            <input type="text" name="rack" id="rack"/>
        </td>
    </tr>
    <tr>
        <td>
            Shelf:
        </td>
        <td>
            <input type="text" name="shelf" id="self"/>
        </td>
    </tr>
    <tr>
        <td>
            <button type="button" onclick="save()" >Save</button>
        </td>
        <td>
            <button type="button" onclick="cancel()">Cancel</button>
        </td>
    </tr>
</table>
<script type="application/javascript">
    function save() {
        var room = $('#room').val();
        if(room == '') {
            alert("Please Provide a Room");
            return false;
        }
        var rack = $('#rack').val();
        if(rack == '') {
            alert("Please Provide a rack");
            return false;
        }
        var row = $('#row').val();
        if(row == '') {
            alert("Please Provide a row");
            return false;
        }
        var self = $('#self').val();
        if(self == '') {
            alert("Please Provide a self");
            return false;
        }
        $.ajax({
           url: 'editRoom.php',
            type:"post",
            data: {
               room: room,
                rack: rack,
                row: row,
                self: self,
                boxId: "<?php echo $boxId; ?>",
                styleId: "<?php echo $styleId; ?>"
            },
            success: function (response) {
               if(response == 1) {
                    alert("Updated");
                    window.location = "<?php echo $mydirectory;?>/admin/inventory/reportViewEdit.php?styleId=<?php echo $styleId; ?>&colorId=<?php echo $colorId; ?>&boxId=<?php echo $boxId; ?>"
                } else {
                    alert("Not Updated! Please Try Again After Some Time");
                    $(location).attr('href',"reportViewEdit.php?styleId=<?php echo $styleId; ?>&colorId=<?php echo $colorId; ?>&boxId=<?php echo $boxId; ?>");
                }
            }
        });
    }
    function cancel() {
        $(location).attr('href',"reportViewEdit.php?styleId=<?php echo $styleId; ?>&colorId=<?php echo $colorId; ?>&boxId=<?php echo $boxId; ?>");
    }
</script>
</body>
</html>