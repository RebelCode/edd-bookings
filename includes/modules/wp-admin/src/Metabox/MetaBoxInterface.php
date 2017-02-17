<?php

namespace RebelCode\WordPress\Admin\Metabox;

use \WP_Screen;

/**
 * Anything that can represent a metabox.
 *
 * @since [*next-version*]
 */
interface MetaBoxInterface
{
    /**
     * Normal metabox context.
     *
     * Displays the metabox on the main page area.
     *
     * @since [*next-version*]
     */
    const CTX_NORMAL    = 'normal';

    /**
     * Side metabox context.
     *
     * Displays the metabox on the side area.
     *
     * @since [*next-version*]
     */
    const CTX_SIDE      = 'side';

    /**
     * Advanced metabox context.
     *
     * Displays the metabox further down the main area.
     *
     * @since [*next-version*]
     */
    const CTX_ADVANCED  = 'advanced';

    /**
     * Default metabox priority.
     *
     * @since [*next-version*]
     */
    const PRIORITY_DEFAULT = 'default';

    /**
     * Low metabox priority.
     *
     * The metabox will be displayed further down.
     *
     * @since [*next-version*]
     */
    const PRIORITY_LOW  = 'low';

    /**
     * High metabox priority.
     *
     * The metabox will be displayed further up.
     *
     * @since [*next-version*]
     */
    const PRIORITY_HIGH = 'high';

    /**
     * Core metabox priority.
     *
     * The metabox will be displayed as high up as possible.
     *
     * @since [*next-version*]
     */
    const PRIORITY_CORE = 'core';

    /**
     * Gets the metabox ID.
     *
     * @since [*next-version*]
     *
     * @return string The metabox ID.
     */
    public function getId();

    /**
     * Gets the metabox title.
     *
     * @since [*next-version*]
     *
     * @return string The metabox title.
     */
    public function getTitle();

    /**
     * Gets the metabox content callback.
     *
     * @since [*next-version*]
     *
     * @return callable The metabox content callback.
     */
    public function getCallback();

    /**
     * Gets the screen where the metabox will be rendered.
     *
     * @since [*next-version*]
     *
     * @return string|array|WP_Screen The screen ID, an array of screen IDs or a WP_Screen instance.
     */
    public function getScreen();

    /**
     * Gets the context of the metabox.
     *
     * The context determines its location on the screen.
     *
     * @since [*next-version*]
     *
     * @return string The context.
     */
    public function getContext();

    /**
     * Gets the priority of the metabox.
     *
     * The priority determines its position in the context.
     *
     * @since [*next-version*]
     *
     * @return string The priority.
     */
    public function getPriority();

    /**
     * Registers this metabox.
     *
     * @since [*next-version*]
     */
    public function register();
}
