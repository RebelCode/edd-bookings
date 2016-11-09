<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\Renderer\CartRenderer;

/**
 * Manages and controls the EDD cart, specifically items in the cart that are bookable.
 *
 * @since 2.1.3
 */
class CartController extends ControllerAbstract
{

    /**
     * Gets the service associated with a specific cart item.
     *
     * @param array $item The cart item.
     * @return \Aventura\Edd\Bookings\Model\Service The serviec instance, or null if no service is associated with the cart item.
     */
    public function getCartItemService(array $item)
    {
        return isset($item['id'])
            ? $this->getPlugin()->getServiceController()->get($item['id'])
            : null;
    }

    /**
     * Checks if the cart item is bookable.
     *
     * @param array $item The cart item.
     * @return boolean True if the cart item is bookable, false if not.
     */
    public function isCartItemBookable(array $item)
    {
        return !is_null($service = $this->getCartItemService($item)) && $service->getBookingsEnabled();
    }

    /**
     * Adds data to the cart items
     *
     * @param  array $item The original cart item.
     * @return array       The filtered item, with added EDD Booking data.
     */
    public function addItemData($item)
    {
        $service = eddBookings()->getServiceController()->get($item['id']);
        // Do not continue if bookings are not enabled
        if (!$service->getBookingsEnabled()) {
            return $item;
        }
        // Get post data string
        $postDataString = filter_input(INPUT_POST, 'post_data');
        // Parse the post data
        $parsedData = null;
        parse_str($postDataString, $parsedData);
        // Do not continue if there is no booking data in POST
        if (!isset($parsedData['edd_bk_start'])) {
            return $item;
        }
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
        // Return the item.
        return $item;
    }

    /**
     * Edits cart item data on AJAX request.
     *
     * @param array $response The AJAX response.
     * @param array $args The arguments.
     * @return array The modified AJAX response.
     */
    public function editItemData($response, $args)
    {
        $cart = edd_get_cart_contents();
        $index = intval($args['index']);
        if ($index < 0) {
            $response['success'] = false;
            $response['error'] = 'Invalid cart item index';
        } else {
            if (!isset($cart[$index]['options'])) {
                $cart[$index]['options'] = array();
            }
            $cart[$index]['options']['edd_bk'] = $args['session'];

            // Filter data
            $filterArgs = array(
                'start'    => FILTER_VALIDATE_INT,
                'duration' => FILTER_VALIDATE_INT,
                'timezone' => FILTER_VALIDATE_INT
            );
            $data = filter_var_array($args['session'], $filterArgs);
            // Add data to item
            $cart[$index]['options']['edd_bk'] = array(
                'start'    => $data['start'],
                'duration' => $data['duration'],
                'timezone' => $data['timezone'],
            );

            \EDD()->session->set('edd_cart', $cart);
            $response['success'] = true;
        }

        return $response;
    }

    /**
     * Modifies the cart item price.
     *
     * @param float $price The item price.
     * @param int $serviceId The ID of the download.
     * @param array $options The cart item options.
     * @return float The new filtered price.
     */
    public function getItemPrice($price, $serviceId, $options)
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
     * Outputs item actions.
     *
     * @param array $item The cart item data associative array.
     * @param int $index The index of the item in the cart.
     */
    public function renderItemActions($item, $index)
    {
        if ($this->isCartItemBookable($item)) {
            echo $this->getPlugin()->renderView('Frontend.Cart.Item.Actions', array(
                'item'  => $item,
                'index' => $index
            ));
        }
    }

    /**
     * Adds booking details to cart items that have bookings enabled.
     *
     * @param array $item The EDD cart item.
     * @param int $index The cart item index.
     */
    public function renderItem($item, $index)
    {
        $renderer = new CartRenderer($item);
        echo $renderer->render(array('index' => $index));
    }

    /**
     * Renders modals for items that do not have sessions in the cart.
     */
    public function renderModals()
    {
        $cart = edd_get_cart_contents();
        foreach ($cart as $index => $item) {
            $service = $this->getCartItemService($item);
            if ($this->isCartItemBookable($item)) {
                echo $this->getPlugin()->renderView('Frontend.Cart.Item.Modal',
                    array(
                    'index'   => $index,
                    'service' => $service->getId()
                ));
            }
        }
    }

    /**
     * Validates the cart items on checkout, to check if they can be booked.
     */
    public function validate()
    {
        $cartItems = edd_get_cart_contents();
        foreach ($cartItems as $key => $item) {
            $this->validateItem($key, $item);
        }
    }

    /**
     * Validates a cart item to check if it can be booked.
     *
     * @param string|integer $index The index of this item in the cart.
     * @param array $item The cart item.
     * @return boolean If the cart item can be booked or not. If the item is not a session, true is returned.
     */
    public function validateItem($index, $item)
    {
        // Get the service
        $id = $item['id'];
        $service = $this->getPlugin()->getServiceController()->get($id);
        if (is_null($service)) {
            return true;
        }
        $name = get_the_title($id);
        // Check if cart item has bookings enabled
        $bookingsEnabled = $service->getBookingsEnabled();
        // Check if item has booking options
        $bookingOptions = isset($item['options']['edd_bk'])
            ? $item['options']['edd_bk']
            : null;
        // Do not continue if bookings are disabled
        if (!$bookingsEnabled) {
            return true;
        }
        // If cart item has bookings enabled, but does not have a selected session
        if (is_null($bookingOptions)) {
            $message = sprintf('The item "%s" in your cart requires a booking session. Kindly choose one.', $name);
            edd_set_error('edd_bk_no_booking', $message);
            return false;
        }

        // Create booking period instance
        $start = filter_var($item['options']['edd_bk']['start'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $duration = filter_var($item['options']['edd_bk']['duration'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $booking = new Period(new DateTime($start), new Duration($duration));
        // If cannot book the chosen session
        if (!$service->canBook($booking)) {
            $dateStr = $booking->getStart()->format(get_option('date_format'));
            $timeStr = $booking->getStart()->format(get_option('time_format'));
            $dateTimeStr = $service->isSessionUnit('days', 'weeks')
                ? $dateStr
                : sprintf('%s %s', $dateStr, $timeStr);
            $message = sprintf(
                _x(
                    'Your chosen "%1$s" session on %2$s is no longer available. It may have been booked by someone else. If you believe this is a mistake, please contact the site administrator.',
                    '%1$s = name of download. %2$s = date and time of session. Example: Your chosen Bike Rental session on 29th June at 12:00 is no longer available ...',
                    'eddbk'
                ),
                get_the_title($item['id']),
                $dateTimeStr
            );
            edd_set_error('edd_bk_double_booking', $message);
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            // Cart changes
            ->addFilter('edd_add_to_cart_item', $this, 'addItemData')
            ->addFilter('edd_cart_item_price', $this, 'getItemPrice', 10, 3)
            // Rendering
            ->addAction('edd_cart_actions', $this, 'renderItemActions', 10, 2)
            ->addAction('edd_checkout_cart_item_title_after', $this, 'renderItem', 10, 2)
            ->addAction('edd_after_checkout_cart', $this, 'renderModals')
            // Validation
            ->addAction('edd_checkout_error_checks', $this, 'validate', 10, 0)
        ;
        // AJAX handlers
        $this->getPlugin()->getAjaxController()
            ->addHandler('edit_cart_item_session', $this, 'editItemData')
        ;
    }

}
