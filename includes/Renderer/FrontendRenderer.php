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
        $textDomain = eddBookings()->getI18n()->getDomain();
        /* @var $service Service */
        $service = $this->getObject();
        $fromShortcode = false;
        // Guard output
        if (!$service->getBookingsEnabled() || (!is_single() && !$service->getMultiViewOutput() && !$fromShortcode)) {
            return '';
        }
        ob_start();
        ?>
        <div class="edd-bk-service-container">
            <div class="edd-bk-loading-container">
                <span>Loading</span>
            </div>
            <input type="hidden" class="edd-bk-start-submit" name="edd_bk_start" />
            <input type="hidden" class="edd-bk-duration-submit" name="edd_bk_duration" />
            <input type="hidden" class="edd-bk-timezone" name="edd_bk_timezone" />
            <div class="edd-bk-datepicker-container">
                <div class="edd-bk-datepicker-skin">
                    <div class="edd-bk-datepicker"></div>
                </div>
                <input type="hidden" class="edd-bk-datepicker-value" value="" />
            </div>
            <div class="edd-bk-msgs">
                <div class="edd-bk-msg datefix-msg">
                    <p>
                        <?php
                        printf(
                            __('The date %s was automatically selected for you as the start date to accomodate %s.', $textDomain),
                            '<span class="edd-bk-datefix-date"></span>',
                            '<span class="edd-bk-datefix-length"></span>'
                        );
                        ?>
                    </p>
                </div>
                <div class="edd-bk-msg invalid-date-msg">
                    <p>
                        <?php
                        printf(
                            __('The date %s cannot accomodate %s Kindly choose another date or duration.', $textDomain),
                            '<span class="edd-bk-invalid-date"></span>',
                            '<span class="edd-bk-invalid-length"></span>'
                        );
                        ?>
                    </p>
                </div>
                <div class="edd-bk-msg no-times-for-date">
                    <p><?php _e('No times are available for this date!', $textDomain); ?></p>
                </div>
                <div class="edd-bk-msg booking-unavailable-msg">
                    <p>
                        <?php
                        _e('Your chosen session is unavailable. It may have been booked by someone else. If you believe this is a mistake, please contact the site administrator.', $textDomain);
                        ?>
                    </p>
                </div>
            </div>
            <div class="edd-bk-session-options-loading">
                <i class="fa fa-cog fa-spin"></i> <?php _e('Loading', $textDomain); ?>
            </div>
            <div class="edd-bk-session-options">
                <p class="edd-bk-if-time-unit">
                    <label>
                        Time:
                        <select class="edd-bk-timepicker">
                        </select>
                    </label>
                </p>
                <p>
                    <label>
                        Duration:
                        <?php
                        $sessionUnit = $service->getSessionUnit();
                        $singleSessionLength = Duration::$sessionUnit(1, false);
                        $sessionLength = $service->getSessionLength() / $singleSessionLength;
                        ?>
                        <input type="number" class="edd-bk-duration"
                               value="<?php echo esc_attr($service->getMinSessions() * $sessionLength); ?>"
                               min="<?php echo esc_attr($service->getMinSessions() * $sessionLength); ?>"
                               max="<?php echo esc_attr($service->getMaxSessions() * $sessionLength); ?>"
                               step="<?php echo esc_attr($sessionLength); ?>" />
                    <span class="edd-bk-session-unit">
                        <?php echo htmlentities($service->getSessionUnit()); ?>
                    </span>
                    </label>
                </p>
                <p class="edd-bk-price">
                    <label>
                        <?php _e('Price:', $textDomain); ?>
                        <span></span>
                    </label>
		</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}
