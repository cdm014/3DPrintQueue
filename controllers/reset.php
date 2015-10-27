<?php 

class resetController extends Controller {
	var $Submission;
	
	function process() {
		$output = "";
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		$this->PAFile = $this->config['modelsPath']."printAttempt.php";
		require_once $this->Submission_file;
		require_once $this->PAFile;
		$this->Submission = new Submission($this->config);	
		$this->printAttempt = new printAttempt($this->config);
		$id = get('id');
		$print_attempts = array();
		$print_attempts = $this->printAttempt->fetch_for_submission($id);
		if ($this->Submission->reset($id)||count($print_attempts)) {
			
			foreach ($print_attempts as $attempt) {
				$this->printAttempt->delete($attempt['id']);
			}
			$action = get('next_action');
			$action = $action ? $action : "home";
			$controllerclass = $action."Controller";
			$controllerfile = $this->config['controllerPath'].$action.".php";
			if (is_file($controllerfile)) {
				require_once $controllerfile;
				$controller = new $controllerclass($this->debugInfo, $this->config);

				$output .= $controller->process();	
			} else {
				$debugInfo['controller_not_exist'] = "The Controller File Doesn't Exist";
				$output .= "<p>Could not load home controller: ".$controllerfile."</p>";
				$debug = true;
			}
		} else {
			$output .= "<p>Could not reset</p>";
		}
		return $output;
	}
}