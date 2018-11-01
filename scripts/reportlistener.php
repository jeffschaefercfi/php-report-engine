<?php

include_once(dirname(dirname(__FILE__)).'/ReportConfig.php');

$report_name = $_GET['report'];
$specific_id = $_GET['specific_id'];
$listener_id = $_GET['listener_id'];

$listener_file = dirname(dirname(__FILE__)).'/listeners/'.$listener_id.'.txt';

if(file_exists($listener_file)){
	$data = file_get_contents($listener_file);
}else{
	$data = json_encode(array(
		'time'=>time(),
		'progress'=>.01,
		'status'=>4,
		'message'=>'Listener file is missing',
	));
}

/*
$response = <<<TEXT
data: {\n
data: "status": "$status",\n
data: "progress": "$progress",\n
data: "message": "$message",\n
data: "time": $time\n
data: }\n\n
TEXT;
*/

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
echo "data: $data" . PHP_EOL;
echo PHP_EOL;
ob_flush();
flush();