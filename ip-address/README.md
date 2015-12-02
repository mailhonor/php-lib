

一. 用法:

下载 ip_address.php zwry.dat 到同一个目录.


include "ip_address.php";

$iA = new zIpAddress(Array("charSet"=>"UTF-8"));

echo $iA->lookup($ip, 1), "\n";



二. zwry.dat的生成.

直接下载本地: zwry.dat, 或

下载最新版纯真ip库qqwry.dat, 在终端下执行 php ./convert.php


