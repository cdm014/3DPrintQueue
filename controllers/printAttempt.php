<?php 

class printAttemptController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$this->debugInfo['printAttemptController->Submission'] = $this->Submission;
		$this->Submission_ID = get('id');
		$this->config['Submission_ID'] = get('id');
		$output = "";
		
		if($this->Submission->attempt_print($this->config['Submission_ID'])) {
			$action = "home";
			$controllerclass = $action."Controller";
			$controllerfile = $this->config['controllerPath'].$action.".php";
			if (is_file($controllerfile)) {
				require_once $controllerfile;
				$controller = new $controllerclass($this->debugInfo, $this->config);

				$output .= $controller->process();	
			} else {
				$debugInfo['controller_not_exist'] = "The Controller File Doesn't Exist";
				$output .= "<p>Could not load home controller</p>";
				$debug = true;
			}	
		} else {
			$this->debugInfo['Update may have failed'];
			$output .= "<p>Updating the record may have failed</p>\n";
		}
		return $output;
	}
}