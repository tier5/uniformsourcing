<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
echo "<title>$compname Internal Intranet</title>";
echo "<head>";
$page="";
$page=substr(strrchr($_SERVER['PHP_SELF'],'/'),1,strlen(strrchr($_SERVER['PHP_SELF'],'/')));
//print '<pre>';print_r($page);print '</pre>';die();
switch($page)
{
	case "samplerequest.list.php":
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/jquery-ui.css\" media=\"all\"></link>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/jquery-ui-1.8.2.css\" media=\"all\"></link>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/lightbox-form.css\" media=\"all\"></link>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/ui.theme.css\" media=\"all\"></link>";	
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min-1.4.2.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min-1.8.2.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.bgiframe-2.1.1.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/samplerequest.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/projectadd.js"></script>';
		break;
	}
	case "reportViewEdit.php":
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/invReport.css\" media=\"all\"></link>";
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/dw_event.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/dw_scroll.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/scroll_controls.js"></script>';
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';		
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/lightbox-form.css\" media=\"all\"></link>";
		break;
	}
	case "project.list.php":
	case "project_purchase.list.php":
	case "project_mgm.list.php":
	case "project_mgm.closed.php":
	case "project_purchase.closed.php":
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/projectPopStyle.css\" media=\"all\"></link>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/projectStyle.css\" media=\"all\"></link>";
		echo '<script type="text/javascript" src="'.$mydirectory.'/js/tablesort.project.js"></script>';
		break;
	}
	case "project.add.php":
	case "project.edit.php":
	case "samplerequest.add.php":
	case "samplerequest.edit.php":
	case "samplerequest_new.add.php":
	case "add_quote.php":
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/projectPopStyle.css\" media=\"all\"></link>";
		break;
	}
	case "project_mgm.add.php":
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/projectPopStyle.css\" media=\"all\"></link>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/tabcontent.css\" media=\"all\"></link>";
		break;
	}
	case "itemList.php":
	{
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/popupStyle.css\" media=\"all\"></link>";
		break;
	}
}
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/style.css\" media=\"all\"></link>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/jquery-ui.css\" media=\"all\"></link>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$mydirectory/js/jquery-ui-1.8.2.css\" media=\"all\"></link>";
echo "</head>";
echo "<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>";
echo "<table height=\"100%\" width=\"100%\" border=0 cellpadding=0 cellspacing=0>";
echo "<tr>";
echo "<td height=\"79\" colspan=\"2\" background=\"$mydirectory/images/bg3.gif\">";
echo "<table border=0 cellpadding=0 cellspacing=0>";
echo "<tr>";
echo "<td><img src=\"$mydirectory/images/logo.gif\" width=\"425\" height=\"79\" border=0></td><td>";
//<!---------------------------- top --------------------------------------->
$querytime=("SELECT * ".
		 "FROM \"timeclock\" ".
		 "WHERE \"firstname\" = '$_SESSION[firstname]' AND \"lastname\" = '$_SESSION[lastname]' AND \"status\" = 'in'");
if(!($resulttime=pg_query($connection,$querytime))){
	print("Failed querytime: " . pg_last_error($connection));
	exit;
}
while($rowtime=pg_fetch_array($resulttime)) {
	$datatime[]=$rowtime;
}
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" id=\"topitems\">";
echo "<tr>";
echo "<td>";
echo "<font face=\"Verdana\" size=\"+1\" color=\"000000\">";
$date=date("g:i A l, F jS, Y");
echo "<b>$date</b></font><br>";
echo "<font face=\"arial\" size=\"-1\">You are logged in as <b>". $_SESSION['firstname']." ". $_SESSION['lastname']."</b><br>";
if(count($datatime) != 0) {
	$timesnow=mktime();
	$times=$datatime[0]['clockin'];
	$times1=date("m/d/Y", $times);
	$times2=date("g:i A", $times);
	echo "You have been clocked in since <b>$times2</b> on <b>$times1</b><br>";
	if(bcsub("$timesnow", "$times") > 86400){
		echo "<font color=\"red\">You have been clocked in for more than 24 hours</font>";
	}
}else{
	echo "You are not clocked in.";
}
echo "</font>";
echo "</td>";
echo "</tr>";
echo "</table>";
//<!------------------------------ top end ------------------------------->
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</td>";
echo "</tr>";
echo "<tr>";

