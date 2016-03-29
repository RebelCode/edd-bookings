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
     * The availability factory.
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
        $this->setAvailabilityFactory($plugin->getAvailabilityController()->getFactory());
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
     * @param AvailabilityFactory $availabilityFactory The availability factory instane to use.
     * @return ServiceFactory This instance.
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
                    'availability_id'   => null
                    )
            );
            // Get the availability
            /* @var $availability AvailabilityInterface */
            $availId = $data['availability_id'];
            // Get the availability using the ID
            $availability = $this->getPlugin()->getAvailabilityController()->get($availId);
            // If availability cannot be retrieved, create a dummy availability, kept in memory NOT in DB
            if (is_null($availability) || $availability === false) {
                $availability = $this->getAvailabilityFactory()->create(array('id' => 0));
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
                    ->setAvailability($availability);
            // If the legacy data was normalized, save the new normalized meta to prevent further normalization.
            // That would create a large number of availabilities and timetables.
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
            // Create availability and timetable
            $serviceName = \get_the_title($args['id']);
            $normalized['availability_id'] = $this->getAvailabilityFactory()->
                    createFromLegacyMeta($serviceName, $legacy['availability']);
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
