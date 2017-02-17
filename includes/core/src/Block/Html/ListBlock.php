<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Description of ListBlock
 *
 * @since [*next-version*]
 */
class ListBlock extends CompositeTag
{

    const TAG_NAME = 'ul';

    const ITEM_TAG_NAME = 'li';

    public function __construct(array $items = array(), array $attributes = array())
    {
        parent::__construct(static::TAG_NAME, $attributes, $this->_itemsToChildren($items));
    }

    protected function _itemsToChildren(array $items)
    {
        $tags = array();

        foreach ($items as $_key => $_val) {
            $tags[$_key] = $this->_itemToChild($_key, $_val);
        }

        return $tags;
    }

    protected function _itemToChild($key, $value)
    {
        return new RegularTag(static::ITEM_TAG_NAME, array(), $value);
    }
}
