<?php

namespace Aventura\Edd\Bookings\Settings\Option;

use \Aventura\Edd\Bookings\Settings\Node\SettingsNodeInterface;

/**
 * Any object that represents a settings option.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface OptionInterface extends SettingsNodeInterface
{

    /**
     * Sanitizes an input value prior to insertion into the database.
     *
     * @param mixed $input The input value.
     * @return mixed The output sanitized value.
     */
    public function sanitize($input);

}
