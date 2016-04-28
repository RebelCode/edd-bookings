<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\CustomPostType\SchedulePostType;
use \Aventura\Edd\Bookings\Factory\TimetableFactory;
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
     * @return ScheduleFactory This instance.
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
     * @return Schedule
     */
    public function create(array $args)
    {
        if (!isset($args['id'])) {
            $schedule = null;
        } else {
            $data = \wp_parse_args($args, array(
                    'timetable_id'  => null
            ));
            /* @var $schedule Schedule */
            $className = $this->getClassName();
            $schedule = new $className($data['id']);
            
            // Get the timetable with the ID in the data, creating a dummy one (stored in memory, but not DB) if a
            // timetable with that ID does not exist
            /* @var $timetable TimetableInterface */
            $timetableId = $data['timetable_id'];
            $timetable = $this->getPlugin()->getTimetableController()->get($timetableId);
            if (is_null($timetable)) {
                $timetable = $this->getTimetableFactory()->create((array('id' => 0)));
            }
            // Set the timetable
            $schedule->setTimetable($timetable);
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
        $timetableLegacyData = $legacy['entries'];
        // Create the timetable
        $timetableId = $this->getTimetableFactory()->createFromLegacyMeta($serviceName, $timetableLegacyData);
        // Save meta
        $this->getPlugin()->getScheduleController()->saveMeta($scheduleId, array(
                'timetable_id'  =>  $timetableId
        ));
        return $scheduleId;
    }

}
