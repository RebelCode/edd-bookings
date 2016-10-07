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
        // Parse args
        $defaultArgs = array(
                'wrap'      => true,
                'header'    => true,
                'infomodal' => true,
                'data'      => array()
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
            printf('<h1><i class="fa fa-calendar"></i> %s</h1>', __('Calendar', 'eddbk'));
        }
        // Print a nonce
        \wp_nonce_field('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce');
        // Show calendar
        printf('<div class="edd-bk-bookings-calendar edd-bk-fc" %s></div>', $dataAttrs);
        // Show infomodal if enabled in args
        if ($args['infomodal']) {
            echo static::renderInfoModal();
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
     * @param array $pArgs Optional array of arguments. Accepted args are:<br/>
     *                     * header - Prints a "Booking Info" header<br/>
     * @return string The rendered HTML output.
     */
    public static function renderInfoModal(array $pArgs = array())
    {
        $defaultArgs = array(
                'header'   => true
        );
        $args = wp_parse_args($pArgs, $defaultArgs);
        ob_start();
        ?>
        <div class="edd-bk-modal edd-bk-bookings-calendar-info">
            <?php if ($args['header']): ?>
                <h4><?php _e('Booking Info'); ?></h4>
            <?php endif; ?>
            <div></div>
        </div>
        <?php
        return apply_filters('edd_bk_booking_info_modal_output', ob_get_clean());   
    }

}
