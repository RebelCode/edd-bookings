<?php

namespace Aventura\Edd\Bookings\Settings\Database\Record;

use \Aventura\Edd\Bookings\Settings\Database\DatabaseInterface;

/**
 * Implementation of a database regular table record.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Record extends AbstractRecord
{

    /**
     * The table where the record is stored.
     *
     * @var DatabaseInterface
     */
    protected $db;

    /**
     * Constructs a new instance.
     *
     * @param DatabaseInterface $table The table instance.
     * @param string $key The option key.
     */
    public function __construct(DatabaseInterface $table, $key)
    {
        $this->setDatabase($table)
            ->setKey($key);
    }

    /**
     * Gets the database controller instance.
     *
     * @return DatabaseInterface The database controller instance.
     */
    public function getDatabase()
    {
        return $this->db;
    }

    /**
     * Sets the DB controller instance.
     *
     * @param DatabaseInterface $db The database controller instance.
     * @return Record This instance.
     */
    public function setDatabase(DatabaseInterface $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getDatabase()->get($this->getKey());
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->getDatabase()->set($this->getKey(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyPath()
    {
        return array($this->getKey());
    }

}
