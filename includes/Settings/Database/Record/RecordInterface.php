<?php

namespace Aventura\Edd\Bookings\Settings\Database\Record;

/**
 * Any record for an option in the database.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface RecordInterface
{

    /**
     * Gets the record key.
     *
     * @return string The key.
     */
    public function getKey();

    /**
     * Gets the record value.
     *
     * @return mixed The value.
     */
    public function getValue();

    /**
     * Sets the record value.
     *
     * @param mixed $value The new value.
     * @return AbstractRecord This instance.
     */
    public function setValue($value);

}
