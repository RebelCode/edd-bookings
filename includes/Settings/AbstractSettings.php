<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;
use \Aventura\Edd\Bookings\Settings\Database\DatabaseInterface;
use \Aventura\Edd\Bookings\Settings\Database\Record\SubRecord;
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

    /**
     * Adds a section to this instance.
     *
     * @param SectionInterface $section The section instance to add.
     * @return static This instance.
     */
    public function addSection(SectionInterface $section)
    {
        // Set section's record as a subrecord of this instance's
        $sectionRecord = new SubRecord($this->getRecord(), $section->getId());
        $section->setRecord($sectionRecord);
        // Add to list
        $this->sections[$section->getId()] = $section;

        return $this;
    }

    /**
     * Adds an array of sections to this instance.
     *
     * @param SectionInterface[] $sections An array of section instances. Non-section array entries will be ignored.
     * @return static This instance.
     */
    public function addSections(array $sections)
    {
        foreach ($sections as $section) {
            if ($section instanceof SectionInterface) {
                $this->addSection($section);
            }
        }

        return $this;
    }

    /**
     * Gets a section with a specific ID.
     *
     * @param string $id The ID of the section to return.
     * @return SectionInterface|null The section instance or null if no section with the given ID was found.
     */
    public function getSection($id)
    {
        return $this->hasSection($id)
            ? $this->sections[$id]
            : null;
    }

    /**
     * Checks if this instance has a section with a specific ID.
     *
     * @param string $id The ID of the section to search for.
     * @return boolean True if a section with the given ID exists, false if not.
     */
    public function hasSection($id)
    {
        return isset($this->sections[$id]);
    }

    /**
     * Removes a section with a specific ID.
     *
     * @param string $id The ID of the section to remove.
     * @return static This instance.
     */
    public function removeSection($id)
    {
        unset($this->sections[$id]);

        return $this;
    }

    /**
     * Removes all sections from this instance.
     *
     * @return static This instance.
     */
    public function resetSections()
    {
        $this->sections = array();

        return $this;
    }

}
