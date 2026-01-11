<?php
header("Content-Type:text/html; charset=utf-8");

include_once __DIR__ . '../../layout/header.php';
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(5);
include_once __DIR__ . '../../app/services/ServiceContainer.php';

$serviceContainer = ServiceContainer::getInstance();
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
$wydaniaRepo = $serviceContainer->getRepository('IssueRepository');
$wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');

$data = $wydaneUbraniaRepo->getWydaneUbraniaWithDetails();
?>
<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('history_clothing_title'); ?></h2>

<table id="example" class="table table-striped table-bordered display text-center align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th scope="col"><?php echo __('clothing_name'); ?></th>
            <th scope="col"><?php echo __('clothing_size'); ?></th>
            <th scope="col"><?php echo __('history_issued_to'); ?></th>
            <th scope="col"><?php echo __('history_details'); ?></th>
        </tr>
    </thead>
    <tbody>
        
    <?php foreach ($data as $index => $row) {
    $viewText = __('history_view');
    echo '<tr>
        <td>' . htmlspecialchars($row['nazwa_ubrania']) . '</td>'
        . '<td>' . htmlspecialchars($row['rozmiar']) . '</td>'
        . '<td>' . htmlspecialchars($row['wydane_dla']) . '</td>'
        . '<td>
            <button class="btn btn-secondary open-modal-btn" 
                    data-id="' . $row['id'] . '" 
                    data-details="' . htmlspecialchars(json_encode($row)) . '">
                ' . $viewText . '
            </button>
        </td>'
        . '</tr>';
} ?>


</tbody>
</table>

<div id="detailModal" class="modal fade" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content container">
            <div class="modal-header">
                <h2 class="modal-title" id="detailModalLabel"><?php echo __('history_details'); ?></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('close'); ?>"></button>
            </div>
            <div class="modal-body">
                <!-- nazwa_ubrania, rozmiar, ilosc, wydane_przez, wydane_dla, data -->       
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('close'); ?></button>
            </div>
        </div>
    </div>
</div>

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
<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>
<?php include_once __DIR__ . '../../layout/footer.php'; ?>

