<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();
extract($_POST);
$body = $_SESSION['emailBody'];
$return_arr['name'] = "";
$return_arr['error'] = "";
$return_arr['email'] = "";

function check_email_address($email) 
{ 
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
     
   }
   return $isValid;
}

function explodeX($delimiters,$string)
{
    $return_array = Array($string); // The array to return
    $d_count = 0;
    while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
    {
        $new_return_array = Array();
        foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
        {
            $put_in_new_return_array = explode($delimiters[$d_count],$el_to_split);
            foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
            {
                $new_return_array[] = $substr;
            }
        }
        $return_array = $new_return_array; // Replace the previous return array by the next version
        $d_count++;
    }
    return $return_array; // Return the exploded elements*/
}


if(isset($_POST['email']) && isset($_POST['subject']))	
{
	extract($_POST);
	if($email =="")
	{
		$return_arr['error'] ="Please enter a valid email";
		echo json_encode($return_arr);
		return;
	}
	if($subject =="")
	{
		$return_arr['error'] ="Please enter a subject";
		echo json_encode($return_arr);
		return;
	}
	if($body =="")
	{
		$return_arr['error'] ="Content appears to be empty";
		echo json_encode($return_arr);
		return;
	}	
	
	if($isMailServer == 'true')
	{
		require('../../mail.php');
		$mail             = new PHPMailer();
	}
	else
	{
		require($PHPLIBDIR.'mailfunctions.php');
	}
	
	$to = explodeX(Array(",",";"),$email);
	for($k=0 ; $k < count($to); $k++)
	{
		
		if(!check_email_address($to[$k]))
		{
			$return_arr['email'] .= $to[$k].', ';
			continue;
		}
		
		if($isMailServer == 'true')
		{
			
						
			$mail->AddReplyTo("Do Not Reply", $name = "DO NOT REPLY");
	
			$mail->From       = "admin@uniformsourcing.com";
			
			$mail->FromName   = "";
			
			$mail->Subject    = $subject;
			
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			
			$mail->MsgHTML($body);			
			
			$mail->AddAddress($to[$k], $name="");
			
			if(!$mail->Send())
			{			
				$return_arr['error'] = "Unable to send email. Please try again later";
				echo json_encode($return_arr);
				return;
			}
		}
		else
		{
			
			$headers=create_smtp_headers($subject, "admin@uniformsourcing.com", $to[$k], $subject,"","text/html");
			$data=$headers. "<html>".$body."</html>";
			if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$to[$k], $data)) == false)
			{
				$return_arr['error'] = "Unable to send email. Please try again later";
				echo json_encode($return_arr);
				return;
			}	
		}
	}
}
echo json_encode($return_arr);
return;
?>