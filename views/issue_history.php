<?php
header("Content-Type:text/html; charset=utf-8");

include_once __DIR__ . '../../layout/header.php';
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(4);
include_once __DIR__ . '../../app/core/ServiceContainer.php';

$serviceContainer = ServiceContainer::getInstance();
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
$wydaniaRepo = $serviceContainer->getRepository('IssueRepository');
$wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');

include_once __DIR__ . '../../layout/ClassModal.php';
$modal = new ClassModal();
?>
<div id="alertContainer"></div>

<div class="d-flex align-items-center">
    <h2 class="mb-4"><?php echo __('history_issue_title'); ?></h2>
    <div id="loadingSpinnerName" class="spinner-border mb-2 mx-4" style="display: none;" role="status">
        <span class="visually-hidden"><?php echo __('loading'); ?>...</span>
    </div>
</div>

<form action="<?php echo $baseUrl; ?>/issue-history" method="get" autocomplete="off">
    <div class="col-md-5">
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="id_pracownika" class="form-label"><?php echo __('issue_employee'); ?>:</label>
                <div id="loadingSpinner" class="spinner-border mb-2" style="display: none;" role="status">
                    <span class="visually-hidden"><?php echo __('loading'); ?>...</span>
                </div>
            </div>
            <div class="mb-3 position-relative inputcontainer">
                <input type="text" class="form-control" maxlength="30" placeholder="<?php echo __('employee_first_name'); ?> <?php echo __('employee_last_name'); ?>" id="username" required>
                <input type="hidden" id="pracownikID" name="pracownikID" value="" />
                <ul id="suggestions" class="list-group position-absolute" style="display: none; z-index: 1000; width: 100%; top: 100%;"></ul>
            </div>
        </div>
        <br />
        <button type="submit" class="btn btn-secondary submitBtn mb-4 p-3"><?php echo __('history_view'); ?></button>
    </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pracownikID']) && !empty($_GET['pracownikID'])) {
    $pracownikID = $_GET['pracownikID'];
    $pracownik = $pracownikRepo->getById($pracownikID);

    if ($pracownik) {
        $imie = $pracownik['imie'];
        $nazwisko = $pracownik['nazwisko'];

        $historia = $wydaniaRepo->getWydaniaByPracownikId($pracownikID);

        if ($historia) {
            echo "<h2>" . __('history_issue_for') . ": $imie $nazwisko</h2> <br/>";
            echo '<table id="example" class="table table-hover table-striped table-bordered text-center align-middle" style="width:100%">';
            echo '<thead class="table-dark"><tr>
            <th scope="col">' . __('history_issue_date') . '</th>
            <th scope="col">' . __('clothing_name') . '</th>
            <th scope="col">' . __('clothing_size') . '</th>
            <th scope="col">' . __('clothing_quantity') . '</th>
            <th scope="col">' . __('history_issued_by') . '</th>
            <th scope="col">' . __('history_cancel_issue') . '</th>
            <th scope="col">' . __('history_issue_status') . '</th>
            <th scope="col">' . __('history_status_change') . '</th>
        </tr></thead>
        <tbody>';

            foreach ($historia as $wydanie) {
                $id_wydania = $wydanie['id_wydania'];
                $data_wydania = $wydanie['data_wydania'];
                $wydane_przez = $wydanie['user_name'];
                $ubrania = $wydaneUbraniaRepo->getUbraniaByWydanieId($id_wydania);

                $oneMonthAfter = date('Y-m-d', strtotime($data_wydania . ' +1 month'));
                $currentDate = date('Y-m-d');

                foreach ($ubrania as $ubranie) {
                    $nazwa_ubrania = $ubranie['nazwa_ubrania'];
                    $nazwa_rozmiaru = $ubranie['nazwa_rozmiaru'];
                    $ilosc = $ubranie['ilosc'];
                    $status = $ubranie['status'];
                    $data_waznosci = date('Y-m-d', strtotime($ubranie['data_waznosci']));
                    $canBeReported = $ubranie['canBeReported'] == 1;

                    $statusText = $status == 1 ? __('history_issued') : ($status == 0 ? __('history_removed_from_report') : ($status == 3 ? __('history_cancelled') : __('history_destroyed_clothing') . ": {$data_waznosci}"));
                    $cancelBtn = $status == 3 ? __('history_cancelled') : __('history_cancel_issue');
                    $withinOneMonth = ($status == 1 && $currentDate <= $oneMonthAfter);
                    $disabledBtn = !$withinOneMonth ? "disabled" : "";
                    $rowClass = $status == 0 ? "table-warning" : ($status == 2 ? "table-danger" : "");
                    $buttonText = $status == 1 ? __('history_remove_from_report') : __('history_add_to_report');
                    $buttonAction = $status == 1 ? __('history_inactive') : __('history_active');
                    $reportDisabledBtn = !$canBeReported || $status == 2 || $status == 3 ? "disabled" : "";
                    $destroyDisabled = $status != 1 ? "disabled" : "";
                    $buttonHtml = "<button class='btn btn-warning cancel-btn' data-id='{$ubranie['id']}' {$disabledBtn}>{$cancelBtn}</button>";

                    echo "<tr class='{$rowClass}'>";
                    echo "<td>" . date('Y-m-d H:i', strtotime($data_wydania)) . "</td>";
                    echo "<td>{$nazwa_ubrania}</td>";
                    echo "<td>{$nazwa_rozmiaru}</td>";
                    echo "<td>{$ilosc}</td>";
                    echo "<td>{$wydane_przez}</td>";
                    if ($disabledBtn) {
                        echo "<td>
                        <span class='d-inline-block' tabindex='0' data-bs-toggle='tooltip' data-bs-placement='top' title='" . __('history_cancel_time_expired') . "'>
                            {$buttonHtml}
                        </span>
                      </td>";
                    } else {
                        echo "<td>{$buttonHtml}</td>";
                    }
                    echo "<td>{$statusText}</td>";
                    echo "<td>
        <div class='d-flex flex-column align-items-center'>
            <button class='btn btn-info inform-btn mb-2' data-id='{$ubranie['id']}' data-action='{$buttonAction}' {$reportDisabledBtn}>{$buttonText}</button>
            <button class='btn btn-danger destroy-btn' data-id='{$ubranie['id']}' {$destroyDisabled}>" . __('history_destroy_clothing') . "</button>
        </div>
      </td>";
                    echo "</tr>";
                }
            }

            echo '</tbody></table>';
        } else {
            echo "<p>" . __('history_no_data_for_user') . "</p>";
        }
    } else {
        echo "<p>" . __('history_employee_not_found') . "</p>";
    }
}

$modal->anulujModal();
$modal->zniszczoneModal();

include_once __DIR__ . '../../layout/footer.php';
?>
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

