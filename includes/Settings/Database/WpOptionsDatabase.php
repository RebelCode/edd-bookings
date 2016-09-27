<?php

namespace Aventura\Edd\Bookings\Settings\Database;

use \Aventura\Edd\Bookings\Settings\Database\Record;

/**
 * Database controller for the WordPress options database table.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WpOptionsDatabase implements DatabaseInterface
{

    const RECORD_DOES_NOT_EXIST = '!record_does_not_exist!';

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Gets a record with a specific key.
     *
     * @param string $key The key.
     * @return Record|null The record with the given key or null if the key was not found.
     */
    public function get($key, $default = null)
    {
        return \get_option($key, $default);
    }

    /**
     * Updates an existing record, creating it if it doesn't exist yet.
     *
     * @param string $key The key.
     * @param mixed $value The value.
     * @return WpOptionsDatabase This instance.
     */
    public function set($key, $value)
    {
        \update_option($key, $value);

        return $this;
    }

    /**
     * Deletes a record with a specific key.
     *
     * @param string $key The key.
     * @return WpOptionsDatabase This instance.
     */
    public function delete($key)
    {
        \delete_option($key);

        return $this;
    }

    /**
     * Checks if a record with a specific key exists.
     *
     * @param string $key The key.
     * @return boolean True if the key was found, false if not.
     */
    public function has($key)
    {
        return $this->get($key, static::RECORD_DOES_NOT_EXIST) !== static::RECORD_DOES_NOT_EXIST;
    }

}
