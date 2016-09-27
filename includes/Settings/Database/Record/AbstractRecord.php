<?php

namespace Aventura\Edd\Bookings\Settings\Database\Record;

/**
 * Abstract implementation of an option record.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class AbstractRecord implements RecordInterface
{

    /**
     * The record key.
     *
     * @var string
     */
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the record key.
     *
     * @param string $key The new key.
     * @return AbstractRecord This instance.
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

}
