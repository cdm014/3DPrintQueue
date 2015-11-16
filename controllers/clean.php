<?php 
	class cleanController extends Controller {
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
			
			$fileBase = "C:\\inetpub\\3dmodels";
			$locBase = "http://www.rpl.org/3dmodels";
			
			if($handle = opendir("C:\\inetpub\\3dmodels")) {
				while (false !== ($entry = readdir($handle))) {
					if($entry != "." && $entry != "..") {
						$data['content'] .= "<p>$entry - ";
						$record = $this->Submission->retrieveByFile($entry);
						
						if($this->Submission->retrieveByFile($entry)) {
							$data['content'] .= "in database";
						} else {
							$data['content'] .= "NOT IN DATABASE";
							if (unlink($fileBase."\\".$entry)) {
								$data['content'] .= " - File Removed ";
							} else {
								$data['content'] .= " - ERROR REMOVING FILE ";
							}
							
						}
						$data['content'].= "</p>\r\n";
						$this->debugInfo[] = array("file" => $entry, "record" => $record);
					}
				}
			}
			
			//fill in place holders and return page
			$baseTemplate->setData($data);
			return $baseTemplate->process();
		
		}
	}