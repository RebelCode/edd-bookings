<?php

namespace RebelCode\Storage\WordPress;

use \Dhii\Di\FactoryInterface;
use \RebelCode\Bookings\Framework\Model\ModelInterface;
use \RebelCode\Bookings\Framework\Storage\ResourceModelInterface;
use \RebelCode\EddBookings\CustomPostType;

/**
 * Basic functionality for a CPT controller.
 *
 * @since [*next-version*]
 */
abstract class AbstractCptController
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
     * The resource model.
     *
     * @since [*next-version*]
     *
     * @var ResourceModelInterface
     */
    protected $resourceModel;

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
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The CPT.
     * @param ResourceModelInterface $resourceModel The resource model.
     * @param FactoryInterface $factory The factory.
     */
    public function __construct(
        $cpt,
        ResourceModelInterface $resourceModel,
        FactoryInterface $factory
    ) {
        $this->setCpt($cpt)
            ->setResourceModel($resourceModel)
            ->setFactory($factory);
    }

    /**
     * Creates a model instance with the given ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the model.
     *
     * @return ModelInterface The model instance.
     */
    abstract protected function _createModel($id);

    /**
     * Gets the CPT.
     *
     * @since [*next-version*]
     *
     * @return CustomPostType The CPT.
     */
    public function getCpt()
    {
        return $this->cpt;
    }

    /**
     * Sets the CPT.
     *
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The new CPT.
     *
     * @return $this This instance.
     */
    public function setCpt($cpt)
    {
        $this->cpt = $cpt;

        return $this;
    }

    /**
     * Gets the resource model.
     *
     * @since [*next-version*]
     *
     * @return ResourceModelInterface The resource model instance.
     */
    public function getResourceModel()
    {
        return $this->resourceModel;
    }

    /**
     * Sets the resource model.
     *
     * @since [*next-version*]
     *
     * @param ResourceModelInterface $resourceModel The resource model instance.
     *
     * @return $this This instance.
     */
    public function setResourceModel($resourceModel)
    {
        $this->resourceModel = $resourceModel;

        return $this;
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
     * Performs a WordPress query to load a set of models.
     *
     * @since [*next-version*]
     *
     * @param array $query The WP_Query arguments.
     *
     * @return ModelInterface[] The models that matched the query.
     */
    public function query(array $query)
    {
        $mainArgs = array(
            'post_type'      => $this->getCpt()->getSlug(),
            'posts_per_page' => -1
        );

        $args    = array_merge($mainArgs, $query);
        $wpQuery = new \WP_Query($args);
        $models  = array();

        foreach ($wpQuery->posts as $_post) {
            $_id          = $_post->ID;
            $models[$_id] = $this->get($_id);
        }

        return $models;
    }

    /**
     * Gets the model from the database with the given ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID.
     *
     * @return ModelInterface The model instance.
     */
    public function get($id)
    {
        $model = $this->_createModel($id);

        return $model->getResourceModel()->load($model);
    }
}
