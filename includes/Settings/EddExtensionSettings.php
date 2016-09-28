<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Controller\ControllerInterface;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Settings\Database\Record\Record;
use \Aventura\Edd\Bookings\Settings\Database\Record\SubRecord;
use \Aventura\Edd\Bookings\Settings\Database\WpOptionsDatabase;
use \Aventura\Edd\Bookings\Settings\Node\SettingsNodeInterface;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * An EDD extension specific implementation of a settings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class EddExtensionSettings extends Settings implements ControllerInterface
{

    const EDD_SETTINGS_OPTION_KEY = 'edd_settings';

    /**
     * The parent plugin instance.
     *
     * @var Plugin
     */
    protected $plugin;

    /**
     * The label shown in the EDD extensions settings page.
     *
     * @var string
     */
    protected $label;

    /**
     * Constructs a new instance.
     *
     * @param Plugin $plugin The plugin instance.
     * @param string $label The label shown in the EDD Extensions settings page.
     */
    public function __construct(Plugin $plugin, $label)
    {
        $this->setPlugin($plugin)
            ->setLabel($label);
        $record = $this->generateEddSettingsSubRecord();
        parent::__construct($record);
    }

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
    public function setPlugin(Plugin $plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * Gets the extension ID.
     * 
     * @return string The extension ID.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the extension ID.
     *
     * @param string $extensionId The new extension ID.
     * @return EddExtensionSettings This instance.
     */
    public function setLabel($extensionId)
    {
        $this->label = $extensionId;
        return $this;
    }

    /**
     * Gets the key of the settings record.
     * 
     * @return string
     */
    public function getRecordKey()
    {
        return $this->getPlugin()->getId();
    }

    /**
     * Gets the EDD settings record key.
     * 
     * @return string
     */
    public function getParentRecordKey()
    {
        return static::EDD_SETTINGS_OPTION_KEY;
    }

    /**
     * Generates a sub-record for this extension with its parent set to EDD's settings record.
     *
     * @return SubRecord The record instance.
     */
    public function generateEddSettingsSubRecord()
    {
        $database = new WpOptionsDatabase();
        $eddSettingsRecord = new Record($database, $this->getParentRecordKey());
        return new SubRecord($eddSettingsRecord, $this->getRecordKey());
    }

    /**
     * Registers the settings with EDD.
     */
    public function registerSettings($settings)
    {
        $toRegister = array();
        foreach ($this->getSections() as $section) {
            // Register a dummy option for the section itself
            $toRegister[$section->getId()] = $this->prepareEddSetting($section);
            // Register an option for each option in the section
            foreach ($section->getOptions() as $option) {
                $prefix = sprintf('%s.', $section->getId());
                $settingData = $this->prepareEddSetting($option, $prefix);
                $settingId = $settingData['id'];
                $toRegister[$settingId] = $settingData;
            }
        }
        // If EDD is at version 2.5 or later...
        if (version_compare(EDD_VERSION, 2.5, '>=')) {
            // Use the previously noted array key as an array key again and next your settings
            $toRegister = array($this->getRecordKey() => $toRegister);
        }
        $allSettings = array_merge($settings, $toRegister);
        return $allSettings;
    }

    /**
     * Prepares the EDD setting for the given Section or Option.
     *
     * @param SettingsNodeInterface $node The section or option to add.
     * @param string $idPrefix [optional] String to prefix to the setting ID.
     * @return array An array of data.
     */
    protected function prepareEddSetting(SettingsNodeInterface $node, $idPrefix = '')
    {
        $isSection = $node instanceof SectionInterface;
        // Create data array
        $data = array();
        $data['id'] = $id = sprintf('%s%s', $idPrefix, $node->getId());
        $data['name'] = $isSection
            ? sprintf('<strong>%s</strong>', $node->getName())
            : $node->getName();
        $data['desc'] = $node->getDescription();
        $data['type'] = $isSection
            ? 'header'
            : 'hook';

        // Set up the hook callback for non-section nodes
        if (!$isSection) {
            $action = sprintf('edd_%s', $id);
            $callback = array($this, 'renderOption');
            if (!has_action($action, $callback)) {
                add_action($action, $callback);
            }
        }

        return $data;
    }

    /**
     * Renders an Edd option.
     *
     * @param array $args The EDD option data.
     */
    public function renderOption($args)
    {
        // Get the ID parts
        $parts = explode('.', $args['id'], 2);
        list($sectionId, $optionId) = (count($parts) === 2)
            ? $parts
            : array('', '');

        // Get the section and option
        $section = $this->getSection($sectionId);
        if (!is_null($section)) {
            $option = $section->getOption($optionId);
            // Render the option
            echo $this->getPlugin()->renderView($option->getView(), $args);
        }
    }

    /**
     * Registers the extension's settings tab in the EDD Extensions setttings page.
     *
     * @param array $tabs The input filter array of tabs.
     * @return array The output array of tabs.
     */
    public function registerEddExtensionsTab($tabs)
    {
        $tabs[$this->getRecordKey()] = $this->getLabel();

        return $tabs;
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            ->addFilter('edd_settings_sections_extensions', $this, 'registerEddExtensionsTab')
            ->addFilter('edd_settings_extensions', $this, 'registerSettings')
        ;
    }

}
