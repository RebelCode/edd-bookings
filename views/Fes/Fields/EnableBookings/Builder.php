<div class="fes-form-rows">
    <label><?= __('Checked by Default', 'eddk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input type="radio" name="fes_input[<?= $data['index'] ?>][checked_default]" value="1" <?php checked($data['characteristics']['checked_default'], '1') ?> />
            <?= __('Yes', 'eddbk'); ?>
        </label>
        <label>
            <input type="radio" name="fes_input[<?= $data['index'] ?>][checked_default]" value="0" <?php checked($data['characteristics']['checked_default'], '0') ?> />
            <?= __('No', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Checkbox Label', 'eddbk') ?></label>
    <textarea name="fes_input[<?= $data['index'] ?>][checkbox_label]" class="smallipopInput" title="<?= __('The text shown next to the checkbox', 'eddbk') ?>"><?= $data['characteristics']['checkbox_label'] ?></textarea>
</div>