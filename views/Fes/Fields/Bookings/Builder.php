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

<hr/>

<!-- Session Length Options -->
<div class="fes-form-rows">
    <label><?= __('"Session Length" Option', 'eddk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][session_length][enabled]"
                value="1"
                <?php checked($data['characteristics']['session_length']['enabled'], '1') ?>
            />
            <?= __('Shown', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][session_length][enabled]"
                value="0"
                <?php checked($data['characteristics']['session_length']['enabled'], '0') ?>
                />
            <?= __('Hidden', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Label Text', 'eddk') ?></label>
    <textarea
        name="fes_input[<?= $data['index'] ?>][session_length][label]"
        class="smallipopInput"
        title="<?= __('The text shown next to the session length option.', 'eddbk') ?>"
        ><?=$data['characteristics']['session_length']['label'] ?></textarea>
</div>

<hr />

<!-- Min/Max Session Options -->
<div class="fes-form-rows">
    <label><?= __('"Min/Max Sessions" Option', 'eddk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][min_max_sessions][enabled]"
                value="1"
                <?php checked($data['characteristics']['min_max_sessions']['enabled'], '1') ?>
            />
            <?= __('Shown', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][min_max_sessions][enabled]"
                value="0"
                <?php checked($data['characteristics']['min_max_sessions']['enabled'], '0') ?>
                />
            <?= __('Hidden', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Label Text', 'eddk') ?></label>
    <textarea
        name="fes_input[<?= $data['index'] ?>][min_max_sessions][label]"
        class="smallipopInput"
        title="<?= __('The text shown next to the min/max sessions option.', 'eddbk') ?>"
        ><?=$data['characteristics']['min_max_sessions']['label'] ?></textarea>
</div>

<hr />

<!-- Min/Max Session Options -->
<div class="fes-form-rows">
    <label><?= __('"Session cost" Option', 'eddk') ?></label>
    <div class="fes-form-sub-fields">
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][session_cost][enabled]"
                value="1"
                <?php checked($data['characteristics']['session_cost']['enabled'], '1') ?>
            />
            <?= __('Shown', 'eddbk'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="fes_input[<?= $data['index'] ?>][session_cost][enabled]"
                value="0"
                <?php checked($data['characteristics']['session_cost']['enabled'], '0') ?>
                />
            <?= __('Hidden', 'eddbk'); ?>
        </label>
    </div>
</div>
<div class="fes-form-rows">
    <label><?= __('Label Text', 'eddk') ?></label>
    <textarea
        name="fes_input[<?= $data['index'] ?>][session_cost][label]"
        class="smallipopInput"
        title="<?= __('The text shown next to the session cost option.', 'eddbk') ?>"
        ><?=$data['characteristics']['session_cost']['label'] ?></textarea>
</div>
