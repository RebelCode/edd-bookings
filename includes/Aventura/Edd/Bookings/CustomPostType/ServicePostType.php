<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Model\Service;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\CartRenderer;
use \Aventura\Edd\Bookings\Renderer\FrontendRenderer;
use \Aventura\Edd\Bookings\Renderer\ServiceRenderer;

/**
 * Service Custom Post Type class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServicePostType extends CustomPostType
{

    /**
     * The CPT slug name.
     */
    const SLUG = 'download';

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
    }

    /**
     * Registers the WordPress metaboxes for this cpt.
     */
    public function addMetaBoxes()
    {
        \add_meta_box('edd-bk-service', __('Booking Options', $this->getPlugin()->getI18n()->getDomain()),
                array($this, 'renderServiceMetabox'), static::SLUG, 'normal', 'high');
    }

    /**
     * Renders the service metabox.
     * 
     * @param WP_Post $post The post.
     */
    public function renderServiceMetabox($post)
    {
        $service = (empty($post->ID))
                ? $this->getPlugin()->getServiceController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getServiceController()->get($post->ID);
        $renderer = new ServiceRenderer($service);
        echo $renderer->render();
    }

    /**
     * Renders a service on the frontend.
     * 
     * @param integer $id The ID of the service.
     * @param array $args Optional array of arguments. Default: array()
     */
    public function renderServiceFrontend($id = null, $args = array())
    {
        // If ID is not passed as parameter, get current loop post ID
        if ($id === null) {
            $id = get_the_ID();
        }
        // Get booking options from args param
        $bookingOptions = isset($args['booking_options'])
                ? $args['booking_options']
                : true;
        if ($bookingOptions === true) {
            $service = $this->getPlugin()->getServiceController()->get($id);
            $renderer = new FrontendRenderer($service);
            echo $renderer->render();
        }
    }
    
    /**
     * Called when a service is saved.
     * 
     * @param integer $postId The post ID
     * @param WP_Post $post The post object
     */
    public function onSave($postId, $post)
    {
        if ($this->_guardOnSave($postId, $post)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_service');
            // Get the meta from the POST data
            $meta = $this->extractMeta($postId);
            // Save its meta
            $this->getPlugin()->getServiceController()->saveMeta($postId, $meta);
        }
    }

    /**
     * Extracts the meta data from submitted POST data.
     * 
     * @param integer $postId The ID of the created/edited post.
     * @return array The extracted meta data
     */
    public function extractMeta($postId)
    {
        // Prepare meta array
        $meta = array(
                'id'                => $postId,
                'bookings_enabled'  => filter_input(INPUT_POST, 'edd-bk-bookings-enabled', FILTER_VALIDATE_BOOLEAN),
                'session_length'    => filter_input(INPUT_POST, 'edd-bk-session-length', FILTER_SANITIZE_NUMBER_INT),
                'session_unit'      => filter_input(INPUT_POST, 'edd-bk-session-unit', FILTER_SANITIZE_STRING),
                'session_cost'      => filter_input(INPUT_POST, 'edd-bk-session-cost', FILTER_VALIDATE_FLOAT),
                'min_sessions'      => filter_input(INPUT_POST, 'edd-bk-min-sessions', FILTER_SANITIZE_NUMBER_INT),
                'max_sessions'      => filter_input(INPUT_POST, 'edd-bk-max-sessions', FILTER_SANITIZE_NUMBER_INT),
                'multi_view_output' => filter_input(INPUT_POST, 'edd-bk-multiview-output', FILTER_VALIDATE_BOOLEAN),
                'availability_id'   => filter_input(INPUT_POST, 'edd-bk-service-availability', FILTER_SANITIZE_STRING),
        );
        // Convert session length into seconds, based on the unit
        $sessionUnit = $meta['session_unit'];
        $meta['session_length'] = Duration::$sessionUnit(1, false) * ($meta['session_length']);
        // Create an availability if necessary
        if ($meta['bookings_enabled'] && $meta['availability_id'] === 'new') {
            $textDomain = $this->getPlugin()->getI18n()->getDomain();
            $serviceName = \get_the_title($postId);
            // Generate names
            $availabilityName = sprintf(__('Schedule for %s', $textDomain), $serviceName);
            $timetableName = sprintf(__('Timetable for %s', $textDomain), $serviceName);
            // Insert timetable and availability
            $timetableId = $this->getPlugin()->getTimetableController()->insert(array(
                    'post_title'    =>  $timetableName
            ));
            $meta['availability_id'] = $this->getPlugin()->getAvailabilityController()->insert(array(
                    'post_title'    =>  $availabilityName
            ));
            // Set new availability to use new timetable
            $this->getPlugin()->getAvailabilityController()->saveMeta($meta['availability_id'], array(
                    'timetable_id'  =>  $timetableId
            ));
        }
        // Filter and return
        $filtered = \apply_filters('edd_bk_service_submitted_meta', $meta);
        return $filtered;
    }

    /**
     * Generic AJAX request handler.
     * 
     * Expects to recieve a POST request in the form:
     * {
     *     service_id: int,
     *     request: string,
     *     args: object/array
     * }
     * 
     * The request will be passed onto whatever is hooked into `edd_bk_service_ajax_{request}` with the params:
     *      (response, service, args)
     * 
     * The hooked in functions are to modify the response and return it. This method will then send it to the client.
     */
    public function handleAjaxRequest()
    {
        $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $request = filter_input(INPUT_POST, 'request', FILTER_SANITIZE_STRING);
        $args = filter_input(INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $response = array(
            'success' => true,
            'error' => ''
        );
        $service = $this->getPlugin()->getServiceController()->get($serviceId);
        if (is_null($service)) {
            $response['error'] = sprintf('Service ID (%s) is invalid or not specified', $serviceId);
        } else {
            $action = sprintf('edd_bk_service_ajax_%s', $request);
            $response['action_called'] = $action;
            $response['args_passed'] = $args;
            $response = \apply_filters($action, $response, $service, $args);
        }
        if ($response['error'] !== '') {
            $response['success'] = false;
        }
        echo json_encode($response);
        die;
    }
    
    /**
     * AJAX handler for sessions request.
     * 
     * @param array $response The response to modify.
     * @param Service $service The service instance.
     * @param array $args Arguments passed along with the request.
     * @return array The modified response.
     */
    public function ajaxGetSessions($response, $service, $args)
    {
        // Check for range values
        if (!isset($args['range_start'], $args['range_end'])) {
            $response['error'] = 'Missing range values';
            return $response;
        }
        // Validate range values
        $rangeStart = filter_var($args['range_start'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $rangeEnd = filter_var($args['range_end'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        // Check if validation successful
        if (is_null($rangeStart) || is_null($rangeEnd)) {
            $response['error'] = 'Invalid range value';
            return $response;
        }
        // Generate the range Period object
        $start = new DateTime($rangeStart);
        $duration = new Duration(abs($rangeEnd - $rangeStart));
        $range = new Period($start, $duration);
        // Clip to the present
        $now = DateTime::now();
        $range->setStart($range->getStart()->isBefore($now, true)? $now : $range->getStart());
        // Generate sessions and return
        $response['sessions'] = $service->generateSessionsForRange($range);
        return $response;
    }
    
    /**
     * AJAX handler for service meta request.
     * 
     * @param array $response The response to modify.
     * @param Service $service The service instance.
     * @param array $args Arguments passed along with the request.
     * @return array The modified response.
     */
    public function ajaxGetMeta($response, $service, $args)
    {
        $meta = $this->getPlugin()->getServiceController()->getMeta($service->getId());
        $sessionUnit = $service->getSessionUnit();
        $meta['session_length_n'] = $service->getSessionLength() / Duration::$sessionUnit(1, false);
        $meta['currency'] = \edd_currency_symbol();
        $response['meta'] = $meta;
        return $response;
    }
    
    /**
     * AJAX handler for booking validation request.
     * 
     * @param array $response The response to modify.
     * @param Service $service The service instance.
     * @param array $args Arguments passed along with the request.
     * @return array The modified response.
     */
    public function ajaxValidateBooking($response, Service $service, $args)
    {
        // Check for booking values
        if (!isset($args['start'], $args['duration'])) {
            $response['error'] = 'Missing booking info';
            return $response;
        }
        $start = filter_var($args['start'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $duration = filter_var($args['duration'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (is_null($start) || is_null($duration)) {
            $response['error'] = 'Booking start/duration is invalid.';
            return $response;
        }
        $booking = new Period(new DateTime($start), new Duration($duration));
        $response['available'] = $service->canBook($booking);
        return $response;
    }
    
    /**
     * Adds data to the cart items
     * 
     * @param  array $item The original cart item.
     * @return array       The filtered item, with added EDD Booking data.
     */
    public function addCartItemData($item)
    {
        $service = eddBookings()->getServiceController()->get($item['id']);
        if ($service->getBookingsEnabled()) {
            // Get post data string
            $postDataString = filter_input(INPUT_POST, 'post_data');
            // Parse the post data
            $parsedData = null;
            parse_str($postDataString, $parsedData);
            // Filter data
            $filterArgs = array(
                    'edd_bk_start'    => FILTER_VALIDATE_INT,
                    'edd_bk_duration' => FILTER_VALIDATE_INT,
                    'edd_bk_timezone' => FILTER_VALIDATE_INT
            );
            $data = filter_var_array($parsedData, $filterArgs);
            // Add data to item
            $item['options']['edd_bk'] = array(
                    'start'    => $data['edd_bk_start'],
                    'duration' => $data['edd_bk_duration'],
                    'timezone' => $data['edd_bk_timezone'],
            );
        }
        // Return the item.
        return $item;
    }

   /**
    * Adds booking details to cart items that have bookings enabled.
    * 
    * @param  array $item The EDD cart item.
    */
    public function renderCartItem($item)
    {
        $renderer = new CartRenderer($item);
        echo $renderer->render();
    }
    
    /**
     * Modifies the cart item price.
     * 
     * @param float $price The item price.
     * @param int $serviceId The ID of the download.
     * @param array $options The cart item options.
     * @return float The new filtered price.
     */
    public function cartItemPrice($price, $serviceId, $options)
    {
        // Check if the booking info is set
	if (isset($options['edd_bk'])) {
            // Get the duration
            $duration = intval($options['edd_bk']['duration']);
            // Get the cost per session
            $service = eddBookings()->getServiceController()->get($serviceId);
            // Calculate the new price
            $price = floatval($service->getSessionCost()) * ($duration / $service->getSessionLength());
        }
        return $price;
    }
    
    /**
     * Adds processing of our `booking_options` attribute for the `[purchase_link]` shortcode.
     * 
     * @param  array $out The output assoc. array of attributes and their values.
     * @param  array $pairs Hell if I know
     * @param  array $atts The input assoc array of attributes passed to the shortcode.
     * @return array The resulting assoc array of attributes and their values.
     */
    public function purchaseLinkShortcode($out, $pairs, $atts)
    {
        if (isset($atts['booking_options'])) {
            $bookingOptions = trim(strtolower($atts['booking_options']));
            $out['booking_options'] = !in_array($bookingOptions, array('no', 'off', 'false', '0'));
        }
        return $out;
    }
    
    /**
     * Regsiters the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('add_meta_boxes', $this, 'addMetaboxes', 5)
                ->addAction('save_post', $this, 'onSave', 10, 2)
                ->addAction('edd_purchase_link_top', $this, 'renderServiceFrontend', 10, 2 )
                // Generic AJAX handler
                ->addAction('wp_ajax_nopriv_edd_bk_service_request', $this, 'handleAjaxRequest')
                ->addAction('wp_ajax_edd_bk_service_request', $this, 'handleAjaxRequest')
                // AJAX request for service meta
                ->addFilter('edd_bk_service_ajax_get_meta', $this, 'ajaxGetMeta', 10, 3)
                // AJAX request for service sessions
                ->addFilter('edd_bk_service_ajax_get_sessions', $this, 'ajaxGetSessions', 10, 3)
                // AJAX request for validating a booking
                ->addFilter('edd_bk_service_ajax_validate_booking', $this, 'ajaxValidateBooking', 10, 3)
                // Cart hooks
                ->addFilter('edd_add_to_cart_item', $this, 'addCartItemData')
                ->addAction('edd_checkout_cart_item_title_after', $this, 'renderCartItem')
                ->addFilter('edd_cart_item_price', $this, 'cartItemPrice', 10, 3)
                // Hook to modify shortcode attributes
                ->addAction('shortcode_atts_purchase_link', $this, 'purchaseLinkShortcode', 10, 3);
    }

}
