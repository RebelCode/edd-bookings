<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\AvailabilityBuilderBlock;
use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\Block\Html\SelfClosingTag;
use \RebelCode\EddBookings\Model\Service;
use \RebelCode\EddBookings\Utils\DateTimeFormatterInterface;
use \RebelCode\WordPress\Admin\Tooltip;

/**
 * Description of BookingOptionsBlock
 *
 * @since [*next-version*]
 */
class BookingOptionsBlock extends AbstractBlock
{
    /**
     * The service instance.
     *
     * @since [*next-version*]
     *
     * @var Service
     */
    protected $service;

    /**
     * The date time formatter.
     *
     * @since [*next-version*]
     *
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * The availability builder block.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityBuilderBlock
     */
    protected $availabilityBuilder;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param Service $service The service instance.
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter.
     * @param AvailabilityBuilderBlock $availabilityBuilder The availability builder block.
     */
    public function __construct(
        Service $service,
        DateTimeFormatterInterface $dateTimeFormatter,
        AvailabilityBuilderBlock $availabilityBuilder
    ) {
        $this->setService($service)
            ->setDateTimeFormatter($dateTimeFormatter)
            ->setAvailabilityBuilder($availabilityBuilder);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $service   = $this->getService();
        $hidden    = new InputTag('hidden', '', 'edd-bk-service-meta', '1');
        $container = new CompositeTag('div',
            array(
                'class' => 'edd-bk-service-container edd-bk-service-enable-bookings-section'
            ),
            array(
                $this->createBookingsEnabledSection($service),

                $this->_createCollapseContainer(array(
                    new SelfClosingTag('hr'),

                    new RegularTag('h3', array(), __('Service Options', 'eddbk')),
                    $this->createSessionLengthSection($service),
                    $this->createNumSessionsSection($service),
                    $this->createSessionPriceSection($service),

                    new RegularTag('h3', array(), __('Available Times', 'eddbk')),
                    $this->getAvailabilityBuilder(),

                    new RegularTag('h3', array(), __('Advanced Settings', 'eddbk')),
                    $this->createCustomerTimezoneSection($service),
                    $this->createPageOutputSection($service)
                ))
            )
        );

        return $hidden . $container;
    }

    public function createBookingsEnabledSection(Service $service)
    {
        return $this->_createLabelledSection(array(
            new EnableBookingsBlock($service),
            new RegularTag('span', array(), __('Enable booking for this download', 'eddbk'))
        ));
    }

    public function createSessionLengthSection(Service $service)
    {
        return $this->_createLabelledSection(array(
            new RegularTag('span', array(), __('Single session length', 'eddbk')),
            new SessionLengthOptionBlock($service),
            new Tooltip(__('Set how long a single session lasts. A "session" can either represent a single booking or a part of a booking, and can be anything from an hour, 15 minutes, to a whole day or even a number of weeks, depending on your use case.', 'eddbk'))
        ), true);
    }

    public function createNumSessionsSection(Service $service)
    {
        return $this->_createLabelledSection(array(
            new RegularTag('span', array(), __('Customers can book from', 'eddbk')),
            new NumSessionsOptionBlock($service),
            new Tooltip(__('The range of number of sessions that a customer can book.', 'eddbk'))
        ), true);
    }

    public function createSessionPriceSection(Service $service)
    {
        return $this->_createLabelledSection(array(
            new CompositeTag('span', array(), array(
                __('Cost per session', 'eddbk'),
                new RegularTag('span', array('class' => 'edd-bk-price-currency'), \edd_currency_symbol())
            )),
            new SessionPriceOptionBlock($service),
            new Tooltip(__('The cost of each session. The total price will be this amount times each booked session', 'eddbk'))
        ), true);
    }

    public function createAvailabilitySection(Service $service)
    {
        return $this->_createSection(array(
            new AvailabilityBuilderBlock($this->getRuleSet(), $service)
        ));
    }

    public function createCustomerTimezoneSection(Service $service)
    {
        $service->isInternational();
        return $this->_createLabelledSection(array(
            new InternationalOptionBlock($service),
            new RegularTag('span', array(), __("Show dates and times on the site using the customers' timezone", 'eddbk')),
            new Tooltip(__('Enable this box to use the customer timezone when showing dates and times on the front-end calendar. This is useful for international services, as customers can make bookings using their local time. However, this is not recommended for local or location-based services.', 'eddbk'))
        ));
    }

    public function createPageOutputSection(Service $service)
    {
        
    }

    protected function _createSection(array $content)
    {
        return new CompositeTag('div', array('class' => 'edd-bk-service-section'), $content);
    }

    protected function _createLabelledSection(array $content, $labelFixed = false)
    {
        $labelClass = $labelFixed
            ? 'fixed'
            : '';

        return $this->_createSection(array(
            new CompositeTag('label', array('class' => $labelClass), $content)
        ));
    }

    protected function _createCollapseContainer(array $children)
    {
        return new CompositeTag('div',
            array('class' => 'edd-bk-collapse-container'),
            $children
        );
    }

    /**
     * Gets the service.
     *
     * @since [*next-version*]
     *
     * @return Service The service instance.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets the service.
     *
     * @since [*next-version*]
     *
     * @param Service $service The new service instance.
     *
     * @return $this This instance.
     */
    public function setService(Service $service)
    {
        $this->service = $service;

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

    public function getAvailabilityBuilder()
    {
        return $this->availabilityBuilder;
    }

    public function setAvailabilityBuilder(AvailabilityBuilderBlock $availabilityBuilder)
    {
        $this->availabilityBuilder = $availabilityBuilder;
        return $this;
    }
}
