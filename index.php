<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//load base classes for controller, model, and template
require_once dirname(__FILE__)."\\controllers\\index.php";
require_once dirname(__FILE__)."\\models\\index.php";
require_once dirname(__FILE__)."\\views\\index.php";
$debug = false;
$output = "";

//setup basic config information
$config = array();
$config['controllerPath'] = dirname(__FILE__)."\\controllers\\";
$config['viewsPath'] = dirname(__FILE__)."\\views\\";
$config['modelsPath'] = dirname(__FILE__)."\\models\\";
$config['mysql_user'] = "root";
$config['mysql_pass'] = "chester";

$debugInfo = array();
$debugInfo['GET'] = $_GET;
$debugInfo['POST'] = $_POST;
$debugInfo['errors'] = array();
$errors = &$debugInfo['errors'];

function load_controller($action, $debugInfo, $config) {
	$controllerfile = dirname(__FILE__)."\\controllers\\".$action.".php";
	if (is_file($controllerfile)) {
		require_once $controllerfile;
		$classname = $action."Controller";
		$temp = new $classname($debugInfo, $config);
		return $temp;
	} else {
		return false;
	}
}

function load_template(&$environment, $fileName, $data) {
	$temp = new Template($environment, dirname(__FILE__)."\\views\\".$fileName.".tpl", $data);
	return $temp;
}

//utility function to get a value regardless of how it was passed
function get($name) {
	if(isset($_GET[$name])) {
		return $_GET[$name];
	} elseif (isset($_POST[$name])) {
		return $_POST[$name];
	} elseif (isset($_COOKIE[$name])) {
		return $_COOKIE[$name];
	}else {
		return false;
	}
}

//check whether we want to display debug information
$debug = get('debug');
if (!$debug) {
	$debug = false;
}

//create database connection and add it to config data.
$mysqli = new mysqli("localhost","root","chester","test");
if($mysqli->connect_errno){
	$errors[] = array('heading' => "Failed to Connect to MySQL", 'Error No.' => $mysqli->connect_errno, "message" => $mysqli->connect_error);
}
$config['dbconnection'] = &$mysqli;
$debugInfo['host_info'] = $mysqli->host_info;

//what do we need to do
$action = get('action');
if(!$action) {
	$action = "login";
}
$config['action'] = $action;
$debugInfo['action'] = $action;

//Load a Controller
//figure out what file to load
$debugInfo['controller file'] = dirname(__FILE__)."\\controllers\\".$action.".php";
$debugInfo['controller class'] = $action."Controller";
$controllerfile = dirname(__FILE__)."\\controllers\\".$action.".php";
//if the file exists
if (is_file($controllerfile)) {
	if(isset($_COOKIE['password']) && $_COOKIE['password'] == "3DPr!nt!ng") {
		setcookie('password','3DPr!nt!ng',time() + 600);
	}
	//load the file
	require_once $controllerfile;
	//figure out the class name of the controller
	$controllerclass = $action."Controller";
	//Create a controller of the right type
	$controller = new $controllerclass($debugInfo, $config);
	//pass control to the controller then return what it feeds back.
	$output .= $controller->process();	
} else {
	//if the file doesn't exist add that to our debug information
	$debugInfo['controller_not_exist'] = "The Controller File Doesn't Exist";
	$errors[] = array("type" => "Controller Error", "msg" => "The controller file doesn't exist", "file" => $controllerfile);
	$debug = true;
}





/*debug */
//add our config information to debug in case we need it
$debugInfo['config'] = $config;
//if we need to display debugging information
if ($debug) {

	$debugData = array();
	$environment = array();
	//get the filename for our debug template
	$debugTemplateFileName = $config['viewsPath']."Debug-info.tpl";
	//get our debug data
	$debugData['DebugInfo'] ="<pre>".print_r($debugInfo,true)."</pre>";
	//load the debug template with the data
	$debugTemplate = new Template($environment, $debugTemplateFileName,$debugData);
	//add the debug information to the page display
	$output .= $debugTemplate->process();
}

/*final output*/
echo $output;