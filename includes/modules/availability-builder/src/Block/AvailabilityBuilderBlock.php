<?php

namespace RebelCode\EddBookings\Block;

use \RebelCode\EddBookings\Block\AvailabilityBuilder\AbstractBuilderBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\BodyBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Row\FooterRowBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Row\HeaderRowBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Model\Service;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;

/**
 * A block for the Availability Builder.
 *
 * @since [*next-version*]
 */
class AvailabilityBuilderBlock extends AbstractBuilderBlock
{
    /**
     * Constructor.
     *
     * @param Service $service
     * @param RuleTypeRegistryInterface $ruleTypes
     */
    public function __construct(
        Service $service,
        RuleTypeRegistryInterface $ruleTypes
    ) {
        parent::__construct($service);

        $this->setRuleTypes($ruleTypes);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $service = $this->getService();

        $container = new CompositeTag('div',
            array(
                'class'   => 'edd-bk-availability-container',
                'data-id' => $service->getId()
            ),
            array(
                // Nonces for form verification and AJAX
                \wp_nonce_field('edd_bk_save_meta', 'edd_bk_availability'),
                \wp_nonce_field('edd_bk_availability_ajax', 'edd_bk_availability_ajax_nonce'),
                new CompositeTag('div', array('class' => 'edd-bk-builder'), array(
                    new HeaderRowBlock(),
                    new BodyBlock(
                        $this->getService(),
                        $this->getRuleTypes()
                    ),
                    new FooterRowBlock()
                ))
            )
        );

        return $container;
    }
}
