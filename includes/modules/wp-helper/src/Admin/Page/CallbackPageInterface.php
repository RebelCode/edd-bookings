<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Represents a page that can be invoked to output its content.
 *
 * @since [*next-version*]
 */
interface CallbackPageInterface extends PageInterface
{
    /**
     * Invokes the page as a callback to output its content.
     *
     * @since [*next-version*]
     */
    public function __invoke();
}
