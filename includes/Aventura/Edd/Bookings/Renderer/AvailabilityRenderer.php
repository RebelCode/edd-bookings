<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Edd\Bookings\Model\Availability;

/**
 * Renders an availability instance.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AvailabilityRenderer extends RendererAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param Availability $availability The availability to render.
     */
    public function __construct(Availability $availability)
    {
        parent::__construct($availability);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Availability
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
        \wp_nonce_field('edd_bk_save_meta', 'edd_bk_availability');
        ?>
        <div class="edd-bk-availability">
            <label>Timetable: </label>
            <select name="edd-bk-availability-timetable-id">
                <?php
                foreach(eddBookings()->getTimetableController()->query() as $timetable) {
                    $id = $timetable->getId();
                    $name = \get_the_title($id);
                    $selected = \selected($this->getObject()->getTimetable()->getId(), $id, false);
                    printf('<option value="%2$s" %1$s>%3$s</option>', $selected, $id, $name);
                }
                ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

}
