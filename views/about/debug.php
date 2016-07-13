<h3>Integrations</h3>
<?php
$integrations = array_keys(eddBookings()->getIntegrations());
if (count($integrations)) : ?>
    <ul style="list-style: disc inside;">
        <?php printf('<li>%s</li>', implode('</li><li>', $integrations)); ?>
    </ul>
<?php else : ?>
    <p>There are no integrations active.</p>
<?php endif; ?>

<hr/>

<h3>Instance Dump</h3>
<?php
ini_set('xdebug.var_display_max_depth', '10');
var_dump(eddBookings());
