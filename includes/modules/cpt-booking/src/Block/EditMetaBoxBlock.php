<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\BlockInterface;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\DumpBlock;
use \RebelCode\EddBookings\Block\Html\FaIcon;
use \RebelCode\EddBookings\Block\Html\FaSpinningIcon;
use \RebelCode\EddBookings\Block\Html\HeadingTag;
use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\LinkTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\Block\Html\SelfClosingTag;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\Utils\DateTimeFormatterInterface;
use \RebelCode\EddBookings\Utils\TimezoneOffsetSelectorBlock;
use \RebelCode\WordPress\Admin\Tooltip;

/**
 * Description of EditMetaBoxBlock
 *
 * @since [*next-version*]
 */
class EditMetaBoxBlock extends AbstractBlock
{
    /**
     * The booking controller instance.
     *
     * @since [*next-version*]
     *
     * @var BookingController
     */
    protected $bookingController;

    /**
     * The EDD HTML renderer.
     *
     * @since [*next-version*]
     *
     * @var \EDD_HTML_Elements
     */
    protected $eddHtml;

    /**
     * The date time formatter.
     *
     * @since [*next-version*]
     *
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param BookingController $bookingController The booking controller instance.
     * @param \EDD_HTML_Elements $eddHtml The EDD HTML renderer.
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter.
     */
    public function __construct(
        BookingController $bookingController,
        \EDD_HTML_Elements $eddHtml,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        $this->setBookingController($bookingController)
            ->setEddHtml($eddHtml)
            ->setDateTimeFormatter($dateTimeFormatter);
    }

    /**
     * Gets the booking controller.
     *
     * @since [*next-version*]
     *
     * @return BookingController The booking controller instance.
     */
    public function getBookingController()
    {
        return $this->bookingController;
    }

    /**
     * Sets the booking controller.
     *
     * @since [*next-version*]
     *
     * @param BookingController $bookingController The new booking controller instance.
     *
     * @return $this This instance.
     */
    public function setBookingController(BookingController $bookingController)
    {
        $this->bookingController = $bookingController;

        return $this;
    }

    /**
     * Gets the EDD HTML renderer.
     *
     * @since [*next-version*]
     *
     * @return \EDD_HTML_Elements
     */
    public function getEddHtml()
    {
        return $this->eddHtml;
    }

    /**
     * Sets the EDD HTML renderer.
     *
     * @since [*next-version*]
     *
     * @param \EDD_HTML_Elements $eddHtml The EDD HTML renderer.
     *
     * @return $this This instance.
     */
    public function setEddHtml(\EDD_HTML_Elements $eddHtml)
    {
        $this->eddHtml = $eddHtml;
        return $this;
    }

    /**
     * Gets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @return DateTimeFormatterInterface The datetime formatter instance.
     */
    public function getDateTimeFormatter()
    {
        return $this->dateTimeFormatter;
    }

    /**
     * Sets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter instance.
     *
     * @return $this This instance.
     */
    public function setDateTimeFormatter(DateTimeFormatterInterface $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;

        return $this;
    }

    /**
     * Gets the booking.
     *
     * @since [*next-version*]
     *
     * @return Booking The booking instance.
     */
    protected function _getBooking()
    {
        global $post;

        return $this->getBookingController()->get($post->ID);
    }

    protected function _getRequiredFieldsMessage()
    {
        return new RegularTag('p', array('class' => 'edd-bk-required-msg'),
            __('The settings marked with an asterisk (*) are required for a booking to be created. Other settings are optional.', 'eddbk')
        );
    }

    protected function _createSection($title)
    {
        $allArgs  = func_get_args();
        $rows     = array_slice($allArgs, 1);
        $heading  = new HeadingTag(4, $title);
        $children = array_merge($heading, $rows);

        return new CompositeTag('div', array(), $children);
    }

    protected function _createLabel($forField, $text, $tooltip)
    {
        return new CompositeTag('label', array('for' => $forField), array(
            new RegularTag('span', array(), $text),
            new Tooltip($tooltip)
        ));
    }

