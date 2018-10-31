<?php
namespace ReportEngine;

class ReportConfig{
    //webroot of the ReportEngine directory
    const WEBROOT = 'http://localhost/ReportEngine/';
    
    //Directory where the report data is stored. This should be moved somewhere off the web root but an inline directory is used by default to give you a quick startup.
    const REPORT_DATA = '../reportdata/';
    
    //Directory where the PHP report scripts are found. By default this is with ReportConfig.php
	const REPORT_DIR = '../reports/';
	
	//This is the location of ListenerCheck.php
	const LISTENER_SCRIPT_LOCATION = '../ListenerCheck.php';
	
	//This is the location of the data file containing the working listener data. It will be changed constantly while a report is running to show the progress.
	const LISTENER_FILE_DIR = '/listeners/';

}