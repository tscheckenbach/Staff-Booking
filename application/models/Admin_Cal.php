<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admincalModel
 *
 * @author tsc
 */
class Model_Admin_Cal extends Model_Cal
{
    
    /**
     * Archiv the selected week, so supervisors cannot edit there entries anymore
     *
     * @param int $dayOfThisWeek
     */
    public function archiveWeek($dayOfThisWeek)
    {
        // Set db-table
        $this->_name = "bookings_archived_weeks";
        
        $date = new Zend_date();
        $date->set($dayOfThisWeek-((date("N")-1)*86400));

        $insertData = array("weekDate" => $date->toString("Y-m-d 00:00:00"));
		$this->insert($insertData);
    }

     /**
	 * Unarchive the selected week, so supervisors can edit there entries again
	 *
     * @param string dayOfThisWeek
	 */
    public function unarchiveWeek($dayOfThisWeek)
    {
        // Set db-table
        $this->_name = "bookings_archived_weeks";

        $date = new Zend_date();
        $date->set($dayOfThisWeek-((date("N")-1)*86400));
        $delDate = $date->toString("Y-m-d 00:00:00");
		$n = $this->delete("weekDate = '" . $delDate . "'");
    }

}
?>
