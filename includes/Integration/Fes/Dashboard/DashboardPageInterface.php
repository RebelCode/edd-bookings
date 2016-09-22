<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

use \Aventura\Edd\Bookings\Controller\ControllerInterface;

/**
 * An FES dashboard page.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface DashboardPageInterface extends ControllerInterface
{

    /**
     * Gets the page ID, referred to as "task" in FES.
     * 
     * @return string The page ID.
     */
    public function getId();

    /**
     * Gets the page title.
     * 
     * @return string The page title.
     */
    public function getTitle();

    /**
     * Gets the page icon name.
     * 
     * Refer to glyphicons (halflings) for icons.
     * 
     * @return string The page icon name.
     */
    public function getIcon();

    /**
     * Renders the page content.
     * 
     * @return string The rendered content.
     */
    public function render();

}
