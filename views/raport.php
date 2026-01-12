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
$ubraniaPoTerminie = $wydaneUbraniaRepo->getUbraniaPoTerminie();
?>
<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('reports_issue_title'); ?></h2>
<table id="example" class="table table-striped table-hover table-bordered text-center align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th scope="col"><?php echo __('reports_expiry_date'); ?></th>
            <th scope="col"><?php echo __('employee_first_name'); ?> <?php echo __('employee_last_name'); ?></th>
            <th scope="col"><?php echo __('employee_position'); ?></th>
            <th scope="col"><?php echo __('clothing_name'); ?></th>
            <th scope="col"><?php echo __('clothing_size'); ?></th>
            <th scope="col"><?php echo __('clothing_quantity'); ?></th>
            <th scope="col"><?php echo __('order_status'); ?></th>
            <th scope="col"><?php echo __('actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $wydania = $wydaniaRepo->getAllWydania();
        if ($wydania) {
            foreach ($wydania as $wydanie) {
                $id_wydania = $wydanie['id_wydania'];
                $pracownikImie = $wydanie['imie'];
                $pracownikNazwisko = $wydanie['nazwisko'];
                $pracownikStanowisko = $wydanie['stanowisko'];
                $ubrania = $wydaneUbraniaRepo->getUbraniaByWydanieIdTermin($id_wydania);

                foreach ($ubrania as $ubranie) {
                    $rowClass = $ubranie['statusText'] === 'Przeterminowane' ? 'table-danger' : ($ubranie['statusText'] === 'Koniec ważności' ? 'table-warning' : '');

                    echo "<tr class='{$rowClass}'>";
                    echo "<td>" . date('Y-m-d H:i', strtotime($ubranie['data_waznosci'])) . "</td>";
                    
                    echo "<td>{$pracownikImie} {$pracownikNazwisko}</td>";
                    echo "<td>{$pracownikStanowisko}</td>";
                    echo "<td>{$ubranie['nazwa_ubrania']}</td>";
                    echo "<td>{$ubranie['nazwa_rozmiaru']}</td>";
                    echo "<td>{$ubranie['ilosc']}</td>";
                    echo "<td>{$ubranie['statusText']}</td>";
                    echo "<td>
                    <div class='d-flex justify-content-between'>
                        <button class='btn btn-primary redirect-btn me-2' 
                                    data-pracownik-id='{$wydanie['pracownik_id']}' 
                                    data-pracownik-imie='{$pracownikImie}' 
                                    data-pracownik-nazwisko='{$pracownikNazwisko}' 
                                    data-pracownik-stanowisko='{$pracownikStanowisko}'>" . __('reports_issue') . "</button>
                        <button class='btn btn-secondary inform-btn p-1' data-raport='true' data-id='{$ubranie['id']}'>" . __('reports_remove_from_report') . "</button>
                     </div>
                        </td>
                    </tr>";
                }
            }
        } else {
            echo "<tr><td colspan='8'>" . __('reports_no_issued_clothing') . "</td></tr>";
        }
        ?>
    </tbody>
</table>

<br />

<h2 class="mb-4 mt-3"><?php echo __('reports_issued_clothing'); ?></h2>
<table id="example1" class="table table-striped table-bordered display text-center align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th scope="col"><?php echo __('clothing_name'); ?></th>
            <th scope="col"><?php echo __('clothing_size'); ?></th>
            <th scope="col"><?php echo __('reports_issued_quantity'); ?></th>
            <th scope="col"><?php echo __('reports_warehouse_quantity'); ?></th>
            <th scope="col"><?php echo __('clothing_min_quantity'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($ubraniaPoTerminie)) : ?>
            <?php foreach ($ubraniaPoTerminie as $ubranie) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($ubranie['nazwa_ubrania']); ?></td>
                    <td><?php echo htmlspecialchars($ubranie['nazwa_rozmiaru']); ?></td>
                    <td><?php echo htmlspecialchars($ubranie['ilosc']); ?></td>
                    <td><?php echo htmlspecialchars($ubranie['ilosc_magazyn']); ?></td>
                    <td><?php echo htmlspecialchars($ubranie['ilosc_min']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5"><?php echo __('no'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once __DIR__ . '../../layout/footer.php'; ?>
<script>
    function initializeDataTable(tableId) {
        new DataTable(tableId, {
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
    }

    initializeDataTable('#example');
    initializeDataTable('#example1');
</script>
<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>