<?php

namespace Aventura\Edd\Bookings\Controller;

/**
 * Main AJAX Controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AjaxController extends ControllerAbstract
{

    const ACTION = 'eddbk_ajax';

    /**
     * Gets the hook handle for an ajax request.
     *
     * @param string $request The request string.
     * @return string The generated hook handle.
     */
    protected function requestHook($request)
    {
        return sprintf('%s_%s', static::ACTION, $request);
    }

    /**
     * On AJAX request.
     *
     * @param boolean $priv Whether or not the request was private.
     */
    public function onRequest($priv = false)
    {
        $request = filter_input(INPUT_POST, 'request', FILTER_SANITIZE_STRING);
        $args = filter_input(INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $inResponse = array(
            'success' => false,
            'error'   => null,
            'result'  => null
        );
        $outResponse = \apply_filters($this->requestHook($request), $inResponse, $args, $priv);
        echo json_encode($outResponse);
        die;
    }

    /**
     * On private AJAX request.
     */
    public function onPrivRequest()
    {
        $this->onRequest(true);
    }

    /**
     * Generic ajax handler for loading views.
     *
     * @param array $response The AJAX response.
     * @param array $args An array of arguments from the AJAX request.
     * @return array The response.
     */
    public function getView($response, $args)
    {
        $parsedArgs = wp_parse_args($args, array(
            'view' => 'Widget.Generic',
            'data' => array()
        ));
        try {
            $html = $this->getPlugin()->renderView($parsedArgs['view'], $parsedArgs['data']);
            $response['result'] = $html;
            $response['success'] = true;
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['error'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Adds a handler - shortcut for adding the correct filter.
     *
     * @param string $request The AJAX request string.
     * @param mixed $component The component that implements $method.
     * @param string $method The method callback.
     * @return \Aventura\Edd\Bookings\Controller\AjaxController This instance.
     */
    public function addHandler($request, $component, $method)
    {
        $this->getPlugin()->getHookManager()->addFilter($this->requestHook($request), $component, $method, 10, 3);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            // Generic AJAX handler
            ->addAction(sprintf('wp_ajax_%s', static::ACTION), $this, 'onRequest')
            ->addAction(sprintf('wp_ajax_nopriv_%s', static::ACTION), $this, 'onPrivRequest')
        ;
        // Common generic handlers
        $this->addHandler('get_view', $this, 'getView');
    }

}
