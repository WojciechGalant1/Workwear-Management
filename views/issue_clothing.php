<?php

include_once __DIR__ . '../../layout/header.php';
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(1);

include_once __DIR__ . '../../app/services/ServiceContainer.php';
include_once __DIR__ . '../../app/helpers/CsrfHelper.php';

$serviceContainer = ServiceContainer::getInstance();
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
$ubranieRepo = $serviceContainer->getRepository('ClothingRepository');
$ubrania = $ubranieRepo->getAllUnique();

include_once __DIR__ . '../../app/helpers/DateHelper.php';

$expire_date_months = [6, 12, 18, 24];

$fromRaport = isset($_GET['fromRaport']) && $_GET['fromRaport'] == '1';
$imie = '';
$nazwisko = '';
$stanowisko = '';

if ($fromRaport) {
    $pracownikId = isset($_GET['pracownikId']) ? htmlspecialchars($_GET['pracownikId']) : '';
    $imie = isset($_GET['imie']) ? htmlspecialchars($_GET['imie']) : '';
    $nazwisko = isset($_GET['nazwisko']) ? htmlspecialchars($_GET['nazwisko']) : '';
    $stanowisko = isset($_GET['stanowisko']) ? htmlspecialchars($_GET['stanowisko']) : '';


    $wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');

    $pracownikId = isset($_GET['pracownikId']) ? htmlspecialchars($_GET['pracownikId']) : '';
    $expiredUbrania = [];

    if ($pracownikId) {
        $wydaniaRepo = $serviceContainer->getRepository('IssueRepository');
        $wydaniaPracownika = $wydaniaRepo->getWydaniaByPracownikId($pracownikId);

        foreach ($wydaniaPracownika as $wydanie) {
            $expiringUbrania = $wydaneUbraniaRepo->getUbraniaByWydanieIdTermin($wydanie['id_wydania']);
            foreach ($expiringUbrania as $ubranie) {
                $expiredUbrania[] = $ubranie;
            }
        }
    }
}
?>

<div id="alertContainer"></div>

<div class="d-flex align-items-center">
    <h2 class="mb-4"><?php echo __('issue_title'); ?></h2>
    <div id="loadingSpinnerName" class="spinner-border mb-2 mx-4" style="display: none;" role="status">
        <span class="visually-hidden"><?php echo __('loading'); ?></span>
    </div>
</div>

