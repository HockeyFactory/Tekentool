<?php

define("BASEPATH", dirname(__FILE__));


function __autoload($class_name)
{
	if (!class_exists($class_name))
	{
		//where to look for files?
		$paths = array('include', 'include/libs', 'include/Classes');

		//convert underscores to slashes
		$filename = str_replace("_", "/", $class_name) . ".php";

		//now try to locate the file
		foreach ($paths as $path)
		{
			if (file_exists(BASEPATH . "/". $path. "/" . $filename))
			{
				require_once BASEPATH . "/". $path. "/" . $filename;
				break;
			}
		}
	}
}


error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Amsterdam');


//set enviroment
if (isset($_SERVER['HF_ENV']))
{
	define("HF_ENV", strtolower($_SERVER['HF_ENV']));
}
else
{
	define("HF_ENV", "production");
}

//execution
try {
	KLogger::instance(BASEPATH . '/include/logs/', (HF_ENV == 'production' ? Klogger::WARN : Klogger::NOTICE));

	if (!empty($_SERVER['PATH_INFO']))
	{
		$path = array_filter(explode("/", $_SERVER['PATH_INFO']));
	}
	else
	{
		$path = array(
			'controller' => 'index',
			'action' => 'index',
			'id' => 0
		);
	}

	if (count($path) == 1)
	{
		$controller = 'index';
		$action = array_shift($path);
		$id = array_shift($path);
	} 
	else
	{
		$controller = array_shift($path);
		$action = array_shift($path);
		$id = array_shift($path);
	}


	$class_name = "Classes_Controller_" . ucfirst(Helper::alpha_num($controller));
	$controller = new $class_name();

	$action = "action_" . Helper::alpha_num($action);

	if (method_exists($controller, $action))
	{
		echo $controller->$action($id);
	}
	else
	{
		throw new Exception('pagina ' . $class_name . '/' . $action . ' niet gevonden');
	}
}
catch (Exception $e)
{
	header("HTTP/1.0 500");

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
	{
		echo json_encode(array('error' => $e->getMessage()));
	}
	else
	{
		echo "Er is een fout opgetreden: <pre>" . $e->getMessage() . "</pre>";
		if ($e->getTrace())
		{
			echo "<pre>" . print_r($e->getTraceAsString(), true) . "</pre>";
		}
	}

	Klogger::instance()->logError($e);

	exit;
}
