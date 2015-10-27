<?php 

class printAttemptsController extends Controller {
	var $Submission;
	var $printAttempt;
	
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		$this->PAFile = $this->config['modelsPath']."printAttempt.php";
		require_once $this->Submission_file;
		require_once $this->PAFile;
		$this->Submission = new Submission($this->config);	
		$this->printAttempt = new printAttempt($this->config);
		$output = "";
		$data = array();
		$baseTemplate = load_template($templatedebug['base'],"base",array());
		$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Print Attempt Information Form"));
		$data['header'] = "";
		$data['content'] = "";
		$data['footer'] = "";
		$data['debug'] = "";
		$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
		$data['header'] = $headerTemplate->process();
		$data['navigation'] = $topTemplate->process();
		$navController = load_controller("navigation",$this->debugInfo, $this->config);
		$data['navigation'] .= $navController->process();
		if(get('CreateAttemptForm')) {
			$formdata = array();
			foreach ($this->printAttempt->columns as $column) {
				$formdata[$column] = "";
			}
			
			$formdata['checked_successful'] = $formdata['successful'];
			$formdata['legend'] = "New Print Attempt Information";
			$formdata['Started'] = date("Y-m-d H:i:s");
			$formdata['FormAction'] = "CreateAttempt";
			
			$formdata['SubmissionID'] = get('SubmissionID');
			$submission = $this->Submission->retrieve($formdata['SubmissionID']);
			//build out a couple of helpers for the template
			$optionsString = "";
			$selectedArray = array();
			foreach ($this->Submission->colors as $color) {
				$optionsString .= "<option {".$color."_SELECTED}>".$color."</option>\n";
				$selectedArray[$color."_SELECTED"] = "";
			}
			$selectedArray[ $submission['Color']."_SELECTED"] = "selected";
			$formdata['color_options'] = $optionsString;
			
			$formdata = array_merge($selectedArray,$formdata);
			
			$PrintAttemptFormTemplate = load_template($templatedebug['printAttemptForm'],"PrintAttemptCreateForm",$formdata);
			$data['content'] .= $PrintAttemptFormTemplate->process();
		}
		
		if(get('CreateAttempt')) {
			//if the form has been filled out
			$attemptData = array();
			foreach ($this->printAttempt->columns as $column) {
				$attemptData[$column] = get($column);
			}
			$attemptData['Grams'] = $attemptData['Grams'] ? $attemptData['Grams'] : 0;
			$attemptData['printing_id'] = get('SubmissionID');
			$created = $this->printAttempt->create($attemptData['printing_id'],$attemptData['Machine'],$attemptData['Started'],$attemptData['Grams'],$attemptData['color']);
			if($created) {
				$action = "PrintInfo";
				$controllerclass = $action."Controller";
				$controllerfile = $this->config['controllerPath'].$action.".php";
				$_POST['id'] = get('SubmissionID');
				$this->config['id'] = get('SubmissionID');
				if (is_file($controllerfile)) {
					require_once $controllerfile;
					$controller = new $controllerclass($this->debugInfo, $this->config);
					return $controller->process();
				} else {
					$debugInfo['controller_not_exist'] = "The Controller File Doesn't Exist";
					$output .= "<p>Could not load home controller</p>";
					$debug = true;
				}
			} else {
				$data['content'] = "<pre>".print_r($this->printAttempt->stmt_error(),true)."</pre>";
			}
		}
		
		if(get('EditAttemptForm')) {
			$AttemptID = get('AttemptID');
			$formdata = $this->printAttempt->retrieve($AttemptID);
			
			$formdata['checked_succesful'] = $formdata['successful'] ? "Checked" : "";
			$formdata['legend'] = "Edit Print Attempt Information";
			$formdata['SubmissionID'] = $formdata['3dprinting_id'];
			$formdata['AttemptID'] = $formdata['id'];
			$optionsString = "";
			$selectedArray = array();
			foreach ($this->Submission->colors as $color) {
				$optionsString .= "<option {".$color."_SELECTED}>".$color."</option>\n";
				$selectedArray[$color."_SELECTED"] = "";
			}
			$formdata['color_options'] = $optionsString;
			$formdata['FormAction'] = "EditAttempt";
			$selectedArray[$formdata['color']."_SELECTED"] = "selected";
			$formdata = array_merge($selectedArray,$formdata);
			$PrintAttemptFormTemplate = load_template($templatedebug['printAttemptForm'],"PrintAttemptCreateForm",$formdata);
			$data['content'] .= $PrintAttemptFormTemplate->process();
		}
		
		if(get('EditAttempt')) {
			$AttemptID = get('AttemptID');
			$printing_id=get('SubmissionID');
			$Machine = get('Machine');
			$Started = get('Started');
			$Hours = get('Hours');
			$Minutes = get('Minutes');
			$Grams = get('Grams');
			$color = get('color');
			$successful = get('successful');
			$Hours = $Hours ? $Hours : 0;
			$Minutes = $Minutes ? $Minutes : 0;
			$updated = $this->printAttempt->update($AttemptID, $printing_id, $Machine, $Started, $Hours, $Minutes, $Grams, $color, $successful);
			if ($updated) {
				$action = "PrintInfo";
				$controllerclass = $action."Controller";
				$controllerfile = $this->config['controllerPath'].$action.".php";
				$_POST['id'] = get('SubmissionID');
				$this->config['id'] = get('SubmissionID');
				if ($successful) {
					$submission = $this->Submission->retrieve($printing_id);
					$submission['printed'] = $successful;
					if ($this->Submission->update($printing_id,$submission)) {
						
					} else {
							$data['content'] = "<p>Couldn't Update Submission.</p>";
					}
				}
				if (is_file($controllerfile)) {
					require_once $controllerfile;
					$controller = new $controllerclass($this->debugInfo, $this->config);
					return $controller->process();
				} else {
					$debugInfo['controller_not_exist'] = "The Controller File Doesn't Exist";
					$output .= "<p>Could not load home controller</p>";
					$debug = true;
				}
				//if attempt was successful adjust model to successful
				
			} else {
				$data['content'] = "<pre>".print_r($this->printAttempt->stmt_error(),true)."</pre>";
			}
			
		}
		$baseTemplate->setData($data);
		return $baseTemplate->process();
	}
}