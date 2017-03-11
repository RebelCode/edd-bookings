<?php

namespace RebelCode\Storage\WordPress\Adapter;

use Dhii\Storage\Query\QueryInterface;
use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Validation\ValidatorInterface;

/**
 * Something that can convert standards-compliant query into
 * WordPress meta query params.
 *
 * @since [*next-version*]
 */
class MetaQueryAdapter extends AbstractQueryAdapter
{
    /**
     * The validator which will validate joins processed by this instance.
     *
     * @since [*next-version*]
     *
     * @var ValidatorInterface
     */
    protected $joinValidator;

    /**
     * @since [*next-version*]
     *
     * @param ValidatorInterface $joinValidator The validator which will validate joins processed by this instance.
     */
    public function __construct(ValidatorInterface $joinValidator)
    {
        $this->_setJoinValidator($joinValidator);
        $this->_construct();
    }

    /**
     * Assigns the validator which will validate joins processed by this instance.
     *
     * @since [*next-version*]
     *
     * @param ValidatorInterface $validator The validator.
     *
     * @return $this This instance.
     */
    protected function _setJoinValidator(ValidatorInterface $validator)
    {
        $this->joinValidator = $validator;

        return $this;
    }

    /**
     * Retrieves the validator which validates joins processed by this instance.
     *
     * @since [*next-version*]
     *
     * @return ValidatorInterface The validator.
     */
    protected function _getJoinValidator()
    {
        return $this->joinValidator;
    }

    /**
     * Generates a WordPress meta query object from standard query params.
     *
     * @param QueryInterface $query
     *
     * @return \WP_Meta_Query The query object that represents a query by post meta using the given query parameters.
     */
    public function getMetaQuery(QueryInterface $query)
    {
        
    }

    protected function _getMetaQueryParams(QueryInterface $query)
    {
        $entity = $this->_getEntityName();
        $join = $query->getJoinExpression();
        if (!($join instanceof An))

        foreach ($join->getTerms() as $_term) {
            $_term->
        }
    }

    protected function _createMetaQuery($params)
    {
        return new \WP_Meta_Query($params);
    }

    protected function _getEntityName()
    {
        return MetaQueryAdapterInterface::ENTITY;
    }

    protected function _validateJoin($join)
    {
        $this->_getJoinValidator()->validate($join);
    }
}
