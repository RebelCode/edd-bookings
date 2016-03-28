<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * Description of CalendarPageRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsCalendarRenderer extends RendererAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        $plugin = $this->getObject();
        $textDomain = $plugin->getI18n()->getDomain();
        ob_start();
        ?>
        <div class="wrap">
            <h1><i class="fa fa-calendar"></i> <?php _e('Calendar', $textDomain); ?></h1>
            <hr/>
            <div class="edd-bk-bookings-calendar"></div>
        </div>
        <?php
        return ob_get_clean();
    }

}
