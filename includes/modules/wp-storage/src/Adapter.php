<?php

namespace RebelCode\Storage\WordPress;

use \Dhii\Espresso\EvaluationException;
use \Dhii\Espresso\Expression\AndExpression;
use \Dhii\Espresso\Expression\EqualsExpression;
use \Dhii\Espresso\Expression\OrExpression;
use \Dhii\Evaluable\EvaluableInterface;
use \Dhii\Expression\ExpressionInterface;
use \Dhii\Expression\LogicalExpressionInterface;
use \Dhii\Storage\AdapterInterface;
use \Dhii\Storage\Operation\OperationInterface;
use \Dhii\Storage\Operation\ResultInterface;
use \Dhii\Storage\Query\ConditionAwareInterface;
use \Dhii\Storage\Query\QueryInterface;
use \Dhii\Storage\Term\EntityFieldInterface;
use \InvalidArgumentException;
use \RebelCode\Storage\WordPress\Operation\Result;
use \RebelCode\Storage\WordPress\Query\WpQuery;
use \WP_Query;

/**
 * Storage adapter.
 *
 * @since [*next-version*]
 */
class Adapter implements AdapterInterface
{
    const META_FIELD_NAME = 'meta';

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function query(OperationInterface $operation, QueryInterface $query)
    {
        if (!$query instanceof WpQuery) {
            throw new InvalidArgumentException(sprintf(
                'Parameter #2 for %s::%s() is not a WpQuery instance',
                get_class($this), __METHOD__)
            );
        }

        switch ($operation->getType()) {
            case OperationInterface::CREATE :
                return $this->create($operation->getData());

            case OperationInterface::READ :
                return $this->read($query);

            case OperationInterface::UPDATE :
                return $this->update($query->getArg('id'), $operation->getData());

            case OperationInterface::DELETE :
                return $this->delete($query);
        }

        return null;
    }

    /**
     * Creates a new post.
     *
     * @since [*next-version*]
     *
     * @param array $data An array of post data.
     *
     * @return Result The result.
     */
    public function create(array $data)
    {
        // Remove ID to make sure that this is a creation operation
        unset($data['ID']);

        return $this->_writePostData($data);
    }

    /**
     * Updates a post.
     *
     * @since [*next-version*]
     *
     * @param array $data An array of post data.
     *
     * @return Result The result.
     */
    public function update($id, array $data)
    {
        if (!is_null($id)) {
            $data['ID'] = $id;
        }

        return $this->_writePostData($data);
    }

    /**
     * Reads posts from storage.
     *
     * @since [*next-version*]
     *
     * @param QueryInterface $query The query.
     *
     * @return Result The result.
     */
    public function read(QueryInterface $query)
    {
        $queryArgs = $this->_prepareQueryArgs($query);

        return $this->_execWpQuery($queryArgs);
    }

    /**
     * Deletes a post from storage.
     *
     * @since [*next-version*]
     *
     * @param WpQuery $query The query.
     *
     * @return Result The result.
     */
    public function delete(WpQuery $query)
    {
        return $this->_deletePost($query->getArg('id'), $query->getArg('trash'));
    }

    /**
     * Writes post data into storage.
     *
     * @since [*next-version*]
     *
     * @param array $data An array of post data.
     *
     * @return Result The result.
     */
    protected function _writePostData(array $data)
    {
        // Normalize and make sure that this is a creation, not an update, by removing the ID
        $normalized = $this->_normalizePostData($data);
        $post       = $normalized['post'];
        $meta       = $normalized['meta'];
        $return     = $this->_wpInsertOrUpdatePost($post);
        $result     = $this->_normalizeResult($return);

        if ($result->getInsertId() && is_array($meta) && count($meta) > 0) {
            $this->_writePostMeta($result, $meta);
        }

        return $result;
    }

    /**
     * Inserts or updates a post.
     *
     * If the 'ID' index is detected in the $post argument, the existing post with that ID in the DB
     * will be updated. Otherwise, a new post will be created and inserted.
     *
     * @since [*next-version*]
     *
     * @param array $post An associative array containing the post data.
     *
     * @return int|WP_Error The ID of the inserted/updated post on success, or a WP_Error instance on failure.
     */
    protected function _wpInsertOrUpdatePost(array $post)
    {
        return isset($post['ID'])
            ? wp_update_post($post, true)
            : wp_insert_post($post, true);
    }

