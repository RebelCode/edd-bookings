<?php
$bookings = $data['bookings'];
$permalink = get_permalink();
?>
<table class="table fes-table table-condensed table-striped" id="fes-order-list">
    <thead>
        <tr>
            <th><?= __('Date and Time', 'edd_fes'); ?></th>
            <th><?= __('Duration', 'edd_fes'); ?></th>
            <th><?= __('Customer', 'edd_fes'); ?></th>
            <th><?= __('Download', 'edd_fes') ?></th>
            <th></th>
            <?php do_action('edd_bk_fes_bookings_table_columns'); ?>
        </tr>
    </thead>
    <tbody>
        <?php if (count($bookings) > 0) : ?>
            <?php foreach ($bookings as $booking) : /* @var $booking Aventura\Edd\Bookings\Model\Booking */ ?>
            <?php
                $service = eddBookings()->getServiceController()->get($booking->getServiceId());
                $datetimeFormat = $service->isSessionUnit('weeks', 'days')
                    ? get_option('date_format')
                    : sprintf('%s %s', get_option('time_format'), get_option('date_format'));
            ?>
            <tr>
                <td class = "fes-order-list-td"><?= $booking->getStart()->format($datetimeFormat) ?></td>
                <td class = "fes-order-list-td"><?= $booking->format('%d') ?></td>
                <td class = "fes-order-list-td">
                    <?php
                    $customer = new EDD_Customer($booking->getCustomerId());
                    echo $customer->name;
                    ?>
                </td>
                <td class = "fes-order-list-td">
                    <a href="<?= add_query_arg(array('task' => 'edit-product', 'post_id' => $booking->getServiceId()), $permalink) ?>">
                        <?= get_the_title($booking->getServiceId()) ?>
                    </a>
                </td>
                <td class = "fes-order-list-td">
                    <a href="<?= add_query_arg(array('task' => 'edit-booking', 'booking_id' => $booking->getId()), $permalink) ?>">
                        <?= __('View Details', 'eddbk') ?>
                    </a>
                </td>
                <?php do_action ('edd_bk_fes_bookings_table_cells', $booking); ?>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="6"><?= __('No bookings found', 'edd_fes') ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
EDD_FES()->dashboard->order_list_pagination();
