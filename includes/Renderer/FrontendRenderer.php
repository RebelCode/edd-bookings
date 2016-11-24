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
        $fromShortcode = false;
        // Guard output
        if (!$service->getBookingsEnabled() || (!is_single() && !$service->getMultiViewOutput() && !$fromShortcode)) {
            return '';
        }
        return eddBookings()->renderView('Frontend.Download.SessionPicker', array(
            'id' => $service->getId()
        ));
    }

}