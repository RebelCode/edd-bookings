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
            <label>Timetable: </label>
            <select name="edd-bk-schedule-timetable-id">
                <option value="new">Create new timetable</option>
                <?php
                $timetables = eddBookings()->getTimetableController()->query();
                if (count($timetables) > 0) :
                    ?>
                    <optgroup label="Timetables">
                    <?php
                    foreach(eddBookings()->getTimetableController()->query() as $timetable) {
                        $id = $timetable->getId();
                        $name = \get_the_title($id);
                        $selected = \selected($this->getObject()->getTimetable()->getId(), $id, false);
                        printf('<option value="%2$s" %1$s>%3$s</option>', $selected, $id, $name);
                    }
                    ?>
                    </optgroup>
                <?php
                endif;
                ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

}
