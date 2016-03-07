<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\Bookable\Availability as DiaryAvailability;

/**
 * Availability model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Availability extends DiaryAvailability
{
    
    /**
     * The ID.
     * 
     * @var integer
     */
    protected $_id;
    
    /**
     * Constructs a new instance.
     * 
     * @param integer $id The ID.
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->setId($id);
    }
    
    /**
     * Gets the ID.
     * 
     * @return integer The ID.
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the ID.
     * 
     * @param integer $id The ID.
     * @return Availability This instance.
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

}
