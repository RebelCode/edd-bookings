<?php
$index = $data['index'];
$name = $data['name'];
$key = $data['key'];
$enabled = $data['enabled'];
$label = $data['label'];
$fullKey = sprintf('fes_input[%s][options][%s]', $index, $key);
?>
<div class="fes-form-rows">
    <label><?= $name ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="<?= $fullKey ?>[enabled]"
                value="1"
                <?php checked($enabled, '1') ?>
            />
            <?= __('Shown', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="<?= $fullKey ?>[enabled]"
                value="0"
                <?php checked($enabled, '0') ?>
                />
            <?= __('Hidden', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Label Text', 'eddbk') ?></label>
    <textarea
        name="<?= $fullKey ?>[label]"
        class="smallipopInput"
        title="<?= __('The text shown next to the option', 'eddbk') ?>"
        ><?= $label ?></textarea>
</div>
