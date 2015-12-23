<?php 
	class cleanController extends Controller {
		var $Submission;
		public function process() {
			$this->Submission_file = $this->config['modelsPath']."submissions.php";
			$this->Attempt_file = $this->config['modelsPath']."printAttempt.php";
			require_once $this->Submission_file;
			require_once $this->Attempt_file;
			$this->Submission =new Submission($this->config);
			$this->Attempt = new printAttempt($this->config);
			
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
			//display form
			$formTemplate = load_template($templatedebug['form'],"cleanForm",array());
			$data['content'].= $formTemplate->process();
			$cleandate = get("endDate");
			if ($cleandate <> "") {
				$sql = "Select * from 3dprinting where submitted < '$cleandate'";
				$stmt = $this->Submission->query($sql);
				
				while ($job = $stmt->fetch_assoc()) {
					$id = $job['ID'];
					$attempts = array();
					$attempts = $this->Attempt->fetch_for_submission($id);
					foreach($attempts as $attempt) {
						$attemptid = $attempt['id'];
						$this->Attempt->delete($attemptid);
					}
					$this->Submission->delete($id);
					
					
				}
			}
			
			//file clean up routine
			$fileBase = "C:\\inetpub\\3dmodels";
			$locBase = "http://www.rpl.org/3dmodels";
			
			if($handle = opendir("C:\\inetpub\\3dmodels")) {
				while (false !== ($entry = readdir($handle))) {
					if($entry != "." && $entry != "..") {
						$data['content'] .= "<p>$entry - ";
						$record = $this->Submission->retrieveByFile($entry);
						
						if($this->Submission->retrieveByFile($entry)) {
							$data['content'] .= "in database";
							if($record['abandoned'] == 1) {
								//job abandoned
								$data['content'] .= "Abandoned ";
								if (unlink($fileBase."\\".$entry)) {
									$data['content'] .= "- File Removed";
								} else {
									$data['content'] .= "- ERROR REMOVING FILE";
								}
							}
								
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