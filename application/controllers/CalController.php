<?php

class CalController extends Zend_Controller_Action
{
	/**
     * Redirector-Helper Object
     *
     * @var obj
     */
	protected $_redirector;

     /**
     *
     * @var object
     */
    protected $model;

    /**
	 * Username
	 * @var string
	 */
	protected $username = "";

    /**
     * Initialize
     */
    public function init()
    {
		// Laden des redirector Helper
		$this->_redirector = $this->_helper->getHelper('Redirector');

        // Laden des Models
        $this->model = new calModel();

    	// Pruefen ob eingeloggt
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('index/index');
        }

        // Load userinfo
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $this->username = $userInfo->username;

    }

    public function indexAction()
    {
        // action body
    }

    public function showAction()
    {
        // action body
    }

    public function bookerAction()
    {
        // action body
    }


}





