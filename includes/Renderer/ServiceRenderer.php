<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Schedule;
use \Aventura\Edd\Bookings\Model\Service;
use Aventura\Edd\Bookings\Utils\UnitUtils;

/**
 * Renders a service.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServiceRenderer extends RendererAbstract
{

    /**
     * Constructs a new instance.
     * 
     * @param Service $service The service to render.
     */
    public function __construct(Service $service)
    {
        parent::__construct($service);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Service
     */
    public function getObject()
    {
        return parent::getObject();
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        $service = $this->getObject();
        ob_start();
        // Use nonce for verification
        \wp_nonce_field('edd_bk_save_meta', 'edd_bk_service');
        ?>
        <input type="hidden" name="edd-bk-service-meta" value="1" />
        <div class="edd-bk-service-container edd-bk-service-enable-bookings-section">
            <div class="edd-bk-service-section">
                <label>
                    <?php $enabledChecked = checked($service->getBookingsEnabled(), true, false); ?>
                    <input type="hidden" name="edd-bk-bookings-enabled" value="0" />
                    <input type="checkbox" name="edd-bk-bookings-enabled" id="edd-bk-bookings-enabled"
                           value="1" <?php echo $enabledChecked; ?> />
                           <?php _e('Enable booking for this download', 'eddbk'); ?>
                </label>
            </div>
            <div class="edd-bk-collapse-container">
                <hr/>
                <h3><?php _e('Session Options', 'eddbk'); ?></h3>
                <div class="edd-bk-service-section">
                    <label class="fixed">
                        <span><?php _e('Single session length', 'eddbk'); ?></span>
                        <?php
                        // Session length is stored in seconds. So we divide by the number of a single session, depending
                        // on the stored unit.
                        $sessionUnit = $service->getSessionUnit();
                        // Fallback
                        if (!method_exists('Aventura\\Diary\\DateTime\\Duration', $sessionUnit)) {
                            $sessionUnit = UnitUtils::UNIT_HOURS;
                        }
                        $singleSessionLength = Duration::$sessionUnit(1, false);
                        $sessionLength = $service->getSessionLength() / $singleSessionLength;
                        ?>
                        <input type="number" min="1" step="1" id="edd-bk-session-length" name="edd-bk-session-length"
                               value="<?php echo esc_attr($sessionLength); ?>" />
                        <select name="edd-bk-session-unit" id="edd-bk-session-unit">
                            <?php
                            $sessionUnits = array(
                                    'seconds' => __('seconds', 'eddbk'),
                                    'minutes' => __('minutes', 'eddbk'),
                                    'hours'   => __('hours', 'eddbk'),
                                    'days'    => __('days', 'eddbk'),
                                    'weeks'   => __('weeks', 'eddbk'),
                            );
                            $filteredSessionUnits = \apply_filters('edd_bk_session_units', $sessionUnits);
                            foreach ($filteredSessionUnits as $_key => $_value) {
                                $_selected = \selected($_key, $sessionUnit, false);
                                printf('<option value="%2$s" %1$s>%3$s</option>', $_selected, $_key, $_value);
                            }
                            ?>
                        </select>
                    </label>
                    <?php
                    echo $this->helpTooltip(
                            __('Set how long a single session lasts. A "session" can either represent a single booking or a part of a booking, and can be anything from an hour, 15 minutes, to a whole day or even a number of weeks, depending on your use case.', 'eddbk')
                    );
                    ?>
                </div>
                <div class="edd-bk-service-section">
                    <label class="fixed">
                        <span><?php _e('Customer can book from', 'eddbk'); ?></span>
                        <input type="number" placeholder="Minimum" min="1" step="1" id="edd-bk-min-sessions"
                               name="edd-bk-min-sessions" value="<?php echo esc_attr($service->getMinSessions()); ?>" />
                        <?php echo _x('to', 'Customer can book from x to y sessions', 'eddbk'); ?>
                        <input type="number" placeholder="Maximum" min="1" step="1" id="edd-bk-max-sessions"
                               name="edd-bk-max-sessions" value="<?php echo esc_attr($service->getMaxSessions()); ?>" />
                        <?php echo _x('sessions', 'Customer can book from x to y sessions', 'eddbk'); ?>
                        <?php
                        echo $this->helpTooltip(
                                __('The range of number of sessions that a customer can book.', 'eddbk'));
                        ?>
                    </label>
                </div>
                <div class="edd-bk-service-section">
                    <label class="fixed">
                        <span>
                            <?php _e('Cost per session', 'eddbk'); ?>
                            <span class="edd-bk-price-currency"><?php echo \edd_currency_symbol(); ?></span>
                        </span>
                        <input type="number" min="0" step="0.01" id="edd-bk-session-cost" name="edd-bk-session-cost"
                               value="<?php echo esc_attr($service->getSessionCost()); ?>" />
                               <?php
                               echo $this->helpTooltip(
                                    __('The cost of each session. The total price will be this amount times each booked session', 'eddbk')
                               );
                               ?>
                    </label>
                </div>

                <h3><?php _e('Available Times', 'eddbk'); ?></h3>
                <div class="edd-bk-service-section">
                    <?php
                    $availability = $service->getAvailability()->getTimetable();
                    $renderer = new AvailabilityRenderer($availability);
                    echo $renderer->render();
                    ?>
                </div>
                <div class="edd-bk-inline-availability-preview-container">
                    <h3><?php _e('Availability Preview', 'eddbk'); ?></h3>
                    <a href="javascript:void(0)" class="edd-bk-preview-toggler">
                        <?php _e('Show/Hide', 'eddbk'); ?>
                    </a>
                    <div class="edd-bk-inline-availability-preview-session-picker"></div>
                </div>

                <h3><?php _e('Advanced Settings', 'eddbk'); ?></h3>
                <div class="edd-bk-service-section">
                    <label>
                        <?php $checked = \checked($service->getUseCustomerTimezone(), true, false); ?>
                        <input type="hidden" name="edd-bk-use-customer-tz" value="0" />
                        <input id="edd-bk-use-customer-tz" type="checkbox" name="edd-bk-use-customer-tz" value="1" <?php echo $checked; ?>/>
                        <span><?php _e("Show dates and times on the site using the customers' timezone", 'eddbk'); ?></span>
                    </label>
                    <?php
                    echo $this->helpTooltip(__('Enable this box to use the customer timezone when showing dates and times on the front-end calendar. This is useful for international services, as customers can make bookings using their local time. However, this is not recommended for local or location-based services.', 'eddbk'));
                    ?>
                </div>
                <div class="edd-bk-service-section">
                    <label>
                        <?php $checked = \checked($service->getMultiViewOutput(), false, false); ?>
                        <input type="hidden" name="edd-bk-single-page-output" value="0" />
                        <input type="checkbox" name="edd-bk-single-page-output" value="1" <?php echo $checked; ?>/>
                        <span><?php _e('Only show the calendar on single download pages', 'eddbk'); ?></span>
                    </label>
                    <?php
                    echo $this->helpTooltip(__("Enable this option to only show the calendar on a Download's individual page and not on pages with multiple downloads, such as on archive pages or pages that use the [downloads] shortcode", 'eddbk'));
                    ?>
                </div>
            </div>
        </div>
        <?php
        $output = ob_get_clean();
        $filteredOutput = \apply_filters('edd_bk_renderered_service', $output);
        return $filteredOutput;
    }

    /**
     * Renders a tooltip.
     * 
     * @param string $text The tooltip text
     * @return string The rendered tooltip HTML.
     */
    public function helpTooltip($text)
    {
        $tooltip = sprintf('<div class="edd-bk-help"><i class="fa fa-fw fa-question-circle"></i><div>%s</div></div>',
                $text);
        $filtered = \apply_filters('edd_bk_tooltip', $tooltip);
        return $filtered;
    }

}
