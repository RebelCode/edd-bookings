<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Basic functionality for a callback page.
 *
 * @since [*next-version*]
 */
abstract class AbstractCallbackPage extends AbstractPage
{
    /**
     * Invokes the page to output its content.
     *
     * @since [*next-version*]
     */
    protected function _invoke()
    {
        echo $this->_getContent();
    }
}
