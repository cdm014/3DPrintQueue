<?php 

class printedController extends Controller {
	var $Submission;
	
	function process() {
		$output = "";
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$id = get('id');
		if ($this->Submission->printed($id)) {
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
			$output .= "<p>Could not mark record as printed</p>";
		}
		return $output;
	}
}
