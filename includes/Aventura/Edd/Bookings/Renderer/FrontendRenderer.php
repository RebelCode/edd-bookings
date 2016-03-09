<?php

namespace Aventura\Edd\Bookings\Renderer;

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
        /* @var $service \Aventura\Edd\Bookings\Model\Service */
        $service = $this->getObject();
        $fromShortcode = false;
        // Guard output
        if (!$service->getBookingsEnabled() || (!is_single() && !$service->getMultiViewOutput() && !$fromShortcode)) {
            return '';
        }
        ob_start();
        ?>
        <div class="edd-bk-service-container">
            <div class="edd-bk-datepicker-container">
                <div class="edd-bk-datepicker-skin">
                    <div class="edd-bk-datepicker"></div>
                </div>
                <input type="hidden" class="edd-bk-datepicker-value" name="edd-bk-date" value="" />
                <input type="hidden" class="edd-bk-timezone" name="edd-bk-timezone" value="" />
            </div>
            <div class="edd-bk-msgs">
                <div class="edd-bk-msg datefix-msg">
                    <p>
                        <?php
                        _e(
                                sprintf(
                                        'The date %s was automatically selected for you as the start date to accomodate %s.',
                                        '<span class="edd-bk-datefix-date"></span>',
                                        '<span class="edd-bk-datefix-length"></span>'), $textDomain);
                        ?>
                    </p>
                </div>
                <div class="edd-bk-msg invalid-date-msg">
                    <p>
                        <?php
                        _e(sprintf('The date %s cannot accomodate %s Kindly choose another date or duration.',
                                        '<span class="edd-bk-invalid-date"></span>',
                                        '<span class="edd-bk-invalid-length"></span>'), $textDomain);
                        ?>
                    </p>
                </div>
                <div class="edd-bk-msg no-times-for-date">
                    <p><?php _e('No times are available for this date!', $textDomain); ?></p>
                </div>
                <div class="edd-bk-msg booking-unavailable-msg">
                    <p>
                        <?php
                        _e('The booking you selected is unavailable! This is either an indication of a problem with our service'
                                . ' or your chosen session has been booked by someone else.', $textDomain);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}
