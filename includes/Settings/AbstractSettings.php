<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * Description of AbstractSettings
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class AbstractSettings implements SettingsInterface
{

    /**
     * The database record instance.
     *
     * @var RecordInterface
     */
    protected $record;

    /**
     * The settings sections.
     * 
     * @var SectionInterface[]
     */
    protected $sections;

    /**
     * The parent plugin instance.
     *
     * @var Plugin
     */
    protected $plugin;

    /**
     * Gets the parent plugin instance.
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Sets the parent plugin instance.
     *
     * @param Plugin $plugin The plugin instance.
     * @return static This instance.
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * Gets the settings DB record.
     *
     * @return RecordInterface The record instance.
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Sets the settings DB record.
     *
     * @param RecordInterface $record The record instance.
     * @return static This instance.
     */
    public function setRecord(RecordInterface $record)
    {
        $this->record = $record;
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
        $sectionRecord = $this->createSectionRecord($section);
        if (!is_null($sectionRecord)) {
            $section->setRecord($sectionRecord);
        }
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

    /**
     * Creates a record for the given section.
     *
     * @param SectionInterface $section The section instance.
     * @return RecordInterface|null The record instance, or null if no record should be created.
     */
    abstract protected function createSectionRecord(SectionInterface $section);

}
