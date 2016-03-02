<?php

namespace Aventura\Edd\Bookings;

/**
 * Generic Custom Post Type model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class CustomPostType
{

    /**
     * The CPT slug name.
     * 
     * @var string
     */
    protected $slug;

    /**
     * The CPT labels
     * 
     * @var string
     */
    protected $labels;

    /**
     * The CPT properties
     * 
     * @var string
     */
    protected $properties;

    /**
     * Constructs the EDD_BK_Custom_Post_Type instance.
     * 
     * @param string $slug The CPT slug name.
     * @param array $labels The CPT labels.
     * @param array $properties The CPT properties.
     */
    public function __construct($slug, $labels = array(), $properties = array())
    {
        $this->setSlug($slug)
                ->setLabels($labels)
                ->setProperties($properties);
    }

    /**
     * Gets the CPT slug name.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the CPT slug name.
     *
     * @param string $slug The slug
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Gets the CPT labels.
     *
     * @return string
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Sets the CPT labels.
     *
     * @param string $labels The labels
     * @return self
     */
    public function setLabels($labels)
    {
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
    public function setLabel($label, $value)
    {
        $this->labels[$label] = $value;
        return $this;
    }

    /**
     * Generates the labels for this CPT using the singular and plural names.
     * 
     * @param string $pSingularName The CPT singular name.
     * @param string $pPluralName The CPT plural name.
     */
    public function generateLabels($pSingularName, $pPluralName)
    {
        $singularName = ucfirst($pSingularName);
        $pluralName = ucfirst($pPluralName);
        $lowerPluralName = strtolower($pluralName);
        $this->labels = array(
            'name' => $pluralName,
            'singular_name' => $singularName,
            'add_new_item' => __('Add New') . ' ' . $singularName,
            'edit_item' => __('Edit') . ' ' . $singularName,
            'new_item' => __('New') . ' ' . $singularName,
            'view_item' => __('View') . ' ' . $singularName,
            'search_items' => __('Search') . ' ' . $pluralName,
            'not_found' => sprintf(_x('No %s found', 'posts', 'edd_bk'), $lowerPluralName),
            'not_found_trash' => sprintf(_x('No %s found in trash', 'posts', 'edd_bk'), $lowerPluralName)
        );
        return $this;
    }

    /**
     * Gets the CPT properties.
     *
     * @return string
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Sets the CPT properties.
     *
     * @param string $properties The properties
     * @return self
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * Sets a single CPT property.
     * 
     * @param string $name  The name of the property to set.
     * @param mixed  $value The value of the property.
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Adds support for a given feature to the CPT.
     * 
     * @param string|array $arg The name of the feature, or an array of feature names.
     */
    public function addSupport($arg)
    {
        add_post_type_support($this->getSlug(), $arg);
    }

    /**
     * Removes support of a given feature from the CPT.
     * 
     * @param string|array $arg The name of the feature, or an array of feature names.
     */
    public function removeSupport($arg)
    {
        $argArray = is_array($arg)
                ? $arg
                : array($arg);
        foreach ($argArray as $feature) {
            remove_post_type_support($this->getSlug(), $feature);
        }
    }

    /**
     * Checks if the CPT supports the given feature.
     * 
     * @param  string  $name The name of the feature.
     * @return boolean       True if the CPT supports the given feature, false otherwise.
     */
    public function supports($name)
    {
        return post_type_supports($this->getSlug(), $name);
    }

    /**
     * Registers the CPT to WordPress.
     */
    public function register()
    {
        $args = array_merge($this->getProperties(), array('labels' => $this->getLabels()));
        register_post_type($this->getSlug(), $args);
    }

}
