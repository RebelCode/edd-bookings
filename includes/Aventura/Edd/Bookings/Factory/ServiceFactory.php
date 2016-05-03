<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\Bookable\Availability\AvailabilityInterface;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Model\Service;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Service Factory class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServiceFactory extends ModelCptFactoryAbstract
{

    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Service';

    /**
     * The schedule factory.
     * 
     * @var ScheduleFactory
     */
    protected $_scheduleFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
        $this->setScheduleFactory($plugin->getScheduleController()->getFactory());
    }

    /**
     * Gets the schedule factory.
     * 
     * @return ScheduleFactory
     */
    public function getScheduleFactory()
    {
        return $this->_scheduleFactory;
    }

    /**
     * Sets the schedule factory to use.
     * 
     * @param ScheduleFactory $scheduleFactory The schedule factory instane to use.
     * @return ServiceFactory This instance.
     */
    public function setScheduleFactory(ScheduleFactory $scheduleFactory)
    {
        $this->_scheduleFactory = $scheduleFactory;
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $args The data to use for instantiation.
     * @return Service The created service instance.
     */
    public function create(array $args = array())
    {
        if (!isset($args['id'])) {
            $service = null;
        } else {
            $didNormalize = isset($args['legacy']);
            $normalized = $this->maybeNormalizeLegacyMeta($args);
            $data = \wp_parse_args(
                    $normalized,
                    array(
                    'bookings_enabled'  => false,
                    'session_length'    => 3600,
                    'session_unit'      => 'hours',
                    'session_cost'      => 0,
                    'min_sessions'      => 1,
                    'max_sessions'      => 1,
                    'multi_view_output' => false,
                    'schedule_id'       => null,
                    'use_customer_tz'   => false,
                    )
            );
            // Get the schedule
            /* @var $schedule Schedule */
            $scheduleId = $data['schedule_id'];
            // Get the schedule using the ID
            $schedule = $this->getPlugin()->getScheduleController()->get($scheduleId);
            // If schedule cannot be retrieved, create a dummy schedule, kept in memory NOT in DB
            if (is_null($schedule) || $schedule === false) {
                $schedule = $this->getScheduleFactory()->create(array('id' => 0));
            }
            /* @var $service Service */
            $className = $this->getClassName();
            $service = new $className($data['id']);
            // Set the data and return
            $service->setBookingsEnabled(filter_var($data['bookings_enabled'], FILTER_VALIDATE_BOOLEAN))
                    ->setSessionLength(intval($data['session_length']))
                    ->setSessionUnit($data['session_unit'])
                    ->setSessionCost(floatval($data['session_cost']))
                    ->setMinSessions(intval($data['min_sessions']))
                    ->setMaxSessions(intval($data['max_sessions']))
                    ->setMultiViewOutput(filter_var($data['multi_view_output'], FILTER_VALIDATE_BOOLEAN))
                    ->setUseCustomerTimezone(filter_var($data['use_customer_tz'], FILTER_VALIDATE_BOOLEAN))
                    ->setSchedule($schedule);
            // If the legacy data was normalized, save the new normalized meta to prevent further normalization.
            // That would create a large number of schedules and availabilities.
            if ($didNormalize) {
                $meta = $data;
                unset($meta['id']);
                $this->getPlugin()->getServiceController()->saveMeta($data['id'], $meta);
            }
        }
        return $service;
    }

    /**
     * Checks if the given data contains legacy meta and if so, converts it into the new format.
     * 
     * @param array $args The meta array
     * @return array The (maybe?) normalized meta
     */
    public function maybeNormalizeLegacyMeta($args)
    {
        $normalized = $args;
        if (!empty($args['legacy'])) {
            // Map the old meta to the new
            $legacy = $args['legacy'];
            $normalized['bookings_enabled'] = $legacy['enabled'];
            $normalized['session_length'] = $legacy['session_length'];
            $normalized['session_unit'] = $legacy['session_unit'];
            // Fix session length - must be in seconds
            if (method_exists('Aventura\\Diary\\DateTime\\Duration', $normalized['session_unit'])) {
               $normalized['session_length'] = call_user_func_array(
                       array('Aventura\\Diary\\DateTime\\Duration', $normalized['session_unit']),
                       array($normalized['session_length'], false));
            }
            $normalized['session_cost'] = $legacy['session_cost'];
            if ($legacy['session_type'] === 'fixed') {
                $normalized['min_sessions'] = $normalized['max_sessions'] = 1;
            } else {
                $normalized['min_sessions'] = $legacy['min_sessions'];
                $normalized['max_sessions'] = $legacy['max_sessions'];
            }
            $normalized['multi_view_output'] = $legacy['multi_view_output'];
            if (filter_var($normalized['bookings_enabled'], FILTER_VALIDATE_BOOLEAN)) {
                // Create schedule and availability
                $serviceName = \get_the_title($args['id']);
                $normalized['schedule_id'] = $this->getScheduleFactory()->
                        createFromLegacyMeta($serviceName, $legacy['schedule']);
            }
            $normalized['use_customer_tz'] = true;
            // Remove the legacy data
            unset($normalized['legacy']);
        }
        return $normalized;
    }

    /**
     * Creates the service CPT.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return ServicePostType The created instance.
     */
    public function createCpt(array $data = array())
    {
        return new ServicePostType($this->getPlugin());
    }

}
