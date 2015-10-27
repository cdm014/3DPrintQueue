<?php 

class homeController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$this->debugInfo['homeController->Submission'] = $this->Submission;
		//$this->tableHeadings = $this->Submission->columns;
		//$mydb[] = $this->tableHeadings;
		
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
		
		//navigation template
		$navController = load_controller("navigation",$this->debugInfo,$this->config);
		$data['navigation'] .= $navController->process();
		
		
		$this->debugInfo['processor messages'] = array();
		$mydb = &$this->debugInfo['processor messages'];
		$mydb[] = "homeController->process called";
		$mydb[] = "Directory: ".dirname(__FILE__);
		$output .= "<h1>It Worked!</h1>";	
		
		$mydb[] = "calling homeController->assemble_list()";
		$data['content'] .= $this->assemble_list();
		
		$data['header'] = $headerTemplate->process();
		
		
		$baseTemplate->setData($data);
		return $baseTemplate->process();
		
	}
	function assemble_list() {
		$mydb = &$this->debugInfo['processor messages'];
		$ListWrapperTplFile = $this->config['viewsPath']."list-wrapper.tpl";
		$mydb[] = "List Wrapper File: $ListWrapperTplFile";
		
		$res = $this->Submission->query("select * from 3dprinting where printed is null order by submitted asc");
		$o = "";
		$data = array();
		$data['table_column_headers'] = $this->generate_table_headings();
		$data['table-column-rows'] = "";
		$list_row_file = $this->config['viewsPath']."list-item.tpl";
		$list_row_string = file_get_contents($list_row_file);
		$junk = array();
		$actionLinkFile = $this->config['viewsPath']."action-link.tpl";
		$resetTemplate = new Template($junk,$actionLinkFile,array());
		$deleteTemplate = new Template($junk,$actionLinkFile,array());
		$mydb[] = "List Item File: $list_row_file";
		while ($rowdata = $res->fetch_assoc()){
			$TemplateDebug = array();
			$resetTemplate->setData(array("action"=>"reset","id" => "{ID}","action_text" => "Reset {ID}"));
			$deleteTemplate->setData(array("action"=>"delete","id"=>"{ID}","action_text" => "Delete This Job"));
			$this->debugInfo['reset-test'][] = $rowdata['ID'];
			$rowdata['submitted'] .= $resetTemplate->process();
			$rowdata['submitted'] .= $deleteTemplate->process();
			$rowdata['cellType'] = "td";
			$rowdata['log_print_attempt_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=printAttempt&id={ID}";
			$rowdata['log_print_attempt_text'] = "log print attempt";
			$rowdata['printed_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=printed&id={ID}";
			$rowdata['printed_text'] = "Set as Printed";
			$rowdata['PatronInfo_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=PatronInfo&id={ID}";
			$rowdata['PatronInfo_text'] = "Edit Patron Info";
			$rowdata['PrintInfo_url'] = "http://staff.rpl.org/3dprintqueue/index.php?action=PrintInfo&id={ID}";
			$rowdata['PrintInfo_text'] = "Edit Printing Info";
			$rowTemplate = new Template($TemplateDebug,$list_row_file,$rowdata);
			$data['table-column-rows'] .= $rowTemplate->process();
		}
		$this->debugInfo['data'] = $data;
		
		$TemplateDebug = array();
		$final_Template = new Template($TemplateDebug,$ListWrapperTplFile,$data);
		$o .= $final_Template->process();
		return $o;
	}
	
	function generate_table_headings() {
		$o = "";
		$headings = array();
		foreach ($this->Submission->columns as $heading) {
			$headings[$heading] = $heading;
		}
		$headings['cellType'] = "th";
		$array = array();
		$headingsTemplate = new Template($array,$this->config['viewsPath']."list-header.tpl",$headings);
		$o .= $headingsTemplate->process();
		return $o;
	}
}