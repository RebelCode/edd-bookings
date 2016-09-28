<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;

/**
 * A node in the settings hierarchy.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SettingsNodeInterface
{

    /**
     * Gets the ID of the option.
     *
     * @return string The ID.
     */
    public function getId();

    /**
     * Gets the name of the option.
     *
     * @return string The name.
     */
    public function getName();

    /**
     * Gets the description of the option.
     *
     * @return string The description.
     */
    public function getDescription();

    /**
     * Gets the database record for this option.
     *
     * @return RecordInterface The record.
     */
    public function getRecord();

    /**
     * Gets the render view name for this option.
     *
     * @return string The view name.
     */
    public function getView();

    /**
     * Sets the ID.
     *
     * @param string $id The new ID.
     * @return static This instance.
     */
    public function setId($id);

    /**
     * Sets the name.
     *
     * @param string $name The new name.
     * @return static This instance.
     */
    public function setName($name);

    /**
     * Sets the description.
     *
     * @param string $description The new description.
     * @return static This instance.
     */
    public function setDescription($description);

    /**
     * Sets the record instance.
     *
     * @param RecordInterface $record The new record instance.
     * @return static This instance.
     */
    public function setRecord(RecordInterface $record = null);

    /**
     * Sets the view name.
     *
     * @param string $view The new view name.
     * @return static This instance.
     */
    public function setView($view);

}
