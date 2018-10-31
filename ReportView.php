<?php
namespace ReportEngine;
include_once(dirname(__FILE__).'/Report.php');
class ReportView{
	function run_data($report,$specific){
		Report::run_data($report,$specific);
	}

	//All data is assumed to be in the data directory
	function read_data($parent_dir,$filename){

		$filename = $GLOBALS['rootpath'].'data/'.$parent_dir.'/'.$filename;
		if(file_exists($filename)){
			header('Cache-Control: no-cache');
			echo file_get_contents($filename);
		}
		exit;
	}

	function rebuild_report($report,$specific_id){
	    echo Report::get($report,$specific_id,null,true);exit;
	}
}

//load function
switch($_GET['action']){
    case 'run_data':
        $report_name = $_GET['report'];
        $specific_id = $_GET['specific_id'];
        ReportView::run_data($report_name,$specific_id);
    break;
    case 'read_data':
    break;
    case 'rebuild_report':
        $report_name = $_GET['report'];
        $specific_id = $_GET['specific_id'];
        ReportView::rebuild_report($report_name,$specific_id);
    break;    
}