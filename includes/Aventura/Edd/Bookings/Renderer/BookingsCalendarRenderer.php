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
                'wrap'     => true,
                'header'   => true,
                'infopane' => true,
                'data'     => array()
        );
        $args = wp_parse_args($pArgs, $defaultArgs);
        // Prepare data attr
        $dataAttrs = '';
        foreach ($args['data'] as $_key => $_value) {
            $dataAttrs .= sprintf(' data-%s="%s"', $_key, esc_attr($_value));
        }
        ob_start();
        // Show header if enabled in args
        if ($args['header']) {
            printf('<h1><i class="fa fa-calendar"></i> %s</h1>', __('Calendar', $textDomain));
        }
        // Show calendar 
        printf('<div class="edd-bk-bookings-calendar" %s></div>', $dataAttrs);
        // Show infopane if enabled in args
        if ($args['infopane']) {
            echo '<hr/>';
            echo static::renderInfoPane();
        }
        // Add the wrap if enabled in args
        $innerOutput = ob_get_clean();
        $outerOutput = $args['wrap']
                ? sprintf('<div class="wrap">%s</div>', $innerOutput)
                : $innerOutput;
        // Return
        return $outerOutput;
    }
    
    /**
     * Renders the info pane.
     * 
     * @param array $args Optional array of arguments. Accepted args are:<br/>
     *                    * header - Prints a "Booking Info" header<br/>
     * @return string The rendered HTML output.
     */
    public static function renderInfoPane($pArgs = array())
    {
        $textDomain = eddBookings()->getI18n()->getDomain();
        $defaultArgs = array(
                'header'   => true
        );
        $args = wp_parse_args($pArgs, $defaultArgs);
        ob_start();
        ?>
        <div class="edd-bk-bookings-calendar-info-pane">
            <?php if ($args['header']): ?>
                <h4><?php _e('Booking Info'); ?></h4>
            <?php endif; ?>
            <div><span><?php _e('Click on a booking to view its details', $textDomain); ?></span></div>
        </div>
        <?php
        return apply_filters('edd_bk_booking_info_pane_output', ob_get_clean());
    }

}
