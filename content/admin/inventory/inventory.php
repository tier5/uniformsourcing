<?php require('Application.php');
	require('../../header.php'); ?>
<table width="100%">
        <tr>
          <td align="center"><font face="arial">
            <center><font size="5">INVENTORY</font><br />
            <br />
            <br />
            <table width="50%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <?php if(isset($_SESSION['employeeType']) && $_SESSION['employeeType']<4){ ?>
                <td><a href="database.php"><img src="<?php echo $mydirectory;?>/images/database.jpg" alt="dtab" width="165" height="99" border="0" /></a></td>  
                <td><a href="styleAdd.php"><img src="<?php echo $mydirectory;?>/images/newInventory.jpg" alt="invtry" width="165" height="99" border="0" /></a></td>
                <?php }?>
                <td><a href="reports.php"><img src="<?php echo $mydirectory;?>/images/reports.jpg" alt="rprts" width="165" height="99" border="0" /></a></td>
                <td><a href="location.php"><img src="<?php echo $mydirectory;?>/images/Locations.jpg" alt="location" width="165" height="99" border="0"></a></td>
              </tr>         
          </table></td>
        </tr>
      </table>
     <?php  require('../../trailer.php');
?>