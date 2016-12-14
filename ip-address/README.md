
一:
下载最新版纯真ip库qqwry.dat, 在终端下执行 php ./convert.php
得到 zwry.dat

二. 用法:
include "ip_address.php";

$iA = new zIpAddress(Array("charSet"=>"UTF-8"));

echo $iA->lookup($ip, 1), "\n";

