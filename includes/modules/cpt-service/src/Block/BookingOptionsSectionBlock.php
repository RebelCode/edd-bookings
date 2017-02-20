<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \Symfony\Component\EventDispatcher\Tests\Service;

/**
 * Description of BookingOptionsSectionBlock
 *
 * @since [*next-version*]
 */
class BookingOptionsSectionBlock extends CompositeTag
{

    protected $service;

    public function __construct(Service $service, $label, $content, $labelFixed = false)
    {
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return new CompositeTag('div', 'edd-bk-service-section', array(
            
        ));
    }

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
