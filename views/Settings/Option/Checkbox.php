<label>
    <input type="hidden" name="<?php echo $data['key']; ?>" value="0" />
    <input type="checkbox" name="<?php echo $data['key']; ?>" value="1" <?php checked($data['value'], "1"); ?> />
    <?php echo $data['desc']; ?>
</label>
