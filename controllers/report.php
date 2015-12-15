<?php

class reportController extends Controller {
	
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		$this->Attempt_file = $this->config['modelsPath']."printAttempt.php";
		require_once $this->Submission_file;
		require_once $this->Attempt_file;
		$this->Submission =new Submission($this->config);
		$this->Attempt = new printAttempt($this->config);
		
		//if a date range has been submitted
		$output = "";
		//load base template
		$baseTemplate = load_template($templatedebug['base'],"base",array());
		$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Models Not Yet Printed"));
		$data['header'] = "";
		$data['content'] = "";
		$data['footer'] = "";
		$data['debug'] = "";
		$data['navigation'] = $topTemplate->process();
		//load header template
		$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
		$data['header'] = $headerTemplate->process();
		//navigation template
		$navController = load_controller("navigation",$this->debugInfo,$this->config);
		$data['navigation'] .= $navController->process();
		$this->debugInfo['processor messages'] = array();
		$mydb = &$this->debugInfo['processor messages'];
		$mydb[] = "homeController->process called";
		$mydb[] = "Directory: ".dirname(__FILE__);
		
		$startDate = get("startDate");
		$lastDate = get("endDate");
		$mydb[] = "startDate: $startDate";
		$mydb[] = "lastDate: $lastDate";
		
		$ReportOptionsArray = array();
		$ReportOptionsArray['startDate'] = $startDate;
		$ReportOptionsArray['endDate'] = $lastDate;
		
		$ReportOptionTemplate = load_template($templatedebug['FormOptions'],"reportOptions",$ReportOptionsArray);
		$data['content'] .= $ReportOptionTemplate->process();
		
		$ReportWrapperData = array();
		$ReportHeaderTemplate = load_template($templatedebug['ReportHeader'],"report-header",$ReportOptionsArray);
		$ReportWrapperData['reportHeader'] = $ReportHeaderTemplate->process();
		
