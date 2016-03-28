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
    public function render(array $pArgs = array())
    {
        $plugin = $this->getObject();
        $textDomain = $plugin->getI18n()->getDomain();
        // Parse args
        $defaultArgs = array(
                'wrap'   => true,
                'header' => true,
                'data'   => array()
        );
        $args = wp_parse_args($pArgs, $defaultArgs);
        // Prepare data attr
        $dataAttrs = '';
        foreach ($args['data'] as $_key => $_value) {
            $dataAttrs .= sprintf(' data-%s="%s"', $_key, esc_attr($_value));
        }
        ob_start();
        if ($args['wrap']) {
            echo '<div class="wrap">';
        }
        if ($args['header']) {
            printf('<h1><i class="fa fa-calendar"></i> %s</h1>', __('Calendar', $textDomain));
            echo '<hr/>';
        }
        printf('<div class="edd-bk-bookings-calendar" %s></div>', $dataAttrs);
        if ($args['wrap']) {
            echo '</div>';
        }
        return ob_get_clean();
    }

}
