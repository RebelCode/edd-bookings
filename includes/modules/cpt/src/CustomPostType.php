<?php

namespace RebelCode\EddBookings;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * Description of AbstractCpt
 *
 * @since [*next-version*]
 */
class CustomPostType extends AbstractBaseComponent
{
    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * The CPT slug name.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $slug;

    /**
     * The CPT labels
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $labels;

    /**
     * The CPT properties
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $properties;

    /**
     * The CPT messages for when a post has been updated.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $updateMsgs;

    /**
     * Constructs a new instance.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param EventManagerInterface $eventManager The event manager instance.
     * @param string $slug The CPT slug name. Default: ''
     * @param array $labels The CPT labels. Default: array()
     * @param array $properties The CPT properties. Default: array()
     * @param array $updateMsgs The CPT update messages. Default: array()
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager,
        $slug = '',
        $labels = array(),
        $properties = array(),
        $updateMsgs = array()
    ) {
        parent::__construct($app);
        $this->setEventManager($eventManager)
            ->setSlug($slug)
            ->setLabels($labels)
            ->setProperties($properties)
            ->setUpdateMessages($updateMsgs);
    }

    /**
     * Gets the event manager.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface The event manager instance.
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface $eventManager The event manager instance.
     *
     * @return $this This instance.
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
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
     * @since [*next-version*]
     *
     * @param string $slug The slug
     *
     * @return $this This instance
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the CPT labels.
     *
     * @since [*next-version*]
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
     * @since [*next-version*]
     *
     * @param string $labels The labels
     *
     * @return $this This instance.
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Sets a single CPT label.
     *
     * @since [*next-version*]
     *
     * @param string $label The name of the label to set.
     * @param string $value the value of the label.
     *
     * @return $this This instance.
     */
    public function setLabel($label, $value)
    {
        $this->labels[$label] = $value;

        return $this;
    }

    /**
     * Generates the labels for this CPT using the singular and plural names.
     *
     * @since [*next-version*]
     *
     * @param string $singularName The CPT singular name.
     * @param string $pluralName The CPT plural name.
     *
     * @return $this This instance.
     */
    public function generateLabels($singularName, $pluralName)
    {
        $lowerPluralName = strtolower($pluralName);
        $this->labels    = array(
            'name'               => $pluralName,
            'singular_name'      => $singularName,
            'add_new'            => _x('Add New', 'eddbk', 'eddbk'),
            'add_new_item'       => sprintf(_x('Add New %s', '%s = name of custom post type. Example: Add New Post', 'eddbk'), $singularName),
            'edit_item'          => sprintf(_x('Edit %s', '%s = name of custom post type. Example: Edit Post', 'eddbk'), $singularName),
            'new_item'           => sprintf(_x('New %s', '%s = name of custom post type. Example: New Post', 'eddbk'), $singularName),
            'view_item'          => sprintf(_x('View %s', '%s = name of custom post type. Example: View Post', 'eddbk'), $singularName),
            'all_items'          => sprintf(_x('All %s', '%s = name of custom post type. Example: All Posts', 'eddbk'), $pluralName),
            'search_items'       => sprintf(_x('Search %s', '%s = name of custom post type. Example: Search Posts', 'eddbk'), $pluralName),
            'not_found'          => sprintf(_x('No %s found', '%s = name of custom post type. Example: No Posts Found', 'eddbk'), $lowerPluralName),
            'not_found_in_trash' => sprintf(_x('No %s found in trash', '%s = name of custom post type. Example: No Posts found in trash', 'eddbk'), $lowerPluralName)
        );
        return $this;
    }

    /**
     * Gets the CPT properties.
     *
     * @since [*next-version*]
     *
     * @return array The properties.
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Sets the CPT properties.
     *
     * @since [*next-version*]
     *
     * @param array $properties The properties
     *
     * @return $this This instance.
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        
        return $this;
    }

    /**
     * Sets a single CPT property.
     *
     * @since [*next-version*]
     *
     * @param string $name  The name of the property to set.
     * @param mixed  $value The value of the property.
     *
     * @return $this This instance.
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Gets the CPT update messages.
     *
     * @since [*next-version*]
     *
     * @return array The update messages.
     */
    public function getUpdateMsgs()
    {
        return $this->updateMsgs;
    }

    /**
     * Sets the CPT update messages.
     *
     * @since [*next-version*]
     *
     * @param array $updateMsgs The new CPT update messages.
     *
     * @return $this This instance.
     */
    public function setUpdateMessages(array $updateMsgs)
    {
        $this->updateMsgs = $updateMsgs;

        return $this;
    }

    /**
     * Adds support for a given feature to the CPT.
     *
     * @since [*next-version*]
     *
     * @param string|array $arg The name of the feature, or an array of feature names.
     *
     * @return $this This instance.
     */
    public function addSupport($arg)
    {
        add_post_type_support($this->getSlug(), $arg);

        return $this;
    }

    /**
     * Removes support of a given feature from the CPT.
     *
     * @since [*next-version*]
     *
     * @param string|array $arg The name of the feature, or an array of feature names.
     *
     * @return $this This instance.
     */
    public function removeSupport($arg)
    {
        $argArray = is_array($arg)
                ? $arg
                : array($arg);
        foreach ($argArray as $feature) {
            remove_post_type_support($this->getSlug(), $feature);
        }

        return $this;
    }

    /**
     * Checks if the CPT supports the given feature.
     *
     * @since [*next-version*]
     *
     * @param  string  $name The name of the feature.
     * 
     * @return boolean       True if the CPT supports the given feature, false otherwise.
     */
    public function supports($name)
    {
        return post_type_supports($this->getSlug(), $name);
    }

    /**
     * Filters the text for notices about updated posts.
     *
     * @since [*next-version*]
     *
     * @param array $messages An array of keys for post types and values as subarrays, containing the string messages.
     * 
     * @return array The filtered messages.
     */
    public function filterUpdateMessages($messages)
    {
        $messages[$this->getSlug()] = $this->getUpdateMsgs();

        return $messages;
    }

    /**
     * Registers the CPT to WordPress.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function register()
    {
        $args = $this->getProperties();
        $args['labels'] = $this->getLabels();

        register_post_type($this->getSlug(), $args);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach(
            'init',
            $this->_callback('register')
        );

        $this->getEventManager()->attach(
            'post_updated_messages',
            $this->_callback('filterUpdateMessages')
        );
    }
}
