<?php

#####						   #####
# This code creates a connection to the Mail eXchanger #
# of someones email address.  After connecting it will #
# send an email to the email address.		       #
# 						       #
# This code was created and owned by,		       #
#		Eric Kinolik, Dire Networks 	       #
#####						   #####

define('mailfunctions', 1);

function create_smtp_headers($subject, $from, $to, $full_from_name='',
							 $full_to_name='', $content='text/plain') {
	$date = date(r);
	
	$header = 'From: ' . $full_from_name . ' <' . $from . '>' . "\n";
	$header .= 'To: ' . $full_to_name . ' <' . $to . '>' . "\n";
	$header .= 'Date: ' . $date . "\n";
	$header .= 'Subject: ' . $subject . "\n";
	if ($content == 'text/html') {
		$header .= 'MIME-Version: 1.0' . "\n";
		$header .= 'Content-Type: ' . $content . "\n";
		$header .= 'Content-Transfer-Encoding: 7bit' . "\n";
		$header .= 'Content-Disposition: inline; filename="email.html"' . "\n";
	} else {
		$header .= 'Content-Type: ' . $content . "\n";
	}
	$header .= "\n";
	return $header;
}


function send_smtp($HOSTNAME, $from, $to, $data, $empty1='', $empty2='') {

global $last_output;

$to = str_replace("\n", "", $to);
$to = str_replace("\r", '', $to);

#This section sends the email to the server.

if (!(ereg("@", $to))) {
	$last_output = "invalid email address\n";
	return FALSE;
}

$todomain = split("@", $to);
$domain = str_replace("\r\n", '', $todomain[1]);
$domain = str_replace('>', '', $domain);
$domain = str_replace('<', '', $domain);
$to = '<' . $to . '>';
$from = '<' . $from . '>';
$data = str_replace("\n", "\r\n", $data);
unset ($todomain);

$fp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
do {
	if(isset($HOSTNAME) AND $HOSTNAME != ""){
		$record = $HOSTNAME;
		$result = @socket_connect($fp, $record, 25);
		if(!$result){continue;} else {$gotone = 1; break 1;}
	}
	elseif (getmxrr($domain, $mxrecord)) {
		foreach ($mxrecord as $record) {
			if ($record == "") {continue;}
			$mx_addrs = gethostbynamel($record);
			foreach ($mx_addrs as $mx_addr) {
				$result = @socket_connect($fp, $record, 25);
				if (!$result) {continue;} else {$gotone = 1; break 2;}
			}
		}
	} else {
		$last_output = "bad mail server";
		return FALSE;
	}
} while(!isset($gotone));

unset ($domain, $record, $mxrecord, $mx_addrs, $mx_addr);

while ($out = socket_read($fp, 1024)) {
	if (strstr($out, "\n")) {break;}
}
unset ($out);

$in = "HELO $HOSTNAME\r\n";
$result = socket_write($fp, $in, strlen($in));
if (!(socketreadline($fp, "250", "HELO"))) 
	return FALSE;

$in = rtrim("MAIL FROM: $from") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "MAIL FROM"))) 
	return FALSE;

$in = rtrim("RCPT TO: $to") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

$exist = socketreadline($fp, "250", "RCPT TO");
if ($exist == -1) 
	return FALSE;
elseif (!$exist)
	return FALSE;


$in = "DATA\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "354", "DATA WAIT"))) 
	return FALSE;

$in = "$data\r\n.\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "DATA WRITE"))) 
	return FALSE;

$in = "QUIT\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "221", "QUIT"))) 
	return FALSE;

socket_close($fp);
unset ($in, $result, $fp, $to);
return 1;
}


function socketreadline($fp, $code, $type) {
# I forgot what this function is for?
	while ($out = socket_read($fp, 1024)) {
		if (strstr($out, "\n")) {break;}
	}

	if (($temp = substr($out, 0, 3)) != "$code") {
		$in = "QUIT\r\n";
		$result = socket_write($fp, $in, strlen($in));
		socket_close($fp);
		global $last_output;
		if ($temp == '550' || $temp == '554' || $temp == '552' || $temp == '553') {
			$last_output = $out;
			return -1;
		} else {
			$last_output = $out;
			return FALSE;
		}
	}
	unset ($out, $fp, $code, $type, $temp);
	return 1;
}

function add_to_email_queue($from, $to, $data, $queue_dir) {
	$time = str_replace(' ', '_', microtime());
	if (substr($queue_dir, -1) == '/')
		$full_path = $queue_dir . $time;
	else
		$full_path = $queue_dir . '/' . $time;
	
	for ($i = 0; $i < 100; $i++) {
		if(!(file_exists($full_path . ".$i"))) {
			$full_path .= ".$i";
			break;
		}
	}
	
	$input = $from . "\n" . $to . "\n\n" . $data;

	$fh = fopen($full_path, 'w');
	fwrite($fh, $input, strlen($input));
	fclose($fh);

}

?>
