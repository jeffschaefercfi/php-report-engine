<?php
//includes - include anything your app needs to run here before processing data

$report_name = $argv[1];
$specific_id = $argv[2];
$listener_id = $argv[3];

//include report file
include_once(dirname(dirname(__FILE__)) . '/Report.php');
include_once(dirname(dirname(__FILE__)).'/reports/'.$report_name.'.php');
$func_name = 'data_'.$report_name;

//call the function that will build the data
if(function_exists($func_name)){
	call_user_func($func_name,$listener_id,$specific_id);
}else{
    ReportEngine\Report::set_listener_content($listener_id,0,ReportEngine\Report::STATUS_FAILED,'Data function does not exist for report: '.$report_name);
}