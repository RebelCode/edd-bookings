<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Edd\Bookings\Model\Schedule;

/**
 * Renders a schedule instance.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ScheduleRenderer extends RendererAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param Schedule $schedule The schedule to render.
     */
    public function __construct(Schedule $schedule)
    {
        parent::__construct($schedule);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Schedule
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
        ob_start();
        \wp_nonce_field('edd_bk_save_meta', 'edd_bk_schedule');
        ?>
        <div class="edd-bk-schedule">
            <label>Availability: </label>
            <select name="edd-bk-schedule-availability-id">
                <option value="new">Create new availability</option>
                <?php
                $availabilities = eddBookings()->getAvailabilityController()->query();
                if (count($availabilities) > 0) :
                    ?>
                    <optgroup label="Availabilities">
                    <?php
                    foreach(eddBookings()->getAvailabilityController()->query() as $availability) {
                        $id = $availability->getId();
                        $name = \get_the_title($id);
                        $selected = \selected($this->getObject()->getAvailability()->getId(), $id, false);
                        printf('<option value="%2$s" %1$s>%3$s</option>', $selected, $id, $name);
                    }
                    ?>
                    </optgroup>
                <?php
                endif;
                ?>
            </select>
            &nbsp;
            <a href="<?php echo admin_url('post.php?post=%s&action=edit'); ?>" target="_blank" class="edd-bk-edit-availability">
                <?php echo _x('Edit', 'edit availability link', 'eddbk'); ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

}
