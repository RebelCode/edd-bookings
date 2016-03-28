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
            <div class="edd-bk-admin-calendar"></div>
            <script type="text/javascript">
                (function($) {
                    $(document).ready(function() {
                        $('div.edd-bk-admin-calendar').fullCalendar({
                            deafaultView: 'week',
                            header: {
                                left: 'today prev,next',
                                center: 'title',
                                right: 'agendaDay,agendaWeek,month'
                            },
                            views: {
                                basic: {},
                                agenda: {},
                                week: {},
                                day: {}
                            },
                            aspectRatio: 2.2,
                            eventSources: [
                                {
                                    url: window.ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'edd_bk_get_bookings_for_calendar'
                                    }
                                }
                            ]
                        });
                    });
                })(jQuery);
            </script>
        </div>
        <?php
        return ob_get_clean();
    }

}
