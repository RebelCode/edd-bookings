<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Model\Service;

/**
 * Basic functionality for a service option block.
 *
 * @since [*next-version*]
 */
abstract class AbstractServiceOptionBlock extends AbstractBlock
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->setService($service);
    }

    /**
     *
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }
}
