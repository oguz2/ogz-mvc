<?php
session_start();
ob_start();

error_reporting(E_ALL);

date_default_timezone_set('Europe/Istanbul');
$dateAndTime = date ("Y.m.d H:i:s");
$lang = "tr_TR";

$path = array(
	'basePath'			=> "kp",
	'systemPath'		=> "System",
	'appPath'			=> "Public",
	'controllerPath'	=> "Controller",
	'viewPath'			=> "View",
	'model'				=> "Model",
	'logPath'			=> 'Logs',
	'configPath'		=> 'Config',
	'libraryPath'		=> 'Library',
	'lang' 				=> 'Language'
	
);
if(!isset($path["appPath"]) OR !is_dir(realpath($path["appPath"])) )
{
	if(realpath("Public") !== false)
	{
		$path["appPath"] =  strtr(realpath(realpath("Public")), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'PUBLIC' folder. Firstly, be again upload this folder on your system.";
		die();
	}
}
else
{
	$path["appPath"] = strtr(realpath($path["appPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}


if(!isset($path["configPath"]) OR !is_dir(realpath($path["configPath"])) )
{
	if(realpath("Config") !== false)
	{
		$path["configPath"] =  strtr(realpath(realpath("Config")), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'PUBLIC' folder. Firstly, be again upload this folder on your system.";
		die();
	}
}
else
{
	$path["configPath"] = strtr(realpath($path["configPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}



if(!isset($path["systemPath"]) OR !is_dir(realpath($path["systemPath"])) )
{
	if(realpath("System") !== false)
	{
		$path["systemPath"] = strtr(realpath("System"), '/', '\\').DIRECTORY_SEPARATOR;
		
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'SYSTEM' folder. Firstly, be again upload this folder on your system.";
		die();
	}	
}	
else
{
	$path["systemPath"] = strtr(realpath($path["systemPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}


$path["controllerPath"] = trim($path["appPath"]).trim($path["controllerPath"]);
if(!isset($path["controllerPath"]) OR !is_dir(realpath($path["controllerPath"])) )
{
	if(realpath($path["appPath"]. "Controller") !== false)
	{
		$path["controllerPath"] = strtr(realpath(realpath($path["appPath"]. "Controller")), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'CONTROLLER' folder. Firstly, be again upload this folder on your system.";
		die();
	}	
}	
else
{
	$path["controllerPath"] = strtr(realpath($path["controllerPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}

$path["viewPath"] = trim($path["appPath"]).trim($path["viewPath"]);
if(!isset($path["viewPath"]) OR !is_dir(realpath($path["viewPath"])) )
{
	if( realpath($path["appPath"]. "View") !== false)
	{
		$path["viewPath"] = strtr(realpath($path["appPath"]. "View"), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'VIEW' folder. Firstly, be again upload this folder on your system.";
		die();
	}	
}	
else
{
	$path["viewPath"] = strtr(realpath($path["viewPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}

$path["model"] = trim($path["appPath"]).trim($path["model"]);
if(!isset($path["model"]) OR !is_dir(realpath($path["model"])) )
{
	if(realpath("Model") !== false)
	{
		$path["model"] = strtr(realpath($path["appPath"]. "Model"), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'MODEL' folder. Firstly, be again upload this folder on your system.";
		die();
	}	
}	
else
{
	$path["model"] = strtr(realpath($path["model"]), '/', '\\').DIRECTORY_SEPARATOR;
}

if(!isset($path["logPath"]) OR !is_dir(realpath($path["logPath"])) )
{
	if(realpath($path["logPath"]) !== false)
	{
		$path["logPath"] = strtr(realpath($path["logPath"]), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'LOGS' folder. Firstly, be again upload this folder on your system.";		
		die();
	}	
}	
else
{
	$path["logPath"] = strtr(realpath($path["logPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}


if(!isset($path["libraryPath"]) OR !is_dir(realpath($path["libraryPath"])) )
{
	if(realpath($path["libraryPath"]) !== false)
	{
		$path["libraryPath"] = strtr(realpath($path["libraryPath"]), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'LOGS' folder. Firstly, be again upload this folder on your system.";		
		die();
	}	
}	
else
{
	$path["libraryPath"] = strtr(realpath($path["libraryPath"]), '/', '\\').DIRECTORY_SEPARATOR;
}

if(!isset($path["lang"]) OR !is_dir(realpath($path["lang"])) )
{
	if(realpath($path["lang"]) !== false)
	{
		$path["lang"] = strtr(realpath($path["lang"]), '/', '\\').DIRECTORY_SEPARATOR;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo "Your system does not have your 'LOGS' folder. Firstly, be again upload this folder on your system.";		
		die();
	}	
}	
else
{
	$path["lang"] = strtr(realpath($path["lang"]), '/', '\\').DIRECTORY_SEPARATOR;
}



define ('systemPath', $path["systemPath"]);
define('appPath', $path["appPath"]);
define('controllerPath', $path["controllerPath"]);
define ('viewPath', $path["viewPath"]);
define('modelPath', $path["model"]);
define('logsPath', $path["logPath"]);
define('configPath', $path["configPath"]);
define('libraryPath', $path['libraryPath']);
define("basePath", $path["basePath"]);
define('lang', $path["lang"]);



require_once($path["systemPath"]."autoLoad.php");
$load = new autoLoad;

if(function_exists("log_Save") === false)
{
	$load->page_load("log");
    function log_Save ( $title, $info, $message)
	{
		static $var ;
		if($var == NULL) $var = new errorLogs;
			$var->errorSave($title, $info, $message);
	}
}

if(function_exists("class_load") === false)
{
	function class_load($filename, $data = NULL, $foldername = NULL, $className = NULL)
	{
		static $var;
		if($var === NULL) $var = new autoLoad;
			return $var->class_load($filename, $data, $foldername, $className);
	}
}

if(function_exists("lang") === false)
{
	$load->page_load($lang.".php", lang);
    function lang ($value, $function_name = "error_info")
	{
		if($function_name != "error_info")
			$function_name = gettype($value) == "integer" || gettype($value) == "int" ? "lang" : $function_name;
			
		static $var ;
		if($var == NULL) $var = new language;
			return $var->$function_name($value);
	}
}


$load->url_page_load();

ob_end_flush();

unset($dateAndTime, $path, $lang, $load);
?>