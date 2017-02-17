<?php

namespace RebelCode\EddBookings\Block\Html;

use RebelCode\EddBookings\Block;

/**
 * Something that generates HTML output.
 *
 * @since [*next-version*]
 */
abstract class AbstractHtmlBlock extends Block\AbstractBlock
{
    /**
     * Single quote character.
     *
     * @since [*next-version*]
     */
    const QUOTE_SINGLE = "'";

    /**
     * Double quote character.
     *
     * @since [*next-version*]
     */
    const QUOTE_DOUBLE = '"';

    /**
     * Get the quote that this class will use to surround attribute values.
     *
     * @since [*next-version*]
     *
     * @return string The string that will surround an HTML attribute value.
     */
    protected function _getAttrQuote()
    {
        return static::QUOTE_DOUBLE;
    }

    /**
     * Generate an attribute-value pair string.
     *
     * This string is intended to be part of an HTML tag.
     *
     * @since [*next-version*]
     *
     * @param string      $name  The attribute name.
     * @param string      $value The attribute value.
     * @param string|null $quote The quote symbol to use to surround the attribute value.
     *
     * @return string A string that represents an HTML tag's attribute-value pair.
     */
    protected function _getAttrPairString($name, $value = '', $quote = null)
    {
        $surround = $this->_defaultQuote($quote);

        return sprintf('%1$s=%3$s%2$s%3$s', $name, $value, $surround);
    }

    /**
     * Generates an array of attribute-value pair strings, by attribute name.
     *
     * @since [*next-version*]
     *
     * @see _getAttrQuote()
     * @see _getAttrPairString()
     * @see _sanitizeAttrName()
     *
     * @param array             $attrs  An associative array of attribute values mapped using
     *                                  attribute name as keys.
     * @param array|string|bool $escape An array containing the names of attribute to escape,
     *                                  a string representing the name of the attribute to escape,
     *                                  or a bool to enable or disable escaping for all attributes.
     * @param string|null       $quote  The quote to use for surrounding the attribute value.
     *                                  One of the QUOTE_* constants.
     *                                  If null, defaults to the return value of `_getAttrQuote()`.
     *                                  Default: null
     *
     * @return array An array, where keys are attribute names, and values are
     *  attribute-value pair strings, e.g. 'name="value"';
     */
    protected function _getAttrsArray($attrs = array(), $escape = true, $quote = null)
    {
        $surround = $this->_defaultQuote($quote);

        $escAttrs = is_string($escape)
            ? array($escape)
            : $escape;

        $attrStrings = array();

        foreach ($attrs as $_key => $_value ) {
            // Should we standardize?
            // If an array - standardize only those attrs
            // If a bool - same for all attrs
            $isEscape = is_array($escAttrs)
                ? in_array($_key, $escAttrs)
                : (bool) $escAttrs;

            // Cleaning attribute name and value
            if ($isEscape) {
                $sKey = $this->_sanitizeAttrName($_key);
                $eValue = $this->_escapeAttrValue($_value, $surround);
            }

            $attrStrings[$sKey] = $this->_getAttrPairString($sKey, $eValue, $surround);
        }

        return $attrStrings;
    }

    /**
     * Generate a string of attribute-value pairs.
     *
     * Intended to be used in an HTML tag.
     *
     * @since [*next-version*]
     *
     * @see _getAttrQuote()
     * @see _getAttrStringsArray()
     *
     * @param array             $attrs  An associative array of attribute values mapped using
     *                                  attribute name as keys.
     * @param array|string|bool $escape An array containing the names of attribute to escape,
     *                                  a string representing the name of the attribute to escape,
     *                                  or a bool to enable or disable escaping for all attributes.
     * @param string|null       $quote  The quote to use for surrounding the attribute value.
     *                                  One of the QUOTE_* constants.
     *                                  If null, defaults to the return value of `_getAttrQuote()`.
     *                                  Default: null
     *
     * @return string A string that consists of attribute-value pairs with spaces in between.
     */
    protected function _getAttrsString($attrs = array(), $escape = true, $quote = null)
    {
        return implode(' ', $this->_getAttrsArray($attrs, $escape, $quote));
    }

    /**
     * Sanitizes an HTML tag's attribute name.
     *
     * Result will not contain white space before or after the name.
     *
     * @since [*next-version*]
     *
     * @param string $attribute The attribute name to sanitize.
     *
     * @return string A valid attribute name.
     */
    protected function _sanitizeAttrName($attribute)
    {
        return trim($attribute);
    }

    /**
     * Escapes an HTML tag's attribute value.
     *
     * If single quote is used to surround the value, only single quotes in the value will be escaped.
     * Otherwise, all quotes in the value will be escaped.
     *
     * @since [*next-version*]
     *
     * @see htmlspecialchars()
     *
     * @param mixed  $value The attribute value.
     * @param string $quote The quote that is used for surrounding the attribute value.
     *                      If null, defaults to the return of _getAttrQuote().
     *                     Default: null.
     *
     * @return string An attribute value that is valid for insertion into an HTML document.
     */
    protected function _escapeAttrValue($value, $quote = null)
    {
        $surround = $this->_defaultQuote($quote);

        $quoteStyle = ($surround === static::QUOTE_SINGLE)
            ? ENT_QUOTES
            : ENT_COMPAT;

        return htmlspecialchars($value, $quoteStyle);
    }

    /**
     * Utility method used internally to default a given quote character.
     *
     * If the given quote character is null, the return of _getAttrQuote() is returned.
     * Otherwise, the given quote is returned as is.
     *
     * @since [*next-version*]
     *
     * @param string|null $quote The quote.
     *
     * @return string
     */
    protected function _defaultQuote($quote)
    {
        return is_null($quote)
            ? $this->_getAttrQuote()
            : $quote;
    }
}
