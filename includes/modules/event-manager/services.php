<?php

use \Dhii\WpEvents\EventManager;

return array(
    'event_manager' => function() {
        return new EventManager();
    }
);
