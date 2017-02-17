<?php

namespace RebelCode\Storage\WordPress\Query;

use \Dhii\Espresso\Expression\AndExpression;
use \Dhii\Espresso\Term\LiteralTerm;
use \Dhii\Storage\Query\QueryInterface;

/**
 * A basic WordPress Query implementation.
 *
 * @since [*next-version*]
 */
class WpQuery implements QueryInterface
{
    /**
     * The post type to query.
     *
     * @since [*next-version*]
     *
     * @var type
     */
    protected $postType;

    /**
     * The arguments.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $postType The post type slug name.
     */
    public function __construct($postType, array $args)
    {
        $this->setPostType($postType)
            ->setArgs($args);
    }

    /**
     * Retrieves the slug name of the post type to be queried.
     *
     * @since [*next-version*]
     *
     * @return string The post type slug name.
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * Sets the post type to be queried.
     *
     * @since [*next-version*]
     *
     * @param string $postType The slug name of the post type to be queried.
     *
     * @return $this This instance.
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;

        return $this;
    }

    /**
     * Retrieves the query arguments.
     *
     * @since [*next-version*]
     *
     * @return array The query arguments.
     */
    public function getArgs()
    {
        $args = $this->args;
        if (isset($args['ID'])) {
            $args['p'] = $args['ID'];
            unset($args['ID']);
        }
        return $args;
    }

    /**
     * Sets the query arguments.
     *
     * @since [*next-version*]
     *
     * @param array $args The arguments.
     *
     * @return $this This instance.
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Retrieves the query arguments.
     *
     * @since [*next-version*]
     *
     * @param string $key The argument key.
     *
     * @return mixed The value of the query argument.
     */
    public function getArg($key)
    {
        return isset($this->args[$key])
            ? $this->args[$key]
            : null;
    }

    /**
     * Sets the value of a single query argument.
     *
     * @since [*next-version*]
     *
     * @param string $key   The argument key.
     * @param mixed  $value The arguments value.
     *
     * @return $this This instance.
     */
    public function setArg($key, $value)
    {
        $this->args[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getEntities()
    {
        return new AndExpression(array(
            new LiteralTerm($this->getPostType())
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return null
     */
    public function getJoinExpression()
    {
        return null;
    }
}
