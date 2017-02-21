<?php

namespace RebelCode\EddBookings;

use \Dhii\App\AppInterface;
use \Dhii\WpEvents\Event;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * An AJAX management component.
 *
 * @since [*next-version*]
 */
class AjaxManager extends AbstractBaseComponent
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
     * The WordPress action for AJAX.
     *
     * WordPress AJAX requests are sent with an "action" index in their POST body.
     * This is used by WordPress to trigger an action with a handle in the form of:
     *
     * "wp_ajax_[action]" or "wp_nopriv_ajax_[action]"
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $action;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param EventManagerInterface $eventManager The event manager.
     * @param string $action The WordPress AJAX action name.
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager,
        /* string */ $action)
    {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setAction($action);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach(
            sprintf('wp_ajax_%s', $this->getAction()),
            $this->_callback('onRequest')
        );
        
        $this->getEventManager()->attach(
            sprintf('wp_ajax_nopriv_%s', $this->getAction()),
            $this->_callback('onPrivRequest')
        );
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
     * Gets the WordPress AJAX action.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets the WordPress AJAX action.
     *
     * @since [*next-version*]
     *
     * @param string $action The new action.
     *
     * @return $this This instance.
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Gets the hook handle for an ajax request.
     *
     * @since [*next-version*]
     *
     * @param string $request The request string.
     *
     * @return string The generated hook handle.
     */
    protected function requestHook($request)
    {
        return sprintf('%1$s_%2$s', $this->getAction(), $request);
    }

    /**
     * On AJAX request.
     *
     * @since [*next-version*]
     *
     * @param boolean $priv Whether or not the request was private.
     */
    public function onRequest($priv = false)
    {
        $args       = filter_input(INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $request    = filter_input(INPUT_POST, 'request', FILTER_SANITIZE_STRING);
        $inResponse = array(
            'success' => false,
            'error'   => null,
            'result'  => null
        );
        
        $event     = $this->requestHook($request);
        $eventArgs = array(
            'request'  => $request,
            'response' => &$inResponse,
            'args'     => $args,
            'priv'     => $priv
        );

        $this->getEventManager()->trigger($event, null, $eventArgs);

        echo json_encode($eventArgs['response']);

        exit;
    }

    /**
     * On private AJAX request.
     *
     * @since [*next-version*]
     */
    public function onPrivRequest()
    {
        $this->onRequest(true);
    }

    /**
     * Adds a handler - shortcut for adding the correct filter.
     *
     * @since [*next-version*]
     *
     * @param string $request The AJAX request string.
     * @param callable $callback The callback.
     *
     * @return $this This instance.
     */
    public function addHandler($request, $callback)
    {
        $this->getEventManager()->attach($this->requestHook($request), $callback);

        return $this;
    }
}
