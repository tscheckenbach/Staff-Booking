<?php
 
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../library');
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../application/models');
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../application/controllers');
require_once("Zend/Loader.php");
Zend_Loader::registerAutoload(); 

defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', '../application');

// Activate error-reporting on our development environment, else we are live
if($_SERVER['HTTP_HOST'] == "supibooking.this.loc")
{
	error_reporting(E_ALL);
	ini_set('display_errors','on');
	$envConfig = new Zend_Config_Ini(APPLICATION_PATH.'/config.ini','development');
	define('APPLICATION_ENVIRONMENT', 'development');
	
	$db = new Zend_Db_Adapter_Pdo_Mysql(array(
	    'host'     => $envConfig->database->params->host,
	    'username' => $envConfig->database->params->username,
	    'password' => $envConfig->database->params->password,
	    'dbname'   => $envConfig->database->params->dbname
	));

        // FirePHP Logger
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        
        // FirePHP Profiler -> profiling all DB-Querys
        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);

        // Attach the profiler to the db adapter
        $db->setProfiler($profiler);
} else {
	error_reporting(0);
	ini_set('display_errors','off');
	$envConfig = new Zend_Config_Ini(APPLICATION_PATH.'/config.ini','live');
	define('APPLICATION_ENVIRONMENT', 'live');
	
	$db = new Zend_Db_Adapter_Pdo_Mysql(array(
	    'host'     => $envConfig->database->params->host,
	    'username' => $envConfig->database->params->username,
	    'password' => $envConfig->database->params->password,
	    'dbname'   => $envConfig->database->params->dbname
	));
}

// Activate default Adapter
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db);
try
{
	require_once(APPLICATION_PATH."/bootstrap.php");
} catch (Exception $exception)
{
	print "Es ist ein Fehler aufgetreten: ".$exception->getMessage()."<br /><pre>".$exception->getTraceAsString()."</pre>";
	exit(1);
} 

Zend_Controller_Front::getInstance()->dispatch();