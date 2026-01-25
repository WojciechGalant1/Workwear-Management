<?php
/**
 * 500 Internal Server Error Page
 * 
 * Available variables:
 * - $exception (Exception) - only in development mode
 */
include_once __DIR__ . '/../../layout/header.php';
?>

<div class="container mt-5">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?php echo __('error_occurred'); ?></h4>
        <p><?php echo __('error_general'); ?></p>
        
        <?php if (ini_get('display_errors') && ($exception ?? null) !== null): ?>
        <hr>
        <details>
            <summary>Debug Info</summary>
            <pre class="mt-2 p-3 bg-light text-dark" style="font-size: 12px;">
Exception: <?php echo htmlspecialchars($exception->getMessage()); ?>

File: <?php echo htmlspecialchars($exception->getFile() . ':' . $exception->getLine()); ?>

Trace:
<?php echo htmlspecialchars($exception->getTraceAsString()); ?>
            </pre>
        </details>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../layout/footer.php'; ?>
