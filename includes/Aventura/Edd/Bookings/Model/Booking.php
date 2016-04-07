<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;

/**
 * Represents a booked time slot.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Booking extends Period
{
    
    const CPT_SLUG = 'edd_booking';
    
    /**
     * The booking ID.
     * 
     * @var integer
     */
    protected $_id;
    
    /**
     * Service ID.
     * 
     * @var integer
     */
    protected $_serviceId;
    
    /**
     * Payment ID.
     * 
     * @var integer
     */
    protected $_paymentId;
    
    /**
     * Customer ID.
     * 
     * @var integer
     */
    protected $_customerId;
    
    /**
     * Client timezone.
     * 
     * @var integer
     */
    protected $_clientTimezone;
    
    /**
     * Contructs a new instance.
     * 
     * @param DateTime $start The start of the booking.
     * @param Duration $duration The duration of the booking.
     * @param integer $serviceId The service ID.
     */
    public function __construct($id, $start, $duration, $serviceId)
    {
        parent::__construct($start, $duration);
        $this->setId($id)
                ->setServiceId($serviceId)
                ->setPaymentId(null)
                ->setCustomerId(null)
                ->setClientTimezone(0);
    }

    /**
     * Gets the booking ID.
     * 
     * @return integer The booking ID.
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Sets the booking ID.
     * 
     * @param integer $id The booking ID
     * @return Booking This instance.
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    /**
     * Gets the service ID.
     * 
     * @return integer The service ID.
     */
    public function getServiceId()
    {
        return $this->_serviceId;
    }

    /**
     * Gets the payment ID.
     * 
     * @return integer The payment ID.
     */
    public function getPaymentId()
    {
        return $this->_paymentId;
    }

    /**
     * Gets the customer ID.
     * 
     * @return integer The customer ID.
     */
    public function getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Gets the client's timezone offset.
     * 
     * @return integer The client's timezone offset.
     */
    public function getClientTimezone()
    {
        return $this->_clientTimezone;
    }

    /**
     * Sets the service ID.
     * 
     * @param integer $serviceId The service ID.
     * @return Booking This instance.
     */
    public function setServiceId($serviceId)
    {
        $this->_serviceId = $serviceId;
        return $this;
    }

    /**
     * Sets the payment ID.
     * 
     * @param integer $paymentId The payment ID.
     * @return Booking This instance.
     */
    public function setPaymentId($paymentId)
    {
        $this->_paymentId = $paymentId;
        return $this;
    }

    /**
     * Sets the customer ID.
     * 
     * @param integer $customerId The customer ID.
     * @return Booking This instance.
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
        return $this;
    }

    /**
     * Sets the client timezone offset.
     * 
     * @param integer $clientTimezone The client timezone offset.
     * @return Booking This instance.
     */
    public function setClientTimezone($clientTimezone)
    {
        $this->_clientTimezone = $clientTimezone;
        return $this;
    }

    /**
     * Gets the booking's start as the cient's local time.
     * 
     * @return DateTime
     */
    public function getClientStart()
    {
        return $this->getStart()->copy()->plus(new Duration($this->getClientTimezone()));
    }
    
    /**
     * Gets the booking's end as the cient's local time.
     * 
     * @return DateTime
     */
    public function getClientEnd()
    {
        return $this->getEnd()->copy()->plus(new Duration($this->getClientTimezone()));
    }
    
}
