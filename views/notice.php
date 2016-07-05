<?php
$dismissClass = $view->dismissible
    ? 'is-dismissible'
    : '';
?>
<div id="<?= $view->id ?>" class="<?= $view->style ?> notice notice-eddbk notice-<?= $view->style; ?> <?= $dismissClass ?>" data-action="<?= $view->action ?>">
    <p><?= $view->text; ?></p>
</div>
