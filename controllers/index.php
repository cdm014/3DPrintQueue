<?php 
class Controller {
	var $config;
	var $debugInfo;
	function __construct(& $debugInfo,& $config){
		$this->debugInfo = & $debugInfo;
		$this->config =& $config;
	}
	
	function template($tplstring, $data) {
		foreach ($data as $name => $string) {
			$token = "{".$name."}";
			$tplstring = str_replace($token,$string,$tplstring);
		}
		return $tplstring;
	}
	
}