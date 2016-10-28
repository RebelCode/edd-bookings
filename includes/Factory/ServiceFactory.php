<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Model\Availability;
use \Aventura\Edd\Bookings\Model\Schedule;
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
        $this->setAvailabilityFactory(new AvailabilityFactory($plugin));
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
     * Gets the default factory options.
     * 
     * @return array
     */
    public static function getDefaultOptions()
    {
        return array(
            'bookings_enabled'  => false,
            'session_length'    => 3600,
            'session_unit'      => 'hours',
            'session_cost'      => 0,
            'min_sessions'      => 1,
            'max_sessions'      => 1,
            'multi_view_output' => false,
            'availability'      => array(),
            'use_customer_tz'   => false,
        );
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
            $data = $this->resolveMeta($args);
            // Get the ID
            $id = $data['id'];
            // Create the availability - uses the same ID as the service
            $availabilityData = maybe_unserialize($data['availability']);
            $availabilityData['id'] = $id;
            $availability = $this->getAvailabilityFactory()->create($availabilityData);
            // Create the schedule - uses the same ID as the service
            $schedule = new Schedule($id);
            $schedule->setAvailability($availability);
            /* @var $service Service */
            $className = $this->getClassName();
            $service = new $className($id);
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
            // That would create a large number of availabilities.
            if ($didNormalize) {
                $meta = $data;
                unset($meta['id']);
                $this->getPlugin()->getServiceController()->saveMeta($id, $meta);
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
                // Create availability
                $serviceName = \get_the_title($args['id']);
                $normalized['availability'] = $this->getAvailabilityFactory()->
                        createFromLegacyMeta($legacy['availability']['entries']);
            }
            $normalized['use_customer_tz'] = true;
            // Remove the legacy data
            unset($normalized['legacy']);
        }
        return $normalized;
    }

    /**
     * Resolves the actual meta data, merging with defaults and normalizing legacy formats.
     *
     * @param array $meta The input array of meta data.
     * @return array The output array of normalized, sanitized, filtered and resolved meta data.
     */
    public function resolveMeta(array $meta)
    {
        $legacyNormalized = $this->maybeNormalizeLegacyMeta($meta);
        $nullFiltered = array_filter($legacyNormalized, function($item) {
            return !is_null($item);
        });
        $resolvedMeta = \wp_parse_args($nullFiltered, static::getDefaultOptions());

        return apply_filters('edd_bk_service_meta', $resolvedMeta);
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
