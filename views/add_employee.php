<?php
include_once __DIR__ . '../../layout/header.php';
?>

<div id="alertContainer"></div>

<h2 class="mb-4"><?php echo __('employee_add_title'); ?></h2>
<form id="pracownikForm" action="<?php echo $baseUrl; ?>/app/http/forms/add_employee.php" method="post" class="needs-validation">
    <?php echo CsrfHelper::getTokenField(); ?>
    <div class="mb-3 col-md-5">
        <label for="imie" class="form-label"><?php echo __('employee_first_name'); ?>:</label>
        <input type="text" class="form-control" id="imie" name="imie" required>
        <div class="invalid-feedback">
            <?php echo __('validation_required'); ?>
        </div>
    </div>

    <div class="mb-3 col-md-5">
        <label for="nazwisko" class="form-label"><?php echo __('employee_last_name'); ?>:</label>
        <input type="text" class="form-control" id="nazwisko" name="nazwisko" required>
        <div class="invalid-feedback">
            <?php echo __('validation_required'); ?>
        </div>
    </div>

    <div class="mb-3 col-md-5">
        <label for="stanowisko" class="form-label"><?php echo __('employee_position'); ?>:</label>
        <input type="text" class="form-control" id="stanowisko" name="stanowisko" required>
        <div class="invalid-feedback">
            <?php echo __('validation_required'); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary submitBtn p-3"><?php echo __('employee_add_title'); ?></button>
</form>

<script type="module" src="<?php echo $baseUrl; ?>/App.js"></script>

<?php include_once __DIR__ . '../../layout/footer.php'; ?>

