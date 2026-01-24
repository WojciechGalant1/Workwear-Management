<?php
use App\Auth\CsrfGuard;

include_once __DIR__ . '../../layout/header.php';
?>

<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('order_add_title'); ?></h2>

<form id="zamowienieForm" action="<?php echo $baseUrl; ?>/app/http/handlers/order/addOrder.php" method="post" class="needs-validation" autocomplete="off" data-ajax-form>
    <?php echo CsrfGuard::getTokenField(); ?>
    <div class="mb-3 p-3" id="ubraniaContainer">
        <div class="row ubranieRow mt-3 mb-3 border border-2 p-3 bg-body rounded">
            <div class="col-md-3 ">
                <label for="kod" class="form-label"><?php echo __('clothing_code'); ?>:</label>
                <input type="text" class="form-control" id="kod" name="ubrania[0][kod]" required>
            </div>
            <div class="col-md-2 position-relative">
                <label for="productName" class="form-label"><?php echo __('clothing_name'); ?>:</label>
                <div class="position-relative inputcontainer">
                    <input type="text" class="form-control" id="productName" name="ubrania[0][nazwa]" value="" required>
                    <ul id="productSuggestions" class="productSuggestions list-group position-absolute" style="display: none; z-index: 1000; width: 100%; top: 100%;"></ul>
                </div>
            </div>
            <div class="col-md-2 position-relative">
                <label for="sizeName" class="form-label"><?php echo __('clothing_size'); ?>:</label>
                <div class="position-relative inputcontainer">
                    <input type="text" class="form-control" id="sizeName" name="ubrania[0][rozmiar]" value="" required>
                    <ul id="sizeSuggestions" class="sizeSuggestions list-group position-absolute" style="display: none; z-index: 1000; width: 100%; top: 100%;"></ul>
                </div>
            </div>
            <div class="col-md-2">
                <label for="ilosc" class="form-label"><?php echo __('clothing_quantity'); ?>:</label>
                <input type="number" class="form-control" name="ubrania[0][ilosc]" min="1" value="1" required>
            </div>
            <div class="col-md-2" style="display: block;">
                <label for="iloscMin" class="form-label"><?php echo __('clothing_min_quantity'); ?>:</label>
                <input type="number" class="form-control" name="ubrania[0][iloscMin]" min="1" value="1" required>
            </div>
            <div class="row mt-3 mb-3 col-md-11">
                <div class="col-md-2">
                    <label for="firma" class="form-label"><?php echo __('clothing_company'); ?>:</label>
                    <input type="text" class="form-control" id="firma" name="ubrania[0][firma]" required>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-success addUbranieBtn"><i class="bi bi-plus-lg"></i> <?php echo __('order_add_item'); ?></button>
                <button type="button" class="btn btn-danger removeUbranieBtn ms-2" style="display: none;"><i class="bi bi-x-lg"></i> <?php echo __('order_remove_item'); ?></button>
            </div>
        </div>
    </div>
    <div class="mb-5 col-md-6">
        <label for="id_uwagi" class="form-label"><?php echo __('order_notes'); ?>:</label>
        <textarea id="id_uwagi" name="uwagi" rows="4" cols="50" class="form-control"></textarea>
    </div>
    <div class="d-flex align-items-center mb-3">
        <button type="submit" class="btn btn-primary submitBtn p-3"><?php echo __('order_add_submit'); ?></button>
        <div id="loadingSpinner" class="spinner-border mb-2 ms-2" style="display: none;" role="status">
            <span class="visually-hidden"><?php echo __('loading'); ?>...</span>
        </div>
    </div>
</form>

<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>

<?php include_once __DIR__ . '../../layout/footer.php'; ?>

