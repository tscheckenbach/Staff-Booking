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
class AdmincalController extends CalController {

    /**
     *
     * @var object
     */
    protected $model;

    /**
     * Initialize
     */
    public function init() {
        // Laden des redirector Helper
        $this->_redirector = $this->_helper->getHelper('Redirector');

        // Laden des Models
        $this->model = new admincalModel();

        // Pruefen ob eingeloggt
        if(!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index/index');
        }

    }

    /**
     * Show cal
     */
    public function showcalAction() {
        if(!$this->_request->isGet("week")) {
            $this->weekParam = date("W",time());
        } else {
            $this->weekParam = $this->_request->getParam("week");
        }

        $kw1 = mktime(0,0,0,1,4,date("Y"));
        $weekToDisplay = $kw1 + 86400 * (7*($this->weekParam-1) - date('w', $kw1)+1);
        $shownCalendarWeek = date("W",$weekToDisplay);

        $this->model->makeCalCaption($weekToDisplay);
        $this->view->isArchived = $this->model->isWeekArchived($weekToDisplay);

        $this->view->openDays = $this->model->getOpenDays();
        $this->view->openHours = $this->model->getOpenHours();
        $this->view->maxHoursOpen = $this->model->getMaxHoursOpen();
        $this->view->minOpenHours = $this->model->getMinOpenHours();
        $this->view->maxOpenHours = $this->model->getMaxOpenHours();
        $this->view->openCloseMatrix = $this->model->getOpenCloseMatrix();
        $this->view->bookedMatrix = $this->model->getBookedMatrix($weekToDisplay,true);
        $this->view->dayOfThisWeek = $weekToDisplay;
        $this->view->shownCalendarWeek = $shownCalendarWeek;
        $this->view->thisCalendarWeek = date("W");
    }

    /**
     * Archiv week
     */
    public function archiveAction() {
        $dayOfThisWeek = $this->_request->getParam("dayOfThisWeek");
        $this->model->archiveWeek($dayOfThisWeek);
        $this->_redirect("/admincal/showcal/week/".$this->_request->getParam("week"));
    }

    /**
     * Unarchiv week
     */
    public function unarchiveAction() {
        $dayOfThisWeek = $this->_request->getParam("dayOfThisWeek");
        $this->model->unarchiveWeek($dayOfThisWeek);
        $this->_redirect("/admincal/showcal/week/".$this->weekParam);
    }
}
?>
