<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Edd\Bookings\Settings\Option\OptionInterface;
use \Exception;

/**
 * A renderer implementation for Settings Options.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class OptionRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        $option = $this->getObject();
        if (!($option instanceof OptionInterface)) {
            throw new Exception(sprintf('%s can only render instances of OptionInterface!', __CLASS__));
        }
        // Prepare the name attribute to mirror the record path
        $recordPath = $option->getRecord()->getKeyPath();
        $nameAttr = sprintf('[%s]', implode('][', $recordPath));
        $data['recordPath'] = $recordPath;
        $data['key'] = esc_attr($nameAttr);
        // Render
        return eddBookings()->renderView($option->getView(), $data);
    }

}
