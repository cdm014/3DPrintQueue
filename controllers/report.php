<?php

class reportController extends Controller {
	
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		$this->Attempt_file = $this->config['modelsPath']."printAttempt.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		
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
				$sql = "Select * from 3dprinting where submitted >= '$startDate' and submitted <= '$lastDate'";
				$stmt = $this->Submission->query($sql);
				$jobs =  array();
				while ($job = $stmt->fetch_assoc()) {
					$jobs[] = $job;
				}
				$this->debugInfo['report_list'] = $jobs;
				
				$NumberOfJobs = count($jobs);
				$GramsByColor = array();
				$JobsByColor = array();
				$PrintedByColor = array();
				$PickedUpByColor = array();
				$TotalHours = 0;
				$TotalPrinted = 0;
				$TotalPickedUp = 0;
				foreach($jobs as $thisJob) {
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
					
				}
				$TotalGrams = 0;
				foreach($GramsByColor as $Grams) {
					$TotalGrams += $Grams;
				}
				$reportData = array();
				$reportData['Number of Jobs'] = $NumberOfJobs;
				$reportData['Total Grams'] = $TotalGrams; 
				$reportData['Total Hours'] = $TotalHours;
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