<?php 

	class createController extends Controller {
		var $Submission;
		public function process() {
			//load submission model
			$this->Submission_file = $this->config['modelsPath']."submissions.php";
			require_once $this->Submission_file;
			$this->Submission = new Submission($this->config);	
			
			//load base template for page
			$baseTemplate = load_template($templatedebug['base'],"base",array());
			$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Model Printing Submission Form"));
			$navController = load_controller("navigation",$this->debugInfo, $this->config);
			$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
			$data = array();
			$data['header'] = "";
			$data['content'] = "";
			$data['footer'] = "";
			$data['debug'] = "";
			$data['navigation'] = $topTemplate->process();
			$data['navigation'] .= $navController->process();
			$data['header'] = $headerTemplate->process();
			if (!get('create')) {
				//display form
				$CreateFormData = array();
				if(get('debug') > 0) {
					$CreateFormData['debugVar'] = "<input type='hidden' name='debug' value='1' />";
				}
				$CreateFormTemplate = load_template($templatedebug['CreateForm'],"CreateForm",$CreateFormData);
				$data['content'] .= $CreateFormTemplate->final_output();
				
				

			} else {
				//try to create job
				$nofile = false;
				$errormsg = "";
				if(array_key_exists('model',$_FILES)){
					$this->debugInfo["is there a file"] = "I think so";
					$this->debugInfo["Files"] = $_FILES;
					//if we submitted a model then check it
					if($_FILES['model']['error'] && $_FILES['model']['error'] != 4) {
						switch($_FILES['model']['error']){
							case 1:
							case 2:
								$errormsg .= "<p>".'$_FILES error - '."The file was too large.</p";
								$CreateFormTemplate = load_template($templatedebug['CreateForm'],"CreateForm",$CreateFormData);
								$data['content'] .= $errormsg.$CreateFormTemplate->final_output();
								break;
							default:
								$errormsg .="<p>".'$_FILES error - '."An error occurred. Please e-mail our reference staff to see if they can assist you.</p>";
								$CreateFormTemplate = load_template($templatedebug['CreateForm'],"CreateForm",$CreateFormData);
								$data['content'] .= $errormsg.$CreateFormTemplate->final_output();
								break;
						}
					} elseif ($_FILES['model']['error'] == 4) {
						$nofile = true;
						$this->debugInfo["is there a file"] = "No File";
					}
				
				} else {
					$this->debugInfo["is there a file"] = "No File";
					$nofile = true;
				}
				
				
				$uploadDir = "C:\\inetpub\\3DModels";
				$fromFiles = array("name","error","tmp_name");
				$modelData = array();
				foreach ($this->Submission->columns as $column) {
					if(array_key_exists($column,$_POST)){
						$modelData[$column] = $_POST[$column];
					}
				}
				
				if (!$nofile) {
					$fileName = date("Ymdhis")."-".$_FILES['model']['name'];
				} else {
					$fileName = "Nofile.stl";
					$modelData['patron_name'] = "No File - ".$modelData['patron_name'];
					
				}
				$modelData['final_location'] = "$uploadDir\\".$fileName;
				$modelData['test_location'] = "http://www.rpl.org/3dmodels/".$fileName;
				$modelData['submitted'] = date("Y-m-d H:i:s");
				if((!$nofile && move_uploaded_file($_FILES['model']['tmp_name'],$modelData['final_location']))||$nofile) {
					$modelData['Grams'] = 0;
					$modelData['actual_hours'] = 0;
					$modelData['actual_minutes'] = 0;
					if($this->Submission->create($modelData)) {
						//if everything worked
						$homeController = load_controller('home',$this->debugInfo,$this->config);
						return $homeController->process();
					} else {
						//if adding to the database didn't work
						$data['content'].="<p>Couldn't add information</p>";
						$data['content'].= "<pre>".print_r($this->Submission->stmt_error(),true)."</pre>";
					}
						
				} else {
					//moving the file didnt work
					$data['content'].= "<p>Couldn't Move the file</p>";
					
				}
					
				
			}			
			
			
			//fill in place holders and return page
			$baseTemplate->setData($data);
			return $baseTemplate->process();
		
		}
	}