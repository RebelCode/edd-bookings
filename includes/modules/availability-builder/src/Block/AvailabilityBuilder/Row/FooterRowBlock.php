<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

use \RebelCode\EddBookings\Block\AvailabilityBuilder\Cell\FootingCellBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\FaIcon;
use \RebelCode\EddBookings\Block\Html\RegularTag;

/**
 * Description of FooterRowBlock
 *
 * @since [*next-version*]
 */
class FooterRowBlock extends AbstractRowBlock
{
    /**
     * The HTML class for the footer row.
     *
     * @since [*next-version*]
     */
    const FOOTER_ROW_CLASS = 'edd-bk-footer';

    /**
     * Gets the HTML class for this footer row element.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getFooterRowClass()
    {
        return static::FOOTER_ROW_CLASS;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRowClass()
    {
        return sprintf('%1$s %2$s', parent::getRowClass(), $this->getFooterRowClass());
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContent()
    {
        return array(
            new FootingCellBlock('help',
                new RegularTag('span', array(),
                    __('Rules further down the table take priority.', 'eddbk')
                )
            ),
            new FootingCellBlock('add-rule',
                new CompositeTag('button', array('class' => 'button button-secondary'), array(
                    new FaIcon('plus', array('class' => 'edd-bk-add-rule-icon')),
                    new FaIcon('hourglass', array('class' => 'edd-bk-add-rule-loading')),
                    __('Add', 'eddbk')
                ))
            )
        );
    }
}