    protected function _getServiceInfoSingular()
    {
        $message = sprintf(
            _x(
                'The %1$s service uses a session length of %2$s %3$s and customers can book %4$s session(s).',
                'Example: The Bike Rental service uses a session length of 30 minutes and cusomters can book 3 session(s).',
                'eddbk'
            ),
            new RegularTag('span', array('class' => 'service-name')),
            new RegularTag('span', array('class' => 'session-length')),
            new RegularTag('span', array('class' => 'session-unit')),
            new RegularTag('span', array('class' => 'num-sessions'))
        );

        return new CompositeTag('p',
            array(
                'id'    => 'service-info-msg-singular',
                'class' => 'info-msg'
            ),
            array(
                new FaIcon('info-circle'), $message
            )
        );
    }

    protected function _getServiceInfoPlural()
    {
        $message = sprintf(
            _x(
                'The %1$s service uses a session length of %2$s %3$s and customers can book between %4$s and %5$s sessions.',
                'Example: The Bike Rental service uses a session length of 2 hours and cusomters can book between 1 and 2 sessions',
                'eddbk'
            ),
            new RegularTag('span', array('class' => 'service-name')),
            new RegularTag('span', array('class' => 'session-length')),
            new RegularTag('span', array('class' => 'session-unit')),
            new RegularTag('span', array('class' => 'min-sessions')),
            new RegularTag('span', array('class' => 'max-sessions'))
        );

        return new CompositeTag('p',
            array(
                'id'    => 'service-info-msg-plural',
                'class' => 'info-msg'
            ),
            array(
                new FaIcon('info-circle'), $message
            )
        );
    }

    protected function _getServiceInfoBookingsDisabled()
    {
        // Message for services with bookings disabled
        return new CompositeTag('p',
            array(
                'id'    => 'service-info-bookings-disabled',
                'class' => 'info-msg'),
            array(
                new FaIcon('warning'),
                __('This service does not have bookings enabled. However, you can still create bookings for it.', 'eddbk')
            )
        );
    }

    protected function _getCustomerHeaderToggle()
    {
        return new CompositeTag('small', array(), array(
            new LinkTag(
                new FaIcon('mouse-pointer') . __('Choose an existing customer', 'eddbk'),
                null,  // url
                false, // new tab
                array(
                    'id'    => 'choose-customer',
                    'class' => 'edd-bk-if-create-customer'
                )
            ),
            new LinkTag(
                new FaIcon('plus') . __('Create new customer', 'eddbk'),
                null,  // url
                false, // new tab
                array(
                    'id'    => 'create-customer',
                    'class' => 'edd-bk-if-choose-customer'
                )
            )
        ));
    }

