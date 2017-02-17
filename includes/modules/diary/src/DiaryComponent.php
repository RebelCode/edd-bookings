<?php

namespace RebelCode\EddBookings\Component;

use \Dhii\App\AppInterface;
use \Dhii\Storage\AdapterInterface;
use \RebelCode\Diary\Diary;
use \RebelCode\EddBookings\System\Component\ComponentInterface;

/**
 * Component class for the Diary library instance.
 *
 * @since [*next-version*]
 */
class DiaryComponent extends Diary implements ComponentInterface
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
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The app instance.
     */
    public function __construct(AppInterface $app, AdapterInterface $storageAdapter)
    {
        $this->_setApp($app)
            ->setStorageAdapter($storageAdapter);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return AppInterface The app instance.
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Sets the app.
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

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {

    }
}
