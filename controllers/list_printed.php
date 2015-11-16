<?php 

class list_printedController extends Controller {
	
	function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$output = "";
		//query the table for printed submissions
		$res = $this->Submission->query("Select * from 3dprinting where printed = 1 and (picked_up is NULL or picked_up = 0)");
		$templatedebug = array();
		$templatedebug['base'] = array();
		$topTemplate = load_template($templatedebug['top'],"top",array("page_title" => "Printed Models"));
		//load base templatedebug
		$baseTemplate = load_template($templatedebug['base'],"base",array());
		$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "Printed Models"));
		$data['navigation'] = $topTemplate->process();
		
		//navigation template
		$navController = load_controller("navigation",$this->debugInfo,$this->config);
		$data['navigation'] .= $navController->process();
		
		$data['content'] = "";
		//load master template for table
		$tableTemplate = load_template($templatedebug['list-wrapper'],"list-wrapper",array());
		//build array for table headings
		$headings = array();
		foreach ($this->Submission->columns as $heading) {
			$headings[$heading] = $heading;
		}
		$headings['cellType'] = "th";
		$tableHeadingTemplate = load_template($templatedebug['table-headings'],"list-header",$headings);
		$tableData['table_column_headers'] = $tableHeadingTemplate->process();
		$tableData['table-column-rows'] = "";
		$rowTemplate = load_template($templatedebug['table-row'][],"list-item-printed",array());
		$resetTemplate = load_template($templatedebug['action-link'][],"action-link",array());
		$contactedLink = load_template($templatedebug['action-link'][],"action-link",array());
		$pickedUpLink = load_template($templatedebug['action-link'][],"action-link", array());
		while ($rowdata = $res->fetch_assoc()) {
			$resetTemplate->reset();
			$contactedLink->reset();
			$pickedUpLink->reset();
			$contactedLink->setData(array("action" =>"set_contacted","id" => "{ID}","action_text" => "Click here if Patron has been contacted","params" => ""));
			$resetTemplate->setData(array("action"=>"reset","id" => "{ID}","action_text" => "Reset {ID}", "params" => "next_action=list_printed"));
			$pickedUpLink->setData(array("action" =>"picked_up","id" => "{ID}","action_text" => "Mark as Picked Up", "params" => "next_action=list_printed"));
			$rowTemplate->reset();
			$rowdata['submitted'] .= $resetTemplate->process();
			$rowdata['cellType'] = "td";
			$rowdata['contacted'] = "<p>".$rowdata['contacted']."</p>".$contactedLink->process();
			$rowdata['picked_up'] = "<p>".$rowdata['picked_up']."</p>".$pickedUpLink->process();
			$rowdata['PrintInfo_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=PrintInfo&id={ID}";
			$rowdata['PrintInfo_text'] = "Edit Printing Info";
			$rowTemplate->setData($rowdata);
			$tableData['table-column-rows'] .= $rowTemplate->process();
		}
		
		//build data for table headings
		
		$data['header'] = $headerTemplate->process();
		$tableTemplate->setData($tableData);
		$data['content'] .= $tableTemplate->process();
		$data['footer'] = "";
		$data['debug'] = "";
		$baseTemplate->setData($data);
		return $baseTemplate->process();
		
	}
	
}