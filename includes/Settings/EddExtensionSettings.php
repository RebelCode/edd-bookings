<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Controller\ControllerInterface;
use \Aventura\Edd\Bookings\Plugin;

/**
 * An EDD extension specific implementation of a settings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class EddExtensionSettings extends Settings implements ControllerInterface
{

    const RECORD_KEY = 'eddbk';

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
     * Gets the label of the EDD extensions tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Bookings', 'eddbk');
    }

    /**
     * Loads the settings from the config file.
     */
    public function loadConfig()
    {
        $config = $this->getPlugin()->loadConfigFile('settings');
        if (is_array($config)) {
            $this->addSections($config);
        }
    }

    public function registerEddExtensionsTab($tabs)
    {
        $tabs[static::RECORD_KEY] = $this->getTabLabel();
        return $tabs;
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
            $toRegister = array(static::RECORD_KEY => $toRegister);
        }
        $allSettings = array_merge($settings, $toRegister);
        return $allSettings;
    }

    protected function prepareEddSetting($arg, $idPrefix = '')
    {
        if (!($arg instanceof SectionInterface) && !($arg instanceof OptionInterface)) {
            throw new InvalidArgumentException('Expected argument to be a section or an option.');
        }

        $id = $idPrefix . $arg->getId();

        if ($arg instanceof SectionInterface) {
            $name = sprintf('<strong>%s</strong>', $arg->getName());
            $type = 'header';
        } else {
            $name = $arg->getName();
            $desc = $arg->getDescription();
            $type = 'hook';
            // Set up the hook callback
            $action = sprintf('edd_%s', $id);
            $callback = array($this, 'renderOption');
            if (!has_action($action, $callback)) {
                add_action($action, $callback);
            }
        }
        $data = compact('id', 'name', 'desc', 'type');
        $data['salami'] = sprintf('edd_%s', $id);
        return $data;
    }

    public function renderOption($args)
    {
        $id = $args['id'];

        $parts = explode('.', $id, 2);
        if (count($parts) !== 2) {
            return;
        }
        list($sectionId, $optionId) = $parts;
        $section = $this->getSection($sectionId);
        if (is_null($section)) {
            return;
        }
        $option = $section->getOption($optionId);
        echo $this->getPlugin()->renderView($option->getView(), $args);
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->loadConfig();
        $this->getPlugin()->getHookManager()
            ->addFilter('edd_settings_sections_extensions', $this, 'registerEddExtensionsTab')
            ->addFilter('edd_settings_extensions', $this, 'registerSettings')
        ;
    }

}
