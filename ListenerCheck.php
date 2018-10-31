<?php
$uri = $_SERVER['REQUEST_URI'];

list($rootdir,$x) = explode('/public_html/',__FILE__);

$GLOBALS['rootpath'] = $rootdir;

if(strpos($uri,'/ReportEngine_listener.listener/') !== FALSE){
	list($base_url,$listener_request) = explode('/ReportEngine_listener.listener/',$uri);
	$args = explode('/',$listener_request);
	$listener_script = $args[0];
	$GLOBALS['listener_args'] = $args;
	include_once(ReportEngine\ReportConfig\LISTENER_FILE_DIR.$listener_script.'.php');
	exit;
}