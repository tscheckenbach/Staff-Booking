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
        $this->model = new Model_Cal();

    	// Pruefen ob eingeloggt
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('index/index');
        }

        // Load userinfo
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $this->username = $userInfo->username;

    }

	/**
	 * Show cal
	 */
    public function showcalAction()
    {
        if(!$this->_request->isGet("week")) {
                $weekParam = 0;
        } else {
                $weekParam = $this->_request->getParam("week");
        }
        $weekToDisplay = time()+(86400*7)*$weekParam;

	$this->model->makeCalCaption($weekToDisplay);
        $this->view->isArchived = $this->model->isWeekArchived($weekToDisplay);

        $this->view->username = $this->username;
        $this->view->openDays = $this->model->getOpenDays();
        $this->view->openHours = $this->model->getOpenHours();
        $this->view->maxHoursOpen = $this->model->getMaxHoursOpen();
        $this->view->minOpenHours = $this->model->getMinOpenHours();
        $this->view->maxOpenHours = $this->model->getMaxOpenHours();
        $this->view->openCloseMatrix = $this->model->getOpenCloseMatrix();
        $this->view->bookedMatrix = $this->model->getBookedMatrix($weekToDisplay);
        $this->view->nextWeekParam = $weekParam + 1;
        $this->view->lastWeekParam = $weekParam - 1;
    }

    /**
     * Book or unbook
     */
    public function bookerAction()
    {
            $this->_helper->viewRenderer->setNoRender();
            $selectedDate = $this->_request->getParam("selectedDate");
            $selectedWeek = $this->_request->getParam("week");
            $model = new bookModel();
            switch($this->_request->getParam("do")){
                case "book": $model->book($selectedDate);
                                        break;
                case "unbook": $model->unbook($selectedDate);
                                        break;
                case "bookwholeday": $model->handleWholeDay($selectedDate, "book");
                            $this->_redirect('/cal/showcal/week/' . $selectedWeek);
                            break;
                case "unbookwholeday": $model->handleWholeDay($selectedDate, "unbook");
                            $this->_redirect('/cal/showcal/week/' . $selectedWeek);
                            break;
            }
    }
}





