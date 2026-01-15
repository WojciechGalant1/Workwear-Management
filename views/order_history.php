<?php
header("Content-Type:text/html; charset=utf-8");

include_once __DIR__ . '../../layout/header.php';
?>

    <h2 class="mb-4"><?php echo __('history_order_title'); ?></h2>
    
    <table id="example" class="table table-striped table-bordered display text-center align-middle" style="width:100%">
        <thead class="table-dark">
        <tr>
            <th scope="col"><?php echo __('history_date'); ?></th>
            <th scope="col"><?php echo __('clothing_name'); ?></th>
            <th scope="col"><?php echo __('clothing_size'); ?></th>
            <th scope="col"><?php echo __('clothing_company'); ?></th>
            <th scope="col"><?php echo __('history_added_by'); ?></th>
            <th scope="col"><?php echo __('clothing_quantity'); ?></th>
            <th scope="col"><?php echo __('order_status'); ?></th>
            <th scope="col"><?php echo __('order_notes'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($zamowienia as $zamowienie) { ?>
            <tr>
                <td><?php echo date('Y-m-d H:i', strtotime($zamowienie['data_zamowienia'])); ?></td>
                <td><?php echo $zamowienie['nazwa_ubrania']; ?></td>
                <td><?php echo $zamowienie['rozmiar_ubrania']; ?></td>
                <td><?php echo $zamowienie['firma']; ?></td>
                <td><?php echo $zamowienie['nazwa_uzytkownika']; ?></td>
                <td><?php echo $zamowienie['ilosc']; ?></td>
                <td><?php echo $zamowienie['status'] == 1 ? __('order_approved') : ($zamowienie['status'] == 2 ? __('order_stocktaking') : __('order_no_data')); ?></td>
                <td><?php echo $zamowienie['uwagi']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
    <script>
        new DataTable('#example', {
            lengthMenu: [
                [15, 25, 50, -1],
                [15, 25, 50, "<?php echo __('table_all'); ?>"],
            ],
            language: {
                processing: "<?php echo __('table_processing'); ?>",
                search: "<?php echo __('search'); ?>:",
                lengthMenu: "<?php echo __('table_show_menu'); ?>",
                info: "<?php echo __('table_info'); ?>",
                infoEmpty: "<?php echo __('table_info_empty'); ?>",
                infoFiltered: "<?php echo __('table_info_filtered'); ?>",
                infoPostFix: "",
                loadingRecords: "<?php echo __('table_loading'); ?>",
                zeroRecords: "<?php echo __('table_zero_records'); ?>",
                emptyTable: "<?php echo __('table_empty'); ?>",
                paginate: {
                    first: "<?php echo __('table_first'); ?>",
                    previous: "<?php echo __('table_previous'); ?>",
                    next: "<?php echo __('table_next'); ?>",
                    last: "<?php echo __('table_last'); ?>",
                },
            }
        });
    </script>

<?php include_once __DIR__ . '../../layout/footer.php'; ?>

