<?php
use App\Auth\CsrfGuard;

header("Content-Type:text/html; charset=utf-8");

include_once __DIR__ . '../../layout/header.php';
?>

<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('employee_title'); ?></h2>
<table id="example" class="table table-striped table-bordered display text-center align-middle" style="width:100%">
    <thead class="table-dark">
    <tr>
        <th scope="col"><?php echo __('employee_first_name'); ?></th>
        <th scope="col"><?php echo __('employee_last_name'); ?></th>
        <th scope="col"><?php echo __('employee_position'); ?></th>
        <th scope="col"><?php echo __('employee_status'); ?></th>
        <th scope="col"><?php echo __('edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($pracownicy as $index => $pracownik) { ?>
        <tr>
            <td><?php echo htmlspecialchars((string)$pracownik['imie']); ?></td>
            <td><?php echo htmlspecialchars((string)$pracownik['nazwisko']); ?></td>
            <td><?php echo htmlspecialchars((string)$pracownik['stanowisko']); ?></td>
            <td><?php echo $pracownik['status'] == 1 ? __('employee_active') : '[brak danych]'; ?></td>
            <td class="text-center">
                <button class="btn btn-secondary open-modal-btn" data-index="<?php echo $index; ?>"
                        data-id="<?php echo $pracownik['id_pracownik']; ?>"><?php echo __('edit'); ?>
                </button>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script id="pracownicy-data" type="application/json"><?php echo json_encode($pracownicy, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?></script>

<!-- Modal -->
<div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content container">
            <div class="modal-header">
                <h2 class="modal-title" id="editModalLabel"><?php echo __('employee_edit_title'); ?></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('close'); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="edycjaPracownikaForm" action="<?php echo $baseUrl; ?>/app/Http/Handlers/Employee/UpdateEmployeeHandler.php" method="post" class="needs-validation" novalidate data-ajax-form>
                    <?php echo CsrfGuard::getTokenField(); ?>
                    <input type="hidden" id="pracownik_id" name="id">
                    <div class="mb-3 mt-2">
                        <label for="imie" class="form-label"><?php echo __('employee_first_name'); ?>:</label>
                        <input type="text" class="form-control" id="imie" name="imie" required>
                        <div class="invalid-feedback">
                            <?php echo __('validation_required'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nazwisko" class="form-label"><?php echo __('employee_last_name'); ?>:</label>
                        <input type="text" class="form-control" id="nazwisko" name="nazwisko" required>
                        <div class="invalid-feedback">
                            <?php echo __('validation_required'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="stanowisko" class="form-label"><?php echo __('employee_position'); ?>:</label>
                        <input type="text" class="form-control" id="stanowisko" name="stanowisko" required>
                        <div class="invalid-feedback">
                            <?php echo __('validation_required'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label"><?php echo __('employee_status'); ?>:</label>
                        <select id="status" name="status" class="form-select data_w-select" required>
                            <option value="1"><?php echo __('employee_active'); ?></option>
                            <option value="0"><?php echo __('employee_inactive'); ?></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="submit" form="edycjaPracownikaForm" id="zapiszPracownika" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
        </div>
    </div>
</div>

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

