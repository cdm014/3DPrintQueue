<?php 

class set_contactedController extends Controller {
	var $Submission;
	public function process() {
		$this->Submission_file = $this->config['modelsPath']."submissions.php";
		require_once $this->Submission_file;
		$this->Submission =new Submission($this->config);
		$id = get('id');
		if($this->Submission->set_contacted($id)) {
			$cont = load_controller('list_printed',$this->debugInfo, $this->config);
			
		} else {
			$cont = load_controller('list_printed',$this->debugInfo, $this->config);
		}
		return $cont->process();			
	}
}