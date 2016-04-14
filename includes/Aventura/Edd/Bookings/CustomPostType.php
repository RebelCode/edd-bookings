<?php

namespace Aventura\Edd\Bookings;

/**
 * Generic Custom Post Type model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class CustomPostType
{

    protected $_plugin;


    /**
     * The CPT slug name.
     * 
     * @var string
     */
    protected $_slug;

    /**
     * The CPT labels
     * 
     * @var string
     */
    protected $_labels;

    /**
     * The CPT properties
     * 
     * @var string
     */
    protected $_properties;

    /**
     * Constructs the EDD_BK_Custom_Post_Type instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     * @param string $slug The CPT slug name.
     * @param array $labels The CPT labels.
     * @param array $properties The CPT properties.
     */
    public function __construct($plugin, $slug, $labels = array(), $properties = array())
    {
        $this->setPlugin($plugin)
                ->setSlug($slug)
                ->setLabels($labels)
                ->setProperties($properties);
    }

    /**
     * Gets the parent plugin instance.
     * 
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->_plugin;
    }

    /**
     * Sets the parent plugin instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     * @return CustomPostType This instance.
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }
    
    /**
     * Gets the CPT slug name.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->_slug;
    }

    /**
     * Sets the CPT slug name.
     *
     * @param string $slug The slug
     * @return self
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
        return $this;
    }

    /**
     * Gets the CPT labels.
     *
     * @return string
     */
    public function getLabels()
    {
        return $this->_labels;
    }

    /**
     * Sets the CPT labels.
     *
     * @param string $labels The labels
     * @return self
     */
    public function setLabels($labels)
    {
        $this->_labels = $labels;
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
        $this->_labels[$label] = $value;
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
        $this->_labels = array(
                'name'               => _x($pluralName, 'post type name', 'edd_bk'),
                'singular_name'      => _x($singularName, 'singular post type name', 'edd_bk'),
                'add_new'            => _x('Add New', 'edd_bk', 'edd_bk'),
                'add_new_item'       => sprintf(_x('Add New %s', 'add new post', 'edd_bk'), $singularName),
                'edit_item'          => sprintf(_x('Edit %s', 'edit post', 'edd_bk'), $singularName),
                'new_item'           => sprintf(_x('New %s', 'new post', 'edd_bk'), $singularName),
                'view_item'          => sprintf(_x('View %s', 'view post', 'edd_bk'), $singularName),
                'all_items'          => sprintf(_x('All %s', 'all posts', 'edd_bk'), $pluralName),
                'search_items'       => sprintf(_x('Search %s', 'post', 'edd_bk'), $pluralName),
                'not_found'          => sprintf(_x('No %s found', 'posts', 'edd_bk'), $lowerPluralName),
                'not_found_in_trash' => sprintf(_x('No %s found in trash', 'posts', 'edd_bk'), $lowerPluralName)
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
        return $this->_properties;
    }

    /**
     * Sets the CPT properties.
     *
     * @param string $properties The properties
     * @return self
     */
    public function setProperties($properties)
    {
        $this->_properties = $properties;
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
        $this->_properties[$name] = $value;
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
        $args = $this->getProperties();
        $args['labels'] = $this->getLabels();
        register_post_type($this->getSlug(), $args);
    }
    
    /**
     * Filters the text for notices about updated posts.
     * 
     * @param array $messages An array of keys for post types and values as subarrays, containing the string messages.
     * @return array The filtered messages.
     */
    public function filterUpdatedMessages($messages)
    {
        $labels = $this->getLabels();
        $messages[$this->getSlug()] = array(
                1  => sprintf(__('%s updated.', 'edd_bk'), $labels['singular_name']),
                4  => sprintf(__('%s updated.', 'edd_bk'), $labels['singular_name']),
                6  => sprintf(__('%s published.', 'edd_bk'), $labels['singular_name']),
                7  => sprintf(__('%s saved.', 'edd_bk'), $labels['singular_name']),
                8  => sprintf(__('%s submitted.', 'edd_bk'), $labels['singular_name']),
                10 => sprintf(__('%s draft updated.', 'edd_bk'), $labels['singular_name']),
        );
        return $messages;
    }
    
    /**
     * Used internally to guard the `save_post` hook.
     */
    protected function _guardOnSave($postId, $post) {
        if (empty($_POST) || !get_post($postId) || !isset($post->post_type) || $post->post_type !== $this->getSlug()) {
            return false;
        }
        // Check for auto save / bulk edit
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
                (defined('DOING_AJAX') && DOING_AJAX) ||
                isset($_REQUEST['bulk_edit'])) {
            return false;
        }
        // Check user permissions
        if (!current_user_can('edit_post', $postId)) {
            return false;
        }
        return true;
    }
    
    /**
     * Registers the WordPress hooks.
     */
    abstract public function hook();

}
