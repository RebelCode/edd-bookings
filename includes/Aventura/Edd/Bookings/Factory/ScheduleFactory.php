<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\CustomPostType\SchedulePostType;
use \Aventura\Edd\Bookings\Factory\AvailabilityFactory;
use \Aventura\Edd\Bookings\Model\Availability;
use \Aventura\Edd\Bookings\Model\Schedule;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Schedule Factory class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ScheduleFactory extends ModelCptFactoryAbstract
{

    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Schedule';

    /**
     * The availability factory instance to use for creating availabilities.
     * 
     * @var AvailabilityFactory
     */
    protected $_availabilityFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Gets the availability factory.
     * 
     * @return AvailabilityFactory
     */
    public function getAvailabilityFactory()
    {
        return $this->_availabilityFactory;
    }

    /**
     * Sets the availability factory to use.
     * 
     * @param AvailabilityFactory $availabilityFactory The availability factory to use.
     * @return ScheduleFactory This instance.
     */
    public function setAvailabilityFactory(AvailabilityFactory $availabilityFactory)
    {
        $this->_availabilityFactory = $availabilityFactory;
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $args The data to use for instantiation.
     * @return Schedule
     */
    public function create(array $args)
    {
        if (!isset($args['id'])) {
            $schedule = null;
        } else {
            $data = \wp_parse_args($args, array(
                    'availability_id'  => null
            ));
            /* @var $schedule Schedule */
            $className = $this->getClassName();
            $schedule = new $className($data['id']);
            
            // Get the availability with the ID in the data, creating a dummy one (stored in memory, but not DB) if a
            // availability with that ID does not exist
            /* @var $availability Availability */
            $availabilityId = $data['availability_id'];
            $availability = $this->getPlugin()->getAvailabilityController()->get($availabilityId);
            if (is_null($availability)) {
                $availability = $this->getAvailabilityFactory()->create((array('id' => 0)));
            }
            // Set the availability
            $schedule->setAvailability($availability);
        }
        // Return created instance
        return $schedule;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $data Optional array of data. Default: array()
     * @return SchedulePostType The created instance.
     */
    public function createCpt(array $data = array())
    {
        return new SchedulePostType($this->getPlugin());
    }
    
    /**
     * Creates a schedule from legacy meta.
     * 
     * @param string $serviceName The name of the parent service.
     * @param meta $legacy The legacy meta
     * @return string The created schedule ID.
     */
    public function createFromLegacyMeta($serviceName, $legacy)
    {
        // Create the schedule
        $scheduleTitle = sprintf("%s's Schedule", $serviceName);
        $scheduleId = $this->getPlugin()->getScheduleController()->insert(array(
                'post_title' => $scheduleTitle
        ));
        // Get the legacy meta
        $availabilityLegacyData = $legacy['entries'];
        // Create the availability
        $availabilityId = $this->getAvailabilityFactory()->createFromLegacyMeta($serviceName, $availabilityLegacyData);
        // Save meta
        $this->getPlugin()->getScheduleController()->saveMeta($scheduleId, array(
                'availability_id'  =>  $availabilityId
        ));
        return $scheduleId;
    }

}
