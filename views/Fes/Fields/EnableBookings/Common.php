<label>
    <input type="checkbox" id="<?= $data['template'] ?>" name="<?= $data['name'] ?>" value="on" <?= checked('1', $data['value']); ?> />
    <?= $data['characteristics']['checkbox_label'] ?>
</label>