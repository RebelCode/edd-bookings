<?php

namespace Aventura\Edd\Bookings\Settings\Section;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;
use \Aventura\Edd\Bookings\Settings\Option\OptionInterface;

/**
 * Describes a settings section, which represents a grouped collection of options.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SectionInterface
{

    /**
     * Gets the section ID.
     * 
     * @return string The ID.
     */
    public function getId();

    /**
     * Gets the section name.
     *
     * @return string The name.
     */
    public function getName();
    /**
     * Gets the options in this section.
     * 
     * @return OptionInterface[] An array of option instances.
     */
    public function getOptions();

    /**
     * Gets the database record for this section.
     *
     * @return RecordInterface The record.
     */
    public function getRecord();

    /**
     * Sets the section ID.
     *
     * @param string $id The section ID.
     * @return static This instance.
     */
    public function setId($id);

    /**
     * Sets the section name.
     *
     * @param string $name The section name.
     * @return static This instance.
     */
    public function setName($name);

    /**
     * Sets the record for this section.
     *
     * @param RecordInterface $record The record.
     * @return static This instance.
     */
    public function setRecord(RecordInterface $record = null);

}
