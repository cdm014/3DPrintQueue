<?php

class loginController extends Controller {
	function process() {
		$realpassword = "3DPr!nt!ng";
		$password = get('password');
		if (!$password || $password != $realpassword) {
			//display login form
			$unusedArray = array();
			$formTemplate = load_template($unusedArray,"loginForm",array());
			//I don't do anything with this array for now
			$templateDebug=array();
			$data = array();
			$data['header'] = "";
			$data['content'] = "";
			$data['footer'] = "";
			$data['debug'] = "";
			$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue"));
			$data['header'] = $headerTemplate->process();
			$output = "";
			$baseTemplate = load_template($templateDebug['base'],"base",array());
			$topTemplate = load_template($templateDebug['top'],"top",array("page_title"=>"Login Form"));
			$data['navigation'] = $topTemplate->process();
			$headerTemplate = load_template($templatedebug['header'],"header",array("pageTitle" => "3D Printer Queue Login"));
			$data['content'] = $formTemplate->process();
			$baseTemplate->setData($data);
			return $baseTemplate->process();
		
		} else {
			//set login cookie to disable password request for 10 minutes
			setcookie('password','3DPr!nt!ng',time() + 600);
			//pass control onto home
			$config['action'] = "home";
			$homeController = load_controller('home',$this->debugInfo,$this->config);
			return $homeController->process();
		}
	}
}