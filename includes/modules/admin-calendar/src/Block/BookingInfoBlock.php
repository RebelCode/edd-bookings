<?php

namespace RebelCode\EddBookings\Admin\Calendar\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\LinkTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\Utils\DateTimeFormatter;
use \RebelCode\EddBookings\Utils\DateTimeFormatterInterface;

/**
 * Description of BookingInfoBlock
 *
 * @since [*next-version*]
 */
class BookingInfoBlock extends AbstractBlock
{

    /**
     * The booking being rendered.
     *
     * @since [*next-version*]
     *
     * @var Booking
     */
    protected $booking;

    /**
     * The datetime formatter.
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
     * @param Booking $booking The booking to render.
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter to render dates.
     */
    public function __construct(
        Booking $booking,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        $this->setBooking($booking)
            ->setDateTimeFormatter($dateTimeFormatter);
    }

    /**
     * Gets the booking.
     *
     * @since [*next-version*]
     *
     * @return Booking The booking instance.
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Sets the booking.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The new booking instance.
     *
     * @return $this This instance.
     */
    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Gets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @return DateTimeFormatter The datetime formatter instance.
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
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter.
     *
     * @return $this This instance.
     */
    public function setDateTimeFormatter(DateTimeFormatterInterface $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return new CompositeTag('table',
            array('class' => 'widefat edd-bk-booking-details fixed'),
            array(
                new CompositeTag('tbody', array(), array(
                    $this->getIdRow(),
                    $this->getServiceRow(),
                    $this->getPaymentRow(),
                    $this->getStartRow(),
                    $this->getEndRow(),
                    $this->getDurationRow(),
                    $this->getCustomerRow(),
                    $this->getViewDetailsRow()
                ))
            )
        );
    }

    protected function _createRow($label, $content)
    {
        return new CompositeTag('tr', array(), array(
            new RegularTag('td', array(), $label),
            new RegularTag('td', array(), $content)
        ));
    }

    public function getIdRow()
    {
        return $this->_createRow(__('ID'), sprintf('#%d', $this->getBooking()->getId()));
    }

    public function getServiceRow()
    {
        $serviceId   = $this->getBooking()->getServiceId();
        $service     = \get_post($serviceId);

        if (!$service) {
            return '';
        }

        $url  = \admin_url(sprintf('post.php?action=edit&post=%d', $serviceId));
        $link = new LinkTag($service->post_title, $url);

        return $this->_createRow(__('Service', 'eddbk'), $link);
    }

    public function getPaymentRow()
    {
        $paymentId = $this->getBooking()->getPaymentId();

        if (!$paymentId) {
            return '';
        }

        $label = sprintf('#%d', $paymentId);
        $url   = \admin_url(
            sprintf(
                'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%d',
                $paymentId
            )
        );
        $link  = new LinkTag($label, $url);

        return $this->_createRow(__('Payment', 'eddbk'), $link);
    }

    public function getStartRow()
    {
        return $this->_createRow(
            __('Start', 'eddbk'),
            $this->getDateTimeFormatter()->formatDateTime($this->getBooking()->getStart())
        );
    }

    public function getEndRow()
    {
        return $this->_createRow(
            __('End', 'eddbk'),
            $this->getDateTimeFormatter()->formatDateTime($this->getBooking()->getStart())
        );
    }

    public function getDurationRow()
    {
        return $this->_createRow(
            __('Duration', 'eddbk'),
            $this->getDateTimeFormatter()->formatDuration($this->getBooking()->getDuration())
        );
    }

    public function getCustomerRow()
    {
        $customerId = $this->getBooking()->getCustomerId();

        if (!$customerId) {
            return '';
        }

        $customer = new \EDD_Customer($customerId);
        $url      = admin_url(
            sprintf(
                'edit.php?post_type=download&page=edd-customers&view=overview&id=%d',
                $customerId
            )
        );
        $link = new LinkTag($customer->name, $url);

        return $this->_createRow(__('Customer', 'eddbk'), $link);
    }

    public function getViewDetailsRow()
    {
        $url = sprintf('post.php?action=edit&post=%s', $this->getBooking()->getId());

        return new CompositeTag('tr', array(), array(
            new CompositeTag('td', array('colspan' => 2), array(
                new LinkTag(__('View/Edit Details', 'eddbk'), admin_url($url), false, array(
                    'class' => 'edd-bk-view-booking-details'
                ))
            ))
        ));
    }
}
