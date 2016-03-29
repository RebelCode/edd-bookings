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
     * @param array $args The data to use for instantiation.
     * @return AvailabilityInterface
     */
    public function create(array $args)
    {
        if (!isset($args['id'])) {
            $availability = null;
        } else {
            $data = \wp_parse_args($args, array(
                    'timetable_id'  => null
            ));
            /* @var $availability AvailabilityInterface */
            $className = $this->getClassName();
            $availability = new $className($data['id']);
            
            // Get the timetable with the ID in the data, creating a dummy one (stored in memory, but not DB) if a
            // timetable with that ID does not exist
            /* @var $timetable TimetableInterface */
            $timetableId = $data['timetable_id'];
            $timetable = $this->getPlugin()->getTimetableController()->get($timetableId);
            if (is_null($timetable)) {
                $timetable = $this->getTimetableFactory()->create((array('id' => 0)));
            }
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
    
    /**
     * Creates an availability from legacy meta.
     * 
     * @param string $serviceName The name of the parent service.
     * @param meta $legacy The legacy meta
     * @return string The created availability ID.
     */
    public function createFromLegacyMeta($serviceName, $legacy)
    {
        // Create the availability
        $availabilityTitle = sprintf("%s's Availability", $serviceName);
        $availabilityId = $this->getPlugin()->getAvailabilityController()->insert(array(
                'post_title' => $availabilityTitle
        ));
        // Get the legacy meta
        $timetableLegacyData = $legacy['entries'];
        // Create the timetable
        $timetableId = $this->getTimetableFactory()->createFromLegacyMeta($serviceName, $timetableLegacyData);
        // Save meta
        $this->getPlugin()->getAvailabilityController()->saveMeta($availabilityId, array(
                'timetable_id'  =>  $timetableId
        ));
        return $availabilityId;
    }

}
