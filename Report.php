<?php
namespace ReportEngine;

include_once(dirname(__FILE__) . '/ReportConfig.php');

class Report{

	const STATUS_FAILED = 1;
	const STATUS_RUNNING = 2;
	const STATUS_COMPLETE = 3;
	const STATUS_NOFILE = 4;
	
	static private $n = 0;//report counter; allows multiple reports to run with a unique id

	static function get($report, $specific_id=0, $refresh_after=null,$report_only=false){
		++self::$n;
		$report_wrapper_id = 'report'.self::$n;
		$report_display = '';

		//include report file
		include_once(dirname(__FILE__) . '/reports/'.$report.'.php');
		//check refresh time and refresh_after
	
		if(!$report_only) {
			//get data date
			$filename_func = 'get_data_files_' . $report;
			$filenames = array();
			if (function_exists($filename_func)) {
				$filenames = $filename_func();
			}
			$data_date = 0;
			foreach ($filenames as $filename) {
				if (file_exists($filename)) {
					$modtime = filemtime($filename);
					if ($data_date == 0) {
						$data_date = $modtime;
					} elseif ($modtime < $data_date) {//get oldest mod time
						$data_date = $modtime;
					}
				}
			}
			//refresh_after 0 means now, -1 means never, and any other time is the number of seconds since the last time the data was refreshed and null means use default
			if ($refresh_after === null) {//if null get default from file
				$filename_func = 'default_refresh_' . $report;
				if (function_exists($filename_func)) {
					$refresh_after = $filename_func();
				}
			}

			if ($refresh_after == -1) {
				$refresh = false;
			}//do not refresh
			elseif ($refresh_after == 0) {
				$refresh = true;
			} elseif ($data_date == 0) {
				$refresh = true;
			}//data does not exist yet
			elseif ($refresh_after + $data_date < time()) {
				$refresh = true;
			} else {
				$refresh = false;
			}

			if ($refresh) {
				self::run_data($report, $specific_id);//then run data function
			}

		}

		//display report
			//if no data exists yet then the report should indicate this
		$report_display_func = 'display_'.$report;
		if(function_exists($report_display_func)){
			$report_display .= '<div id="'.$report_wrapper_id.'">';
			$report_display .= self::get_controls($report,$report_wrapper_id,$specific_id);
			$report_display .= $report_display_func();
			$report_display .= '</div>';
		}
		return $report_display;
	}

	static private function listener_id($report,$specific_id=0){
		return md5($report.'_'.$specific_id);
	}

	static function get_listener_last_updated_time($listener_id){
	    $filename = dirname(__FILE__).'/listeners/'.$listener_id.'.txt';
		if(file_exists($filename)){
			return filemtime($filename);
		}else{
			return null;
		}
	}

	static function run_data($report,$specific_id=0){

		$listener_id = Report::listener_id($report,$specific_id);
		//include report file
		include_once(dirname(__FILE__) . '/reports/'.$report.'.php');
		$report_data_url = dirname(__FILE__) . '/scripts/reportdata.php';
		Report::set_listener_content($listener_id,0,Report::STATUS_RUNNING,$report);
		//exec in background
		$cmd = 'php '.$report_data_url.' '.$report.' '.$specific_id.' '.$listener_id;
		exec($cmd);
		//exit;
	}


	static function get_controls($report,$report_wrapper_id,$specific_id=0){
		$controls = '';

		$listener_id = Report::listener_id($report,$specific_id);
		$listener_updated = Report::get_listener_last_updated_time($listener_id);

		//Progress Bar - https://kimmobrunfeldt.github.io/progressbar.js/
		$progressbar_plugin_file = ReportConfig::WEBROOT.'plugins/progressbar.js';
		$controls .= '<script type="text/javascript" src="'.$progressbar_plugin_file.'"></script>';
		$controls .= <<<HTML
		<div id="pbar$report" style=""></div><p id="pbartext$report"></p>
<style>
		#pbar$report {
		  margin: 20px;
		  width: 400px;
		  height: 8px;
		  position: relative;
		}
</style>
HTML;

		//Last Updated String
		if(is_numeric($listener_updated)){
			$last_updated_string = 'Last Updated: '.date('m/d/Y H:i:s',$listener_updated);
		}else{
			$last_updated_string = '';
		}

		$controls .= '<span id="last_updated_'.$report_wrapper_id.'">'.$last_updated_string.'</span><br>';

		//Refresh Button
		$status_complete = Report::STATUS_COMPLETE;
		$data_url = ReportConfig::WEBROOT.'/ReportView.php?action=run_data&report='.$report.'&specific_id='.$specific_id;
		$listener_url = ReportConfig::WEBROOT.'scripts/reportlistener.php?report='.$report.'&specific_id='.$specific_id.'&listener_id='.$listener_id;
		$report_display_url = ReportConfig::WEBROOT.'/ReportView.php?action=rebuild_report&report='.$report;
		$controls .= '<a href="#" onClick="refresh_report(\''.$report.'\',\''.$specific_id.'\',\''.$data_url.'\',\''.$listener_url.'\')">(Refresh)</a>';
		$controls .= <<<HTML
<script
  src="https://code.jquery.com/jquery-3.3.1.js"
  integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
<script>
function refresh_report(reportname,specific_id,data_url,listener_url){
//remove last updated text
	$('#last_updated_$report_wrapper_id').hide();
//start Report::run_data
	$.get(data_url,function(){
		//listen for data
		if(!!window.EventSource){
			var source = new EventSource(listener_url);
			var pbar = new ProgressBar.Line('#pbar'+reportname, {
  strokeWidth: 4,
  easing: 'easeInOut',
  duration: 1400,
  color: '#FFE082',
  trailColor: '#eee',
  trailWidth: 1,
  svgStyle: {width: '100%', height: '100%'},
  text: {
    style: {
      // Text color.
      // Default: same as stroke color (options.color)
      color: '#999',
      position: 'absolute',
      right: '0',
      top: '30px',
      padding: 0,
      margin: 0,
      transform: null
    },
    autoStyleContainer: false
  },
  from: {color: '#FFEA82'},
  to: {color: '#ED6A5A'},
  step: function(state, pbar) {
    pbar.setText(Math.round(pbar.value() * 100) + ' %');
  }
});
			source.addEventListener('message', function(e) {
				var data = JSON.parse(e.data);
				console.log(data.time, data.message);
				if(data.status == $status_complete){
					pbar.setText('Done');
					pbar.animate(1);
					source.close();
					setTimeout(function(){
					  	//rewrite report
						$('#$report_wrapper_id').load('$report_display_url');
					}, 3000);
					
					//remove progress bar

				}else{
					pbar.setText(data.message);
					pbar.animate(data.progress);
				}
			}, false);
		}
	});


}
</script>
HTML;


		return $controls;
	}

	static function set_listener_content($listener_id,$progress,$status,$message=''){
		$listener_dir = dirname(__FILE__).ReportConfig::LISTENER_FILE_DIR;
		$listener_file = $listener_dir.$listener_id.'.txt';
        $contents = json_encode(array(
			'time'=>time(),
			'progress'=>round($progress,4),
			'status'=>$status,
			'message'=>$message,
		));
		file_put_contents($listener_file,$contents);
	}

}