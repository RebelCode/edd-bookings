<?php

namespace RebelCode\Wp\Admin\Page;

use RebelCode\EddBookings\Block\BlockInterface;

/**
 * Represents a page that can also be a block.
 *
 * @since [*next-version*]
 */
interface BlockPageInterface extends PageInterface, BlockInterface
{
}