echo "<td width=\"200\" height=\"19\" align=\"center\" class=\"color2\"><div align=\"left\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Welcome to the Intranet</div></td>";
echo "</tr>";
echo "<tr><td colspan=\"2\">";
//<!-------------------------------- menu -------------------------------------->
echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
echo "<tr>";
  
 echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" 		            onClick=\"parent.location='$mydirectory/directory/directorytoc.php'\">Internal Directory</td>";
 echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\"   onClick=\"parent.location='$mydirectory/officecal/list_calendar.php'\">Office Calendar</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/accounting/'\">Accounting</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/production/productiontoc.php'\">Production</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='http://mail.i2net.com/sqwebmail'\">E-Mail</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/sales/'\">Sales</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/operations/'\">Operations</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/humanresources/'\">Human Resources</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/support/'\">Support</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\" onClick=\"parent.location='$mydirectory/admin/'\">Administration</td>";
  echo "<td class=\"menu\" onmouseover=\"this.className='menu_on';\" onmouseout=\"this.className='menu'\"  onclick=\"window.open('http://www.uniforms.net/', 'frame1');\">WWW Site</td></tr>";
 
echo "</table>";
//<!----------------------- menu end ---------------------------->
echo "</td></tr>";
echo "<tr>";
echo "<td height=\"12\" colspan=\"2\" background=\"$mydirectory/images/bg2.gif\"><img src=\"$mydirectory/images/bg2.gif\" width=\"2\" height=\"12\"  border=\"0\"></td>"; 
echo "</tr>";
echo "<tr>";
echo "<td height=\"*\" width=\"100%\" align=\"center\" valign=\"top\">";
echo "<table width=\"100%\"><tr>";
echo "<td align=\"center\"><a href=\"$mydirectory/index.php\"><img src=\"$mydirectory/images/top01.gif\" border=\"0\"></a></td>";
//<!----------------------------------------------------------- Clock IN-Out ---------------------------------------------->
if(count($datatime) == "0") {
	echo "<form action=\"$mydirectory/humanresources/timeclock/clockin.php\" method=\"POST\">";
	echo "<td align=\"center\">";
	echo "<input type=\"Hidden\" name=\"FirstName\" value=\"".$_SESSION['firstname']."\">";
	echo "<input type=\"Hidden\" name=\"LastName\" value=\"".$_SESSION['lastname']."\">";
	echo "<input type=\"image\" src=\"$mydirectory/images/clockin.gif\" value=\"Clock In\" border=\"0\">";
	echo "</td>";
	echo "</form>";
	echo "</cfoutput>";
}else{
	echo "<form action=\"$mydirectory/humanresources/timeclock/clockout.php\" method=\"POST\">";
	echo "<td align=\"center\">";
	echo "<input type=\"Hidden\" name=\"FirstName\" value=\"".$_SESSION['firstname']."\">";
	echo "<input type=\"Hidden\" name=\"LastName\" value=\"".$_SESSION['lastname']."\">";
	echo "<input type=\"image\" src=\"$mydirectory/images/clockout.gif\" value=\"Clock Out\" border=\"0\">";
	echo "</td>";
	echo "</form>";
}
//<!----------------------------------------------------------- Clock IN-Out end ---------------------------------------------->
echo "<td align=\"center\"><a href=\"$mydirectory/logout.php\"><img src=\"$mydirectory/images/top03.gif\" border=\"0\"></a></td>";
echo "<td align=\"center\"><a href=\"$mydirectory/help.php\"><img src=\"$mydirectory/images/top04.gif\" border=\"0\"></a></td>";
echo "</tr></table>";
echo "<table width=\"100%\"><tr><td>";
?>
