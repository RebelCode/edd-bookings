<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\Bookable\Availability\AvailabilityInterface;
use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Model\Service;

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
     * @param array $arg The data to use for instantiation.
     * @return Service The created service instance.
     */
    public function create(array $arg = array())
    {
        if (!isset($arg['id'])) {
            $service = null;
        } else {
            $data = \wp_parse_args($arg,
                    array(
                    'session_length'    => 1,
                    'session_unit'      => 'hours',
                    'session_cost'      => 0,
                    'min_sessions'      => 1,
                    'max_sessions'      => 1,
                    'multi_view_output' => false,
                    'availability_id'   => null
            ));
            // Attempt to create a new availability if none specified and no availabilities exist
            $availId = $data['availability_id'];
            $availabilities = $this->getPlugin()->getAvailabilityController()->query();
            if (is_null($availId) && count($availabilities) === 0) {
                $availId = $this->getPlugin()->getAvailabilityController()->insert();
            }
            /* @var $availability AvailabilityInterface */
            $availability = is_null($availId)
                    ? $this->getAvailabilityFactory()->create(array('id' => 0))
                    : $this->getPlugin()->getAvailabilityController()->get($availId);
            /* @var $service Service */
            $className = $this->getClassName();
            $service = new $className($data['id']);
            // Set the data and return
            $service->setSessionLength(intval($data['session_length']))
                    ->setSessionUnit($data['session_unit'])
                    ->setSessionCost(floatval($data['session_cost']))
                    ->setMinSessions(intval($data['min_sessions']))
                    ->setMaxSessions(intval($data['max_sessions']))
                    ->setMultiViewOutput(filter_var($data['multi_view_output'], FILTER_VALIDATE_BOOLEAN))
                    ->setAvailability($availability);
        }
        return $service;
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
