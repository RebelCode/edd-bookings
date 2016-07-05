<?php

namespace Aventura\Edd\Bookings;

/**
 * Handles plugin admin notices.
 * 
 * @since 2.0.1
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Notices
{

    /**
     * The name of the view file.
     */
    const VIEW = 'notice';

    /**
     * Creates a notice.
     * 
     * @param string $id The HTML ID of the notice.
     * @param string $text The (translated) HTML text for the notice.
     * @param string $style The notice style: success, updated and error. Default: 'updated'
     * @param boolean $dismissible True to make the notice dismissible, false to make it persistent. Default: true
     * @param string $action The action to call when the notice is dismissed. Use empty string to ignore. Default: ''
     * @return string The HTML for the notice.
     */
    public static function create($id, $text, $style = 'updated', $dismissible = true, $action = '')
    {
        $view = (object) compact('id', 'text', 'style', 'dismissible', 'action');
        ob_start();
        require static::viewPath();
        return ob_get_clean();
    }

    /**
     * Gets the full path to the notice view file.
     * 
     * @return string The full absolute file path to the notice view file.
     */
    public static function viewPath()
    {
        return realpath(sprintf('%s%s.php', EDD_BK_VIEWS_DIR, static::VIEW));
    }

}
