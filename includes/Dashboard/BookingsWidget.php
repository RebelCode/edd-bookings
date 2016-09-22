<?php

namespace Aventura\Edd\Bookings\Dashboard;

use \Aventura\Edd\Bookings\Dashboard\Widget\WidgetAbstract;

/**
 * Description of BookingsWidget
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsWidget extends WidgetAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function renderOutput()
    {
        echo '<p>Output</p>';
    }

    /**
     * {@inheritdoc}
     */
    public function renderOptions()
    {
        echo '<p>Options</p>';
    }

}
