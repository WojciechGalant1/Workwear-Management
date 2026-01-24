<?php
include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';

class ClassModal {
    public function anulujModal(): void {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
        
        echo '
<div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmCancelModalLabel">' . LocalizationHelper::translate('modal_cancel_issue_title') . '</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . LocalizationHelper::translate('close') . '"></button>
            </div>
            <div class="modal-body">
               ' . LocalizationHelper::translate('modal_cancel_issue_message') . '
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . LocalizationHelper::translate('cancel') . '</button>
                <button type="button" class="btn btn-primary" id="confirmCancelBtn">' . LocalizationHelper::translate('confirm') . '</button>
            </div>
        </div>
    </div>
</div>
';
    }

    public function zniszczoneModal(): void {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
        
        echo '
<div class="modal fade" id="confirmDestroyModal" tabindex="-1" aria-labelledby="confirmDestroyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDestroyModalLabel">' . LocalizationHelper::translate('modal_destroy_clothing_title') . '</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . LocalizationHelper::translate('close') . '"></button>
            </div>
            <div class="modal-body">
                ' . LocalizationHelper::translate('modal_destroy_clothing_message') . '
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . LocalizationHelper::translate('cancel') . '</button>
                <button type="button" class="btn btn-primary" id="confirmDestroyBtn">' . LocalizationHelper::translate('confirm') . '</button>
            </div>
        </div>
    </div>
</div>
';
    }
   
}
