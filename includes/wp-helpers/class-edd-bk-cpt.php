<?php

/**
 * Model class for a custom post type.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\WP_Helpers
 */
class EDD_BK_Custom_Post_Type {
	
	/**
	 * The CPT slug name.
	 * @var string
	 */
	protected $slug;

	/**
	 * The CPT labels
	 * @var string
	 */
	protected $labels;

	/**
	 * The CPT properties
	 * @var string
	 */
	protected $properties;

	/**
	 * Constructs the EDD_BK_Custom_Post_Type instance.
	 * 
	 * @param string $slug       The CPT slug name.
	 * @param array  $labels     The CPT labels.
	 * @param array  $properties The CPT properties.
	 */
	public function __construct( $slug, $labels = array(), $properties = array() ) {
		$this->slug = $slug;
		$this->labels = $labels;
		$this->properties = $properties;
	}

	/**
	 * Gets the CPT slug name.
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * Sets the CPT slug name.
	 *
	 * @param string $slug The slug
	 * @return self
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}

	/**
	 * Gets the CPT labels.
	 *
	 * @return string
	 */
	public function getLabels() {
		return $this->labels;
	}

	/**
	 * Sets the CPT labels.
	 *
	 * @param string $labels The labels
	 * @return self
	 */
	public function setLabels( $labels ) {
		$this->labels = $labels;
		return $this;
	}

	/**
	 * Sets a single CPT label.
	 * 
	 * @param string $label The name of the label to set.
	 * @param string $value the value of the label.
	 * @return self
	 */
	public function setLabel( $label, $value ) {
		$this->labels[ $label ] = $value;
		return $this;
	}

	/**
	 * Generates the labels for this CPT using the singular and plural names.
	 * 
	 * @param  string $singularName The CPT singular name.
	 * @param  string $pluralName   the CPT plural name.
	 */
	public function generateLabels( $singularName, $pluralName ) {
		$singularName = ucfirst( $singularName );
		$pluralName = ucfirst( $pluralName );
		$lowerSingularName = strtolower( $singularName );
		$lowerPluralName = strtolower( $pluralName );
		$this->labels = array(
			'name'				=>	$pluralName,
			'singular_name'		=>	$singularName,
			'add_new_item'		=>	__( 'Add New' ) . ' ' . $singularName,
			'edit_item'			=>	__( 'Edit' ) . ' ' . $singularName,
			'new_item'			=>	__( 'New' ) . ' ' . $singularName,
			'view_item'			=>	__( 'View' ) . ' ' . $singularName,
			'search_items'		=>	__( 'Search' ) . ' ' . $pluralName,
			'not_found'			=>	sprintf( _x( 'No %s found', 'posts', 'edd_bk' ), $lowerPluralName ),
			'not_found_trash'	=>	sprintf( _x( 'No %s found in trash', 'posts', 'edd_bk' ), $lowerPluralName )
		);
	}

	/**
	 * Gets the CPT properties.
	 *
	 * @return string
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Sets the CPT properties.
	 *
	 * @param string $properties The properties
	 * @return self
	 */
	public function setProperties( $properties ) {
		$this->properties = $properties;
		return $this;
	}

	/**
	 * Sets a single CPT property.
	 * 
	 * @param string $name  The name of the property to set.
	 * @param mixed  $value The value of the property.
	 */
	public function setProperty( $name, $value ) {
		$this->properties[ $name ] = $value;
	}

	/**
	 * Registers the CPT to WordPress.
	 */
	public function register() {
		$args = array_merge( $this->properties, array( 'labels' => $this->labels ) );
		register_post_type( $this->slug, $args );
	}

}
