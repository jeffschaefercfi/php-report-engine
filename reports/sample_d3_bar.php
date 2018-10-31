<?php
use ReportEngine\ReportConfig;

//bar_simple_eventperday_last30

function data_sample_d3_bar($listener_id,$account_id){

	$dateindex = array();
	ReportEngine\Report::set_listener_content($listener_id,0,ReportEngine\Report::STATUS_RUNNING,'Gathering Data...');
	sleep(1);//to show progress bar working - remove this line in a production system

	//Get data from the database (mocked up with random numbers in this sample
	$data = array();
	$total_data_points = 100;//$total_data_points = count($rowsfromdatabase);
	for($i=0;$i<$total_data_points;++$i){
		$data[] = rand(0,5);
		$progress = $i/$total_data_points;//progress should be between 1 and 0
		//Notice that we use $progress*.9 below. This is because we want this part of the data grab to run from 0 to 90%
		ReportEngine\Report::set_listener_content($listener_id,$account_id,$progress*.9,ReportEngine\Report::STATUS_RUNNING,'Building Fake Data...');
//		sleep(1);
	}

	$csvfile = fopen(dirname(dirname(__FILE__)) .ReportEngine\ReportConfig::REPORT_DATA.'sample_d3_bar.csv','w');
	fputcsv($csvfile,array('key','value'));

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

function display_sample_d3_bar(){

	$id = 'sample_d3_bar';

	$files = get_data_files_sample_d3_bar();
	$csvfile = $files['main'];
	$data_url = ReportEngine\ReportConfig::WEBROOT.'reportdata/sample_d3_bar.csv';

	$report = '';
	
	$report .= '<script src="https://d3js.org/d3.v5.js"></script>';//include d3.js
	$report .= <<<HTML
<div id="$id"></div>
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

	$report .= <<<HTML

<script>

var margin = {top: 20, right: 20, bottom: 70, left: 40},
    width = 600 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

var svg = d3.select("#$id").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", 
          "translate(" + margin.left + "," + margin.top + ")");



d3.csv("$data_url", function(data) {

  svg.selectAll("bar")
      .data(data)
    .enter().append("rect")
      .style("fill", "steelblue")

});

</script>
HTML;


	return $report;
}

function default_refresh_sample_d3_bar(){
	return 86400;
}

function get_data_files_sample_d3_bar(){
	return array('main'=>dirname(dirname(__FILE__)).ReportEngine\ReportConfig::REPORT_DATA.'/sample_d3_bar.csv');
}
