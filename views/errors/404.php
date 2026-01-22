<?php
/**
 * 404 Not Found Error Page
 */
include_once __DIR__ . '/../../layout/header.php';
?>

<div class="container mt-5">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?php echo LocalizationHelper::translate('error_not_found'); ?></h4>
        <p><?php echo LocalizationHelper::translate('error_page_not_found'); ?></p>
        <hr>
        <p class="mb-0">
            <a href="/" class="btn btn-primary"><?php echo LocalizationHelper::translate('back_to_home'); ?></a>
        </p>
    </div>
</div>

<?php include_once __DIR__ . '/../../layout/footer.php'; ?>
