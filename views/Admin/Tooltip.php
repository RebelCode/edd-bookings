<?php
$data = wp_parse_args($data, array(
    'icon'  => 'question-circle',
    'align' => array('right', 'bottom')
));
?>
<span class="edd-bk-help <?php echo implode(' ', $data['align']) ?>">
    <i class="fa fa-fw fa-<?php echo $data['icon']; ?>"></i>
    <div><?php echo $data['text']; ?></div>
</span>
