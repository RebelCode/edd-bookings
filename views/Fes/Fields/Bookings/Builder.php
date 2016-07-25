<hr/>

<!-- Enable Bookings Checkbox Options -->
<div class="fes-form-rows">
    <label><?= __('"Enable Bookings" Checkbox', 'eddk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][bookings_enabled][enabled]"
                value="1"
                <?php checked($data['characteristics']['bookings_enabled']['enabled'], '1') ?>
            />
            <?= __('Shown', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][bookings_enabled][enabled]"
                value="0"
                <?php checked($data['characteristics']['bookings_enabled']['enabled'], '0') ?>
                />
            <?= __('Hidden', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Label Text', 'eddbk') ?></label>
    <textarea
        name="fes_input[<?= $data['index'] ?>][bookings_enabled][label]"
        class="smallipopInput"
        title="<?= __('The text shown next to the checkbox', 'eddbk') ?>"
        ><?=$data['characteristics']['bookings_enabled']['label'] ?></textarea>
</div>
