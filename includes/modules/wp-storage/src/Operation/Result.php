<?php

namespace RebelCode\Storage\WordPress\Operation;

use \Dhii\Storage\Operation\ResultInterface;
use \Dhii\Storage\ResultSetInterface;

/**
 * Description of Result
 *
 * @since [*next-version*]
 */
class Result implements ResultInterface
{

    /**
     * The result set, if applicable.
     *
     * @since [*next-version*]
     *
     * @var ResultSetInterface|null
     */
    protected $resultSet;

    /**
     * The inserted ID, if applicable.
     *
     * @since [*next-version*]
     *
     * @var int|null
     */
    protected $insertedId;

    /**
     * The error message, in any.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $errorMessage;

    public function __construct(ResultSetInterface $resultSet = null, $insertedId = null, $errorMessage = '')
    {
        $this->setResultSet($resultSet)
            ->setInsertedId($insertedId)
            ->setErrorMessage($errorMessage);
    }

    public function getResultSet()
    {
        return $this->resultSet;
    }

    public function getInsertId()
    {
        return $this->insertedId;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function setResultSet(ResultSetInterface $resultSet = null)
    {
        $this->resultSet = $resultSet;
        return $this;
    }

    public function setInsertedId($insertedId)
    {
        $this->insertedId = $insertedId;
        return $this;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

}
