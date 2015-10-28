<?php 

class PrintInfoController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		$this->PAFile = $this->config['modelsPath']."printAttempt.php";
		require_once $this->Submission_file;
		require_once $this->PAFile;
		$this->Submission = new Submission($this->config);	
		$this->printAttempt = new printAttempt($this->config);
		$output = "";
		$baseTemplate = load_template($templatedebug['base'],"base",array());
		$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Model Printing Information Form"));
		$data = array();
		$data['header'] = "";
		$data['content'] = "";
		$data['footer'] = "";
		$data['debug'] = "";
		$data['navigation'] = $topTemplate->process();
		$navController = load_controller("navigation",$this->debugInfo, $this->config);
		$data['navigation'] .= $navController->process();
		$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
		$data['header'] = $headerTemplate->process();
		$Update = get("UpdatePrintInfo");
		if(!$Update) {
		//if we haven't submitted updated information
			$id = get('id');
			$this->debugInfo['id'] = $id;
			$this->Submission->set_id($id);
			$submission = $this->Submission->retrieve($id);
			
			//build out a couple of helpers for the template
			$optionsString = "";
			$selectedArray = array();
			foreach ($this->Submission->colors as $color) {
				$optionsString .= "<option {".$color."_SELECTED}>".$color."</option>\n";
				$selectedArray[$color."_SELECTED"] = "";
			}
			$selectedArray[ $submission['Color']."_SELECTED"] = "selected";
			
			
			
			
			$PrintInfoData = array();
			$PrintInfoData['optionsString'] = $optionsString;
			if($submission['printed']) {
				$PrintInfoData['printCheckBox'] = "checked";
			} else {
				$PrintInfoData['printCheckBox'] = "";
			}
			$PrintInfoData = array_merge($PrintInfoData,$selectedArray,$submission);
			$PrintInfoData['action_name'] = "UpdatePrintInfo";
			$PrintInfoTemplate = load_template($templatedebug['PrintInfo'],"PrintInfo",$PrintInfoData);
			$data['content'] = $PrintInfoTemplate->process();
			$LogAttemptTemplate = load_template($templatedebug['logAttempt'],"printInfoPrintAttempt",array("ID" => $id));
			$data['content'] .= $LogAttemptTemplate->process();
			//pull print attempt data and add it to $data['content']
			$attempts = $this->printAttempt->fetch_for_submission($id);
			$attempt_string = "";
			$printAttemptSummaryTemplate = load_template($templatedebug['printAttemptSummary'],"printAttemptSummaryView",array());
			if (count($attempts)) {
				//$attempt_string = "<pre>".print_r($attempts,true)."</pre>";
				foreach($attempts as $attempt) {
					if ($attempt['successful']) {
						$attempt['successful_text'] = "Yes";
					} else {
						$attempt['successful_text'] = "No";
					}
					
					
					$printAttemptSummaryTemplate->reset();
					$printAttemptSummaryTemplate->setData($attempt);
					$attempt_string .= $printAttemptSummaryTemplate->process();
				}
			} else {
				$attempt_string = "<p>No Print Attempts Yet</p>";
			}
			$data['content'] .= $attempt_string;
			//$data['content'] .= "<pre>".print_r($this->debugInfo,true)."</pre>";
			
			$baseTemplate->setData($data);
			return $baseTemplate->process();
		} else {
		//if we do have updated data
			$output = "";
			$data['content'] = "";
			$id = get('id');
			$pidata = array();
			$pidata['ID'] = get('id');
			//grab data from post
			$pidata = array_merge($_POST,$pidata);
			
			/*
			//old way just use the grabbed data to update
			if($this->Submission->update($id,$pidata)){
				$action="home";
				$controllerclass = $action."Controller";
				$controllerfile = $this->config['controllerPath'].$action.".php";
				/*
				//debug 
				$data['content'] .= "<p>Post</p><pre>".print_r($_POST,true)."</pre>";
				$data['content'] .= "<p>Data</p><pre>".print_r($pidata,true)."</pre>";
				
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
				
			} else {
				$data['content'] .= "<p>Update Failed</p>";
				$data['content'] .= "<pre>".print_r($this->Submission->stmt_error(),true)."</pre>";
			}
			//*/
			
			//new way
			//check if successful
			if ($pidata['printed']) {
			//did we check printed
			//then update the submission as normal
			} else {
			//if we didn't check printed
			//then record the information as an attempt
			
			}
		}
		$baseTemplate->setData($data);
		return $baseTemplate->process();
	}
}