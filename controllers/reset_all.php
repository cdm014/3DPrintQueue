<?php 

class reset_allController extends Controller {
	
	function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		if ($this->Submission->reset_all()){
			$home = load_controller("home",$this->debugInfo,$this->config);
			return $home->process();
		}
	}
}