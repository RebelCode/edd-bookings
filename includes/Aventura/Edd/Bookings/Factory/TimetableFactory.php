<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\CustomPostType\TimetablePostType;
use \Aventura\Edd\Bookings\Model\Timetable;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Description of TimetableFactory
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimetableFactory extends ModelCptFactoryAbstract
{
    
    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Timetable';
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
    }
    
    /**
     * Creates a timetable instance.
     * 
     * @param array $data Array of data to use for creating the instance.
     * @return Timetable The created instance.
     */
    public function create(array $data)
    {
        if (!isset($data['id'])) {
            $timetable = null;
        } else {
            /* @var $timetable Timetable */
            $className = $this->getClassName();
            $timetable = new $className($data['id']);
        }
        // Return created instance
        return $timetable;
    }

    /**
     * Creates the CPT instance.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function createCpt(array $data = array())
    {
        return new TimetablePostType($this->getPlugin());
    }

}
