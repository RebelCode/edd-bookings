<?php
$renderer = new \Aventura\Edd\Bookings\Renderer\ChangelogRenderer(EDD_BK_DIR . 'changelog.txt');
echo $renderer->render();
