<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\Bookable\Availability\AvailabilityInterface;
use \Aventura\Edd\Bookings\CustomPostType\AvailabilityPostType;
use \Aventura\Edd\Bookings\Factory\TimetableFactory;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Availabiity Factory class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AvailabilityFactory extends ModelCptFactoryAbstract
{
    
    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Availability';
    
    /**
     * The timetable factory instance to use for creating timetables.
     * 
     * @var TimetableFactory
     */
    protected $_timetableFactory;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
        // $this->setTimetableFactory(new TimetableFactory);
    }
    
    /**
     * Gets the timetable factory.
     * 
     * @return TimetableFactory
     */
    public function getTimetableFactory()
    {
        return $this->_timetableFactory;
    }

    /**
     * Sets the timetable factory to use.
     * 
     * @param TimetableFactory $timetableFactory The timetable factory to use.
     * @return AvailabilityFactory This instance.
     */
    public function setTimetableFactory(TimetableFactory $timetableFactory)
    {
        $this->_timetableFactory = $timetableFactory;
        return $this;
    }

        
    /**
     * {@inheritdoc}
     * 
     * @param array $data The data to use for instantiation.
     * @return AvailabilityInterface
     */
    public function create(array $data)
    {
        if (!isset($data['id'])) {
            $availability = null;
        } else {
            /* @var $availability AvailabilityInterface */
            $className = $this->getClassName();
            $availability = new $className($data['id']);
            // Create the timetable
            $timetableId = isset($data['timetable_id'])
                    ? $data['timetable_id']
                    : 0;
            $timetable = ($timetableId === 0) 
                    ? $this->getTimetableFactory()->create((array('id' => 0)))
                    : eddBookings()->getTimetableController()->get($timetableId);
            // Set the timetable
            $availability->setTimetable($timetable);
        }
        // Return created instance
        return $availability;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $data Optional array of data. Default: array()
     * @return AvailabilityPostType The created instance.
     */
    public function createCpt(array $data = array())
    {
        return new AvailabilityPostType($this->getPlugin());
    }

}
