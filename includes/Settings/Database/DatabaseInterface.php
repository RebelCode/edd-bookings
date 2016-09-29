<?php

namespace Aventura\Edd\Bookings\Settings\Database;

/**
 * Any object that can be used for storage of the settings options.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface DatabaseInterface
{

    /**
     * Gets a record with a specific key.
     *
     * @param string $key The key.
     * @return mixed The record value with the given key or null if the key was not found.
     */
    public function get($key, $default = null);

    /**
     * Updates an existing record, creating it if it doesn't exist yet.
     *
     * @param string $key The key.
     * @param mixed $value The value.
     * @return DatabaseInterface This instance.
     */
    public function set($key, $value);

    /**
     * Deletes a record with a specific key.
     *
     * @param string $key The key.
     * @return DatabaseInterface This instance.
     */
    public function delete($key);

    /**
     * Checks if a record with a specific key exists.
     *
     * @param string $key The key.
     * @return boolean True if the key was found, false if not.
     */
    public function has($key);

}
