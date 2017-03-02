<?php

namespace RebelCode\Block\Html;

/**
 * Description of SelectTag
 *
 * @since [*next-version*]
 */
class SelectTag extends CompositeTag
{

    const TAG_NAME = 'select';
    const OPTION_TAG_NAME = 'option';
    const GROUP_TAG_NAME = 'optgroup';

    public function __construct(array $items = array(), $selected = null, array $attributes = array())
    {
        parent::__construct(static::TAG_NAME, $attributes, $this->_itemsToTags($items, $selected));
    }

    protected function _itemsToTags(array $items, $selected = null)
    {
        $tags = array();

        foreach ($items as $_val => $_text) {
            $tags[$_val] = $this->_itemToTag($_val, $_text, (string) $_val === (string) $selected);
        }

        return $tags;
    }

    protected function _itemToTag($value, $text, $selected)
    {
        if (is_array($text)) {
            return $this->_itemToGroup($value, $text, $selected);
        }
        return $this->_itemToOption($value, $text, $selected);
    }

    protected function _itemToGroup($name, $items, $selected)
    {
        return new CompositeTag(
            static::GROUP_TAG_NAME,
            array('label' => $name),
            $this->_itemsToTags($items, $selected)
        );
    }

    protected function _itemToOption($value, $text, $selected)
    {
        $attrs = array('value' => $value);

        if ($selected) {
            $attrs['selected'] = 'selected';
        }

        return new RegularTag(
            static::OPTION_TAG_NAME,
            $attrs,
            $text
        );
    }
}
