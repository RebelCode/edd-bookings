<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Base class for a page that is also a block.
 *
 * @since [*next-version*]
 */
abstract class AbstractBlockPage extends AbstractPage
{    
    /**
     * Casts the page into a string to get its content.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _toString()
    {
        return $this->_getContent();
    }
}
