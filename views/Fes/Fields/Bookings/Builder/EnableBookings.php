<?php

$base = $data['base'];
$key = 'bookings_enabled';
$name = __('"Enable Bookings" Option', 'eddk');
echo $base($key, $name, $data);

$fullKey = sprintf('fes_input[%s][options][%s]', $data['index'], $key);
?>

<div class="fes-form-rows">
    <label><?= __('Default', 'eddbk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="<?= $fullKey ?>[default]"
                value="1"
                <?php checked($data['default'], '1') ?>
            />
            <?= __('Enabled', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="<?= $fullKey ?>[default]"
                value="0"
                <?php checked($data['default'], '0') ?>
                />
            <?= __('Disabled', 'eddbk'); ?>
        </label>
    </div>
</div>

<div class="fes-form-rows">
    <label><?= __('Hide other options when disabled', 'eddbk') ?></label>
    <input type="hidden" name="<?= $fullKey ?>[hide_others]" value="0" />
    <input
        type="checkbox"
        name="<?= $fullKey ?>[hide_others]"
        value="1"
        <?php checked((bool) $data['hide_others']) ?>
    />
</div>
