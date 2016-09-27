<?php

namespace Aventura\Edd\Bookings\Settings\Database\Record;

/**
 * A record that exists as an array entry inside the value of another record's array value.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SubRecord extends AbstractRecord
{

    /**
     * The parent database record.
     *
     * @var RecordInterface
     */
    protected $parentRecord;

    /**
     * Constructs a new instance.
     *
     * @param RecordInterface $parentRecord The parent record instance.
     * @param string $key The key of the record.
     */
    public function __construct(RecordInterface $parentRecord, $key)
    {
        $this->setParentRecord($parentRecord)
            ->setKey($key);
    }

    /**
     * Gets the parent record instance.
     *
     * @return RecordInterface The parent record instance.
     */
    public function getParentRecord()
    {
        return $this->parentRecord;
    }

    /**
     * Sets the parent record instance.
     *
     * @param RecordInterface $parentRecord The parent record.
     * @return SubRecord This instance.
     */
    public function setParentRecord(RecordInterface $parentRecord)
    {
        $this->parentRecord = $parentRecord;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($default = null)
    {
        $parentValue = $this->getParentRecord()->getValue();
        
        if (!is_array($parentValue)) {
            throw new \Exception(
                sprintf(
                    'Invalid parent record value for  record "%s". Value for "%s" is not an array.',
                    $this->getKey(),
                    $this->getParentRecord()->getKey()
                )
            );
        }
        
        return isset($parentValue[$this->getKey()])
            ? $parentValue[$this->getKey()]
            : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $parentValue = $this->getParentRecord()->getValue();
        $parentValue[$this->getKey()] = $value;
        $this->getParentRecord()->setValue($parentValue);

        return $this;
    }

}
