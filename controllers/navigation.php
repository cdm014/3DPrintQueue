<?php 

class navigationController extends Controller {
	function process() {
		$discard = array();
		$navTemplate = load_template($discard,"navigation",array());
		$navlinkTemplate = load_template($discard,'nav-link-td',array());
		$data = array("nav-links" => "");
		$actions = array("create" => "Create a New Job","home" => "Waiting to be printed","list_printed" => "Waiting for Contact/Pick Up","list_picked_up" => "Already Picked Up" ,"report" => "Reports","clean" => "Clean Up Database");
		foreach ($actions as $action => $action_text) {
			$navlinkTemplate->reset();
			$navlinkTemplate->setData(array("action" => $action, "action_text" => $action_text));
			$data['nav-links'] .= $navlinkTemplate->process();
		}
		$navTemplate->setData($data);
		return $navTemplate->process();
		
	}
}