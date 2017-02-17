<?php

namespace RebelCode\EddBookings\System\Component;

use \Dhii\App\AppInterface;

/**
 * Basic functionality for a component.
 *
 * @since [*next-version*]
 */
abstract class AbstractComponent
{
    /**
     * The app instance.
     *
     * @since [*next-version*]
     *
     * @var AppInterface
     */
    protected $app;

    /**
     * Gets the parent app.
     *
     * @since [*next-version*]
     *
     * @return AppInterface The parent app instance.
     */
    protected function _getApp()
    {
        return $this->app;
    }

    /**
     * Sets the parent app.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The app instance.
     *
     * @return $this This instance.
     */
    protected function _setApp(AppInterface $app)
    {
        $this->app = $app;

        return $this;
    }
}
