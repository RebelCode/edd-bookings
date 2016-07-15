<?php

namespace Aventura\Edd\Bookings\Integration\Core;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Abstract implementation of an integration.
 * 
 * Uses the ControllerAbstract implementation to inherit the parent plugin property and its getter/setter methods,
 * along with a default contructor.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class IntegrationAbstract extends ControllerAbstract implements IntegrationInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = eddBookings();
        }
        parent::__construct($plugin);
    }
    
}
