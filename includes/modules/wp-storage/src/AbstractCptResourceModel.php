<?php

namespace RebelCode\Storage\WordPress;

use \Dhii\Storage\AdapterInterface;
use \RebelCode\Bookings\Framework\Model\ModelInterface;
use \RebelCode\Bookings\Framework\Storage\AbstractResourceModel;
use \RebelCode\Bookings\Framework\Storage\ResourceModelInterface;
use \RebelCode\EddBookings\CustomPostType;

/**
 * Basic functionality for a WordPress custom post type Resource Model.
 *
 * @since [*next-version*]
 */
abstract class AbstractCptResourceModel extends AbstractResourceModel implements ResourceModelInterface
{
    /**
     * The CPT.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $cpt;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The custom post type.
     * @param AdapterInterface $storageAdapter The storage adapter instance.
     */
    public function __construct(CustomPostType $cpt, AdapterInterface $storageAdapter)
    {
        parent::__construct(array());

        $this->_setStorageAdapter($storageAdapter)
            ->setCpt($cpt);
    }

    /**
     * Gets the custom post type.
     *
     * @since [*next-version*]
     *
     * @return CustomPostType The custom post type.
     */
    public function getCpt()
    {
        return $this->cpt;
    }

    /**
     * Sets the custom post type.
     *
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The custom post type.
     *
     * @return $this This instance.
     */
    public function setCpt($cpt)
    {
        $this->cpt = $cpt;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function load(ModelInterface $model)
    {
        $id = $model->getId();

        if ($this->hasData($id)) {
            return $this->getData($id);
        }

        if (\get_post_type($id) === $this->getCpt()->getSlug()) {
            $meta = \get_post_custom($id);
            $data = $this->_metaToData($meta);
            $model->setData($data);
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function save(ModelInterface $model)
    {
        $id   = $model->getId();
        $data = $model->getData();
        $meta = $this->_dataToMeta($data);

        foreach ($meta as $_key => $_val) {
            \update_post_meta($id, $_key, $_val);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function delete(ModelInterface $model)
    {
        $id = $model->getId();

        if (\get_post_type($id) === $this->getCpt()->getSlug()) {
            \wp_delete_post($id, true);
        }

        return false;
    }

    /**
     * Flattens a given meta array.
     *
     * Since there can be multiple meta values with the same key, `get_post_custom()` will
     * return each meta key mapped to an array of values.
     *
     * @param array $meta The input meta array.
     * @return array The output array.
     */
    protected function _flattenMetaArray($meta)
    {
        array_walk($meta, function(&$val) {
            if (is_array($val)) {
                $val = $val[0];
            }
        });

        return $meta;
    }

    /**
     * Transforms meta data into model data for loading from DB.
     *
     * @since [*next-version*]
     *
     * @param array $meta An array of meta data.
     *
     * @return array The array of model data.
     */
    abstract protected function _metaToData(array $meta);

    /**
     * Transforms model data into a meta array for saving into DB.
     *
     * @since [*next-version*]
     *
     * @param array $data An array of data.
     *
     * @return array The meta array.
     */
    abstract protected function _dataToMeta(array $data);
}
