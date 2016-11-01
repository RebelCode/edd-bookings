<?php
$i = $data['index'];
$sId = $data['service'];
?>

<div class="modal fade edd-bk-modal" data-cart-index="<?= $i; ?>" tabindex="1" role="dialog" aria-labelledby="edd-bk-modal-label-<?= $i; ?>" aria-hidden="true">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title"><?= get_the_title($sId) ?></h4>
            </div>

            <div class="edd-bk-service-session-picker" data-service-id="<?= $sId ?>"></div>
            <a href="#" class="edd-bk-edit-cart-item-session edd-add-to-cart button blue edd-submit edd-has-js">
                <span class="edd-add-to-cart-label"><?php _e('Choose Session', 'eddbk'); ?></span>
                <span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>
            </a>

        </div>
    </div>
</div>
