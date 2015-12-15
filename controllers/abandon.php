<?php 

class abandonController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$id = get('id');
		$output = "";
		
		if ($this->Submission->set_abandoned($id)) {
			$action = get('next_action');
			if (!$action) {
				$action = "home";
			}
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
			$output .= "<p>Could not mark as abandoned</p>";
		}
		return $output;
		
	}
}