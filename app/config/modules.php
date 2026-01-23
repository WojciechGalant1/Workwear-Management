<?php

/**
 * Configuration for JavaScript modules required by each page
 */
return [
    'add_employee.php' => '',  // FormHandler obsługuje automatycznie
    'issue_clothing.php' => 'ClothingManager,WorkerSuggestions,ModalIssueClothing,ChangeStatus',
    'employee_list.php' => 'ModalEditEmployee',
    'add_order.php' => 'ProductSuggestions,CheckClothing',
    'issue_history.php' => 'WorkerSuggestions,ChangeStatus,CancelIssue,DestroyClothing',
    'raport.php' => 'RedirectStatus,ChangeStatus',
    'warehouse_list.php' => 'EditClothing',
    'clothing_history.php' => 'ClothingHistoryDetails',
    'default' => ''
]; 