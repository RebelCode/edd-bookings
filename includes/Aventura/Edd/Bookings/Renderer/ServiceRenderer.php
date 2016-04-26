<?php

namespace Aventura\Edd\Bookings\Renderer;

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
                    $singleSessionLength = \Aventura\Diary\DateTime\Duration::$sessionUnit(1, false);
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
                    <span><?php _e('Schedule:', $textDomain); ?></span>
                    <select name="edd-bk-service-availability">
                        <option value="new"><?php _e('Create new schedule and timetable'); ?></option>
                        <?php
                        $secondQuery = eddBookings()->getAvailabilityController()->query();
                        if (count($secondQuery) > 0) :
                            ?>
                            <optgroup label="Schedules">
                            <?php
                            foreach ($secondQuery as $availability) {
                                $availabilityId = $availability->getId();
                                $availabilityTitle = \get_the_title($availabilityId);
                                $timetableId = $availability->getTimetable()->getId();
                                $timetableTitle = get_the_title($timetableId);
                                $timetableIdAttr = sprintf('data-timetable-id="%s"', esc_attr($timetableId));
                                $timetableTitleAttr = sprintf('data-timetable-title="%s"', esc_attr($timetableTitle));
                                $selected = \selected($service->getAvailability()->getId(), $availabilityId, false);
                                printf('<option value="%2$s" %1$s %4$s %5$s>%3$s</option>', $selected, $availabilityId,
                                        $availabilityTitle, $timetableIdAttr, $timetableTitleAttr);
                            }
                            ?>
                            </optgroup>
                        <?php
                        endif;
                        ?>
                    </select>
                    <?php
                    echo $this->helpTooltip(
                            __('The schedule to use for this download. Choose <em>"Create new schedule and 
                                    timetable"</em> to create and use a new schedule and timetable, instead of 
                                    using existing ones.', $textDomain)
                    );
                    ?>
                </label>
                <a class="edd-bk-help-toggler"><?php _e('Need help?', $textDomain); ?></a>
            </div>
            <div class="edd-bk-service-section edd-bk-service-links">
                <label>
                    <span><?php _e('Links', 'eddbk'); ?></span>
                    <i class="fa fa-pencil"></i>
                    <a href="<?php echo admin_url('post.php?post=%s&action=edit'); ?>" target="_blank" class="edd-bk-schedule-link">
                        <?php _e('Edit Schedule', 'eddbk'); ?>
                    </a>
                    |
                    <i class="fa fa-lg fa-calendar"></i>
                    <a href="<?php echo admin_url('post.php?post=%s&action=edit'); ?>" target="_blank" class="edd-bk-timetable-link">
                        <?php _e('Edit') ?> <span></span>
                    </a>
                </label>
            </div>
            <div class="edd-bk-help-section">
                <p>
                    <?php
                    _e(
                    'Schedules are a new concept introduced in version 2.0 that, together with Timetables, replace the
                    calendar builder that was shown here in previous versions.', $textDomain);
                    ?>
                </p>
                <p>
                    <?php
                    _e('
                    Your Bookable Downloads use a Schedule, which is used as a storage for
                    your Download\'s bookings, so bookings made for a particular download will be registered to that
                    Download\'s schedule. This concept allows you to set up multiple
                    Downloads using the same Schedule so that their bookings will be shared. This means that dates and
                    times booked for one Download will also become unavailable for booking for other Downloads that use
                    the same Schedule.', $textDomain);
                    ?>
                </p>
                <p>
                    <?php
                    _e(
                    "In turn, each Schedule uses a Timetable, which is a set of rules that determine the days and times 
                    available for a particular booking.",
                    $textDomain);
                    ?>
                </p>
                <p>
                    <?php
                    _e(
                    'You are not required to have your Downloads share Schedules and Timetables; each download
                    can have its own pair. This feature is useful for individuals who, for
                    example, can provide multiple types of services, but not simultaneously.', $textDomain);
                    ?>
                </p>
            </div>
            <div class="edd-bk-service-section">
                <label>
                    <span><?php _e('Show calendar in multi-views', $textDomain); ?></span>
                    <input type="hidden" name="edd-bk-multiview-output" value="0" />
                    <?php $checked = \checked($service->getMultiViewOutput(), true, false); ?>
                    <input type="checkbox" name="edd-bk-multiview-output" value="1" <?php echo $checked; ?>/>
                </label>
                <?php
                echo $this->helpTooltip(__('Enable this box to show the calendar on pages with multiple download views,'
                                . ' such as on pages that have the [downloads] shortcode', $textDomain));
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
