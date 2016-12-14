<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Service;

/**
 * Description of FrontendRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FrontendRenderer extends RendererAbstract
{

    public function render(array $data = array())
    {
        /* @var $service Service */
        $service = $this->getObject();
        $override = isset($data['override'])
            ? !!$data['override']
            : false;
        // Check if bookings enabled and can output on this page
        if ($service->getBookingsEnabled() && (is_single() || $service->getMultiViewOutput() || $override)) {
            return eddBookings()->renderView('Frontend.Download.SessionPicker', array(
                'id' => $service->getId()
            ));
        }

        return '';
    }

}
