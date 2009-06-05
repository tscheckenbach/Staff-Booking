<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_config;

    protected function _initConfig()
    {
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH."/configs/application.ini",APPLICATION_ENV);
        Zend_Registry::set('config', $this->_config);
        Zend_Registry::set('env', APPLICATION_ENV);
    }

        protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => APPLICATION_PATH));
        return $moduleLoader;
    }

    protected function _initViewHelpers()
    {

        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('Supibooking');

        // Tell the view where it finds Zend_Dojo ViewHelper
        $layout->getView()->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
    }

    protected function _initDatabase()
    {
        
    }
}

