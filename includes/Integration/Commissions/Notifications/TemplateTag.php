<?php

namespace Aventura\Edd\Bookings\Integration\Commissions\Notifications;

use \Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag\TemplateTagAbstract;

/**
 * Concrete implementation of a generic template tag, that utilizes an internally stored callback for processing the
 * template tag.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TemplateTag extends TemplateTagAbstract
{

    /**
     * The processing callback.
     * 
     * @var callable
     */
    protected $callback;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $desc, callable $callback)
    {
        parent::__construct($name, $desc);
        $this->setCallback($callback);
    }

    /**
     * Gets the callback.
     * 
     * @return callable The callback.
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the callback.
     * 
     * @param callable $callback The callback.
     * @return TemplateTag This instance.
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function process($downloadId, $commissionId)
    {
        return call_user_func($this->callback, $downloadId, $commissionId);
    }

}
