<?php

/**
 * Configuration for JavaScript modules required by each page
 */
return array(
    'add_employee.php' => 'AlertManager',
    'issue_clothing.php' => 'ClothingManager,AlertManager,WorkerSuggestions,ModalIssueClothing,ChangeStatus',
    'employee_list.php' => 'AlertManager,ModalEditEmployee',
    'add_order.php' => 'AlertManager,ProductSuggestions,CheckClothing',
    'issue_history.php' => 'WorkerSuggestions,ChangeStatus,CancelIssue,DestroyClothing',
    'raport.php' => 'RedirectStatus,ChangeStatus',
    'warehouse_list.php' => 'AlertManager,EditClothing',
    'clothing_history.php' => 'ClothingHistoryDetails',
    'default' => ''
); 