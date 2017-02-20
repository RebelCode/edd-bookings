<?php

namespace RebelCode\EddBookings\Model;

use \DateTimeZone;
use \Dhii\Di\FactoryInterface;
use \Dhii\Storage\AdapterInterface;
use \RebelCode\Diary\DateTime\DateTime;
use \RebelCode\EddBookings\CustomPostType;
use \RebelCode\Storage\WordPress\AbstractCptResourceModel;

/**
 * A resource model implementation for booking models.
 *
 * @since [*next-version*]
 */
class BookingResourceModel extends AbstractCptResourceModel
{
    /**
     * The factory - used to create menus.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param CustomPostType $postType The CPT
     * @param AdapterInterface $storageAdapter The storage adapter.
     * @param FactoryInterface $factory The factory.
     */
    public function __construct(CustomPostType $postType, AdapterInterface $storageAdapter, FactoryInterface $factory)
    {
        parent::__construct($postType, $storageAdapter);

        $this->setFactory($factory);
    }

    /**
     * Gets the factory instance.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the factory instance.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface $factory The new factory instance.
     *
     * @return $this This instance.
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function _dataToMeta(array $data)
    {
        $meta = $data;

        if (is_string($data['start'])) {
            $meta['start'] = DateTime::parse($data['start'], new DateTimeZone('UTC'))
                ->getTimestamp();
        }

        if (is_string($data['end'])) {
            $meta['end'] = DateTime::parse($data['end'], new DateTimeZone('UTC'))
                ->getTimestamp();
        }

        return $meta;
    }

    /**
     *
     * @param array $meta
     * @return array
     */
    protected function _metaToData(array $meta)
    {
        $data = $this->_flattenMetaArray($meta);

        /*
         * @todo Find out why bookings might not have any meta data (maybe fixed in 3.0.0)
         */
        if (!isset($data['start'])) {
            return $data;
        }

        // We don't need the duration since 3.0.0
        if (!isset($data['end']) && isset($data['duration'])) {
            $data['end'] = (int) $data['start'] + (int) $data['duration'];
            unset($data['duration']);
        }

        // Prepare the 'start' and 'end' datetimes
        $startTime     = (int) $data['start'];
        $endTime       = (int) $data['end'];
        $data['start'] = $this->getFactory()->make('datetime', array($startTime));
        $data['end']   = $this->getFactory()->make('datetime', array($endTime));

        $data['payment'] = new \EDD_Payment($data['payment_id']);
        $data['customer'] = new \EDD_Customer($data['payment_id']);

        return $data;
    }
}
