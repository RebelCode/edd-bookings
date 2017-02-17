<?php

namespace RebelCode\WordPress\Admin;

use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\FaIcon;
use \RebelCode\EddBookings\Block\Html\RegularTag;

/**
 * Description of Tooltip
 *
 * @since [*next-version*]
 */
class Tooltip extends CompositeTag
{

    const TAG_NAME = 'div';
    const CLASS_NAME = 'edd-bk-help';

    const ALIGN_1_LEFT   = 'left';
    const ALIGN_1_RIGHT  = 'right';
    const ALIGN_2_TOP    = 'top';
    const ALIGN_2_BOTTOM = 'bottom';

    const DEFAULT_ICON = 'question-circle';

    public function __construct(
        $text,
        $align1 = self::ALIGN_1_RIGHT,
        $align2 = self::ALIGN_2_BOTTOM,
        $icon = self::DEFAULT_ICON
    ) {
        $fullAlign     = $this->alignClassName($align1, $align2);
        $fullClassName = sprintf('%1$s %2$s', static::CLASS_NAME, $fullAlign);

        parent::__construct(static::TAG_NAME, array('class' => $fullClassName), array(
            new FaIcon($icon),
            new RegularTag('div', array(), \wpautop($text))
        ));
    }

    public function alignClassName($align1, $align2)
    {
        return sprintf('%1$s %2$s', $align1, $align2);
    }
}
