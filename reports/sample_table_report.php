<?php
use ReportEngine\ReportConfig;

//bar_simple_eventperday_last30

function data_sample_table_report($listener_id,$account_id){

   
	$dateindex = array();
	ReportEngine\Report::set_listener_content($listener_id,0,ReportEngine\Report::STATUS_RUNNING,'Gathering Data...');
	sleep(1);//to show progress bar working - remove this line in a production system

	//Get data from the database (mocked up with random numbers in this sample
	$data = array();
	$total_data_points = 100;//$total_data_points = count($rowsfromdatabase);
	for($i=0;$i<$total_data_points;++$i){
	    if(rand(0,4)==4){sleep(1);}//to show progress bar working - remove this line in a production system
		$data[] = rand(0,5);
		$progress = $i/$total_data_points;//progress should be between 1 and 0
		//Notice that we use $progress*.9 below. This is because we want this part of the data grab to run from 0 to 90%
		ReportEngine\Report::set_listener_content($listener_id,$account_id,$progress*.9,ReportEngine\Report::STATUS_RUNNING,'Building Fake Data...');
//		sleep(1);
	}

	$csvfile = fopen(dirname(dirname(__FILE__)) .ReportEngine\ReportConfig::REPORT_DATA.'sample_table_report.csv','w');

	$n = 0;
	$datacount = count($data);
	foreach($data as $key=>$value){
	    fputcsv($csvfile,array($key,$value));
		$progress = ((++$n/$datacount)*.1)+.9;//we ran from 0 to 90% above so here we need to run from 90% to 100%
		ReportEngine\Report::set_listener_content($listener_id,$progress,ReportEngine\Report::STATUS_RUNNING,'Writing Dataset...');
//		sleep(1);
	}

    //Mark as done
	ReportEngine\Report::set_listener_content($listener_id,1,ReportEngine\Report::STATUS_COMPLETE);
}

function display_sample_table_report(){

	$id = 'sample_table_report';

	$data_path = dirname(dirname(__FILE__)).'/reportdata/sample_table_report.csv';

	if(file_exists($data_path)){
	    $datafile = fopen($data_path,'r');
	}else{
	    return 'Data not built yet: '.$data_path;
	}

	$numberindex = array();
	$data = fgetcsv($datafile);
	do{
	    $numberindex[$data[1]]++;
	    $data = fgetcsv($datafile);
	}while($data != null);
	
	$report = '';
	
	$report .= '<h1>Display rank of numbers 0-5. How often were they chosen in rand(0,5)?</h1>';
	$report .= <<<HTML
<div id="$id">

<table>

<tr><th>Number</th><th>Times Chosen</th></tr>

HTML;

	foreach($numberindex as $num=>$count){
	    $report .= '<tr><td>'.$num.'</td><td>'.$count.'</td></tr>';
	}
	
	$report .= <<<HTML

</table>

</div>
	<style>

	.axis {
	  font: 10px sans-serif;
	}

	.axis path,
	.axis line {
	  fill: none;
	  stroke: #000;
	  shape-rendering: crispEdges;
	}

	</style>
HTML;




	return $report;
}

function default_refresh_sample_table_report(){
	return 86400;
}

function get_data_files_sample_table_report(){
	return array('main'=>dirname(dirname(__FILE__)).ReportEngine\ReportConfig::REPORT_DATA.'/sample_table_report.csv');
}
