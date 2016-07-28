<?php

$base = $data['base'];
$key = 'use_customer_tz';
$name = __('"Use Customer Timezone" Option', 'eddk');
$fullKey = sprintf('fes_input[%s][options][%s]', $data['index'], $key);

echo $base($key, $name, $data);

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