    protected function _getPaymentSection(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            $this->_createLabel(
                'payment',
                __('Payment #', 'eddbk'),
                __('The ID of the EDD Payment associated with this booking.', 'eddbk')
            ),
            new InputTag('number', 'payment', 'booking[payment_id]', $booking->getPaymentId()),
        ));
    }

    protected function _getServiceSection(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            $this->_createLabel(
                'service',
                __('Service', 'eddbk'),
                __('The Download being provided as a service for this booking. ', 'eddbk')
            ),
            $this->getEddHtml()->product_dropdown(array(
                'id'       => 'service',
                'class'    => 'service-id',
                'name'     => 'booking[service_id]',
                'selected' => $booking->getServiceId(),
                'chosen'   => true
            ))
        ));
    }

    protected function _getServiceInfoSection(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            new CompositeTag('p', array('id' => 'service-info-loading'), array(
                new FaSpinningIcon('spinner'), __('Getting service information ...', 'eddbk')
            )),
            $this->_getServiceInfoSingular(),
            $this->_getServiceInfoPlural(),
            $this->_getServiceInfoBookingsDisabled()
        ));
    }

    protected function _getChooseCustomerSection(Booking $booking)
    {
        return new CompositeTag('div', array('class' => 'edd-bk-if-choose-customer'), array(
            $this->_createLabel(
                'customer',
                __('Existing Customer', 'eddbk'),
                __('Choose the customer associated with this booking or create a new one.', 'eddbk')
            ),
            $this->getEddHtml()->customer_dropdown(array(
                'id'       => 'customer',
                'class'    => 'customer-id',
                'name'     => 'booking[customer_id]',
                'selected' => $booking->getCustomerId(),
                'chosen'   => true
            ))
        ));
    }

    protected function _getCreateCustomerSection()
    {
        return new CompositeTag('div', array('class' => 'edd-bk-if-create-customer'), array(
            new CreateCustomerBlock()
        ));
    }

    protected function _getBookingStartDetails(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            $this->_createLabel(
                'start',
                __('Start', 'eddbk'),
                __('The date and time when this booking begins, relative to your server timezone.', 'eddbk')
            ),
            new InputTag(
                'text',
                'start',
                'booking[start]',
                $this->getDateTimeFormatter()->formatDatetime($booking->getStart()),
                array(
                    'class'    => 'edd-bk-datetime',
                    'required' => 'required'
                )
            )
        ));
    }

    protected function _getBookingEndDetails(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            $this->_createLabel(
                'end',
                __('End', 'eddbk'),
                __('The date and time when this booking ends, relative to your server timezone.', 'eddbk')
            ),
            new InputTag(
                'text',
                'end',
                'booking[end]',
                $this->getDateTimeFormatter()->formatDatetime($booking->getEnd()),
                array(
                    'class'    => 'edd-bk-datetime',
                    'required' => 'required'
                )
            )
        ));
    }

    protected function _getBookingAdvancedTimesSection($type)
    {
        return new CompositeTag('div', array('class' => 'advanced-times'), array(
            new RegularTag('label'),
            new CompositeTag('div', array(), array(
                new CompositeTag('p', array(
                        'id'    => sprintf('%s-utc', $type),
                        'class' => 'utc-time'
                    ),
                    array(
                        __('Universal Time:', 'eddbk') . ' ',
                        new RegularTag('code', array(), __('...', 'eddbk'))
                    )
                ),
                new CompositeTag('p', array(
                        'id'    => sprintf('%s-customer', $type),
                        'class' => 'customer-time'
                    ),
                    array(
                        __('Customer Time:', 'eddbk') . ' ',
                        new RegularTag('code', array(), __('...', 'eddbk'))
                    )
                )
            ))
        ));
    }

    protected function _getBookingDurationSection()
    {
        return new CompositeTag('div', array(), array(
            new RegularTag('label', array('for' => 'duration'), __('Duration', 'eddbk')),
            new RegularTag('code', array('id' => 'duration'), '')
        ));
    }

    protected function _getCustomerTimezoneSection(Booking $booking)
    {
        return new CompositeTag('div', array(), array(
            new CompositeTag('label', array('for' => 'customer_tz'), array(
                new RegularTag('span', array(), __('Customer Timezone', 'eddbk')),
                new Tooltip(__("The customer's timezone. This is optional and is only used if the service is configured to allow local times to be shown to customers.", 'eddbk'))
            )),
            new TimezoneOffsetSelectorBlock($booking->getCustomerTzOffset(), array(
                'id'       => 'customer_tz',
                'name'     => 'booking[client_tz]'
            ))
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $booking = $this->_getBooking();

        $container = new CompositeTag('div', array('class' => 'edd-bk-booking-details'), array(
            $this->_getRequiredFieldsMessage(),
            
            new SelfClosingTag('hr'),

            new RegularTag('h4', array(), __('Payment and Service', 'eddbk')),
            $this->_getPaymentSection($booking),
            $this->_getServiceSection($booking),
            $this->_getServiceInfoSection($booking),

            new SelfClosingTag('hr'),

            new CompositeTag('h4', array(), array(
                __('Customer', 'eddbk'), $this->_getCustomerHeaderToggle()
            )),
            $this->_getChooseCustomerSection($booking),
            $this->_getCreateCustomerSection(),

            new SelfClosingTag('hr'),

            new RegularTag('h4', array(), __('Booking Details', 'eddbk')),
            $this->_getBookingStartDetails($booking),
            $this->_getBookingAdvancedTimesSection('start'),
            $this->_getBookingEndDetails($booking),
            $this->_getBookingAdvancedTimesSection('end'),

            $this->_getBookingDurationSection(),

            $this->_getCustomerTimezoneSection($booking)
        ));

        $tzField = new InputTag('hidden', 'server-tz', '', 3600 * (int) \get_option('gmt_offset'));

        return $tzField . $container;
    }
}
