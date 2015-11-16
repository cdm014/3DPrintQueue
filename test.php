<?php
/*
ini_set('display_errors', 'On');
error_reporting(E_ALL);
*/
require_once dirname(__FILE__)."\\views\\index.php";
$environment = array();

file_get_contents(null);
$test_template = new Template($environment, dirname(__FILE__)."\\views\\list-column-cell.tpl",array( "cellType" => "td", "data1" => "{patron_name}","patron_name" => "Chester Mealer"));
$o = $test_template->process();
//$o .= "\n\n<pre>".print_r($environment,true)."</pre>";
echo $o;