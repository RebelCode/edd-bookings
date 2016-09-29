<?php

namespace Aventura\Edd\Bookings\Settings\Section;

use \Aventura\Edd\Bookings\Settings\Database\Record\SubRecord;
use \Aventura\Edd\Bookings\Settings\Option\OptionInterface;

/**
 * Standard implementation of a settings section.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Section extends AbstractSection
{

    /**
     * The options.
     * 
     * @var OptionInterface[]
     */
    protected $options;

    /**
     * Constructs a new instance.
     *
     * @param string $id The section ID.
     * @param string $name The section name.
     * @param OptionInterface[] $options [optional] An array of options to add to this section. Default = array()
     */
    public function __construct($id, $name, array $options = array())
    {
        $this->setId($id)
            ->setName($name)
            ->resetOptions()
            ->addOptions($options)
            ->setRecord(null)
            ->setDefault(array());
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds an option.
     *
     * @param OptionInterface $option The option to add.
     * @return Section This instance.
     */
    public function addOption(OptionInterface $option)
    {
        // If this section has a record, make the option's record a subrecord of it
        if (!is_null($this->getRecord())) {
            $option->setRecord(new SubRecord($this->getRecord(), $option->getId()));
        }
        // Add to list
        $this->options[$option->getId()] = $option;

        return $this;
    }

    /**
     * Adds all valid option instances from a given array.
     *
     * @param array $options An array of options. Non-option entries will be ignored.
     * @return Section This instance.
     */
    public function addOptions(array $options)
    {
        foreach ($options as $option) {
            if ($option instanceof OptionInterface) {
                $this->addOption($option);
            }
        }

        return $this;
    }

    /**
     * Checks if the section has an option with a specific ID.
     *
     * @param string $id The ID to search for.
     * @return boolean True if the section has an option with the given ID, false if not.
     */
    public function hasOption($id)
    {
        return isset($this->options[$id]);
    }

    /**
     * Gets an option with a specific ID.
     *
     * @param string $id The ID to search for.
     * @return OptionInterface|null The option instance or null if no option with the given ID exists in this section.
     */
    public function getOption($id)
    {
        return $this->hasOption($id)
            ? $this->options[$id]
            : null;
    }

    /**
     * Removes an option with a specific ID from this section.
     *
     * @param string $id The ID to search for.
     * @return Section This instance.
     */
    public function removeOption($id)
    {
        unset($this->options[$id]);

        return $this;
    }

    /**
     * Removes all options from this section.
     *
     * @return Section This instance.
     */
    public function resetOptions()
    {
        $this->options = array();

        return $this;
    }

}
