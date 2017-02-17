<?php

namespace RebelCode\Storage\WordPress\Operation;

use \Dhii\Storage\Operation\OperationInterface;

/**
 * Description of Operation
 *
 * @since [*next-version*]
 */
class Operation implements OperationInterface
{
    protected $data;

    protected $type;

    public function __construct($type, array $data = array())
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this->type;
    }
}
