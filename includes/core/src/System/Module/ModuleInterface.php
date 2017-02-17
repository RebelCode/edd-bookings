<?php

namespace RebelCode\EddBookings\System\Module;

use \RebelCode\Bookings\Framework\Data\DataReadableInterface;

/**
 *
 * @since [*next-version*]
 */
interface ModuleInterface extends DataReadableInterface
{
    public function getId();

    public function getName();

    public function getDirectory();

    public function getFilePath();

    public function exec();
}
