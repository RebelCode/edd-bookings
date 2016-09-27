<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;
use \Aventura\Edd\Bookings\Settings\Database\DatabaseInterface;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * Description of AbstractSettings
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class AbstractSettings extends ControllerAbstract implements SettingsInterface
{

    /**
     * The database controller instance.
     *
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * The settings sections.
     * 
     * @var SectionInterface[]
     */
    protected $sections;

    /**
     * {@inheritdoc}
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Sets the database controller instance.
     *
     * @param DatabaseInterface $database The database controller instance.
     * @return static This instance.
     */
    public function setDatabase(DatabaseInterface $database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections()
    {
        return $this->sections;
    }

}
