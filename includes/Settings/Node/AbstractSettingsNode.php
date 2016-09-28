<?php

namespace Aventura\Edd\Bookings\Settings\Node;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;

/**
 * Description of AbstractSettingsNode
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AbstractSettingsNode implements SettingsNodeInterface
{

    /**
     * The ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The name.
     *
     * @var string
     */
    protected $name;

    /**
     * The description.
     *
     * @var string
     */
    protected $description;

    /**
     * The option's default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * The record instance.
     *
     * @var RecordInterface
     */
    protected $record;

    /**
     * The view name.
     *
     * @var string
     */
    protected $view;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * {@inheritdoc}
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Sets the option's default value.
     *
     * @param mixed $default The default value.
     * @return static This instance.
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecord(RecordInterface $record = null)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

}
