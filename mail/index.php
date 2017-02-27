<?php
$to        = 'To Email ID';
$message   = 'Type in message here';
$subject   = 'Test relay.i2net.com';
$error     = '';
$msg_class = 'style="color:red;font-weight: bold"';
if (isset($_POST['send']) && $_POST['send'] == 'Send')
{
    extract($_POST);
    if ($_POST['to'] != '' && $_POST['to'] != 'To Email ID')
    {
        if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $_POST['to']))
        {
            $msg_class = 'style="color:red;font-weight: bold"';
            $error     = "Please enter valid email address";
        }
    }
    else
    {
        $msg_class = 'style="color:red;font-weight: bold"';
        $error     = "Please enter email id";
    }
    if ($error == '')
    {
        require '../Application.php';
        require($PHPLIBDIR . 'mailfunctions.php');
        $headers = create_smtp_headers(trim($subject), "admin@uniformsourcing.com", trim($to), 'Web Admin', "", "text/html");
        $data    = $headers . "<html><body><p>" . nl2br($message) . "</p></body></html>";
        if ((send_smtp($mailServerAddress,"admin@uniformsourcing.com", trim($to), $data)) === false)
        {
            $msg_class = 'style="color:red;font-weight: bold"';
		global $last_output;
            $error     = "Unable to send email to $to $last_output<br>";
        }
        else
        {
            $msg_class = 'style="color:green;font-weight: bold"';
            $error     = 'Email send successfully';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <style type="text/css" media="all">
            body{
                font-family: serif Arial verdana;
            }
            div{
                font-family: verdana Arial;
                font-size: 16px;
            }
            fieldset{
                width:500px;
                padding: 20 10 10 10;
            }
            fieldset legend{
                font-size: 16px;
                font-weight: bold;
                color: #333333;
                padding-bottom: 5px;
            }
            input[type="text"]{
                width: 250px;
                color: #330033;
            }
            textarea{
                color: #330033;
            }
        </style>
    </head>
    <body>
        <div <?php echo $msg_class; ?>><?php echo $error; ?></div><br/>
        <fieldset>
            <legend>&nbsp;Email Form&nbsp;</legend>
            <form action="." method="post">
                <table>
                    <tr>
                        <td>To:</td><td>&nbsp;</td><td><input type="text" name="to" onfocus="if (this.value == 'To Email ID') {this.value = '';}" onblur="if (this.value == '') {this.value = 'To Email ID';}" title="To Email ID" value="<?php echo $to; ?>" /></td>                    
                    </tr>
                    <tr>
                        <td>Subject:</td><td>&nbsp;</td><td><input width="100px" type="text" name="subject" onfocus="if (this.value == 'Test relay.i2net.com') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Test relay.i2net.com';}" title="Subject" value="<?php echo $subject; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Message:</td><td>&nbsp;</td><td><textarea name="message" cols="30" rows="5" onfocus="if (this.value == 'Type in message here') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Type in message here';}" title="Message" value="Type in message here" ><?php echo $message; ?></textarea></td>
                    </tr>
                    <tr>
                        <td align="right"><input type="submit" value="Send" autofocus="autofocus" name="send"/></td><td colspan="2" style="padding-left:5px;"><input type="reset" /></td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </body>
</html>
