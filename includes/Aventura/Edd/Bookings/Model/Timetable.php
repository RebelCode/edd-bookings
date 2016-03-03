<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\Bookable\Availability\Timetable as DiaryTimetable;

/**
 * Timetable model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Timetable extends DiaryTimetable
{
    
    /**
     * The ID.
     * 
     * @var integer
     */
    protected $_id;
    
    public function __construct($id)
    {
        parent::__construct();
        $this->setId($id);
    }
    
    /**
     * Gets the ID.
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the ID.
     * 
     * @param integer $id The ID.
     * @return Timetable This instance.
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

}
