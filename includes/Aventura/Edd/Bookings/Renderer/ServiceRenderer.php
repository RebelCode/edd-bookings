<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Schedule;
use \Aventura\Edd\Bookings\Model\Service;

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
        $textDomain = eddBookings()->getI18n()->getDomain();
        $service = $this->getObject();
        ob_start();
        // Use nonce for verification
        \wp_nonce_field('edd_bk_save_meta', 'edd_bk_service');
        ?>
        <div class="edd-bk-service-container edd-bk-service-enable-bookings-section">
            <div class="edd-bk-service-section">
                <label>
                    <?php $enabledChecked = checked($service->getBookingsEnabled(), true, false); ?>
                    <input type="hidden" name="edd-bk-bookings-enabled" value="0" />
                    <input type="checkbox" name="edd-bk-bookings-enabled" id="edd-bk-bookings-enabled"
                           value="1" <?php echo $enabledChecked; ?> />
                           <?php _e('Enable booking for this download', $textDomain); ?>
                </label>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Session length', $textDomain); ?></span>
                    <?php
                    // Session length is stored in seconds. So we divide by the number of a single session, depending
                    // on the stored unit.
                    $sessionUnit = $service->getSessionUnit();
                    $singleSessionLength = Duration::$sessionUnit(1, false);
                    $sessionLength = $service->getSessionLength() / $singleSessionLength;
                    ?>
                    <input type="number" min="1" step="1" id="edd-bk-session-length" name="edd-bk-session-length"
                           value="<?php echo esc_attr($sessionLength); ?>" />
                    <select name="edd-bk-session-unit">
                        <?php
                        $sessionUnits = array(
                                'seconds' => __('seconds', $textDomain),
                                'minutes' => __('minutes', $textDomain),
                                'hours'   => __('hours', $textDomain),
                                'days'    => __('days', $textDomain),
                                'weeks'   => __('weeks', $textDomain),
                        );
                        $filteredSessionUnits = \apply_filters('edd_bk_session_units', $sessionUnits);
                        foreach ($filteredSessionUnits as $_key => $_value) {
                            $_selected = \selected($_key, $sessionUnit, false);
                            printf('<option name="%2$s" %1$s>%3$s</option>', $_selected, $_key, $_value);
                        }
                        ?>
                    </select>
                </label>
                <?php
                echo $this->helpTooltip(
                        __(
                                'Set how long a single session lasts. A "session" can either represent a single booking or a part of a booking, and can be anything from an hour, 15 minutes, to a whole day or even a number of weeks, depending on your use case.',
                                $textDomain
                        )
                );
                ?>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Customer can book from', $textDomain); ?></span>
                    <input type="number" placeholder="Minimum" min="1" step="1" id="edd-bk-min-sessions"
                           name="edd-bk-min-sessions" value="<?php echo esc_attr($service->getMinSessions()); ?>" />
                    to
                    <input type="number" placeholder="Maximum" min="1" step="1" id="edd-bk-max-sessions"
                           name="edd-bk-max-sessions" value="<?php echo esc_attr($service->getMaxSessions()); ?>" />
                    sessions.
                    <?php
                    echo $this->helpTooltip(
                            __('The range of number of sessions that a customer can book.', $textDomain));
                    ?>
                </label>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span>
                        <?php _e('Cost per session', $textDomain); ?>
                        <span class="edd-bk-price-currency"><?php echo \edd_currency_symbol(); ?></span>
                    </span>
                    <input type="number" min="0" step="0.01" id="edd-bk-session-cost" name="edd-bk-session-cost"
                           value="<?php echo esc_attr($service->getSessionCost()); ?>" />
                           <?php
                           echo $this->helpTooltip(
                                   __('The cost of each session. The total price will be this amount times each booked session',
                                           $textDomain
                                   )
                           );
                           ?>
                </label>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Availability Rules', $textDomain); ?></span>
                    <select name="edd-bk-service-availability">
                        <option value="new"><?php _e('Create new'); ?></option>
                        <?php
                        $secondQuery = eddBookings()->getAvailabilityController()->query();
                        if (count($secondQuery) > 0) :
                            ?>
                            <optgroup label="Availabilities">
                            <?php
                            foreach ($secondQuery as $availability) {
                                /* @var $availability Availability */
                                $availId = $availability->getId();
                                $availTitle = get_the_title($availId);
                                $selected = \selected($service->getSchedule()->getAvailability()->getId(), $availId, false);
                                printf('<option value="%2$s" %1$s>%3$s</option>', $selected, $availId, $availTitle);
                            }
                            ?>
                            </optgroup>
                        <?php
                        endif;
                        ?>
                    </select>
                    <?php
                    echo $this->helpTooltip(
                            __('Choose the availability to use for this download or select <em>"Create new"</em> to
                                create and use a new one.', $textDomain)
                    );
                    ?>
                    <!-- Links -->
                    <span class="edd-bk-availability-links-section">
                        <i class="fa fa-lg fa-pencil"></i>
                        <a id="edd-bk-edit-rules" href="<?php echo admin_url('post.php?post=%s&action=edit'); ?>" target="_blank" class="edd-bk-availability-link">
                            <?php _e('Edit rules', 'eddbk'); ?></a>
                    </span>
                    <p style="display: none;" id="edd-bk-edit-rules-second-text">
                        <?php _e('Click again to open a new page where you can edit the selected availability.', $textDomain); ?>
                    </p>
                    <!-- Help text for "Create New" -->
                    <span class="edd-bk-availability-create-new-tooltip"> 
                       <?php _e('A new availability will be created once you save or publish this Download.'); ?>
                    </span>
                </label>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Use Customer Timezone on site', $textDomain); ?></span>
                    <input type="hidden" name="edd-bk-use-customer-tz" value="0" />
                    <?php $checked = \checked($service->getUseCustomerTimezone(), true, false); ?>
                    <input type="checkbox" name="edd-bk-use-customer-tz" value="1" <?php echo $checked; ?>/>
                </label>
                <?php
                echo $this->helpTooltip(__('Enable this box to use the customer timezone when showing dates and times 
                        on the front-end calendar. This is useful for international services, as customers can make
                        bookings using their local time. However, this is not recommended for local or location-based
                        services.', $textDomain));
                ?>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Show calendar in multi-views', $textDomain); ?></span>
                    <input type="hidden" name="edd-bk-multiview-output" value="0" />
                    <?php $checked = \checked($service->getMultiViewOutput(), true, false); ?>
                    <input type="checkbox" name="edd-bk-multiview-output" value="1" <?php echo $checked; ?>/>
                </label>
                <?php
                echo $this->helpTooltip(__('Enable this box to show the calendar on pages with multiple download views 
                                such as on pages that have the [downloads] shortcode', $textDomain));
                ?>
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
