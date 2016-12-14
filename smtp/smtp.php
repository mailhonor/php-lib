<?php
function zms_mail_smtp($host, $port, $from, $password, $to, $m){
	$fp=fsockopen($host, $port);
	if(!$fp){
		echo "can not open $host:$port\n";
		die();
	}
	smtp_get_response($fp);
	smtp_fputs($fp, "EHLO www.com\r\n");
	smtp_get_response($fp);
	if($password){
		smtp_fputs($fp, "auth login\r\n");
		smtp_get_response($fp);
		smtp_fputs($fp, base64_encode($from)."\r\n");
		smtp_get_response($fp);
		smtp_fputs($fp, base64_encode($password)."\r\n");
	}
	smtp_fputs($fp, "MAIL FROM: <$from>\r\n");
	smtp_get_response($fp);
	smtp_fputs($fp, "RCPT TO: <$to>\r\n");
	smtp_get_response($fp);
	smtp_fputs($fp, "DATA\r\n");
	smtp_get_response($fp);
	smtp_fputs($fp,$m);
	smtp_fputs($fp,"\r\n.\r\n");
	smtp_get_response($fp);
	smtp_fputs($fp, "QUIT\r\n");
	smtp_get_response($fp);
	smtp_get_response($fp);
	smtp_get_response($fp);
	fclose($fp);
}

function smtp_get_response($fp){
	$end = false;
	$line='AAAAAA';
	while($line[3]!=' '){
		$line = fgets($fp, 5120);
		echo $line;
	}
	if($line[0]!='2' and $line[0]!='3'){
		smtp_fputs($fp, "quit\r\n");
		die();
	}
}

function smtp_fputs($fp, $str)
{
	echo "\r\n";
	echo $str;
	fputs($fp, $str);
}


for($i=0;$i<10;$i++) $argv[]="";

$host = $argv[1];
$from = $argv[2];
$to = $argv[3];

$p = strpos($host, ":");
if($p!==false){
	$port = substr($host, $p+1);
	$host = substr($host, 0, $p);
}else{
	$port = 25;
}

$p = strpos($from, "/");
if($p!==false){
	$password = substr($from, $p+1);
	$from = substr($from, 0, $p);
}else{
	$password = false;
}

$m=file_get_contents("php://stdin");
zms_mail_smtp($host, $port, $from, $password, $to, $m);