		$ReportWrapperTemplate = load_template($templatedebug['ReportWrapper'],"report-wrapper",$ReportWrapperData);
		$data['content']. $ReportWrapperTemplate->process(); 
		
	
		
		
		$startDate = get("startDate");
		$lastDate = get("endDate");
		$mydb[] = "startDate: $startDate";
		$mydb[] = "lastDate: $lastDate";
		if($startDate && $lastDate) {
				//display report
				$data['content'].="<p>Options ARE selected</p>";
				//need to fix lastdate
				$sql = "Select * from 3dprinting where submitted >= '$startDate' and submitted <= '$lastDate 23:59:59'";
				$stmt = $this->Submission->query($sql);
				$jobs =  array();
				while ($job = $stmt->fetch_assoc()) {
					$jobs[] = $job;
				}
				//$jobs holds submissions
				//$this->debugInfo['report_list'] = $jobs;
				$this->debugInfo['Attempts'] = array();
				
				$NumberOfJobs = count($jobs);
				$GramsByColor = array();
				$JobsByColor = array();
				$PrintedByColor = array();
				$PickedUpByColor = array();
				$Abandoned = array();
				$Abandoned['GramsByColor'] = array();
				$Abandoned['JobsByColor'] = array();
				$Abandoned['TotalHours'] = 0;
				$Abandoned['TotalJobs'] = 0;
				
				$TotalHours = 0;
				$TotalPrinted = 0;
				$TotalPickedUp = 0;
				$this->debugInfo['AttemptsCount'] = array();
				foreach($jobs as $thisJob) {
					
					$Attempts = array();
					$Attempts = $this->Attempt->fetch_for_submission($thisJob['ID']);
					$JobID = $thisJob['ID'];
					
					//$this->debugInfo['Attempts'][$JobID] = $Attempts;
					$this->debugInfo['AttemptsCount'][$JobID] = array();
					$this->debugInfo['AttemptsCount'][$JobID]['count']	= count($Attempts);
					
					$color = $thisJob['Color'];
					$grams = $thisJob['Grams'];
					$hours = $thisJob['actual_hours'] + ($thisJob['actual_minutes'] / 60);
					
					if(array_key_exists($color,$GramsByColor)) {
						$GramsByColor[$color] += $grams;
					} else {
						$GramsByColor[$color] = $grams;
					}
					
					if (array_key_exists($color,$JobsByColor)) {
						$JobsByColor[$color]++;
					} else {
						$JobsByColor[$color] = 1;
					}
					if(!array_key_exists($color,$PrintedByColor)){
						$PrintedByColor[$color] = 0;
					}
					
					if (!array_key_exists($color,$PickedUpByColor)){
						$PickedUpByColor[$color] = 0;
					}
					if($thisJob['printed'] == 1) {
						$PrintedByColor[$color]++;
						$TotalPrinted++;
					}
					if($thisJob['picked_up'] == 1) {
						$PickedUpByColor[$color]++;
						$TotalPickedUp++;
					}
					$TotalHours += $hours;
					if($thisJob['abandoned']) {
						$this->debugInfo['Abandoned'][$JobID] = 'Abandoned';
						if(array_key_exists($color,$Abandoned['GramsByColor'])) {
							$Abandoned['GramsByColor'][$color] += $grams;
						} else {
							$Abandoned['GramsByColor'][$color] = $grams;
						}
						
						if (array_key_exists($color,$Abandoned['JobsByColor'])) {
							$Abandoned['JobsByColor'][$color]++;
						} else {
							$Abandoned['JobsByColor'][$color] = 1;
						}
						$Abandoned['TotalHours'] += $hours;
						$Abandoned['TotalJobs']++;
					}
					if(count($Attempts) > 0) {
						$this->debugInfo['AttemptsCount'][$JobID]['msg'] = 'trying';
						$this->debugInfo['AttemptsCount'][$JobID]['Attempts'] = $Attempts;
						
						foreach ($Attempts as $thisAttempt) {
							//$this->debugInfo['AttemptsCount'][$JobID]['count2'] = count($jobAttempts);
							if (count($thisAttempt) > 0) {
								if (!$thisAttempt['successful']) {
									$color = $thisAttempt['color'];
									$grams = $thisAttempt['Grams'];
									$hours = $thisAttempt['Hours'] + ($thisAttempt['Minutes'] / 60);
									if(array_key_exists($color,$GramsByColor)) {
										$GramsByColor[$color] += $grams;
									} else {
										$GramsByColor[$color] = $grams;
									}
									if (array_key_exists($color,$JobsByColor)) {
										$JobsByColor[$color]++;
									} else {
										$JobsByColor[$color] = 1;
									}
									$TotalHours += $hours;
									
									if($thisJob['abandoned']) {
										if(array_key_exists($color,$Abandoned['GramsByColor'])) {
											$Abandoned['GramsByColor'][$color] += $grams;
										} else {
											$Abandoned['GramsByColor'][$color] = $grams;
										}
										if (array_key_exists($color,$Abandoned['JobsByColor'])) {
											$Abandoned['JobsByColor'][$color]++;
										} else {
											$Abandoned['JobsByColor'][$color] = 1;
										}
										$Abandoned['TotalHours'] += $hours;
									}
								}
								
								
							}
						}
					} else {
						$this->debugInfo['AttemptsCount'][$JobID]['msg'] = 'skipping';
					}
					
				}
				$TotalGrams = 0;
				foreach($GramsByColor as $Grams) {
					$TotalGrams += $Grams;
				}
				
				$Abandoned['TotalGrams'] = 0;
				
				foreach($Abandoned['GramsByColor'] as $Grams) {
					$Abandoned['TotalGrams'] += $Grams;
				}
				$reportData = array();
				$reportData['Number of Jobs'] = $NumberOfJobs;
				$reportData['Abandoned Jobs'] = $Abandoned['TotalJobs'];
				$reportData['Total Grams'] = $TotalGrams; 
				$reportData['Abandoned Total Grams'] = $Abandoned['TotalGrams'];
				$reportData['Total Hours'] = $TotalHours;
				$reportData['Abandoned Hours'] = $Abandoned['TotalHours'];
				$reportData['Grams By Color']	= $GramsByColor;
				
				$reportData['Jobs By Color'] = $JobsByColor;
				$reportData['Printed By Color'] = $PrintedByColor;
				$reportData['Picked Up By Color'] = $PickedUpByColor;
				
				$reportData['Total Printed'] = $TotalPrinted;
				$reportData['Total Picked Up'] = $TotalPickedUp;
				$this->debugInfo['Report Data'] = $reportData;
				
				
				$ReportWrapperData = array();
				
				$ReportHeaderTemplate = load_template($templatedebug['ReportHeader'],"report-header",$ReportOptionsArray);
				$ReportWrapperData['reportHeader'] = $ReportHeaderTemplate->process();
				$ReportWrapperData['reportData'] = "";
				
				$ReportRowTemplate = load_template($templatedebug['ReportRow'],"report-data-row",array());
				
				
				foreach($reportData as $key => $value) {
					$ReportRowTemplate->reset();
					$temparray = array("key" => $key, "value" => $value);
					$ReportRowTemplate->setData($temparray);
					if(!is_array($value)) {
						$ReportWrapperData['reportData'] .= $ReportRowTemplate->process();
					}
				}
				
				foreach($reportData['Grams By Color'] as $color => $grams) {
					$ReportRowTemplate->reset();
					$temparray = array("key" => $color." grams", "value" => $grams);
					$ReportRowTemplate->setData($temparray);
					$ReportWrapperData['reportData'] .= $ReportRowTemplate->process();
					$ReportRowTemplate->reset();
					$abandonedGrams = 0;
					if(!array_key_exists($color,$Abandoned['GramsByColor'])) {
						$abandonedGrams = 0;
					} else {
						$abandonedGrams = $Abandoned['GramsByColor'][$color];
					}
						
					$temparray = array("key" => "abandoned ".$color." grams", "value" => $abandonedGrams);
					$ReportRowTemplate->setData($temparray);
					$ReportWrapperData['reportData'] .= $ReportRowTemplate->process();
					
				}
				
				
				$ReportWrapperTemplate = load_template($templatedebug['ReportWrapper'],"report-wrapper",$ReportWrapperData);
				$data['content'] .= $ReportWrapperTemplate->process(); 
				
				
		} else {
			
			//display form
			$data['content'].="<p>Options not selected</p>";
			
			
			
			
			
		}
		$baseTemplate->setData($data);
		return $baseTemplate->process();
			
	
	}
}