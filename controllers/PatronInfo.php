<?php 

class PatronInfoController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission = new Submission($this->config);
		$Update = get("UpdatePatronInfo");
		if (!$Update) {
			$id = get('id');
			$this->Submission->set_id($id);
			$submission = $this->Submission->retrieve($id);
			
			$output = "";
			$baseTemplate = load_template($templatedebug['base'],"base",array());
			$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Patron Information Form"));
			$data = array();
			$data['header'] = "";
			$data['content'] = "";
			$data['footer'] = "";
			$data['debug'] = "";
			$data['navigation'] = $topTemplate->process();
			$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
			$data['header'] = $headerTemplate->process();

			$navController = load_controller("navigation",$this->debugInfo,$this->config);
			$data['navigation'] .= $navController->process();
			$submission['action_name'] = "UpdatePatronInfo";
			$PatronInfoTemplate = load_template($templatedebug['PatronInfo'],"PatronInfo",$submission);
			$data['content'] = $PatronInfoTemplate->process();
			$baseTemplate->setData($data);
			return $baseTemplate->process();
		} else {
			$output = "";
			$id = get('id');
			$data = array();
			$data['ID'] = get('id');
			$data = array_merge($_POST,$data);
			$this->Submission->update($id,$data);
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
			return $output;
		}
		
			
	}
}