    /**
     * Writes a given set of post meta for a specific post into the DB.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the post.
     * @param array $meta An associative array containing meta values by their respective meta keys.
     *
     * @return $this This instance.
     */
    protected function _writePostMeta($id, array $meta)
    {
        foreach ($meta as $_key => $_val) {
            update_post_meta($id, $_key, $_val);
        }

        return $this;
    }

    /**
     * Normalizes the given post data.
     *
     * @since [*next-version*]
     *
     * @param array $data An associative array containing the post data.
     *
     * @return array An array with two indexes:
     *               "post": an array of post data mapped to their respective post column names
     *               "meta": any other data found in the argument array that is not mapped to a known post column.
     */
    protected function _normalizePostData($data)
    {
        $columns = array_flip($this->_getPostColumns());
        $post = array_intersect_key($data, $columns);
        /* @var $post array Map of data only for post entitiy, by column name */
        $meta = array_diff_key($data, $columns);
        /* @var $meta Other data that does not belong to the post entity */

        /**
         * After v4.4, WP can accept post meta to be changed together
         * with the post itself.
         */
        if (version_compare($this->_getWpVersion(), '4.4.0', '>=')) {
            $post['meta_input'] = $meta;
            $meta = array();
        }

        return array(
            'post' => $post,
            'meta' => $meta
        );
    }

    /**
     * Generates the params to give verbatim to {@see \WP_Query}.
     *
     * @since [*next-version*]
     *
     * @param QueryInterface $query The query to generate parans from.
     *
     * @return array The {@see \WP_Query} params.
     */
    protected function _prepareQueryArgs(QueryInterface $query)
    {
        $queryArgs = array();
        $queryArgs['post_type'] = $this->_processEntities($query);

        if ($query instanceof ConditionAwareInterface) {
            $condition = $this->_processCondition($query->getCondition());
            $queryArgs = array_merge($queryArgs, $condition);
        }
    }

    /**
     * Generates a list of entities.
     *
     * @since [*next-version*]
     *
     * @param QueryInterface $query The query to process entities of.
     *
     * @return array A list of entity names to query.
     */
    protected function _processEntities(QueryInterface $query)
    {
        return array_map(function(EvaluableInterface $term) {
            try {
                return $term->evaluate();
            } catch (EvaluationException $ex) {
                return null;
            }
        }, $query->getEntities()->getTerms());
    }

    protected function _generateMetaQuery()
    {
        
    }

    /**
     * Generates a map of conditions to pass verbatim as a WP meta query.
     *
     * For example:
     * ```php
     *  $args = array(
     *      'post_type'  => 'product',
     *      'meta_query' => array(
     *          'relation' => 'OR',
     *          array(
     *              'key'     => 'color',
     *              'value'   => 'orange',
     *              'compare' => '=',
     *          ),
     *          array(
     *              'relation' => 'AND',
     *              array(
     *                  'key' => 'color',
     *                  'value' => 'red',
     *                  'compare' => '=',
     *              ),
     *              array(
     *                  'key' => 'size',
     *                  'value' => 'small',
     *                  'compare' => '=',
     *              ),
     *          ),
     *      ),
     *  );
     * ```
     *
     * @param ExpressionInterface $expr
     *
     * @return array An array of meta conditions.
     *
     * @throws InvalidArgumentException If expression contains an unsupported condition.
     */
    protected function _processCondition(ExpressionInterface $expr)
    {
        $arr = array();

        foreach ($expr as $_term) {
            $arr = array_merge($arr, $this->_processConditionTerm($_term));
        }
        $terms = $expr->getTerms();
        $count = count($terms);

        if ($expr instanceof EqualsExpression && $count >= 2) {

        }

        $relationship = $this->_getMetaQueryRelationship($expr);

        if (is_null($relationship)) {
            throw new InvalidArgumentException(sprintf(
                'Meta queries can only be AndExpression or OrExpression instances. "%s" given.',
                get_class($expr)
            ));
        }

        $arrayQuery = array(
            'relationship' => $relationship
        );

        foreach ($expr->getTerms() as $_term) {
            $arrayQuery[] = $this->_processConditionTerm($_term);
        }

        return $arrayQuery;
    }

