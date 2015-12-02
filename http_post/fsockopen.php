<?
function request_by_socket($remote_server,$remote_path,$post_string,$port = 80,$timeout = 30)
{   
	$socket = fsockopen($remote_server, $port, $errno, $errstr, $timeout);   
	if (!$socket) die("$errstr($errno)");   

	fwrite($socket, "POST $remote_path HTTP/1.0\r\n");   
	fwrite($socket, "User-Agent: Socket Example\r\n");   
	fwrite($socket, "HOST: $remote_server\r\n");   
	fwrite($socket, "Content-type: application/x-www-form-urlencoded\r\n");   
	fwrite($socket, "Content-length: " . (strlen($post_string)) . "\r\n");   
	fwrite($socket, "Accept:*/*\r\n");   
	fwrite($socket, "\r\n");   
	fwrite($socket, "$post_string");   
	fwrite($socket, "");   

	$header = "";   
	while (($str = fgets($socket, 4096))!="\r\n") {   
		$header .= $str;   
	}   
	$data = "";   
	while (!feof($socket)) {   
		$data .= fgets($socket, 4096);   
	}   
	echo "$header\r\n$data\r\n";
	return $data;   
}   

$req = Array(1, 213, "FAdsfdsaF");
$req_string = json_encode($req);
$post_data = array(   
	'req' => $req_string,
	'req2' => "req2222222string"
);   
$post_string = http_build_query($post_data);   
$res_data = request_by_socket('nsmail_dev', '/mds/client_send.php', $post_string);
list($res_data)=explode('完毕.', $res_data);
$res=json_decode($res_data, true);

var_dump($res);

