<?php

$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');

Zend_Date::setOptions(array('format_type' => 'php'));

if(APPLICATION_ENVIRONMENT == "development")
{
	$frontController -> throwExceptions(true);
}

$layout = Zend_Layout::startMvc();
// Tell the view where it finds Zend_Dojo ViewHelper
$layout->getView()->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

unset($frontController);