    /**
     *
     * @param EvaluableInterface $term
     * @return type
     *
     * @throws InvalidArgumentException If term is not supported.
     */
    protected function _processConditionTerm(EvaluableInterface $term)
    {
        if ($term instanceof EqualsExpression) {
            return $this->_processSingleExpression($term);
        }

        if ($term instanceof LogicalExpressionInterface) {
            return $this->_processCondition($term);
        }

        throw new InvalidArgumentException(sprintf(
            '%s::%s() arg #1 is not an accepted EvaluableInterface type. %s given',
            get_called_class(), __METHOD__, get_class($term)
        ));
    }

    /**
     *
     * @param EntityFieldInterface $term
     *
     * @return
     */
    protected function _processSingleExpression(EntityFieldInterface $term)
    {
        $entity = $term->getEntityName();
        $field  = $term->getFieldName();


        if ($term->getFieldName() === static::META_FIELD_NAME) {
            return array(

            );
        }
    }

    protected function _getMetaQueryRelationship(ExpressionInterface $expr)
    {
        if ($expr instanceof AndExpression) {
            return 'AND';
        }

        if ($expr instanceof OrExpression) {
            return 'OR';
        }

        return null;
    }

    /**
     * Executes a WP_Query instance.
     *
     * @since [*next-version*]
     *
     * @param WP_Query $queryArgs The query.
     *
     * @return ResultInterface The result.
     */
    protected function _execWpQuery($queryArgs)
    {
        $wpQuery = new WP_Query($queryArgs);
        $posts   = array_map(function($post) {
            return $post->to_array();
        }, $wpQuery->posts);
        
        return $this->_createResult($posts);
    }

    /**
     * Deletes a post.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the post to delete.
     * @param bool $trash If true, the post will be trashed instead.
     *
     * @return ResultInterface The result.
     */
    protected function _deletePost($id, $trash = false)
    {
        $success = wp_delete_post($id, !$trash);

        if ($success) {
            return $this->_createResult(array($success), null, '');
        }

        return $this->_createResult(array(), null, sprintf('Failed to delete post with ID #%d', $id));
    }

    /**
     * Normalizes the result from a WordPress DB operation into a standard result object instance.
     *
     * @since [*next-version*]
     *
     * @param mixed $result The result obtained from a WordPress DB operation.
     *
     * @return ResultInterface The result.
     */
    protected function _normalizeResult($result)
    {
        if (is_wp_error($result)) {
            return $this->_createResult(array(), null, $result->get_error_message());
        }

        return $result;
    }

    /**
     * Gets the current WordPress version.
     *
     * @since [*next-version*]
     *
     * @return string The current WordPress version.
     */
    protected function _getWpVersion()
    {
        return bloginfo('version');
    }

    /**
     * Creates a result.
     *
     * @since [*next-version*]
     *
     * @param array $data The result data, if applicable. Default: array()
     * @param type $insertedId The inserted ID, if applicable. Default: null
     * @param type $errorMessage The error message, if any. Default: ""
     *
     * @return Result The created instance.
     */
    protected function _createResult(array $data = array(), $insertedId = null, $errorMessage = '')
    {
        return new Result($this->_createResultSet($data), $insertedId, $errorMessage);
    }

    /**
     * Creates a result set.
     *
     * @since [*next-version*]
     *
     * @param array $data The result set data.
     *
     * @return ResultSet The created instance.
     */
    protected function _createResultSet(array $data = array())
    {
        return new ResultSet($data);
    }

    /**
     * The columns for the posts table.
     *
     * Also represents the indexes accepted in a post data array.
     *
     * @since [*next-version*]
     *
     * @return array An array containing the string names for post columns.
     */
    protected function _getPostColumns()
    {
        return array(
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_content_filtered',
            'post_title',
            'post_excerpt',
            'post_status',
            'post_type',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_parent',
            'menu_order',
            'post_mime_type',
            'post_category',
            'tax_input',
            'meta_input',
            'guid',
            'import_id',
            'context',
        );
    }
}
