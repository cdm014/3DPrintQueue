<?php 

class list_picked_upController extends Controller {
	var $Submission;
	function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission = new Submission($this->config);
		//setup output variable
		$output = "";
		
		//setup data for templates/views
		$data = array();
		$data['header'] = "";
		$data['content'] = "";
		$data['footer'] = "";
		$data['debug'] = "";
		$templateDebug = array();
		//load our base page template
		$baseTemplate = load_template($templateDebug['base'],"base",array());
		
		//load top of page
		$topTemplate = load_template($templateDebug['top'],"top",array("page_title" => "Picked Up Already"));
		//add top of page to basic navigation area
		$data['navigation'] = $topTemplate->process();
		//load information for <head> tag
		$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
		$data['header'] = $headerTemplate->process();
		//navigation template
		$navController = load_controller("navigation",$this->debugInfo,$this->config);
		$data['navigation'] .= $navController->process();
		//array to hold our column headings
		$headings = array();
		foreach ($this->Submission->columns as $heading) {
			$headings[$heading] = $heading;
		}
		//set the cell type to <th>
		$headings['cellType'] = "th";
		$array = array();
		//template to generate the table headers just call $headingsTemplate->process();
		$headingsTemplate = new Template($array,$this->config['viewsPath']."list-header.tpl",$headings);
		$ListWrapperTplFile = $this->config['viewsPath']."list-wrapper.tpl";
		$res = $this->Submission->query("select * from 3dprinting where picked_up > 0 order by submitted asc");
		$tblText = "";
		$tblData = array();
		$tblData['table_column_headers'] = $headingsTemplate->process();
		$tblData['table-column-rows'] = "";
		$list_row_file = $this->config['viewsPath']."list-item-printed.tpl";
		$list_row_string = file_get_contents($list_row_file);
		$junk = array();
		$actionLinkFile = $this->config['viewsPath']."action-link.tpl";
		$resetTemplate = new Template($junk,$actionLinkFile,array());
		while($rowdata = $res->fetch_assoc()){
			$tblTemplateDebug = array();
			$resetTemplate->setData(array("action"=>"reset","id" => "{ID}","action_text" => "Reset {ID}","params" =>"&next_action=list_picked_up"));
			$rowdata['submitted'] .= $resetTemplate->process();
			$rowdata['cellType'] = "td";
			$rowdata['PrintInfo_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=PrintInfo&id={ID}";
			$rowdata['PrintInfo_text'] = "Edit Printing Info";
			$rowTemplate = new Template($tblTemplateDebug,$list_row_file,$rowdata);
			$tblData['table-column-rows'].=$rowTemplate->process();
		}
		$finalTemplate = new Template($junk,$ListWrapperTplFile,$tblData);
		$data['content'] = $finalTemplate->process();
		$baseTemplate->setData($data);
		return $baseTemplate->process();
			
	}
}