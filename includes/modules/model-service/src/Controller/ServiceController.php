<?php

namespace RebelCode\EddBookings\Controller;

use \RebelCode\Storage\WordPress\AbstractCptController;

/**
 * Description of ServiceController
 *
 * @since [*next-version*]
 */
class ServiceController extends AbstractCptController
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createModel($id)
    {
        $service = $this->getFactory()->make('service', array(
            'id' => $id
        ));

        return $service;
    }
}
