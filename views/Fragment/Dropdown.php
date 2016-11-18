<?php
$defaults = array(
    'id'       => '',
    'class'    => '',
    'name'     => '',
    'items'    => array(),
    'selected' => '',
);
$args = wp_parse_args($data, $defaults);
?>
<select
    id="<?php echo esc_attr($args['id']); ?>"
    class="<?php echo esc_attr($args['class']); ?>"
    name="<?php echo esc_attr($args['name']); ?>"
    >
    <?php foreach ($args['items'] as $_key => $_val): ?>
    <option
        value="<?php echo esc_attr($_key); ?>"
        <?php selected($_key, $args['selected']) ?>
        >
        <?php echo $_val; ?>
    </option>
    <?php endforeach; ?>
</select>
