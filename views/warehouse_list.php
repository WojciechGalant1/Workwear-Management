<?php
use App\Auth\CsrfGuard;

header("Content-Type:text/html; charset=utf-8");

include_once __DIR__ . '../../layout/header.php';
?>

<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('warehouse_title'); ?></h2>
<table id="example" class="table table-striped table-bordered display text-center align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th scope="col"><?php echo __('clothing_name'); ?></th>
            <th scope="col"><?php echo __('clothing_size'); ?></th>
            <th scope="col"><?php echo __('clothing_quantity'); ?></th>
            <th scope="col"><?php echo __('clothing_min_quantity'); ?></th>
            <th scope="col"><?php echo __('warehouse_order'); ?></th>
            <th scope="col"><?php echo __('edit'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($ubrania as $ubranie) {
            $ile = $ubranie['ilosc'];
            $ileMin = $ubranie['iloscMin'];
            echo '<tr><td>' . htmlspecialchars($ubranie['nazwa_ubrania']) . '</td>'
                . '<td>' . htmlspecialchars($ubranie['nazwa_rozmiaru']) . '</td>'
                . '<td>' . $ile . '</td>'
                . '<td>' . $ileMin . '</td>'
                . ($ile >= $ileMin ? '<td>' . __('no') . '</td>' : '<td class="table-danger">' . __('warehouse_order_now') . '</td>')
                . '<td><button class="btn btn-secondary open-modal-btn" 
                                    data-id="' . $ubranie['id'] . '" 
                                    >' . __('edit') . '</button></td>'
                . '</tr>';
        }

        ?>
    </tbody>
</table>

<div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content container">
            <div class="modal-header">
                <h2 class="modal-title" id="editModalLabel"><?php echo __('edit'); ?></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('close'); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="edycjaUbraniaForm" action="test" method="post" class="needs-validation" novalidate>
                    <?php echo CsrfGuard::getTokenField(); ?>
                    <input type="hidden" id="id_ubrania" name="id">
                    <div class="mb-3 mt-2">
                        <label for="productName" class="form-label"><?php echo __('clothing_name'); ?>:</label>
                        <input type="text" class="form-control" id="productName" name="nazwa" required>
                    </div>
                    <div class="mb-3">
                        <label for="sizeName" class="form-label"><?php echo __('clothing_size'); ?>:</label>
                        <input type="text" class="form-control" id="sizeName" name="rozmiar" required>
                    </div>
                    <div class="mb-3">
                        <label for="ilosc" class="form-label"><?php echo __('warehouse_current'); ?>:</label>
                        <input type="number" class="form-control" id="ilosc" name="ilosc" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="iloscMin" class="form-label"><?php echo __('clothing_min_quantity'); ?>:</label>
                        <input type="number" class="form-control" id="iloscMin" name="iloscMin" min="1" required>
                    </div>
                    <div class="mb-5">
                        <label for="id_uwagi" class="form-label"><?php echo __('order_notes'); ?>:</label>
                        <textarea id="id_uwagi" name="uwagi" rows="4" cols="50" class="form-control" spellcheck="false"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="submit" form="edycjaUbraniaForm" id="zapiszUbranie" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
        </div>
    </div>
</div>


<script id="ubrania-data" type="application/json"><?php echo json_encode($ubrania, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?></script>
<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>
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

