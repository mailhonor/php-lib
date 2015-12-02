<?
function send_post($url, $post_data) {   
	$postdata = http_build_query($post_data);   
	$options = array(   
		'http' => array(   
			'method' => 'POST',   
			'header' => 'Content-type:application/x-www-form-urlencoded',   
			'content' => $postdata,   
			'timeout' => 15 * 60 // 超时时间（单位:s）   
		)   
	);   
	$context = stream_context_create($options);   
	$result = file_get_contents($url, false, $context);   
	return $result;   
}   

//使用方法   
$req = Array(1, 213, "FAdsfdsaF");
$req_string = json_encode($req);
$post_data = array(   
	'req' => $req_string,
	'req2' => "req2222222string"
);   
$res = send_post('http://127.0.0.1/mds/client_send.php', $post_data);   

var_dump(json_decode($res, true));
