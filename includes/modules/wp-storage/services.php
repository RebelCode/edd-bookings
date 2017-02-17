<?php

use \RebelCode\Storage\WordPress\Adapter;

return array(
    'storage_adapter' => function() {
        return new Adapter();
    }
);
