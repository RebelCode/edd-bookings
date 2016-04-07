<?php

namespace Aventura\Edd\Bookings\Integration;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;

/* * Description of FesIntegration
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */

class FesIntegration extends ControllerAbstract implements IntegrationInterface
{

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addFilter('fes_load_fields_array', $this, 'registerFields');
    }

    public function registerFields($fields)
    {
        if (class_exists('\EDD_Front_End_Submissions') && version_compare(fes_plugin_version, '2.3', '>=')) {
            Fes\BookingsField::$plugin = $this->getPlugin();
            $fields['edd-bk-bookings-enabled'] = 'Aventura\\Edd\\Bookings\\Integration\\Fes\\BookingsField';
        }
        return $fields;
    }
    
}
