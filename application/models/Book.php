<?php
/**
 * bookModel.php 
 * @author  Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 */

/**
 * bookModel
 * @author  Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 * 
 * Book and unbook entries
 */
class Model_Book extends Zend_Db_Table
{
	/**
	 * Name of database table to use
	 * @var string
	 */
	protected $_name = 'bookings';

	/**
	 * Username
	 * @var string
	 */
	protected $username = "";
    
    /**
     * Construct
     */
	public function init()
    {
        // Load userinfo
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $this->username = $userInfo->username;
    }

	/**
	 * Book the selected entry
	 *
     * @param string selectedDate		The Date (Y-m-d H:i:s) to book
     * @return true
	 */
	public function book($selectedDate)
    {

		$insertData = array(
			"username" => $this->username,
			"bookingDate" => $selectedDate);
		try{
            $this->insert($insertData);
        }catch(Exception $e){
            
        }
		
		return true;
	}
	
	/**
	 * Unbook the selected entry
	 * 
     * @param string selectedDate		The Date (Y-m-d H:i:s) to unbook
     * @return true
	 */
	public function unbook($selectedDate)
    {
		$n = $this->delete("username = '" . $this->username . "' AND bookingDate = '" . $selectedDate ."'");
		
		return true;
	}

    /**
	 * Book whole Day
	 *
     * @param string selectedDate		The Date (Y-m-d) to book
     * @return true
	 */
	public function handleWholeDay($selectedDate, $handle)
    {
        $date = new Zend_Date();
        $date->set($selectedDate);
        // selected DayIndex. Minus one cause our index starts with 0
        $strDayIndex = $date->toString("N") -1;

        // read config
        $schoolconfig = new Zend_Config_Ini(APPLICATION_PATH.'/config.ini','schoolconfig');
        $minOpenHours = $schoolconfig->openHours->$strDayIndex->start->value;
        $maxOpenHours = $schoolconfig->openHours->$strDayIndex->end->value;

        // for all hours of the selected day: book OR unbook
        for($i = $minOpenHours; $i <= $maxOpenHours; $i++) {
            $dateToHandle = $selectedDate . " " . $i . ":00:00";
            if($handle == "book"){
                $this->book($dateToHandle);
            }else{
                $this->unbook($dateToHandle);
            }
		}
    }
}
?>