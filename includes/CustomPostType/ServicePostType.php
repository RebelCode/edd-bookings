<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\Availability\Rule\Renderer\RuleRendererAbstract;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Model\Service;
use \Aventura\Edd\Bookings\Notices;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;
use \Aventura\Edd\Bookings\Renderer\CartRenderer;
use \Aventura\Edd\Bookings\Renderer\FrontendRenderer;
use \Aventura\Edd\Bookings\Renderer\ServiceRenderer;
use \Aventura\Edd\Bookings\Utils\UnitUtils;

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
     * The ID used to represent a fake service used soley for previewing purposes.
     */
    const PREVIEW_SERVICE_ID = -15;

    /**
     * Items in the cart that do not have sessions.
     *
     * @var array
     */
    protected $itemsNoSession = array();

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
        // Query fix
        global $post, $wp_query;
        $wp_query->post = $post;
        
        \add_meta_box('edd-bk-service', __('Booking Options', 'eddbk'),
                array($this, 'renderServiceMetabox'), static::SLUG, 'normal', 'high');

        \add_meta_box('edd-bk-availability-preview', __('Availability Preview', 'eddbk'),
            array($this, 'renderPreviewMetabox'), static::SLUG, 'side', 'core');
    }

    /**
     * Renders the service metabox.
     * 
     * @param WP_Post $post The post.
     */
    public function renderServiceMetabox($post)
    {
        $service = (!$post->ID)
                ? $this->getPlugin()->getServiceController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getServiceController()->get($post->ID);
        $renderer = new ServiceRenderer($service);
        echo $renderer->render();
    }

    /**
     * Renders the preview metabox.
     */
    public function renderPreviewMetabox()
    {
        echo $this->getPlugin()->renderView('Settings.Download.AvailabilityPreview', array());
    }

    /**
     * Renders a service on the frontend.
     * 
     * @param integer $id The ID of the service.
     * @param array $args Optional array of arguments. Default: array()
     */
    public function renderServiceFrontend($id = null, $args = array())
    {
        // If ID is null, get it from the loop
        if ($id === null) {
            $id = get_the_ID();
        }
        // Check if the render process is triggered by a shortcode
        $fromShortcode = isset($args['edd_bk_from_shortcode']);
        // Get booking options from args param
        $bookingOptions = isset($args['booking_options'])
                ? $args['booking_options']
                : true;
        // If bookings enabled, continue to render
        if ($bookingOptions === true) {
            // Get the service
            $service = $this->getPlugin()->getServiceController()->get($id);
            // If the service is not free
            if ($service->getSessionCost() > 0) {
                // Remove the Free Downloads filter (Free Downloads removes the calendar output)
                remove_filter( 'edd_purchase_download_form', 'edd_free_downloads_download_form', 200, 2 );
            }
            $renderer = new FrontendRenderer($service);
            echo $renderer->render(array(
                'override' => $fromShortcode
            ));
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
        if (!$this->_guardOnSave($postId, $post)) {
            return;
        }
        // Check if triggered through a POST request (the WP Admin new/edit page, FES submission, etc.)
        if (filter_input(INPUT_POST, 'edd-bk-bookings-enabled', FILTER_SANITIZE_STRING)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_service');
            // Get the meta from the POST data
            $meta = $this->extractMeta($postId);
            // Save its meta
            $this->getPlugin()->getServiceController()->saveMeta($postId, $meta);
            // Get the service and check if it has availability times
            $service = $this->getPlugin()->getServiceController()->get($postId);
            if (!is_null($service)) {
                // Set meta value and save
                $rules = $service->getAvailability()->getTimetable()->getRules();
                $noticeMeta = array(
                    'no_avail_times_notice' => intval(count($rules) === 0) && $service->getBookingsEnabled()
                );
                $this->getPlugin()->getServiceController()->saveMeta($postId, $noticeMeta);
            }
        }
    }
    
    /**
     * Checks the number of availability rules on the Download edit page and shows a notice if there are none.
     * 
     * @since 2.0.1
     */
    public function noAvailabilityRulesNotice()
    {
        $services = $this->getPlugin()->getServiceController()->query(array(
            array(
                'key'     => $this->getPlugin()->getServiceController()->metaPrefix('no_avail_times_notice'),
                'value'   => 1,
                'compare' => '='
            )
        ));

        foreach($services as $service) {
            $downloadName = get_the_title($service->getId());
            $downloadUrl = sprintf('post.php?post=%s&action=edit', $service->getId());
            $link = sprintf('href="%s"', admin_url($downloadUrl));
            $text = sprintf(
                _x(
                    'The %s download does not have any available times set. The calendar on your website will not work without at least one availability time.',
                    '%s = download name. Example: The Bike Rental download does not have any ...',
                    'eddbk'
                ),
                sprintf('<a href="%1$s">%2$s</a>', $link, $downloadName)
            );
            $id = sprintf('no-avail-times-%s', $service->getId());
            echo Notices::create($id, $text, 'error', true, 'edd_bk_no_avail_notice_dismiss');
        }

        return;
    }

    /**
     * Called when the "no availability rules" notice is dismissed.
     *
     * Clears the meta entry that signifies lack of available times.
     */
    public function onNoAvailabilityRulesNoticeDismiss()
    {
        // Get the notice index from POST
        $notice = filter_input(INPUT_POST, 'notice', FILTER_SANITIZE_STRING);
        if (!is_string($notice)) {
            die;
        }
        // Explode by dash and get last part
        $parts = explode('-', $notice);
        $id = array_pop($parts);
        // Use last part as service ID to update the meta
        if ($id) {
            $this->getPlugin()->getServiceController()->saveMeta($id, array(
                'no_avail_times_notice' => 0
            ));
        }
        die;
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
                'multi_view_output' => !filter_input(INPUT_POST, 'edd-bk-single-page-output', FILTER_VALIDATE_BOOLEAN),
                'use_customer_tz'   => filter_input(INPUT_POST, 'edd-bk-use-customer-tz', FILTER_VALIDATE_BOOLEAN),
                'availability'      => array(
                        'type'      => filter_input(INPUT_POST, 'edd-bk-rule-type', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
                        'start'     => filter_input(INPUT_POST, 'edd-bk-rule-start', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
                        'end'       => filter_input(INPUT_POST, 'edd-bk-rule-end', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
                        'available' => filter_input(INPUT_POST, 'edd-bk-rule-available', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)
                )
        );
        // Convert session length into seconds, based on the unit
        $sessionUnit = $meta['session_unit'];
        $meta['session_length'] = Duration::$sessionUnit(1, false) * ($meta['session_length']);
        // Compile availability rules
        $rules = array();
        for($i = 0; $i < count($meta['availability']['type']); $i++) {
            $rules[] = array(
                    'type' => str_replace('\\', '\\\\', $meta['availability']['type'][$i]),
                    'start' => $meta['availability']['start'][$i],
                    'end' => $meta['availability']['end'][$i],
                    'available' => $meta['availability']['available'][$i],
            );
        }
        $meta['availability'] = array(
                'rules' => $rules
        );
        // Filter and return
        $filtered = \apply_filters('edd_bk_service_submitted_meta', $meta);
        return $filtered;
    }

    /**
     * Sanitizes post meta imported via the WordPress Importer.
     *
     * @param array $meta An array of imported meta data.
     * @param int $postId The ID of the post for which this meta data is being imported.
     * @param array $post An array containing the post data, similar to a WP_Post object.
     * @return array The sanitized meta data.
     */
    public function sanitizeImportedPostMeta($meta, $postId, $post)
    {
        if (!$post['post_type'] === $this->getSlug()) {
            return $meta;
        }
        // Prepare to generate the sanitized meta array
        $sanitized = array();
        foreach ($meta as $i => $entry) {
            $key = $entry['key'];
            $value = $entry['value'];
            // Get the sanitization method
            $method = $this->getImportSanitizationMethodName($key);
            // Check for existence
            if (method_exists($this, $method)) {
                // Invoke sanitization and obtain sanitized key and value
                list($key, $value) = $this->$method($post, $key, $value);
            }
            // Add to sanitization result
            $sanitized[$i] = compact('key', 'value');
        }
        // Return sanitized meta
        return $sanitized;
    }

    /**
     * Gets the import sanitization method name for a meta key.
     *
     * Note: the method is not guaranteed to exist!
     *
     * @param string $key The meta key.
     * @return string The method name.
     */
    public function getImportSanitizationMethodName($key)
    {
        $parts = explode('_', $key);
        $partsUcFirst = array_map('ucfirst', $parts);
        $gluedParts = implode('', $partsUcFirst);
        return sprintf('sanitizeImported%sMeta', $gluedParts);
    }

    /**
     * Sanitizes the imported availability meta.
     *
     * @param array $post An array containing the post data, similar to a WP_Post object.
     * @param string $key The meta key.
     * @param string $value The meta value. A serialized string in this case.
     * @return string The sanitized availability meta value, as a serialized string.
     */
    public function sanitizeImportedEddBkAvailabilityMeta($post, $key, $value)
    {
        // We will need to perform string manipulation, so we'll convert into JSON to prevent from corrupting the
        // serialized string
        $json = json_encode(unserialize($value));
        /**
         * The serialized string should contain class names with double slash namespace separators.
         *
         * When converted into JSON, each slash will be escaped, therefore each set of slashes will
         * become four slashes. Hence, when we search for four slashes, we must escape the slashes in the search string.
         * That's why the search string consists of eight slashes.
         *
         * But if the sets of four slashes are not found, then the string must only have sets of two slashes.
         * This means that the serialized version only has 1 slash. This is problematic, so we must add more slashes.
         *
         * This is done by replacing every slash with two slashes - since we can't know for sure if each set of slashes
         * consists of one of two slashes or if the number of slashes in each set is consisten throughout the entire
         * JSON string.
         */
        if (strpos($json, '\\\\\\\\') === false) {
            $json = str_replace('\\', '\\\\', $json);
            $value = serialize(json_decode($json, true));
            printf('<pre>%s</pre>', print_r($value, true));
        }
        return array($key, $value);
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
        if (is_null($service) && $serviceId !== 0) {
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
     * Handles AJAX request for UI rows.
     */
    public function ajaxAvailabilityRowRequest($response, $serviceId, $args)
    {
        \check_admin_referer('edd_bk_availability_ajax', 'edd_bk_availability_ajax_nonce');
        $ruleType = $args['ruletype'];
        $rendered = null;
        if ($ruleType === false) {
            $response['error'] = __('No rule type specified.', 'eddbk');
        } elseif (!$ruleType) {
            $rendered = AvailabilityRenderer::renderRule(null);
        } else {
            $rendererClass = AvailabilityRenderer::getRuleRendererClassName($ruleType);
            /* @var $renderer RuleRendererAbstract */
            $renderer = $rendererClass::getDefault();
            // Generate rendered output
            $start = $renderer->renderRangeStart();
            $end = $renderer->renderRangeEnd();
            $rendered = compact('start', 'end');
        }
        if (!is_null($rendered)) {
            $response['rendered'] = $rendered;
        }
        return $response;
    }
    
    /**
     * AJAX handler for sessions request.
     * 
     * @param array $response The response to modify.
     * @param array $args Arguments passed along with the request.
     * @return array The modified response.
     */
    public function ajaxGetSessions($response, $args)
    {
        $args = wp_parse_args($args, array(
            'service_id'  => 0,
            'range_start' => null,
            'range_end'   => null,
        ));
        $serviceId = intval($args['service_id']);
        $service = ($serviceId === static::PREVIEW_SERVICE_ID)
            ? $this->ajaxCreatePreviewService($args)
            : $this->getPlugin()->getServiceController()->get($serviceId);
        if (is_null($service)) {
            $response['error'] = 'Invalid service ID';
            $response['success'] = false;
            return $response;
        }
        // Validate range values
        $rangeStart = filter_var($args['range_start'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $rangeEnd = filter_var($args['range_end'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        // Check if validation successful
        if (is_null($rangeStart) || is_null($rangeEnd)) {
            $response['error'] = 'Invalid range value(s)';
            $response['success'] = false;
            return $response;
        }
        // Clip to the present
        // Get the current datetime's date, and add the larger of the following:
        // * The ceiling of the the current time relative to the session length
        // * The session legnth
        $start = new DateTime($rangeStart);
        $now = DateTime::now();
        if ($start->isBefore($now, true)) {
            // For day units, use 1 day as session length
            $sessionLength = $service->isSessionUnit(UnitUtils::UNIT_DAYS, UnitUtils::UNIT_WEEKS)
                ? Duration::days(1)->getSeconds()
                : $service->getMinSessionLength();
            $roundedTime = (int) ceil($now->getTime()->getTimestamp() / $sessionLength) * $sessionLength;
            $max = (int) max($sessionLength, $roundedTime);
            $clippedStart = $now->copy()->getDate()->plus(new Duration($max));
            $start = $clippedStart;
        }
        // Fix the range end
        // This solves the end of month issue where the last session of the month is not available.
        // Consider: 2 day session length for a month with 31 days. If the given range is from the 1st till the 31st, the 31st day will not fit
        // the range and will be unavailable.
        $end = $rangeEnd + $service->getMinSessionLength();
        // Create Period range object
        $duration = new Duration(abs($end - $start->getTimestamp() + 1));
        $range = new Period($start, $duration);
        // Generate sessions and return
        $response['sessions'] = $service->generateSessionsForRange($range);
        $response['range'] = array(
                $range->getStart()->getTimestamp(),
                $range->getEnd()->getTimestamp()
        );
        return $response;
    }

    /**
     * Creates a dummy service instance for use when previewing availability.
     *
     * The service is created using the standard procedure: via the factory. The factory data
     * is received from the AJAX request. This allows the JS to control the service's creation.
     *
     * @param array $args The argument data received from the AJAX request.
     * @return Service The created instance.
     */
    public function ajaxCreatePreviewService($args)
    {
        $data = array(
            'id'                => static::PREVIEW_SERVICE_ID,
            'bookings_enabled'  => true,
            'session_length'    => intval($args['session_length']),
            'session_unit'      => $args['session_unit'],
            'session_cost'      => floatval($args['session_cost']),
            'min_sessions'      => intval($args['min_sessions']),
            'max_sessions'      => intval($args['max_sessions']),
            'availability'      => array(
                'rules' => $args['availability'],
            )
        );
        return $this->getPlugin()->getServiceController()->getFactory()->create($data);
    }

    /**
     * AJAX handler for service meta request.
     * 
     * @param array $response The response to modify.
     * @param Service $service The service instance.
     * @param array $args Arguments passed along with the request.
     * @return array The modified response.
     */
    public function ajaxGetMeta($response, $args)
    {
        $serviceId = intval($args['service_id']);
        $service = ($serviceId === static::PREVIEW_SERVICE_ID)
            ? new Service($serviceId)
            : $this->getPlugin()->getServiceController()->get($serviceId);

        $meta = $this->getPlugin()->getServiceController()->getMeta($service->getId());
        $sessionUnit = $service->getSessionUnit();
        $meta['session_length_n'] = $service->getSessionLength() / Duration::$sessionUnit(1, false);
        $meta['currency'] = \edd_currency_symbol();
        $meta['server_tz'] = $this->getPlugin()->getServerTimezoneOffsetSeconds();
        $response['meta'] = $meta;
        $response['success'] = true;

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
     * Filters a service's price.
     *
     * @param float $price The input price.
     * @param int $downloadId The download ID.
     * @return float The output price.
     */
    public function filterServicePrice($price, $downloadId)
    {
        $service = $this->getPlugin()->getServiceController()->get($downloadId);
        return (!is_null($service) && $service->getBookingsEnabled())
            ? floatval($service->getSessionCost())
            : $price;
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

        // Add an indication that we are rendering from a shortcode
        $out['edd_bk_from_shortcode'] = true;

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
            ->addAction('edd_purchase_link_top', $this, 'renderServiceFrontend', 10, 2)
            // Generic AJAX handler
            ->addAction('wp_ajax_nopriv_edd_bk_service_request', $this, 'handleAjaxRequest')
            ->addAction('wp_ajax_edd_bk_service_request', $this, 'handleAjaxRequest')
            // AJAX request for service meta
            ->addFilter('edd_bk_service_ajax_get_meta', $this, 'ajaxGetMeta', 10, 3)
            // AJAX request for validating a booking
            ->addFilter('edd_bk_service_ajax_validate_booking', $this, 'ajaxValidateBooking', 10, 3)
            // AJAX request for availability row
            ->addFilter('edd_bk_service_ajax_availability_row', $this, 'ajaxAvailabilityRowRequest', 10, 3)
            // Price filters
            ->addFilter('edd_get_download_price', $this, 'filterServicePrice', 10, 2)
            // Hook to modify shortcode attributes
            ->addAction('shortcode_atts_purchase_link', $this, 'purchaseLinkShortcode', 10, 3)
            // Admin notice for downloads without availability rules
            ->addAction('admin_notices', $this, 'noAvailabilityRulesNotice')
            ->addAction('wp_ajax_edd_bk_no_avail_notice_dismiss', $this, 'onNoAvailabilityRulesNoticeDismiss')
            // Filter to sanitizing post meta on import
            ->addFilter('wp_import_post_meta', $this, 'sanitizeImportedPostMeta', 10, 3)
        ;
        $this->getPlugin()->getAjaxController()
            ->addHandler('get_sessions', $this, 'ajaxGetSessions')
            ->addHandler('get_meta', $this, 'ajaxGetMeta')
        ;
    }

}
