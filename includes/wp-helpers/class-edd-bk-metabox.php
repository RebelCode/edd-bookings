<?php

/**
 * Metabox class.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\WP_Helpers
 */
class EDD_BK_Metabox {

	// Priority constants
	const PRIORITY_HIGH = 'high';
	const PRIORITY_CORE = 'core';
	const PRIORITY_DEFAULT = 'default';
	const PRIORITY_LOW = 'low';

	// Context constants
	const CONTEXT_NORMAL = 'normal';
	const CONTEXT_ADVANCED = 'advanced';
	const CONTEXT_SIDE = 'side';

	/**
	 * Metabox's ID.
	 * @var string
	 */
	private $id;

	/**
	 * The title of the metabox, shown in the metabox's header.
	 * @var string
	 */
	private $title;

	/**
	 * The name of the view (PHP file without extension) for this metabox.
	 * @var string
	 */
	private $view;

	/**
	 * The metabox's priority within its context.
	 * @var string
	 */
	private $priority;

	/**
	 * The context of the metabox.
	 * @var string.
	 */
	private $context;

	/**
	 * The screen where the metabox will be shown.
	 * @var string
	 */
	private $screen;

	/**
	 * Constructor.
	 * 
	 * @param string $id       The Metabox ID
	 * @param string $title    The Metabox title
	 * @param string $view     The Metabox view name
	 * @param string $context  The Metabox context
	 * @param string $priority The Metabox priority
	 * @param string $screen   The Metabox screen
	 */
	public function __construct( $id, $title, $view, $context = self::CONTEXT_NORMAL, $priority = self::PRIORITY_DEFAULT, $screen = 'download' ) {
		$this->id = $id;
		$this->title = $title;
		$this->view = $view;
		$this->context = $context;
		$this->priority = $priority;
		$this->screen = $screen;
	}

	/**
	 * Gets the Metabox ID.
	 *
	 * @return string
	 */
	public function get_ID() {
		return $this->id;
	}

	/**
	 * Sets the Metabox ID.
	 *
	 * @param string $id the id
	 * @return self
	 */
	public function set_ID( $id ) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Gets the The title of the metabox, shown in the metabox's header.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Sets the The title of the metabox, shown in the metabox's header.
	 *
	 * @param string $title the title
	 * @return self
	 */
	public function set_title( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Gets the The name of the view (PHP file without extension) for this metabox.
	 *
	 * @return string
	 */
	public function get_view() {
		return $this->view;
	}

	/**
	 * Sets the The name of the view (PHP file without extension) for this metabox.
	 *
	 * @param string $view the view
	 * @return self
	 */
	public function set_view( $view ) {
		$this->view = $view;
		return $this;
	}

	/**
	 * Gets the The metabox's priority within its context.
	 *
	 * @return string
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * Sets the The metabox's priority within its context.
	 *
	 * @param string $priority the priority
	 * @return self
	 */
	public function set_priority( $priority ) {
		$this->priority = $priority;
		return $this;
	}

	/**
	 * Gets the The context of the metabox.
	 *
	 * @return string
	 */
	public function get_context() {
		return $this->context;
	}

	/**
	 * Sets the The context of the metabox.
	 *
	 * @param string $context the context
	 * @return self
	 */
	public function set_context( $context ) {
		$this->context = $context;
		return $this;
	}

	/**
	 * Gets the The screen where the metabox will be shown.
	 *
	 * @return string
	 */
	public function get_screen() {
		return $this->screen;
	}

	/**
	 * Sets the The screen where the metabox will be shown.
	 *
	 * @param string $screen the screen
	 * @return self
	 */
	public function set_screen( $screen ) {
		$this->screen = $screen;
		return $this;
	}

	/**
	 * Registers the metabox to WordPress.
	 */
	public function register() {
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'render' ),
			$this->screen,
			$this->context,
			$this->priority
		);
	}

	/**
	 * Renders the metabox by loading the view file.
	 */
	public function render() {
		// If it doesn't exist, throw an exception
		if ( ! file_exists( $this->view ) ) {
			throw new EDD_BK_Exception( __( 'Metabox view does not exist:', EDD_Bookings::TEXT_DOMAIN ) . " [{$view_file}]" );
		}
		// Prepare required data
		$admin = EDD_Bookings::instance()->get_admin();
		// Load the view file
		include $this->view;
	}
}
