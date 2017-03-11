<?php

namespace RebelCode\Storage\WordPress\Adapter\Validation;

use Dhii\Validation\AbstractValidatorBase;
use Dhii\Expression\LogicalExpressionInterface;

/**
 * Validates a join to be used in a meta query adapter.
 *
 * @since [*next-version*]
 */
class MetaQueryJoinValidator extends AbstractValidator
{
    protected $allowedTypes = array();

    /**
     * Initializes validation constraints.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function _init()
    {
        $this->allowedTypes[] = 'Dhii\\Expression\\LogicalExpressionInterface';

        return $this;
    }

    protected function _getValidationErrors($subject)
    {
        $errors = array();
        $this->_processTypes($errors, $subject);

        return $errors;
    }

    /**
     * Processes type constraints.
     *
     * @since [*next-version*]
     *
     * @param array $errors The list of errors, to which to append error strings, if any.
     * @param mixed $subject The subject being validated.
     */
    protected function _processTypes(&$errors, $subject)
    {
        $allowed = $this->_getAllowedTypes();
        foreach ($allowed as $_type) {
            if (!$this->_isOfType($subject, $_type)) {
                $errors[] = sprintf($this->__('Subject must be of one of the following types: %1$s', 'List of types'), implode(', ', $allowed));
            }
        }
    }

    /**
     * Retrieves a list of types that the validation subject must match.
     *
     * @since [*next-version*]
     *
     * @return array The types.
     */
    protected function _getAllowedTypes()
    {
        return $this->allowedTypes;
    }

    /**
     * Retrieves the list of allowed types.
     *
     * @since [*next-version*]
     *
     * @param array $types The list of types.
     *
     * @return $this This instance.
     */
    protected function _setAllowedTypes(array $types)
    {
        $this->allowedTypes = $types;

        return $this;
    }

    /**
     * Determines whether the subject is of the specified type.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The subject to check the type of.
     * @param string $type The name of the type.
     *  Can be primitive, e.g. 'int', or a class name, or 'callable'.
     *
     * @return bool True if subject matches the specified type; false otherwise.
     */
    protected function _isOfType($subject, $type)
    {
        $primitive = sprintf('is_%1$s', $type);
        if (function_exists($primitive)) {
            return call_user_func_array($primitive, array($subject));
        }

        return is_a($subject, $type);
    }
}
