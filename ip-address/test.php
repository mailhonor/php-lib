<?
include "ip_address.php";

$argv[]="192.168.1.1";

$iA = new zIpAddress(Array("charSet"=>"UTF-8"));

$ip = gethostbyname($argv[1]);

$ret = $iA->lookup($ip, 1);

echo $ret, "\n";


