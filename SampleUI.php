<?php
ini_set('display_errors',1);
include_once(dirname(__FILE__) . '/Report.php');
        
echo ReportEngine\Report::get('sample_d3_bar',0);
