<?php
/**
 * calModel.php 
 * @author  Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 */

/**
 * calModel
 * @author  Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 */
class Model_Cal extends Zend_Db_Table
{
	
    /**
     * Name of database table to use
     * @var string
     */
    protected $_name = 'bookings';

    /**
     * Number of days in week -> open
     * @var int
     */
    protected $numDaysOpen = 0;

    /**
     * Days of week with base = $time
     * beginning on Monday
     * @var array
     */
    protected $openDays = array();

    /**
     * Open hours of days
     * @var array
     */
    protected $openHours = array();

    /**
     * Max hours open of all days
     * @var int
     */
    protected $maxOpenHours = 0;

    /**
     * Min hours open of all days
     * @var int
     */
    protected $minOpenHours = 24;

    /**
     * 2Dimensional-Array with 1 for opened and 0 for closed (matrix)
     * @var array
     */
    protected $openCloseMatrix = array();

    /**
     * 2Dimensional-Array with 1 for booked and 0 for not booked (matrix)
     * @var array
     */
    protected $bookedMatrix = array();

    /**
     *  The current username
     * @var string
     */
    protected $username = "";
	
	/**
	 * Make caption for cal-table, x-axis = days of week with date
	 * y-axis = hours
         *
         * @todo umwandeln in ini
         * @param int $time		The starting time
         * @return void
	 */
	public function makeCalCaption($time) {
            $schoolconfig = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','schoolconfig');
            $this->numDaysOpen = $schoolconfig->openDays->value;
            $date = new Zend_Date();
//                Zend_Debug::dump(date("N"));
//                Zend_Debug::dump($time);
//                Zend_Debug::dump(date("d.m.Y H:i:s",$time));

            for($i=0;$i<$this->numDaysOpen;$i++){
                    $date->set($time-((date("N",$time)-1)*86400)+86400*$i);
//                        Zend_Debug::dump($time-((date("N",$time)-1)*86400)+86400*$i);
                    $this->openDays[$i] = $date->getTimestamp();//->toString("l d.m.Y");
                    $strDayIndex = strval($i);
                    if($this->maxOpenHours < $schoolconfig->openHours->$strDayIndex->end->value){
                            $this->maxOpenHours = $schoolconfig->openHours->$strDayIndex->end->value;
                    }

                    if($this->minOpenHours > $schoolconfig->openHours->$strDayIndex->start->value){
                            $this->minOpenHours = $schoolconfig->openHours->$strDayIndex->start->value;
                    }
            }

            for($i = $this->minOpenHours; $i <= $this->maxOpenHours; $i++) {
                    $date->set($date->toString("d.m.Y") . " " . $i . ':00:00');
                    $from = $date->toString("H:i");
                    $date->set($date->toString("d.m.Y") . " " . ($i+1) . ':00:00');
                    $to = $date->toString("H:i");
                    $this->openHours[$i] = $from . " - " . $to;
            }
	}
	
	/**
	 * Make matrix with hours open=1 and closed=0
	 * x-axis = days, y-axis hours
	 * @return array $openCloseMatrix
	 */
	public function getOpenCloseMatrix()
	{
            $schoolconfig = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','schoolconfig');
	    for($a=0;$a<count($this->openDays);$a++) {
			for($b=$this->minOpenHours;$b<=$this->maxOpenHours;$b++) {
				$strDayIndex = strval($a);
			if($b >= $schoolconfig->openHours->$strDayIndex->start->value && $b <= $schoolconfig->openHours->$strDayIndex->end->value) {
					$this->openCloseMatrix[$a][$b] = 1;
				} else {
					$this->openCloseMatrix[$a][$b] = 0;
				}
			}
		}
		return $this->openCloseMatrix;
	}

	/**
	 * Make matrix with hours booked=1 and not booked=0
	 * x-axis = days, y-axis hours
     *
     * @param int $weekToDisplay
     * @param boolean $allUsers
     * @param boolean $showUsernames
	 * @return array $openCloseMatrix
	 */	
	public function getBookedMatrix($weekToDisplay, $allUsers = false)
	{
            $date = new Zend_Date();

            // if there is not all info available
            if($this->numDaysOpen == 0){
                $this->makeCalCaption($weekToDisplay);
            }

            if($allUsers == false){
                // Load userinfo
                $userInfo = Zend_Auth::getInstance()->getStorage()->read();
                $this->username = $userInfo->username;
            }

            for($i=0;$i<$this->numDaysOpen;$i++){
                $date->set($weekToDisplay-((date("N")-1)*86400)+86400*$i);
                $this->openDays[$i] = $date->toString("l d.m.Y");

                for($j = $this->minOpenHours; $j <= $this->maxOpenHours; $j++) {
                    $date->set($date->toString("d.m.Y") . " " . $j . ':00:00');

                    if($allUsers){
                        $rowset = $this->fetchAll('bookingDate = "' . $date->toString("Y-m-d H:i") . '"');
                    }else{
                        $rowset = $this->fetchAll('bookingDate = "' . $date->toString("Y-m-d H:i") . '" AND username = "' . $this->username . '"');
                        $info = $this->username;
                    }

                    // admincal/showcal
                    if($allUsers){
                        $info = array();
                        foreach($rowset as $row){
                            array_push(&$info, $row->username);
                        }
                    }

                    if($rowset->count() >= 1) {
                    //exit(var_dump($info));
                            $this->bookedMatrix[$i][$j] = $info;
                    } else {
                            $this->bookedMatrix[$i][$j] = "nicht gebucht";
                    }
                }
            }
        return $this->bookedMatrix;
    }
	
     /**
     * Returns if a week is archived
     *
     * @param int $dayOfThisWeek
     * @return bollean
     */
    public function isWeekArchived($dayOfThisWeek)
    {
        // Set db-table
        $this->_name = "bookings_archived_weeks";

        $date = new Zend_date();
        $date->set($dayOfThisWeek-((date("N")-1)*86400));
        $rowset = $this->fetchAll('weekDate = "' . $date->toString("Y-m-d 00:00:00") . '"');

        // Reset db-table
        $this->_name = "bookings";
        
        if($rowset->count() == 1){
            return true;
        }
        return false;
    }

	/**
	 * Get open days
	 * @return array $openDays
	 */
    public function getOpenDays() {
		return $this->openDays;
	}
	
	/**
	 * Get open hours
	 * @return array $openHours
	 */
    public function getOpenHours() {
		return $this->openHours;
	}
	
	/**
	 * Get min open hours
	 * @return array $minOpenHours
	 */
    public function getMinOpenHours() {
		return $this->minOpenHours;
	}
	
	/**
	 * Get max open hours
	 * @return array $maxOpenHours
	 */
    public function getMaxOpenHours() {
		return $this->maxOpenHours;
	}
	
	/**
	 * Get max hours open
	 * @return array $maxOpenHours
	 */
	public function getmaxHoursOpen() {
		return $this->maxOpenHours - $this->minOpenHours;
	}
}

