<?php

namespace Aventura\Edd\Bookings\Settings\Section;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;

/**
 * Abstract implementation of a settings section.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class AbstractSection implements SectionInterface
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
     * The record.
     * 
     * @var RecordInterface
     */
    protected $record;

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
    public function getRecord()
    {
        return $this->record;
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
    public function setRecord(RecordInterface $record = null)
    {
        $this->record = $record;

        return $this;
    }

}
