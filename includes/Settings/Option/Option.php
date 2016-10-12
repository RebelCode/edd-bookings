<?php

namespace Aventura\Edd\Bookings\Settings\Option;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;

/**
 * Concrete implementation of a standard option.
 *
 * This class only serves a baseline - it performs no sanitization on input data.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Option extends AbstractOption
{

    /**
     * The default view name.
     */
    const DEFAULT_VIEW = 'Settings.Option.Default';

    /**
     * Constructs a new instance.
     *
     * @param string $id The option ID.
     * @param string $name The option name.
     * @param string $description [optional] The option description. Default = ''.
     * @param string $view [optional] The option view name. Defaults to {@link Option::DEFAULT_VIEW}.
     */
    public function __construct($id, $name, $description = '', $default = '', $view = null)
    {
        $this->setId($id)
            ->setName($name)
            ->setDescription($description)
            ->setDefault($default)
            ->setView(is_null($view)? static::DEFAULT_VIEW : $view)
            ->setRecord(null)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function sanitize($input)
    {
        return $input;
    }

}