<form id="wydajUbranieForm" action="<?php echo $baseUrl; ?>/app/http/forms/issue_clothing.php" method="post" autocomplete="off">
    <?php echo CsrfHelper::getTokenField(); ?>
    <div class="mb-3 col-md-6">
        <div class="d-flex justify-content-between">
            <label for="username" class="form-label"><?php echo __('issue_employee'); ?>:</label>
        </div>
        <div class="mb-3 position-relative inputcontainer">
            <input type="text" class="form-control" maxlength="30" placeholder="<?php echo __('employee_first_name'); ?> <?php echo __('employee_last_name'); ?>" id="username"
                value="<?php echo trim("$imie $nazwisko $stanowisko") !== '' ? "$imie $nazwisko ($stanowisko)" : ''; ?>" required>
            <input type="hidden" id="pracownikID" name="pracownikID" value="<?php echo $pracownikId; ?>" />
            <ul id="suggestions" class="list-group position-absolute" style="display: none; z-index: 1000; width: 100%; top: 100%;"></ul>
        </div>
    </div>
    <div id="ubraniaContainer" class="p-3 bg-body rounded">
        <div class="border border-2 p-3 row ubranieRow mt-3 mb-3 bg-body rounded">
            <div class="mb-3 col-md-11">
                <div class="form-check form-check-inline border-bottom border-3">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                    <label class="form-check-label" for="inlineRadio1"><?php echo __('issue_clothing_name_size'); ?></label>
                </div>
                <div class="form-check form-check-inline border-bottom border-primary border-3">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" checked>
                    <label class="form-check-label" for="inlineRadio2"><?php echo __('clothing_code'); ?></label>
                </div>
            </div>
            <div class="col-md-2 nazwaSection" style="display: none;">
                <label for="id_ubrania" class="form-label"><?php echo __('clothing_name'); ?>:</label>
                <select id="id_ubrania" name="ubrania[0][id_ubrania]" class="form-select ubranie-select" data-live-search="true" disabled>
                    <option value=""><?php echo __('issue_select_clothing'); ?></option>
                    <?php foreach ($ubrania as $ubranie) { ?>
                        <option value="<?php echo $ubranie['id']; ?>">
                            <?php echo $ubranie['nazwa']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 rozmiarSection" style="display: none;">
                <label for="id_rozmiar" class="form-label"><?php echo __('clothing_size'); ?>:</label>
                <select id="id_rozmiar" name="ubrania[0][id_rozmiar]" class="form-select rozmiar-select" data-live-search="true" disabled>
                    <option value=""><?php echo __('issue_select_size'); ?></option>
                </select>
            </div>
            <div class="col-md-4 kodSection" style="display: block;">
                <label for="kod" class="form-label"><?php echo __('clothing_code'); ?>:</label>
                <input type="text" class="form-control kod-input" id="kod" name="ubrania[0][kod]">
                <input type="hidden" id="id_ubrania" name="ubrania[0][id_ubrania]" value="" />
                <input type="hidden" id="id_rozmiar" name="ubrania[0][id_rozmiar]" value="" />
            </div>
            <div class="col-md-2">
                <label for="ilosc" class="form-label"><?php echo __('clothing_quantity'); ?>:</label>
                <input type="number" class="form-control" min="1" value="1" id="ilosc" name="ubrania[0][ilosc]" required>
            </div>
            <div class="col-md-3">
                <label for="data_waznosci" class="form-label"><?php echo __('issue_expiry_date'); ?>:</label>
                <select id="data_waznosci" name="ubrania[0][data_waznosci]" class="form-select data_w-select" required>
                <?php foreach ($expire_date_months as $expire_date_month): ?>
                        <option value="<?= $expire_date_month; ?>">
                            <?= $expire_date_month; ?> <?php echo __('issue_months'); ?> (<?= newExpirationDate($expire_date_month); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-between">
                <button type="button" class="btn btn-success addUbranieBtn "><i class="bi bi-plus-lg"></i> <?php echo __('issue_add_clothing'); ?></button>
                <button type="button" class="btn btn-danger removeUbranieBtn ms-2" style="display: none;"><i class="bi bi-x-lg"></i> <?php echo __('issue_remove_clothing'); ?></button>
            </div>
        </div>
    </div>
    <div class="mb-5 col-md-6">
        <label for="id_uwagi" class="form-label"><?php echo __('order_notes'); ?>:</label>
        <textarea id="id_uwagi" name="uwagi" rows="4" cols="50" class="form-control"></textarea>
    </div>
    <div class="d-flex align-items-center mt-3 mb-3">
        <button type="submit" class="btn btn-primary submitBtn mb-3 p-3"><?php echo __('issue_submit'); ?></button>
        <div id="loadingSpinner" class="spinner-border mb-2 mx-4" style="display: none;" role="status">
            <span class="visually-hidden"><?php echo __('loading'); ?>...</span>
        </div>
    </div>
</form>

<!-- modal-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel"><?php echo __('issue_status_change_title'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('close'); ?>"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo __('clothing_name'); ?></th>
                            <th scope="col"><?php echo __('clothing_size'); ?></th>
                            <th scope="col"><?php echo __('clothing_quantity'); ?></th>
                            <th scope="col"><?php echo __('issue_expiry_date'); ?></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($expiredUbrania)) : ?>
                            <?php foreach ($expiredUbrania as $ubranie) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ubranie['nazwa_ubrania']); ?></td>
                                    <td><?php echo htmlspecialchars($ubranie['nazwa_rozmiaru']); ?></td>
                                    <td><?php echo htmlspecialchars($ubranie['ilosc']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($ubranie['data_waznosci'])); ?></td>
                                    <td>
                                        <button class="btn btn-secondary inform-btn"
                                            data-id="<?php echo htmlspecialchars($ubranie['id']); ?>"
                                            id="statusBtn-<?php echo $ubranie['id']; ?>"><?php echo __('reports_remove_from_report'); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmButton" class="btn btn-primary"><?php echo __('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    window.fromRaport = <?php echo json_encode($fromRaport); ?>;
</script>

<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>
<?php include_once __DIR__ . '../../layout/footer.php'; ?>

