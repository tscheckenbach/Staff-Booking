<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdmincalController
 *
 * @author tsc
 */
class AdmincalController extends CalController
{

    /**
     *
     * @var object
     */
    protected $model;

    /**
     * Initialize
     */
    public function init()
    {
        // Laden des redirector Helper
        $this->_redirector = $this->_helper->getHelper('Redirector');

        // Laden des Models
        $this->model = new Model_Admin_Cal();

    	// Pruefen ob eingeloggt
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('/index/index');
        }

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

        $this->view->openDays = $this->model->getOpenDays();
        $this->view->openHours = $this->model->getOpenHours();
        $this->view->maxHoursOpen = $this->model->getMaxHoursOpen();
        $this->view->minOpenHours = $this->model->getMinOpenHours();
        $this->view->maxOpenHours = $this->model->getMaxOpenHours();
        $this->view->openCloseMatrix = $this->model->getOpenCloseMatrix();
        $this->view->bookedMatrix = $this->model->getBookedMatrix($weekToDisplay,true);
        $this->view->nextWeekParam = $weekParam + 1;
        $this->view->lastWeekParam = $weekParam - 1;
        $this->view->dayOfThisWeek = $weekToDisplay;
    }

    /**
     * Archiv week
     */
    public function archiveAction()
    {
        $dayOfThisWeek = $this->_request->getParam("dayOfThisWeek");
        $this->model->archiveWeek($dayOfThisWeek);
        $this->_redirect("/admincal/showcal");
    }

    /**
     * Unarchiv week
     */
    public function unarchiveAction()
    {
        $dayOfThisWeek = $this->_request->getParam("dayOfThisWeek");
        $this->model->unarchiveWeek($dayOfThisWeek);
        $this->_redirect("/admincal/showcal");
    }
}
?>
