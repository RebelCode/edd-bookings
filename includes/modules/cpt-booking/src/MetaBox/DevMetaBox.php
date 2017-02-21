<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\MetaBox;

use \RebelCode\WordPress\Admin\Metabox\MetaBox;

/**
 * Metabox implementation for the booking developer data metabox.
 *
 * @since [*next-version*]
 */
class DevMetaBox extends MetaBox
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        parent::onAppReady();

        $this->getEventManager()->attach('admin_init', $this->_callback('setHidden'));
    }

    /**
     * Sets the metabox to be hidden when the page is rendered.
     *
     * The metabox can still be shown from the Screen Options.
     *
     * @since [*next-version*]
     */
    public function setHidden()
    {
        $userId = \get_current_user_id();

        $userMetaKey   = sprintf('metaboxhidden_%s', $this->getScreen());
        $userMetaValue = \get_user_meta($userId, $userMetaKey, true);

        if (!is_array($userMetaValue)) {
            $userMetaValue = array();
        }

        if (!in_array($this->getId(), $userMetaValue)) {
            $userMetaValue[] = 'dev-booking-data';
        }
        
        \update_user_meta($userId, $userMetaKey, $userMetaValue);

        return $this;
    }
}
