<?php
/*
function handler( $errno, $errstr, $errfile, $errline){
	
	$o = "<pre>\n";
	$o .= "---\nHandler called\n";
	$o .= "Error Number $errno\n";
	$o .= "String: $errstr\n";
	$o .= "File: $errfile\n";
	$o .= "Line: $errline\n";
	$o .= "---\n</pre>";
	echo $o;
	return true;

}
*/
class Template {
	private $filename;
	public $filestring;
	private $data;
	public $fileKeys;
	private $environment;
	
	public $processedString;
	function __construct(&$environment,$filename,$data){
		//set_error_handler("handler");
		$this->filename = $filename;
		$this->environment =&$environment;
		if(is_file($this->filename)){
			$this->filestring = htmlspecialchars(file_get_contents($this->filename));
		} else {
			$this->filestring = "";
		}
		$this->processedString = $this->filestring;
		$this->setFileKeys();
		$this->data = $data;
		$this->environment[] = array(
			"Template Constructor Called", 
			"filename"=>$this->filename, 
			"filestring" => $this->filestring,
			"data" => $this->data
		);
		//restore_error_handler();
		
	}
	
	public function process(){
		$toRun = True;
		while ($toRun){
			//log
			$this->environment[] = array("m1" => "process() called","filestring" => $this->filestring, "processedString" => $this->processedString);
			
			
			//run substitutions
			foreach ($this->data as $key => $value) {
				$this->environment[] = array("key" => $key, "value" => $value , "string" => $this->processedString);
				$token = "{".$key."}";
				$this->processedString = str_replace($token,$value,$this->processedString);
				$this->environment[] = array("new string" => $this->processedString);
				$this->setFileKeys();
			}
			//Check whether anymore keys match
			$toRun = false;
			foreach($this->fileKeys[1] as $keyname) {
				if (array_key_exists($keyname,$this->data)) {
					$toRun = True;
				}
			}
			
		}
		return htmlspecialchars_decode($this->processedString);
	}
	
	public function setData($data) {
		$this->data = $data;
		
	}
	
	public function setFile($filename) {
		$this->filename = $filename;
		if (is_file($this->filename)){
			$this->filestring = file_get_contents($this->filename);
		} else {
			$this->filestring = "";
		}
		$this->processedString = $this->filestring;
		$this->setFileKeys();
		
	}
	
	public function setFileString($fileString) {
		$this->filestring = $fileString;
		$this->processedString = $this->filestring;
		$this->setFileKeys();
		
	}
	
	public function setFileKeys() {
		$this->fileKeys = null;
		$returnValue = preg_match_all('/{([^}]*)}/', $this->processedString, $this->fileKeys);
		return $this->fileKeys;
	}
	
	public function reset() {
		if (is_file($this->filename)){
			$this->filestring = file_get_contents($this->filename);
		} else {
			$this->filestring = "";
		}
		$this->processedString = $this->filestring;
		$this->setFileKeys();
	}
	
	public function strip_unused_tags() {
		$tempkeys = array();
		foreach ($this->fileKeys[1] as $keyname) {
			//build array of keys we don't have to replace with empty strings
			if (!array_key_exists($keyname,$this->data)) {
				$tempkeys[$keyname] = "";
			}
		}
		$throwaway = array();
		//create a template
		$temp = new Template ($throwaway,null,$tempkeys);
		//use our template as it exists right now
		$temp->setFileString($this->processedString);
		$retstring = $temp->process();
		//update our processedString to version without unused tags
		$this->processedString = $retstring;
	}
	
	public function final_output() {
		//we call this initially because the processing can build new tags
		$o = $this->process();
		$o = $this->strip_unused_tags();
		return $this->process();
	}
	

	
}