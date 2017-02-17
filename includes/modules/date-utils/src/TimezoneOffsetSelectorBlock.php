<?php

namespace RebelCode\EddBookings\Utils;

use \RebelCode\EddBookings\Block\Html\SelectTag;

/**
 * Description of TimezoneOffsetSelectorBlock
 *
 * @since [*next-version*]
 */
class TimezoneOffsetSelectorBlock extends SelectTag
{    
    public function __construct($selected, array $attributes = array())
    {
        parent::__construct($this->_generateItems(), $selected, $attributes);
    }

    protected function _generateItems()
    {
        $items = array();

        for ($i = -12; $i <= 14; $i += 0.5) {
            $_key    = $i * 3600;
            $_sign   = ($i < 0)? '-' : '+';
            $_hours  = abs($i);
            $_fractn = $i - floor($i);
            $_mins   = $_fractn * 60;
            $_prefix = sprintf('UTC%s%d', $_sign, $_hours);
            $_suffix = ($_fractn > 0)
                ? sprintf(':%02d', $_mins)
                : '';

            $items[$_key] = $_prefix . $_suffix;
        }

        return $items;
    }
}
