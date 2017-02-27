<?php
require('Application.php');
session_start();
define('SQL_PASS', '#EDC$RFV');
$message = "";
$sql = "";
$error = 0;
$attribute = "";
if(isset($_POST['key_submit']) && $_POST['key_submit'] == 'Submit')
{
	if(isset($_POST['key']) && $_POST['key'] == SQL_PASS)
	{
		$_SESSION['sql_run'] = trim($_POST['key']);
	}
	else
	{
		if(isset($_SESSION['sql_run']))
			unset($_SESSION['sql_run']);
		$attribute = 'style="background-color:#E6E197;height=70px;"';
		$message = "Please provide valid Security key !";
	}
}
else if(isset($_POST['run']) && $_POST['run'] == 'Run SQL' && $_SESSION['sql_run'] == SQL_PASS)
{
	extract($_POST);
	if($sql != "")
	{
		if(!($result= pg_query($connection,$sql)))
		{
			$error = 1;
			$attribute = 'style="background-color:#E6E197;height=70px;"';
			$message = "<strong>Error:</strong><br />".pg_last_error();
		}
		pg_free_result($result);
		if($error == 0)
		{
			$attribute = 'style="background-color:#ADE2AD;height=70px;"';
			$message = "Database Updation was successfully Done.!";
		}
	}
	if($error == 0 && $message == "")
	{
		$attribute = 'style="background-color:#ADE2AD;height=70px;"';
		$message = "Please add SQL commands on text area to update the Database.";
	}
	
}
else if(isset($_POST['export']) && $_POST['export'] == 'Export DB as SQL' && $_SESSION['sql_run'] == SQL_PASS)
{
	//exec("export PGPASSWORD={$db_pass} && export PGUSER={$db_uname} && pg_dump -h {$db_server} {$db_name} -CdiOv > ./{$db_name}_backup.sql && unset PGPASSWORD && unset PGUSER");
	$DUMPALL='/usr/bin/pg_dumpall';
	$PGDUMP='/usr/bin/pg_dump'; 
	
	$command = "{$PGDUMP} -d -O -Fp {$db_name} -U {$db_uname} -f  ./{$db_name}_backup.sql";	
	exec($command);
	$message = "Export Complete!";
	$attribute = 'style="background-color:#ADE2AD;height=70px;"';
	
	if ($fd = fopen ("{$db_name}_backup.sql", "r")) 
	{
		$fsize = filesize("{$db_name}_backup.sql");
		$path_parts = pathinfo("{$db_name}_backup.sql");
		$ext = strtolower($path_parts["extension"]);
		switch ($ext) {
			case "pdf":
			header("Content-type: application/pdf"); // add here more headers for diff. extensions
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
			break;
			case "pptx":
			header("Content-type: application/pptx"); // add here more headers for diff. extensions
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
			break;
			default;
			header("Content-type: application/octet-stream");
			header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		}
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
		fclose ($fd);
	}
	else
	{
		$attribute = 'style="background-color:#E6E197;height=70px;"';
		$message = "Error during export. Please contact system admin";
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SQL Update</title>
</head>

<body>
<div style="width:700px;height:700px;">
<fieldset>
<div align="center" <?php echo $attribute; ?>>
<?php if($message != "") echo '<p align="left">'.$message.'</p>';?>
&nbsp;</div>
<form action="run_sql.php" method="post">
<?php
if(isset($_SESSION['sql_run']) && $_SESSION['sql_run'] == SQL_PASS)
{
	?>
<div align="right"><input type="submit" name="export" value="Export DB as SQL" /></div>
<div style="width:50%;" align="left"><strong>SQL Commands:</strong></div>
<div><textarea name="sql" cols="100" rows="30"><?php echo $sql; ?></textarea></div>
<div><input type="submit" name="run" value="Run SQL" /><input type="reset" value="cancel" /></div>
<?php
}
else
{
?>
	<table width="600" border="0" cellspacing="0" cellpadding="0">  
  <tr>
    <td align="right"><strong>Security Code: </strong></td>
    <td width="5px">&nbsp;</td>
    <td><input type="password" name="key" value=""/></td>
  </tr>
  <tr>
  <td>&nbsp;</td>
  <td width="5px">&nbsp;</td>
    <td><input type="submit" name="key_submit" value="Submit" /><input type="reset" value="cancel" /></td>
  </tr>
</table>
<?php
}
?>
</form>
</fieldset>
</div>
</body>
</html>