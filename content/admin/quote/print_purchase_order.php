<?php
require('Application.php');
extract($_POST);
$body = $_SESSION['emailBody'];
?>
<html>
<body>
<?php echo $body;?>
</body>
<script type="text/javascript">
window.print();
</script>
</html>
